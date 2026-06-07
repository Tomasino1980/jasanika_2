<?php

declare(strict_types=1);

namespace Jasanika\Core;

use Jasanika\Widgets\WidgetAreaManager;

/**
 * Centralized layout region rendering.
 *
 * Responsibilities:
 * - Render sidebar widget region with accessible markup
 * - Render footer widget regions with responsive grid markup
 * - Handle empty widget areas gracefully (no visible output)
 * - Standardize region output across the theme
 *
 * No direct dynamic_sidebar() calls exist outside this class.
 * No widget rendering logic exists inside template files.
 *
 * Accessibility:
 * - Sidebar uses <aside aria-label="Sidebar">
 * - Footer regions use <section aria-label="Footer Widgets">
 */
final class LayoutRegionRenderer
{
    private WidgetAreaManager $widgetAreaManager;

    private const SIDEBAR_AREA_ID = 'primary-sidebar';

    private const FOOTER_REGIONS = [
        'footer-left',
        'footer-center',
        'footer-right',
    ];

    public function __construct(WidgetAreaManager $widgetAreaManager)
    {
        $this->widgetAreaManager = $widgetAreaManager;
    }

    /**
     * Check whether the primary sidebar has active widgets.
     */
    public function hasSidebar(): bool
    {
        return $this->widgetAreaManager->hasActiveWidgets(self::SIDEBAR_AREA_ID);
    }

    /**
     * Render the primary sidebar widget region.
     *
     * Outputs an accessible <aside> element with the primary sidebar
     * widget area. Returns early with no output if no widgets are active.
     */
    public function renderSidebar(): void
    {
        if (!$this->hasSidebar()) {
            return;
        }

        echo '<aside id="jas-sidebar" class="jas-sidebar" aria-label="Sidebar">' . "\n";
        $this->widgetAreaManager->renderWidgetArea(self::SIDEBAR_AREA_ID);
        echo '</aside>' . "\n";
    }

    /**
     * Render all footer widget regions as a responsive grid.
     *
     * Outputs a container with three <section> elements for
     * footer-left, footer-center, and footer-right widget areas.
     *
     * Returns early with no output if none of the footer regions
     * have active widgets.
     */
    public function renderFooterRegions(): void
    {
        $hasWidgets = false;

        foreach (self::FOOTER_REGIONS as $region) {
            if ($this->widgetAreaManager->hasActiveWidgets($region)) {
                $hasWidgets = true;
                break;
            }
        }

        if (!$hasWidgets) {
            return;
        }

        echo '<div class="jas-footer-widgets" aria-label="Footer Widgets">' . "\n";
        echo '  <div class="jas-footer-widgets__grid">' . "\n";

        foreach (self::FOOTER_REGIONS as $region) {
            $this->renderFooterRegion($region);
        }

        echo '  </div>' . "\n";
        echo '</div>' . "\n";
    }

    /**
     * Render a single footer widget region.
     *
     * Outputs a <section> element for the given footer widget area.
     * Returns early with no output if no widgets are active.
     */
    private function renderFooterRegion(string $region): void
    {
        if (!$this->widgetAreaManager->hasActiveWidgets($region)) {
            return;
        }

        $label = $this->getFooterRegionLabel($region);

        printf(
            '    <section class="jas-footer-widgets__column" aria-label="%s">' . "\n",
            esc_attr($label)
        );

        $this->widgetAreaManager->renderWidgetArea($region);

        echo '    </section>' . "\n";
    }

    /**
     * Get a human-readable label for a footer region.
     */
    private function getFooterRegionLabel(string $region): string
    {
        return match ($region) {
            'footer-left'   => 'Footer Widgets Left',
            'footer-center' => 'Footer Widgets Center',
            'footer-right'  => 'Footer Widgets Right',
            default         => 'Footer Widgets',
        };
    }

    /**
     * Render a generic widget region.
     *
     * Generic renderer for any registered widget area.
     * Wraps the output in a <section> with the given CSS class and aria label.
     *
     * Returns early with no output if no widgets are active.
     */
    public function renderRegion(string $areaId, string $cssClass, string $ariaLabel): void
    {
        if (!$this->widgetAreaManager->hasActiveWidgets($areaId)) {
            return;
        }

        printf(
            '<section class="%s" aria-label="%s">' . "\n",
            esc_attr($cssClass),
            esc_attr($ariaLabel)
        );

        $this->widgetAreaManager->renderWidgetArea($areaId);

        echo '</section>' . "\n";
    }
}
