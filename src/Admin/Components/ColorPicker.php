<?php

declare(strict_types=1);

namespace Jasanika\Admin\Components;

/**
 * Modern Color Picker Component — M32.
 *
 * Renders a color picker field with preview swatch and HEX input.
 * The frontend JS enhances this with a floating picker panel
 * containing saturation square, hue slider, and HEX/RGB inputs.
 *
 * Responsibilities:
 * - Render color picker field with swatch preview
 * - Render HEX input synchronized with color value
 * - Output data attributes for JS enhancement
 * - No picker panel HTML (JS generates the floating panel)
 *
 * Architecture:
 * - ColorPicker owns UI rendering
 * - Settings Framework owns value storage
 * - ThemeSettingsCompiler owns variable generation
 * - No duplicated logic
 *
 * Usage in templates:
 *   ColorPicker::render('primary_color', '#b78acb', 'Primary Color');
 *
 * M32 — Modern Color Picker & Theme Designer
 */
final class ColorPicker
{
    /**
     * Render a single color picker field.
     *
     * Outputs a swatch + HEX input combo that gets enhanced
     * into a full color picker by admin-color-picker.js.
     *
     * @param string $key   Setting key (used as form field name and id).
     * @param string $value Current color value (HEX, RGB, or HSL).
     * @param string $label Field label text.
     */
    public static function render(string $key, string $value, string $label = ''): void
    {
        $value = $value !== '' ? $value : '#b78acb';

        printf(
            '<div class="jas-cp" data-key="%s">',
            esc_attr($key)
        );

        // Hidden field for WordPress Settings API submission
        printf(
            '<input type="hidden" id="%s" name="%s" value="%s" class="jas-cp__hidden" />',
            esc_attr($key),
            esc_attr($key),
            esc_attr($value)
        );

        // Swatch preview
        printf(
            '<span class="jas-cp__swatch" style="background-color:%s;" tabindex="0" role="button" aria-label="%s"></span>',
            esc_attr($value),
            esc_attr__('Pick color', 'jasanika')
        );

        // HEX text input (visible, editable)
        printf(
            '<input type="text" class="jas-cp__hex" value="%s" maxlength="9" spellcheck="false" aria-label="%s" />',
            esc_attr($value),
            esc_attr($label ?: $key)
        );

        echo '</div>';
    }

    /**
     * Render the color palette preset selector.
     *
     * Displays clickable palette swatches that apply preset
     * color values to all color picker fields on the page.
     *
     * M32 — Color Palette System (Part 6).
     *
     * @param array<string, string> $palettes Palette slug => label.
     */
    public static function renderPalettes(array $palettes = []): void
    {
        if (empty($palettes)) {
            $palettes = [
                'default' => __('Default', 'jasanika'),
                'modern'  => __('Modern', 'jasanika'),
                'minimal' => __('Minimal', 'jasanika'),
                'business'=> __('Business', 'jasanika'),
                'dark'    => __('Dark', 'jasanika'),
                'light'   => __('Light', 'jasanika'),
                'warm'    => __('Warm', 'jasanika'),
                'cold'    => __('Cold', 'jasanika'),
            ];
        }

        echo '<div class="jas-palettes">';

        foreach ($palettes as $slug => $label) {
            printf(
                '<button type="button" class="jas-palettes__item" data-palette="%s" title="%s">',
                esc_attr($slug),
                esc_attr($label)
            );
            echo '<span class="jas-palettes__label">' . esc_html($label) . '</span>';
            echo '</button>';
        }

        echo '</div>';
    }

    /**
     * Render the theme preview card.
     *
     * Displays a live preview of the current color scheme
     * with header, button, card, and typography samples.
     *
     * M32 — Theme Preview Card (Part 5).
     */
    public static function renderPreview(): void
    {
        echo '<div class="jas-theme-preview">';

        // Collapsible toggle header (default collapsed)
        echo '<button type="button" class="jas-theme-preview__toggle" aria-expanded="false" onclick="';
        echo 'var b=this.nextElementSibling,n=this.querySelector(\'.jas-theme-preview__toggle-icon\');';
        echo 'if(b){var o=b.classList.contains(\'jas-theme-preview__body--open\');';
        echo 'b.classList.toggle(\'jas-theme-preview__body--open\');';
        echo 'this.setAttribute(\'aria-expanded\',!o);';
        echo 'n.style.transform=o?\'rotate(-90deg)\':\'rotate(0deg)\';}';
        echo '">';
        echo '<span class="jas-theme-preview__toggle-icon">&#9660;</span>';
        echo esc_html__('Theme Preview', 'jasanika');
        echo '</button>';

        // Preview body (hidden by default)
        echo '<div class="jas-theme-preview__body">';

        // Header preview
        echo '<div class="jas-theme-preview__header">';
        echo '<span class="jas-theme-preview__brand">' . esc_html__('Site Name', 'jasanika') . '</span>';
        echo '<span class="jas-theme-preview__nav-link">' . esc_html__('Home', 'jasanika') . '</span>';
        echo '<span class="jas-theme-preview__nav-link">' . esc_html__('About', 'jasanika') . '</span>';
        echo '<span class="jas-theme-preview__nav-link">' . esc_html__('Contact', 'jasanika') . '</span>';
        echo '</div>';

        // Button preview
        echo '<div class="jas-theme-preview__buttons">';
        printf(
            '<button type="button" class="jas-theme-preview__btn jas-theme-preview__btn--primary">%s</button>',
            esc_html__('Primary Button', 'jasanika')
        );
        printf(
            '<button type="button" class="jas-theme-preview__btn jas-theme-preview__btn--accent">%s</button>',
            esc_html__('Accent Button', 'jasanika')
        );
        echo '</div>';

        // Card preview
        echo '<div class="jas-theme-preview__card">';
        echo '<div class="jas-theme-preview__card-header">' . esc_html__('Card Title', 'jasanika') . '</div>';
        echo '<div class="jas-theme-preview__card-body">';
        echo '<p>' . esc_html__('Sample card content with body text to demonstrate the typography and color scheme in context.', 'jasanika') . '</p>';
        echo '</div>';
        echo '</div>';

        // Typography preview
        echo '<div class="jas-theme-preview__typo">';
        echo '<div class="jas-theme-preview__heading">' . esc_html__('Heading Typography', 'jasanika') . '</div>';
        echo '<p class="jas-theme-preview__body-text">' . esc_html__('Body text sample demonstrating the selected font family, color, and line height in a realistic reading context.', 'jasanika') . '</p>';
        echo '</div>';

        echo '</div>'; // .jas-theme-preview__body
        echo '</div>'; // .jas-theme-preview
    }
}