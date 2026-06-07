/**
 * Jasanika 2 Modern Color Picker
 *
 * Vanilla JavaScript color picker inspired by VS Code, Figma,
 * and modern design tool workflows.
 *
 * M32 — Modern Color Picker & Theme Designer
 *
 * Features:
 * - Saturation/Lightness square
 * - Hue slider
 * - Opacity slider
 * - HEX input (sync)
 * - RGB input (sync)
 * - Live preview swatch
 * - Floating panel
 * - Theme preview card updates
 * - Palette preset application
 *
 * No dependencies.
 * No jQuery.
 * ~25KB unminified.
 */
(function () {
    'use strict';

    // ============================================================
    //  Color Utilities
    // ============================================================

    var ColorUtils = {
        /**
         * Convert HSV to HEX string.
         */
        hsvToHex: function (h, s, v) {
            var rgb = this.hsvToRgb(h, s, v);
            return this.rgbToHex(rgb.r, rgb.g, rgb.b);
        },

        /**
         * Convert HSV to RGB.
         */
        hsvToRgb: function (h, s, v) {
            var f, p, q, t, hi;
            h = Math.max(0, Math.min(360, h));
            s = Math.max(0, Math.min(100, s));
            v = Math.max(0, Math.min(100, v));

            hi = Math.floor(h / 60) % 6;
            f = h / 60 - Math.floor(h / 60);
            p = v * (1 - s / 100);
            q = v * (1 - f * s / 100);
            t = v * (1 - (1 - f) * s / 100);

            var r, g, b;
            v = v / 100;
            p = p / 100;
            q = q / 100;
            t = t / 100;

            switch (hi) {
                case 0: r = v; g = t; b = p; break;
                case 1: r = q; g = v; b = p; break;
                case 2: r = p; g = v; b = t; break;
                case 3: r = p; g = q; b = v; break;
                case 4: r = t; g = p; b = v; break;
                case 5: r = v; g = p; b = q; break;
            }

            return {
                r: Math.round(r * 255),
                g: Math.round(g * 255),
                b: Math.round(b * 255)
            };
        },

        /**
         * Convert RGB to HEX string.
         */
        rgbToHex: function (r, g, b) {
            return '#' + ((1 << 24) | (r << 16) | (g << 8) | b).toString(16).slice(1).toLowerCase();
        },

        /**
         * Convert HEX string to RGB.
         */
        hexToRgb: function (hex) {
            hex = hex.replace(/^#/, '');
            if (hex.length === 3) {
                hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
            }
            var bigint = parseInt(hex, 16);
            return {
                r: (bigint >> 16) & 255,
                g: (bigint >> 8) & 255,
                b: bigint & 255
            };
        },

        /**
         * Convert RGB to HSV.
         */
        rgbToHsv: function (r, g, b) {
            r = r / 255;
            g = g / 255;
            b = b / 255;

            var max = Math.max(r, g, b),
                min = Math.min(r, g, b);
            var h, s, v = max;

            var d = max - min;
            s = max === 0 ? 0 : d / max;

            if (max === min) {
                h = 0;
            } else {
                switch (max) {
                    case r: h = (g - b) / d + (g < b ? 6 : 0); break;
                    case g: h = (b - r) / d + 2; break;
                    case b: h = (r - g) / d + 4; break;
                }
                h = Math.round(h * 60);
            }

            return {
                h: Math.round(h),
                s: Math.round(s * 100),
                v: Math.round(v * 100)
            };
        },

        /**
         * Parse any color string to HEX.
         */
        toHex: function (color) {
            if (!color) return '#b78acb';
            if (color.charAt(0) === '#') return color;
            if (color.startsWith('rgba') || color.startsWith('rgb')) {
                var parts = color.replace(/[^\d,]/g, '').split(',');
                if (parts.length >= 3) {
                    return this.rgbToHex(
                        parseInt(parts[0]),
                        parseInt(parts[1]),
                        parseInt(parts[2])
                    );
                }
            }
            return '#b78acb';
        },

        /**
         * Get text contrast color (black or white) for a HEX color.
         */
        getContrast: function (hex) {
            var rgb = this.hexToRgb(hex);
            var luminance = (0.299 * rgb.r + 0.587 * rgb.g + 0.114 * rgb.b) / 255;
            return luminance > 0.5 ? '#1b1a1f' : '#f5f2f7';
        },

        /**
         * Get RGBA string for a HEX color with given opacity.
         */
        hexToRgba: function (hex, alpha) {
            var rgb = this.hexToRgb(hex);
            return 'rgba(' + rgb.r + ',' + rgb.g + ',' + rgb.b + ',' + alpha + ')';
        }
    };

    // ============================================================
    //  Color Picker Panel
    // ============================================================

    /**
     * Creates and manages a single color picker floating panel.
     */
    function ColorPickerPanel(swatchEl, hiddenInput, hexInput) {
        this.swatch = swatchEl;
        this.hidden = hiddenInput;
        this.hexInput = hexInput;
        this.panel = null;
        this.isOpen = false;
        this.currentColor = hexInput.value || '#b78acb';

        // HSV state
        var hsv = ColorUtils.rgbToHsv(
            ColorUtils.hexToRgb(this.currentColor).r,
            ColorUtils.hexToRgb(this.currentColor).g,
            ColorUtils.hexToRgb(this.currentColor).b
        );
        this.h = hsv.h;
        this.s = hsv.s;
        this.v = hsv.v;
        this.a = 1;

        this._bindEvents();
    }

    ColorPickerPanel.prototype = {
        _bindEvents: function () {
            var self = this;

            // Click swatch or hex input to open/close panel
            var toggleOpen = function (e) {
                e.stopPropagation();
                if (self.isOpen) {
                    self.close();
                } else {
                    self.open();
                }
            };

            this.swatch.addEventListener('click', toggleOpen);
            this.swatch.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    toggleOpen(e);
                }
            });

            // Typing in hex input updates the color
            this.hexInput.addEventListener('input', function () {
                var val = this.value.trim();
                if (/^#[0-9a-f]{6}$/i.test(val)) {
                    self._setFromHex(val);
                    self._syncPreview();
                    self._updateSliders();
                }
            });

            this.hexInput.addEventListener('blur', function () {
                var val = this.value.trim();
                if (!/^#[0-9a-f]{6}$/i.test(val)) {
                    this.value = self.currentColor;
                }
            });
        },

        open: function () {
            if (this.isOpen) return;
            this.isOpen = true;

            this._buildPanel();
            this._positionPanel();
            this._syncPanelUI();
            this.swatch.classList.add('jas-cp__swatch--active');

            var self = this;
            var closeHandler = function (e) {
                if (self.panel && !self.panel.contains(e.target) && e.target !== self.swatch && e.target !== self.hexInput) {
                    self.close();
                    document.removeEventListener('mousedown', closeHandler);
                }
            };

            // Delay to avoid immediate close from the opening click
            setTimeout(function () {
                document.addEventListener('mousedown', closeHandler);
            }, 10);
        },

        close: function () {
            if (!this.isOpen) return;
            this.isOpen = false;

            if (this.panel && this.panel.parentNode) {
                this.panel.parentNode.removeChild(this.panel);
            }
            this.panel = null;
            this.swatch.classList.remove('jas-cp__swatch--active');
        },

        _buildPanel: function () {
            var panel = document.createElement('div');
            panel.className = 'jas-cp-panel';
            panel.setAttribute('role', 'dialog');
            panel.setAttribute('aria-label', 'Color picker');

            // Saturation square
            var satDiv = document.createElement('div');
            satDiv.className = 'jas-cp-panel__sat';
            var satInner = document.createElement('div');
            satInner.className = 'jas-cp-panel__sat-inner';
            var satCanvas = document.createElement('canvas');
            satCanvas.className = 'jas-cp-panel__sat-canvas';
            satCanvas.width = 200;
            satCanvas.height = 160;

            var cursor = document.createElement('div');
            cursor.className = 'jas-cp-panel__cursor';

            satInner.appendChild(satCanvas);
            satInner.appendChild(cursor);
            satDiv.appendChild(satInner);

            // Hue slider
            var hueDiv = document.createElement('div');
            hueDiv.className = 'jas-cp-panel__slider';
            var hueTrack = document.createElement('div');
            hueTrack.className = 'jas-cp-panel__track jas-cp-panel__track--hue';
            var hueThumb = document.createElement('div');
            hueThumb.className = 'jas-cp-panel__thumb';
            hueTrack.appendChild(hueThumb);
            hueDiv.appendChild(hueTrack);

            // Opacity slider
            var opDiv = document.createElement('div');
            opDiv.className = 'jas-cp-panel__slider';
            var opTrack = document.createElement('div');
            opTrack.className = 'jas-cp-panel__track jas-cp-panel__track--opacity';
            var opThumb = document.createElement('div');
            opThumb.className = 'jas-cp-panel__thumb';
            opTrack.appendChild(opThumb);
            opDiv.appendChild(opTrack);

            // Inputs row
            var inputsDiv = document.createElement('div');
            inputsDiv.className = 'jas-cp-panel__inputs';

            // HEX
            var hexGroup = document.createElement('div');
            hexGroup.className = 'jas-cp-panel__input-group';
            var hexLabel = document.createElement('span');
            hexLabel.className = 'jas-cp-panel__input-label';
            hexLabel.textContent = 'HEX';
            var hexField = document.createElement('input');
            hexField.type = 'text';
            hexField.className = 'jas-cp-panel__input jas-cp-panel__input--hex';
            hexField.maxLength = 9;
            hexField.spellcheck = false;
            hexGroup.appendChild(hexLabel);
            hexGroup.appendChild(hexField);

            // R
            var rGroup = document.createElement('div');
            rGroup.className = 'jas-cp-panel__input-group';
            var rLabel = document.createElement('span');
            rLabel.className = 'jas-cp-panel__input-label';
            rLabel.textContent = 'R';
            var rField = document.createElement('input');
            rField.type = 'number';
            rField.className = 'jas-cp-panel__input jas-cp-panel__input--rgb';
            rField.min = 0;
            rField.max = 255;
            rGroup.appendChild(rLabel);
            rGroup.appendChild(rField);

            // G
            var gGroup = document.createElement('div');
            gGroup.className = 'jas-cp-panel__input-group';
            var gLabel = document.createElement('span');
            gLabel.className = 'jas-cp-panel__input-label';
            gLabel.textContent = 'G';
            var gField = document.createElement('input');
            gField.type = 'number';
            gField.className = 'jas-cp-panel__input jas-cp-panel__input--rgb';
            gField.min = 0;
            gField.max = 255;
            gGroup.appendChild(gLabel);
            gGroup.appendChild(gField);

            // B
            var bGroup = document.createElement('div');
            bGroup.className = 'jas-cp-panel__input-group';
            var bLabel = document.createElement('span');
            bLabel.className = 'jas-cp-panel__input-label';
            bLabel.textContent = 'B';
            var bField = document.createElement('input');
            bField.type = 'number';
            bField.className = 'jas-cp-panel__input jas-cp-panel__input--rgb';
            bField.min = 0;
            bField.max = 255;
            bGroup.appendChild(bLabel);
            bGroup.appendChild(bField);

            inputsDiv.appendChild(hexGroup);
            inputsDiv.appendChild(rGroup);
            inputsDiv.appendChild(gGroup);
            inputsDiv.appendChild(bGroup);

            panel.appendChild(satDiv);
            panel.appendChild(hueDiv);
            panel.appendChild(opDiv);
            panel.appendChild(inputsDiv);

            this.panel = panel;
            this.satCanvas = satCanvas;
            this.satCursor = cursor;
            this.satInner = satInner;
            this.hueTrack = hueTrack;
            this.hueThumb = hueThumb;
            this.opTrack = opTrack;
            this.opThumb = opThumb;
            this.hexField = hexField;
            this.rField = rField;
            this.gField = gField;
            this.bField = bField;

            document.body.appendChild(panel);

            // Draw saturation canvas
            this._drawSatCanvas();
            this._initDragHandlers();

            // Input sync
            var self = this;
            hexField.addEventListener('input', function () {
                var val = this.value.trim();
                if (/^#[0-9a-f]{6}$/i.test(val)) {
                    self._setFromHex(val);
                    self._syncPreview();
                    self._updateSliders();
                    self._drawSatCanvas();
                }
            });

            [rField, gField, bField].forEach(function (field) {
                field.addEventListener('input', function () {
                    var r = parseInt(rField.value) || 0;
                    var g = parseInt(gField.value) || 0;
                    var b = parseInt(bField.value) || 0;
                    r = Math.max(0, Math.min(255, r));
                    g = Math.max(0, Math.min(255, g));
                    b = Math.max(0, Math.min(255, b));
                    var hex = ColorUtils.rgbToHex(r, g, b);
                    self._setFromHex(hex);
                    self._syncPreview();
                    self._updateSliders();
                    self._drawSatCanvas();
                });
            });
        },

        _drawSatCanvas: function () {
            var ctx = this.satCanvas.getContext('2d');
            var w = 200, h = 160;

            // Hue color at top-right corner
            var hueColor = ColorUtils.hsvToHex(this.h, 100, 100);

            // White → hue gradient (top to bottom: white to hue)
            var grad = ctx.createLinearGradient(0, 0, w, 0);
            grad.addColorStop(0, '#ffffff');
            grad.addColorStop(1, hueColor);
            ctx.fillStyle = grad;
            ctx.fillRect(0, 0, w, h);

            // Transparent → black gradient (top to bottom)
            var grad2 = ctx.createLinearGradient(0, 0, 0, h);
            grad2.addColorStop(0, 'rgba(0,0,0,0)');
            grad2.addColorStop(1, '#000000');
            ctx.fillStyle = grad2;
            ctx.fillRect(0, 0, w, h);
        },

        _initDragHandlers: function () {
            var self = this;

            // Saturation square dragging
            var satDrag = function (e) {
                var rect = self.satInner.getBoundingClientRect();
                var x = Math.max(0, Math.min(1, (e.clientX - rect.left) / rect.width));
                var y = Math.max(0, Math.min(1, (e.clientY - rect.top) / rect.height));
                self.s = Math.round((1 - x) * 100);
                self.v = Math.round((1 - y) * 100);
                self._updateFromSat();
                self._syncPreview();
            };

            self._addDragHandler(self.satInner, satDrag, function () {
                self._drawSatCanvas();
            });

            // Hue slider
            var hueDrag = function (e) {
                var rect = self.hueTrack.getBoundingClientRect();
                var pct = Math.max(0, Math.min(1, (e.clientX - rect.left) / rect.width));
                self.h = Math.round(pct * 360);
                self._updateFromSliders();
                self._syncPreview();
                self._drawSatCanvas();
            };

            self._addDragHandler(self.hueTrack, hueDrag);

            // Opacity slider
            var opDrag = function (e) {
                var rect = self.opTrack.getBoundingClientRect();
                var pct = Math.max(0, Math.min(1, (e.clientX - rect.left) / rect.width));
                self.a = Math.round(pct * 100) / 100;
                self._updateFromSliders();
                self._syncPreview();
            };

            self._addDragHandler(self.opTrack, opDrag);
        },

        _addDragHandler: function (track, onDrag, onEnd) {
            var self = this;
            var isDragging = false;

            var moveHandler = function (e) {
                if (!isDragging) return;
                e.preventDefault();
                onDrag(e);
            };

            var upHandler = function () {
                if (isDragging) {
                    isDragging = false;
                    document.removeEventListener('mousemove', moveHandler);
                    document.removeEventListener('mouseup', upHandler);
                    if (onEnd) onEnd();
                }
            };

            track.addEventListener('mousedown', function (e) {
                e.preventDefault();
                isDragging = true;
                onDrag(e);
                document.addEventListener('mousemove', moveHandler);
                document.addEventListener('mouseup', upHandler);
            });
        },

        _positionPanel: function () {
            var rect = this.swatch.getBoundingClientRect();
            var panelWidth = 240;
            var panelHeight = 380;

            var top = rect.bottom + 4;
            var left = rect.left;

            // Ensure panel stays within viewport
            if (left + panelWidth > window.innerWidth - 10) {
                left = window.innerWidth - panelWidth - 10;
            }
            if (left < 10) {
                left = 10;
            }

            this.panel.style.top = top + 'px';
            this.panel.style.left = left + 'px';
        },

        _setFromHex: function (hex) {
            this.currentColor = hex;
            var rgb = ColorUtils.hexToRgb(hex);
            var hsv = ColorUtils.rgbToHsv(rgb.r, rgb.g, rgb.b);
            this.h = hsv.h;
            this.s = hsv.s;
            this.v = hsv.v;
        },

        _updateFromSat: function () {
            this.currentColor = ColorUtils.hsvToHex(this.h, this.s, this.v);
            this._syncFields();
        },

        _updateFromSliders: function () {
            this.currentColor = ColorUtils.hsvToHex(this.h, this.s, this.v);
            this._syncFields();
        },

        _syncFields: function () {
            var rgb = ColorUtils.hexToRgb(this.currentColor);
            this.hexField.value = this.currentColor;
            this.rField.value = rgb.r;
            this.gField.value = rgb.g;
            this.bField.value = rgb.b;
            this.hexInput.value = this.currentColor;
            this.hidden.value = this.currentColor;
        },

        _syncPreview: function () {
            this.swatch.style.backgroundColor = this.currentColor;
            this.hexInput.value = this.currentColor;
            this.hidden.value = this.currentColor;

            // Update theme preview
            updateThemePreview();
        },

        _syncPanelUI: function () {
            this.hexField.value = this.currentColor;
            var rgb = ColorUtils.hexToRgb(this.currentColor);
            this.rField.value = rgb.r;
            this.gField.value = rgb.g;
            this.bField.value = rgb.b;

            this._updateSliders();
        },

        _updateSliders: function () {
            // Position cursors
            var cx = (1 - this.s / 100) * this.satInner.offsetWidth;
            var cy = (1 - this.v / 100) * this.satInner.offsetHeight;
            this.satCursor.style.left = (cx - 6) + 'px';
            this.satCursor.style.top = (cy - 6) + 'px';

            var huePct = this.h / 360;
            this.hueThumb.style.left = (huePct * 100) + '%';

            var opPct = this.a;
            this.opThumb.style.left = (opPct * 100) + '%';

            // Opacity track background
            this.opTrack.style.background = 'linear-gradient(to right, transparent, ' + this.currentColor + ')';
        }
    };

    // ============================================================
    //  Theme Preview Updater
    // ============================================================

    /**
     * Update the theme preview card with current color values.
     */
    function updateThemePreview() {
        var preview = document.querySelector('.jas-theme-preview');
        if (!preview) return;

        // Read current colors from all color pickers
        var colors = {};
        var pickers = document.querySelectorAll('.jas-cp');

        pickers.forEach(function (cp) {
            var key = cp.getAttribute('data-key');
            var hexInput = cp.querySelector('.jas-cp__hex');
            if (key && hexInput) {
                var cleanKey = key.replace(/_/g, '-');
                colors[cleanKey] = hexInput.value;
            }
        });

        // Update header preview
        var header = preview.querySelector('.jas-theme-preview__header');
        if (header && colors['primary-color']) {
            header.style.backgroundColor = ColorUtils.hexToRgba(colors['primary-color'], 0.12);
            header.style.borderBottom = '1px solid ' + ColorUtils.hexToRgba(colors['primary-color'], 0.2);
        }

        // Update buttons
        var primaryBtn = preview.querySelector('.jas-theme-preview__btn--primary');
        if (primaryBtn && colors['primary-color']) {
            primaryBtn.style.backgroundColor = colors['primary-color'];
            primaryBtn.style.color = ColorUtils.getContrast(colors['primary-color']);
        }

        var accentBtn = preview.querySelector('.jas-theme-preview__btn--accent');
        if (accentBtn && colors['accent-color']) {
            accentBtn.style.backgroundColor = colors['accent-color'];
            accentBtn.style.color = ColorUtils.getContrast(colors['accent-color']);
        }

        // Update card surfaces
        var cardHeader = preview.querySelector('.jas-theme-preview__card-header');
        if (cardHeader && colors['surface-color']) {
            cardHeader.style.backgroundColor = colors['surface-color'];
        }

        var cardBody = preview.querySelector('.jas-theme-preview__card-body');
        if (cardBody && colors['surface-color']) {
            cardBody.style.backgroundColor = ColorUtils.hexToRgba(colors['surface-color'], 0.6);
        }

        // Update preview text colors
        var allHeading = preview.querySelectorAll('.jas-theme-preview__heading, .jas-theme-preview__card-header, .jas-theme-preview__toggle');
        if (colors['heading-color']) {
            allHeading.forEach(function (el) {
                el.style.color = colors['heading-color'];
            });
        }

        var allBody = preview.querySelectorAll('.jas-theme-preview__body-text, .jas-theme-preview__card-body p');
        if (colors['text-color']) {
            allBody.forEach(function (el) {
                el.style.color = colors['text-color'];
            });
        }
    }

    // ============================================================
    //  Palette Preset Application
    // ============================================================

    /**
     * Color palette definitions.
     * Each palette defines colors that map to setting keys.
     */
    var PALETTES = {
        'default': {
            'primary-color': '#b78acb',
            'secondary-color': '#24212b',
            'accent-color': '#f1c95d',
            'background-color': '#1b1a1f',
            'surface-color': '#24212b',
            'text-color': '#f5f2f7',
            'heading-color': '#f5f2f7',
            'border-color': 'rgba(255,255,255,0.08)'
        },
        'modern': {
            'primary-color': '#9b72aa',
            'secondary-color': '#2a2533',
            'accent-color': '#e8c44a',
            'background-color': '#1e1d24',
            'surface-color': '#272530',
            'text-color': '#f0edf2',
            'heading-color': '#ffffff',
            'border-color': 'rgba(255,255,255,0.06)'
        },
        'minimal': {
            'primary-color': '#8c8c8c',
            'secondary-color': '#2c2c2c',
            'accent-color': '#d4d4d4',
            'background-color': '#1a1a1a',
            'surface-color': '#242424',
            'text-color': '#e8e8e8',
            'heading-color': '#f0f0f0',
            'border-color': 'rgba(255,255,255,0.05)'
        },
        'business': {
            'primary-color': '#4a90d9',
            'secondary-color': '#2c3e50',
            'accent-color': '#f39c12',
            'background-color': '#1a1d23',
            'surface-color': '#242830',
            'text-color': '#ecf0f1',
            'heading-color': '#ffffff',
            'border-color': 'rgba(255,255,255,0.07)'
        },
        'dark': {
            'primary-color': '#c084fc',
            'secondary-color': '#1e1b2e',
            'accent-color': '#fbbf24',
            'background-color': '#0f0d17',
            'surface-color': '#1a1730',
            'text-color': '#e2dff0',
            'heading-color': '#f0edff',
            'border-color': 'rgba(255,255,255,0.06)'
        },
        'light': {
            'primary-color': '#7c3aed',
            'secondary-color': '#f5f3ff',
            'accent-color': '#f59e0b',
            'background-color': '#ffffff',
            'surface-color': '#f8f7fc',
            'text-color': '#1f1a2e',
            'heading-color': '#0f0a1e',
            'border-color': 'rgba(0,0,0,0.08)'
        },
        'warm': {
            'primary-color': '#d9776a',
            'secondary-color': '#2c2220',
            'accent-color': '#d4a373',
            'background-color': '#1f1a18',
            'surface-color': '#2a2422',
            'text-color': '#f0e8e4',
            'heading-color': '#f5ede8',
            'border-color': 'rgba(255,255,255,0.06)'
        },
        'cold': {
            'primary-color': '#60a5fa',
            'secondary-color': '#1e293b',
            'accent-color': '#34d399',
            'background-color': '#0f172a',
            'surface-color': '#1e293b',
            'text-color': '#e2e8f0',
            'heading-color': '#f1f5f9',
            'border-color': 'rgba(255,255,255,0.06)'
        }
    };

    /**
     * Apply a palette preset to all color picker fields.
     */
    function applyPalette(slug) {
        var palette = PALETTES[slug];
        if (!palette) return;

        var pickers = document.querySelectorAll('.jas-cp');

        pickers.forEach(function (cp) {
            var key = cp.getAttribute('data-key');
            if (!key) return;

            var colorKey = key.replace(/_/g, '-');
            var color = palette[colorKey];
            if (!color) return;

            var swatch = cp.querySelector('.jas-cp__swatch');
            var hexInput = cp.querySelector('.jas-cp__hex');
            var hidden = cp.querySelector('.jas-cp__hidden');

            if (swatch) swatch.style.backgroundColor = color;
            if (hexInput) hexInput.value = color;
            if (hidden) hidden.value = color;
        });

        updateThemePreview();
    }

    // ============================================================
    //  Initialization
    // ============================================================

    function init() {
        var pickers = document.querySelectorAll('.jas-cp');
        if (!pickers.length) return;

        // Initialize each color picker
        pickers.forEach(function (cp) {
            var key = cp.getAttribute('data-key');
            var swatch = cp.querySelector('.jas-cp__swatch');
            var hidden = cp.querySelector('.jas-cp__hidden');
            var hexInput = cp.querySelector('.jas-cp__hex');

            if (!swatch || !hidden || !hexInput) return;

            new ColorPickerPanel(swatch, hidden, hexInput);
        });

        // Initialize palette buttons
        var paletteBtns = document.querySelectorAll('.jas-palettes__item');
        paletteBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var slug = this.getAttribute('data-palette');
                // Remove active state from all buttons
                paletteBtns.forEach(function (b) {
                    b.classList.remove('jas-palettes__item--active');
                });
                this.classList.add('jas-palettes__item--active');
                applyPalette(slug);
            });
        });

        // Initial theme preview update
        updateThemePreview();

        // Update preview when form changes
        document.addEventListener('change', function () {
            updateThemePreview();
        });
    }

    // Run on DOMContentLoaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();