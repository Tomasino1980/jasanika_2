<?php

declare(strict_types=1);

namespace Jasanika\Settings;

use Jasanika\Admin\SettingsManager;
use Jasanika\Design\DesignSettingsManager;
use Jasanika\Design\ThemePresetManager;

/**
 * Theme Settings Compiler — M31.
 *
 * Dedicated service that reads theme settings, normalizes values,
 * and generates CSS variables and frontend configuration for the
 * rendering pipeline.
 *
 * Responsibilities:
 * - Read all appearance settings from DesignSettingsManager
 * - Normalize values to CSS-ready format
 * - Generate CSS custom properties for frontend consumption
 * - Generate frontend theme configuration array
 * - Provide logo dimension CSS variables
 * - Provide container width as CSS variable
 *
 * Architecture rules:
 * - Owns variable generation (no other class generates theme CSS vars)
 * - Reads from DesignSettingsManager (source of truth)
 * - Output consumed by ThemeRenderer (frontend integration)
 * - No duplicated color/settings logic
 * - Single responsibility: settings → CSS variables
 *
 * Flow:
 * DesignSettingsManager
 *   + ThemePresetManager (active preset name)
 *   ↓
 * ThemeSettingsCompiler
 *   ↓
 * ThemeRenderer → wp_add_inline_style() / inline <style>
 *
 * Dependencies:
 * - DesignSettingsManager (normalized color, typography, layout values)
 * - ThemePresetManager (active preset label for configuration)
 * - SettingsManager (for logo dimension and layout controls)
 *
 * Introduced:
 * - M31 — Dynamic Theme Settings Engine
 */
final class ThemeSettingsCompiler
{
    private DesignSettingsManager $designSettingsManager;
    private ThemePresetManager $presetManager;
    private SettingsManager $settingsManager;

    public function __construct(
        DesignSettingsManager $designSettingsManager,
        ThemePresetManager $presetManager,
        SettingsManager $settingsManager
    ) {
        $this->designSettingsManager = $designSettingsManager;
        $this->presetManager = $presetManager;
        $this->settingsManager = $settingsManager;
    }

    /**
     * Compile all theme settings into CSS custom properties.
     *
     * Returns an associative array of CSS variable name → value.
     * These are the dynamic theme variables that override defaults
     * set by DesignTokenGenerator.
     *
     * Resolution order (later overrides earlier):
     * 1. Color scheme tokens from DesignSettingsManager
     * 2. Container width and site layout
     * 3. Typography settings
     * 4. Logo dimension tokens
     * 5. Layout control tokens (header, content, sidebar, footer widths)
     * 6. Section spacing tokens
     *
     * @return array<string, string> CSS variable name → CSS-ready value.
     */
    public function compile(): array
    {
        $tokens = [];

        // --- Color Scheme Tokens ---
        $tokens['--jas-primary-color']       = $this->designSettingsManager->getPrimaryColor();
        $tokens['--jas-color-primary']       = $this->designSettingsManager->getPrimaryColor();
        $tokens['--jas-primary-hover']       = $this->designSettingsManager->getPrimaryColorHover();
        $tokens['--jas-color-primary-hover'] = $this->designSettingsManager->getPrimaryColorHover();
        $tokens['--jas-color-secondary']     = $this->designSettingsManager->getSecondaryColor();
        $tokens['--jas-color-accent']        = $this->designSettingsManager->getAccentColor();
        $tokens['--jas-color-background']    = $this->designSettingsManager->getBackgroundColor();
        $tokens['--jas-color-surface']       = $this->designSettingsManager->getSurfaceColor();
        $tokens['--jas-color-text']          = $this->designSettingsManager->getTextColor();
        $tokens['--jas-color-heading']       = $this->designSettingsManager->getHeadingColor();
        $tokens['--jas-color-border']        = $this->designSettingsManager->getBorderColor();

        // --- Container Width & Layout ---
        $tokens['--jas-container-width'] = $this->designSettingsManager->getContainerWidth();
        $tokens['--jas-site-layout']     = $this->designSettingsManager->getSiteLayout();

        // --- Typography ---
        $tokens['--jas-font-family'] = $this->designSettingsManager->getFontFamily();

        // --- Logo Dimensions ---
        $logoWidth = $this->getLogoWidth();
        $logoHeight = $this->getLogoHeight();

        if ($logoWidth !== '') {
            $tokens['--jas-logo-width'] = $logoWidth;
        }

        if ($logoHeight !== '') {
            $tokens['--jas-logo-height'] = $logoHeight;
        }

        // --- Layout Control Tokens ---
        $layoutTokens = [
            '--jas-header-width'   => 'layout_header_width',
            '--jas-content-width'  => 'layout_content_width',
            '--jas-sidebar-width'  => 'layout_sidebar_width',
            '--jas-footer-width'   => 'layout_footer_width',
            '--jas-section-padding' => 'layout_section_padding',
            '--jas-section-margin'  => 'layout_section_margin',
        ];

        foreach ($layoutTokens as $tokenName => $settingKey) {
            $value = $this->designSettingsManager->getLayoutSetting($settingKey);
            if ($value !== '') {
                $tokens[$tokenName] = $value;
            }
        }

        return $tokens;
    }

    /**
     * Get the frontend theme configuration array.
     *
     * Returns human-readable key-value pairs for debug output
     * and ThemeRenderer configuration.
     *
     * @return array<string, string>
     */
    public function getConfig(): array
    {
        return [
            'Preset'          => $this->presetManager->getActivePresetLabel(),
            'Primary Color'   => $this->designSettingsManager->getPrimaryColor(),
            'Secondary Color' => $this->designSettingsManager->getSecondaryColor(),
            'Accent Color'    => $this->designSettingsManager->getAccentColor(),
            'Background'      => $this->designSettingsManager->getBackgroundColor(),
            'Text Color'      => $this->designSettingsManager->getTextColor(),
            'Container Width' => $this->designSettingsManager->getContainerWidth(),
            'Site Layout'     => $this->designSettingsManager->getSiteLayout(),
            'Typography'      => $this->designSettingsManager->getTypographyKey(),
            'Logo'            => $this->getLogoStatus(),
            'Logo Width'      => $this->getLogoWidth() ?: 'auto',
            'Logo Height'     => $this->getLogoHeight() ?: 'auto',
        ];
    }

    /**
     * Get the logo width CSS value from settings.
     */
    private function getLogoWidth(): string
    {
        $width = $this->settingsManager->get('logo_width');
        return is_string($width) && $width !== '' ? $width : '';
    }

    /**
     * Get the logo height CSS value from settings.
     */
    private function getLogoHeight(): string
    {
        $height = $this->settingsManager->get('logo_height');
        return is_string($height) && $height !== '' ? $height : '';
    }

    /**
     * Get logo status string (Custom/Site Title fallback).
     */
    private function getLogoStatus(): string
    {
        $logoId = (int) $this->settingsManager->get('logo_desktop');

        if ($logoId > 0) {
            return 'Custom';
        }

        return 'Site Title Fallback';
    }
}
