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
 * - Typography
 * - Container Width
 * - Site Layout
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
     */
    public function getFontFamily(): string
    {
        $typography = $this->settingsManager->get('typography');

        return match ($typography) {
            'inter'   => '"Inter", sans-serif',
            'roboto'  => '"Roboto", sans-serif',
            default   => 'system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
        };
    }

    /**
     * Get the raw typography key (system, inter, roboto).
     */
    public function getTypographyKey(): string
    {
        $typography = $this->settingsManager->get('typography');

        if (empty($typography) || !is_string($typography)) {
            return 'system';
        }

        return $typography;
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
            'Typography'       => $this->getTypographyKey(),
            'Container Width'  => $this->getContainerWidth(),
            'Layout'           => $this->getSiteLayout(),
        ];
    }
}
