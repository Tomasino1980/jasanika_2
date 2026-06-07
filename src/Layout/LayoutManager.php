<?php

declare(strict_types=1);

namespace Jasanika\Layout;

use Jasanika\Design\DesignSettingsManager;

/**
 * Layout resolution and management.
 *
 * Single source of truth for page layout selection.
 * Resolves the active layout based on a priority chain:
 *
 * 1. Future Page Meta (not implemented yet)
 * 2. Site Layout Setting
 * 3. Default Layout
 *
 * Currently implements the site layout setting and default fallback.
 * Architecture is future-ready for page-level meta overrides.
 *
 * Supported layouts:
 * - content-sidebar: Content with right sidebar (default)
 * - full-width: Content only, full page width
 * - landing-page: Minimal layout, no sidebar, no widget regions
 */
final class LayoutManager
{
    private DesignSettingsManager $designSettingsManager;

    private const DEFAULT_LAYOUT = 'content-sidebar';

    private const SUPPORTED_LAYOUTS = [
        'content-sidebar' => 'Content + Sidebar',
        'full-width'      => 'Full Width',
        'landing-page'    => 'Landing Page',
    ];

    public function __construct(DesignSettingsManager $designSettingsManager)
    {
        $this->designSettingsManager = $designSettingsManager;
    }

    /**
     * Resolve the active layout for the current request.
     *
     * Priority:
     * 1. Future Page Meta (reserved, not implemented)
     * 2. Site Layout Setting (via DesignSettingsManager)
     * 3. Default layout (content-sidebar)
     *
     * @return string The resolved layout key.
     */
    public function getActiveLayout(): string
    {
        // Future: Check page meta first (reserved for M27+)
        // $pageLayout = $this->resolvePageMetaLayout();
        // if ($pageLayout !== null) {
        //     return $pageLayout;
        // }

        // Check site layout setting
        $siteLayout = $this->designSettingsManager->getSiteLayout();

        // Map site layout values to layout system keys
        $layout = $this->mapSiteLayout($siteLayout);

        if ($this->isLayoutSupported($layout)) {
            return $layout;
        }

        return self::DEFAULT_LAYOUT;
    }

    /**
     * Get all supported layouts with their labels.
     *
     * @return array<string, string> Layout key => human-readable label.
     */
    public function getLayouts(): array
    {
        return self::SUPPORTED_LAYOUTS;
    }

    /**
     * Get the default layout key.
     */
    public function getDefaultLayout(): string
    {
        return self::DEFAULT_LAYOUT;
    }

    /**
     * Get a human-readable label for a layout key.
     */
    public function getLayoutLabel(string $layout): string
    {
        return self::SUPPORTED_LAYOUTS[$layout] ?? 'Unknown';
    }

    /**
     * Check whether a given layout key is supported.
     */
    public function isLayoutSupported(string $layout): bool
    {
        return isset(self::SUPPORTED_LAYOUTS[$layout]);
    }

    /**
     * Map site layout setting values to layout system keys.
     *
     * The site_layout setting historically uses 'full-width' and 'boxed'
     * values. The layout system uses 'content-sidebar', 'full-width',
     * and 'landing-page'.
     *
     * - 'full-width' stays 'full-width'
     * - 'boxed' maps to 'content-sidebar' (boxed was a visual style, not a layout)
     * - Unknown values fall back to default
     */
    private function mapSiteLayout(string $siteLayout): string
    {
        return match ($siteLayout) {
            'full-width' => 'full-width',
            'boxed'      => 'content-sidebar',
            default      => self::DEFAULT_LAYOUT,
        };
    }
}