<?php

declare(strict_types=1);

namespace Jasanika\Design;

use Jasanika\Admin\SettingsManager;

/**
 * Centralized frontend design settings.
 *
 * Single source of truth for frontend appearance configuration.
 * Reads values from SettingsManager and provides normalized
 * design values to the rendering pipeline.
 *
 * Responsibilities:
 * - Centralize frontend design settings
 * - Read values from SettingsManager
 * - Provide normalized design values
 * - Act as single source of truth for frontend appearance
 *
 * Managed settings:
 * - Primary Color
 * - Secondary Color
 * - Accent Color
 * - Background Color
 * - Surface Color
 * - Text Color
 * - Heading Color
 * - Border Color
 * - Typography
 * - Container Width
 * - Site Layout
 *
 * M27: Added color scheme settings (secondary, accent, background,
 * surface, text, heading, border) and expanded typography options.
 *
 * No direct settings lookups should occur outside this class.
 */
final class DesignSettingsManager
{
    private SettingsManager $settingsManager;

    public function __construct(SettingsManager $settingsManager)
    {
        $this->settingsManager = $settingsManager;
    }

    /**
     * Get the primary color hex value.
     *
     * Falls back to #b78acb (design system primary) when no value is set.
     */
    public function getPrimaryColor(): string
    {
        $color = $this->settingsManager->get('primary_color');

        if (empty($color) || !is_string($color)) {
            return '#b78acb';
        }

        return $color;
    }

    /**
     * Get the font-family CSS value based on the typography setting.
     *
     * M27: Expanded font options — Inter, Roboto, Poppins, Montserrat, Open Sans.
     */
    public function getFontFamily(): string
    {
        $typography = $this->settingsManager->get('typography');

        return match ($typography) {
            'inter'      => '"Inter", sans-serif',
            'roboto'     => '"Roboto", sans-serif',
            'poppins'    => '"Poppins", sans-serif',
            'montserrat' => '"Montserrat", sans-serif',
            'open-sans'  => '"Open Sans", sans-serif',
            default      => 'system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
        };
    }

    /**
     * Get the raw typography key (system, inter, roboto, poppins, montserrat, open-sans).
     *
     * M27: Returns lowercase key for all supported fonts.
     */
    public function getTypographyKey(): string
    {
        $typography = $this->settingsManager->get('typography');

        if (empty($typography) || !is_string($typography)) {
            return 'system';
        }

        $valid = ['system', 'inter', 'roboto', 'poppins', 'montserrat', 'open-sans'];

        return in_array($typography, $valid, true) ? $typography : 'system';
    }

    /**
     * Get the container width as a CSS length value.
     */
    public function getContainerWidth(): string
    {
        $width = $this->settingsManager->get('container_width');

        if (empty($width)) {
            $width = '1200';
        }

        return $width . 'px';
    }

    /**
     * Get the site layout value (boxed or full-width).
     */
    public function getSiteLayout(): string
    {
        $layout = $this->settingsManager->get('site_layout');

        if (empty($layout) || !is_string($layout)) {
            return 'full-width';
        }

        return $layout;
    }

    /**
     * Get the secondary color hex value.
     *
     * M27: New color scheme setting. Falls back to #24212b.
     */
    public function getSecondaryColor(): string
    {
        $color = $this->settingsManager->get('secondary_color');

        if (empty($color) || !is_string($color)) {
            return '#24212b';
        }

        return $color;
    }

    /**
     * Get the accent color hex value.
     *
     * M27: New color scheme setting. Falls back to #f1c95d.
     */
    public function getAccentColor(): string
    {
        $color = $this->settingsManager->get('accent_color');

        if (empty($color) || !is_string($color)) {
            return '#f1c95d';
        }

        return $color;
    }

    /**
     * Get the background color hex value.
     *
     * M27: New color scheme setting. Falls back to #1b1a1f.
     */
    public function getBackgroundColor(): string
    {
        $color = $this->settingsManager->get('background_color');

        if (empty($color) || !is_string($color)) {
            return '#1b1a1f';
        }

        return $color;
    }

    /**
     * Get the surface color hex value.
     *
     * M27: New color scheme setting. Falls back to #24212b.
     */
    public function getSurfaceColor(): string
    {
        $color = $this->settingsManager->get('surface_color');

        if (empty($color) || !is_string($color)) {
            return '#24212b';
        }

        return $color;
    }

    /**
     * Get the text color hex value.
     *
     * M27: New color scheme setting. Falls back to #f5f2f7.
     */
    public function getTextColor(): string
    {
        $color = $this->settingsManager->get('text_color');

        if (empty($color) || !is_string($color)) {
            return '#f5f2f7';
        }

        return $color;
    }

    /**
     * Get the heading color hex value.
     *
     * M27: New color scheme setting. Falls back to #f5f2f7.
     */
    public function getHeadingColor(): string
    {
        $color = $this->settingsManager->get('heading_color');

        if (empty($color) || !is_string($color)) {
            return '#f5f2f7';
        }

        return $color;
    }

    /**
     * Get the border color hex value.
     *
     * M27: New color scheme setting. Falls back to rgba(255,255,255,0.08).
     */
    public function getBorderColor(): string
    {
        $color = $this->settingsManager->get('border_color');

        if (empty($color) || !is_string($color)) {
            return 'rgba(255,255,255,0.08)';
        }

        return $color;
    }

    /**
     * Get the primary color hover value.
     *
     * Calculates a lightened hover variant from the current primary color.
     * Uses a predefined lightening factor of 10 % toward white.
     * Falls back to #c79cda (design system primary hover) when no value is set.
     */
    public function getPrimaryColorHover(): string
    {
        $color = $this->getPrimaryColor();

        // Match known primary colors for deterministic behavior
        return match ($color) {
            '#b78acb' => '#c79cda',
            default   => $this->lightenColor($color, 0.10),
        };
    }

    /**
     * Lighten a hex color by a given factor toward white.
     *
     * @param string $hex     Hex color string (with or without #).
     * @param float  $factor  Lightening factor (0.0 = no change, 1.0 = white).
     */
    private function lightenColor(string $hex, float $factor): string
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) !== 6) {
            return $hex;
        }

        $r = (int) hexdec(substr($hex, 0, 2));
        $g = (int) hexdec(substr($hex, 2, 2));
        $b = (int) hexdec(substr($hex, 4, 2));

        $r = (int) round($r + (255 - $r) * $factor);
        $g = (int) round($g + (255 - $g) * $factor);
        $b = (int) round($b + (255 - $b) * $factor);

        return sprintf('#%02x%02x%02x', min(255, $r), min(255, $g), min(255, $b));
    }

    /**
     * Get a layout control setting value.
     *
     * Reads layout-related settings (header width, content width, etc.)
     * from SettingsManager and returns them as CSS-ready strings.
     *
     * @param string $key The setting key (e.g. 'layout_header_width').
     * @return string The CSS value or empty string if not set.
     */
    public function getLayoutSetting(string $key): string
    {
        $value = $this->settingsManager->get($key);

        if (empty($value) || !is_string($value)) {
            return '';
        }

        return $value;
    }

    /**
     * Get all design settings as an associative array of token key to value.
     *
     * Returns normalized CSS-ready values suitable for custom property generation.
     *
     * @return array<string, string>
     */
    public function getAllTokens(): array
    {
        return [
            '--jas-primary-color'    => $this->getPrimaryColor(),
            '--jas-primary-hover'    => $this->getPrimaryColorHover(),
            '--jas-secondary-color'  => $this->getSecondaryColor(),
            '--jas-accent-color'     => $this->getAccentColor(),
            '--jas-color-background' => $this->getBackgroundColor(),
            '--jas-color-surface'    => $this->getSurfaceColor(),
            '--jas-color-text'       => $this->getTextColor(),
            '--jas-color-heading'    => $this->getHeadingColor(),
            '--jas-color-border'     => $this->getBorderColor(),
            '--jas-font-family'      => $this->getFontFamily(),
            '--jas-container-width'  => $this->getContainerWidth(),
            '--jas-site-layout'      => $this->getSiteLayout(),
        ];
    }

    /**
     * Get debug-friendly representation of all design settings.
     *
     * @return array<string, string>
     */
    public function getDebugInfo(): array
    {
        return [
            'Primary Color'    => $this->getPrimaryColor(),
            'Primary Hover'    => $this->getPrimaryColorHover(),
            'Secondary Color'  => $this->getSecondaryColor(),
            'Accent Color'     => $this->getAccentColor(),
            'Background Color' => $this->getBackgroundColor(),
            'Surface Color'    => $this->getSurfaceColor(),
            'Text Color'       => $this->getTextColor(),
            'Heading Color'    => $this->getHeadingColor(),
            'Border Color'     => $this->getBorderColor(),
            'Typography'       => $this->getTypographyKey(),
            'Container Width'  => $this->getContainerWidth(),
            'Layout'           => $this->getSiteLayout(),
        ];
    }
}
