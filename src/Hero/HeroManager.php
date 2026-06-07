<?php

declare(strict_types=1);

namespace Jasanika\Hero;

use Jasanika\Admin\SettingsManager;
use Jasanika\Media\MediaManager;

/**
 * Hero configuration manager — M33.
 *
 * Owns all hero-related settings and provides typed accessors
 * for the HeroRenderer. Supports static hero, slider, layout presets,
 * background types, overlay system, and dual buttons.
 *
 * Settings managed here:
 * - Enable/disable
 * - Hero type (static/slider)
 * - Layout preset (centered, left-aligned, split, minimal, fullscreen)
 * - Hero height mode (auto, medium, large, fullscreen)
 * - Content (title, subtitle, description)
 * - Background type (color, image, gradient)
 * - Overlay (enabled, color, opacity)
 * - Buttons (primary and secondary with label, URL, style)
 * - Slider slides (1-3)
 *
 * Backward compatibility:
 * - hero_height, hero_button_text, hero_button_url still work
 * - New settings take priority when both exist
 */
final class HeroManager
{
    private SettingsManager $settingsManager;
    private MediaManager $mediaManager;

    public function __construct(SettingsManager $settingsManager, MediaManager $mediaManager)
    {
        $this->settingsManager = $settingsManager;
        $this->mediaManager = $mediaManager;
    }

    // ---------------------------------------------------------------
    //  General
    // ---------------------------------------------------------------

    /**
     * Whether the hero section is enabled.
     */
    public function isEnabled(): bool
    {
        return $this->settingsManager->get('hero_enabled') === 'yes';
    }

    /**
     * Get the hero type.
     *
     * Returns 'static' or 'slider'.
     */
    public function getHeroType(): string
    {
        $type = $this->settingsManager->get('hero_type');
        return in_array($type, ['static', 'slider'], true) ? $type : 'static';
    }

    // ---------------------------------------------------------------
    //  Layout Presets (M33)
    // ---------------------------------------------------------------

    /**
     * Get the active hero layout slug.
     *
     * M33: Layout presets replace hardcoded centering.
     */
    public function getLayout(): string
    {
        $layout = $this->settingsManager->get('hero_layout');

        if ($layout !== '' && is_string($layout) && HeroLayout::get($layout) !== null) {
            return $layout;
        }

        return HeroLayout::getDefault();
    }

    /**
     * Get the CSS class for the active layout.
     */
    public function getLayoutClass(): string
    {
        return HeroLayout::getClass($this->getLayout());
    }

    // ---------------------------------------------------------------
    //  Content (M33)
    // ---------------------------------------------------------------

    /**
     * Get the hero title.
     */
    public function getHeroTitle(): string
    {
        $title = $this->settingsManager->get('hero_title');
        return is_string($title) ? $title : '';
    }

    /**
     * Get the hero subtitle.
     */
    public function getHeroSubtitle(): string
    {
        $subtitle = $this->settingsManager->get('hero_subtitle');
        return is_string($subtitle) ? $subtitle : '';
    }

    /**
     * Get the hero description.
     *
     * M33: New setting for longer descriptive text.
     */
    public function getHeroDescription(): string
    {
        $desc = $this->settingsManager->get('hero_description');
        return is_string($desc) ? $desc : '';
    }

    // ---------------------------------------------------------------
    //  Height (M33)
    // ---------------------------------------------------------------

    /**
     * Get the hero height mode.
     *
     * Returns 'auto', 'medium', 'large', or 'fullscreen'.
     *
     * M33: Height modes replace the old fixed pixel height.
     */
    public function getHeightMode(): string
    {
        $mode = $this->settingsManager->get('hero_height_mode');

        if (in_array($mode, ['auto', 'medium', 'large', 'fullscreen'], true)) {
            return $mode;
        }

        return 'medium';
    }

    /**
     * Get the CSS min-height value based on the height mode.
     */
    public function getHeightCss(): string
    {
        return match ($this->getHeightMode()) {
            'auto'       => 'auto',
            'medium'     => '400px',
            'large'      => '600px',
            'fullscreen' => '100vh',
            default      => '400px',
        };
    }

    /**
     * Get the hero height CSS value (backward compatible).
     *
     * Falls back to height mode when the old hero_height is not set.
     */
    public function getHeroHeight(): string
    {
        $height = $this->settingsManager->get('hero_height');
        if (is_string($height) && $height !== '') {
            return $height;
        }

        return $this->getHeightCss();
    }

    // ---------------------------------------------------------------
    //  Background (M33)
    // ---------------------------------------------------------------

    /**
     * Get the background type.
     *
     * Returns 'color', 'image', or 'gradient'.
     */
    public function getBackgroundType(): string
    {
        $type = $this->settingsManager->get('hero_bg_type');
        return in_array($type, ['color', 'image', 'gradient'], true) ? $type : 'color';
    }

    /**
     * Get the background color.
     */
    public function getBackgroundColor(): string
    {
        $color = $this->settingsManager->get('hero_bg_color');
        return is_string($color) && $color !== '' ? $color : '';
    }

    /**
     * Get the hero background image attachment ID.
     */
    public function getHeroBackgroundImageId(): int
    {
        return (int) $this->settingsManager->get('hero_background_image');
    }

    /**
     * Get the hero background image URL.
     */
    public function getHeroBackgroundImageUrl(): string
    {
        $id = $this->getHeroBackgroundImageId();
        if ($id <= 0) {
            return '';
        }
        return $this->mediaManager->getAttachmentUrl($id);
    }

    /**
     * Get the gradient start color.
     */
    public function getGradientStart(): string
    {
        $color = $this->settingsManager->get('hero_gradient_start');
        return is_string($color) && $color !== '' ? $color : '';
    }

    /**
     * Get the gradient end color.
     */
    public function getGradientEnd(): string
    {
        $color = $this->settingsManager->get('hero_gradient_end');
        return is_string($color) && $color !== '' ? $color : '';
    }

    /**
     * Build the inline background CSS based on the active background type.
     */
    public function getBackgroundCss(): string
    {
        $type = $this->getBackgroundType();
        $styles = [];

        if ($type === 'color') {
            $color = $this->getBackgroundColor();
            if ($color !== '') {
                $styles[] = 'background-color:' . $color;
            }
        } elseif ($type === 'image') {
            $url = $this->getHeroBackgroundImageUrl();
            if ($url !== '') {
                $styles[] = 'background-image:url(' . esc_url($url) . ')';
                $styles[] = 'background-size:cover';
                $styles[] = 'background-position:center';
            }
        } elseif ($type === 'gradient') {
            $start = $this->getGradientStart();
            $end = $this->getGradientEnd();
            if ($start !== '' && $end !== '') {
                $styles[] = 'background:linear-gradient(135deg,' . $start . ',' . $end . ')';
            } elseif ($start !== '') {
                $styles[] = 'background:' . $start;
            } elseif ($end !== '') {
                $styles[] = 'background:' . $end;
            }
        }

        return implode(';', $styles);
    }

    // ---------------------------------------------------------------
    //  Overlay (M33)
    // ---------------------------------------------------------------

    /**
     * Whether the overlay is enabled.
     */
    public function isOverlayEnabled(): bool
    {
        return $this->settingsManager->get('hero_overlay_enabled') === 'yes';
    }

    /**
     * Get the overlay color.
     */
    public function getOverlayColor(): string
    {
        $color = $this->settingsManager->get('hero_overlay_color');
        return is_string($color) && $color !== '' ? $color : '#1b1a1f';
    }

    /**
     * Get the overlay opacity (0.0 - 1.0).
     */
    public function getOverlayOpacity(): float
    {
        $opacity = $this->settingsManager->get('hero_overlay_opacity');
        $opacity = (float) $opacity;
        return max(0.0, min(1.0, $opacity));
    }

    /**
     * Build the inline overlay CSS.
     */
    public function getOverlayCss(): string
    {
        if (!$this->isOverlayEnabled()) {
            return '';
        }

        return sprintf(
            'background-color:%s;opacity:%s;',
            $this->getOverlayColor(),
            (string) $this->getOverlayOpacity()
        );
    }

    // ---------------------------------------------------------------
    //  Buttons (M33)
    // ---------------------------------------------------------------

    /**
     * Get the primary button label.
     */
    public function getPrimaryButtonLabel(): string
    {
        $label = $this->settingsManager->get('hero_btn_primary_label');

        if (is_string($label) && $label !== '') {
            return $label;
        }

        // Backward compatibility
        $old = $this->settingsManager->get('hero_button_text');
        return is_string($old) ? $old : '';
    }

    /**
     * Get the primary button URL.
     */
    public function getPrimaryButtonUrl(): string
    {
        $url = $this->settingsManager->get('hero_btn_primary_url');

        if (is_string($url) && $url !== '') {
            return $url;
        }

        // Backward compatibility
        $old = $this->settingsManager->get('hero_button_url');
        return is_string($old) ? $old : '';
    }

    /**
     * Get the secondary button label.
     */
    public function getSecondaryButtonLabel(): string
    {
        $label = $this->settingsManager->get('hero_btn_secondary_label');
        return is_string($label) ? $label : '';
    }

    /**
     * Get the secondary button URL.
     */
    public function getSecondaryButtonUrl(): string
    {
        $url = $this->settingsManager->get('hero_btn_secondary_url');
        return is_string($url) ? $url : '';
    }

    /**
     * Get the button style.
     *
     * Returns 'primary', 'secondary', or 'outline'.
     */
    public function getButtonStyle(): string
    {
        $style = $this->settingsManager->get('hero_button_style');
        return in_array($style, ['primary', 'secondary', 'outline'], true) ? $style : 'primary';
    }

    // ---------------------------------------------------------------
    //  Slider
    // ---------------------------------------------------------------

    /**
     * Get all slides for the slider type.
     *
     * @return HeroSlide[]
     */
    public function getSlides(): array
    {
        $slides = [];
        $count = 3;

        for ($i = 1; $i <= $count; $i++) {
            $slide = new HeroSlide(
                $i,
                $this->getSlideSetting('hero_slide_' . $i . '_title'),
                $this->getSlideSetting('hero_slide_' . $i . '_subtitle'),
                (int) $this->getSlideSetting('hero_slide_' . $i . '_image'),
                $this->getSlideSetting('hero_slide_' . $i . '_button_text'),
                $this->getSlideSetting('hero_slide_' . $i . '_button_url')
            );

            if ($slide->hasContent()) {
                $slides[] = $slide;
            }
        }

        return $slides;
    }

    /**
     * Get a single slide by index (1-based).
     */
    public function getSlide(int $index): ?HeroSlide
    {
        $slides = $this->getSlides();

        foreach ($slides as $slide) {
            if ($slide->getIndex() === $index) {
                return $slide;
            }
        }

        return null;
    }

    /**
     * Get a setting value for a slide field.
     */
    private function getSlideSetting(string $key): string
    {
        $value = $this->settingsManager->get($key);
        return is_string($value) ? $value : '';
    }

    // ---------------------------------------------------------------
    //  Debug Info
    // ---------------------------------------------------------------

    /**
     * Get all hero settings for debug output.
     *
     * @return array<string, mixed>
     */
    public function getDebugInfo(): array
    {
        return [
            'Enabled'          => $this->isEnabled() ? 'yes' : 'no',
            'Type'             => $this->getHeroType(),
            'Layout'           => $this->getLayout(),
            'Height Mode'      => $this->getHeightMode(),
            'Title'            => $this->getHeroTitle(),
            'Subtitle'         => $this->getHeroSubtitle(),
            'Description'      => $this->getHeroDescription() !== '' ? 'yes' : 'no',
            'Background Type'  => $this->getBackgroundType(),
            'Overlay'          => $this->isOverlayEnabled() ? 'yes' : 'no',
            'Overlay Opacity'  => (string) $this->getOverlayOpacity(),
            'Primary Button'   => $this->getPrimaryButtonLabel() !== '' ? $this->getPrimaryButtonLabel() . ' → ' . $this->getPrimaryButtonUrl() : '—',
            'Secondary Button' => $this->getSecondaryButtonLabel() !== '' ? $this->getSecondaryButtonLabel() . ' → ' . $this->getSecondaryButtonUrl() : '—',
            'Button Style'     => $this->getButtonStyle(),
        ];
    }
}