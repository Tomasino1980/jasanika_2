<?php

declare(strict_types=1);

namespace Jasanika\Header;

/**
 * Header Layout definitions, validation, and rendering configuration.
 *
 * Owns all layout definitions for the Dynamic Header Builder.
 * Each layout defines which zones are available and how the header
 * inner structure is positioned.
 *
 * Supported layouts:
 * - logo-left             — Branding left, nav right (default)
 * - logo-center           — Branding centered, nav below
 * - logo-right            — Branding right, nav left
 * - logo-menu             — Branding left, menu right, no search/cta
 * - logo-menu-search      — Branding left, menu + search right
 * - logo-menu-cta         — Branding left, menu + CTA button right
 * - logo-menu-search-cta  — Branding left, menu + search + CTA right
 *
 * Future layout expansion:
 * Add new layout entries to the LAYOUTS array. No other code changes
 * are required in this class. The renderer consumes layout config.
 */
final class HeaderLayout
{
    /**
     * Registered layout definitions.
     *
     * Each layout defines:
     * - label       — Human-readable name
     * - description — Purpose explanation
     * - zones       — Ordered array of zones in the header inner area
     *
     * Available zones:
     * - branding   — Logo / site identity
     * - nav        — Primary navigation
     * - search     — Search toggle / field
     * - cta        — Call-to-action button
     *
     * @var array<string, array{label: string, description: string, zones: string[]}>
     */
    private const LAYOUTS = [
        'logo-left' => [
            'label'       => 'Logo Left',
            'description' => 'Branding on the left, navigation on the right.',
            'zones'       => ['branding', 'nav'],
        ],
        'logo-center' => [
            'label'       => 'Logo Center',
            'description' => 'Branding centered above the navigation.',
            'zones'       => ['branding', 'nav'],
        ],
        'logo-right' => [
            'label'       => 'Logo Right',
            'description' => 'Branding on the right, navigation on the left.',
            'zones'       => ['branding', 'nav'],
        ],
        'logo-menu' => [
            'label'       => 'Logo + Menu',
            'description' => 'Branding left, primary menu right.',
            'zones'       => ['branding', 'nav'],
        ],
        'logo-menu-search' => [
            'label'       => 'Logo + Menu + Search',
            'description' => 'Branding left, menu and search toggle right.',
            'zones'       => ['branding', 'nav', 'search'],
        ],
        'logo-menu-cta' => [
            'label'       => 'Logo + Menu + CTA',
            'description' => 'Branding left, menu and CTA button right.',
            'zones'       => ['branding', 'nav', 'cta'],
        ],
        'logo-menu-search-cta' => [
            'label'       => 'Logo + Menu + Search + CTA',
            'description' => 'Branding left, menu, search toggle, and CTA button right.',
            'zones'       => ['branding', 'nav', 'search', 'cta'],
        ],
    ];

    /**
     * Get all registered layout definitions.
     *
     * @return array<string, array{label: string, description: string, zones: string[]}>
     */
    public function getAllLayouts(): array
    {
        return self::LAYOUTS;
    }

    /**
     * Get a single layout definition by slug.
     *
     * Returns the layout definition or null when not found.
     *
     * @return array{label: string, description: string, zones: string[]}|null
     */
    public function getLayout(string $slug): ?array
    {
        return self::LAYOUTS[$slug] ?? null;
    }

    /**
     * Get zones for a given layout slug.
     *
     * Returns the ordered zone array.
     * Falls back to logo-left zones when the layout is unknown.
     *
     * @return string[]
     */
    public function getZones(string $slug): array
    {
        $layout = $this->getLayout($slug);

        if ($layout === null) {
            return self::LAYOUTS['logo-left']['zones'];
        }

        return $layout['zones'];
    }

    /**
     * Get the label for a layout slug.
     *
     * Falls back to 'Logo Left' when the layout is unknown.
     */
    public function getLabel(string $slug): string
    {
        $layout = $this->getLayout($slug);

        if ($layout === null) {
            return self::LAYOUTS['logo-left']['label'];
        }

        return $layout['label'];
    }

    /**
     * Validate a layout slug.
     *
     * Returns true when the layout is registered.
     */
    public function isValid(string $slug): bool
    {
        return isset(self::LAYOUTS[$slug]);
    }

    /**
     * Get all valid layout slugs for select options.
     *
     * @return array<string, string> Slug → Label pairs.
     */
    public function getSelectOptions(): array
    {
        $options = [];

        foreach (self::LAYOUTS as $slug => $config) {
            $options[$slug] = $config['label'];
        }

        return $options;
    }

    /**
     * Check whether a layout includes a specific zone.
     */
    public function hasZone(string $slug, string $zone): bool
    {
        $zones = $this->getZones($slug);

        return in_array($zone, $zones, true);
    }
}