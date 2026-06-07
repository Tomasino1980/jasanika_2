<?php

declare(strict_types=1);

namespace Jasanika\Header;

/**
 * Mobile Navigation Foundation.
 *
 * Provides mobile menu data for the responsive navigation system.
 * This class owns the mobile breakpoint configuration and menu
 * state management for the frontend JavaScript.
 *
 * This is a foundation class. Future milestones (Mega Menu, advanced
 * mobile navigation) will extend this service.
 *
 * Responsibilities:
 * - Define mobile breakpoint
 * - Provide mobile menu toggle markup configuration
 * - Prepare mobile menu data for JavaScript consumption
 */
final class MobileMenu
{
    /**
     * Mobile breakpoint in pixels.
     *
     * Below this width, the mobile navigation is active.
     * At or above this width, desktop navigation is used.
     */
    private const MOBILE_BREAKPOINT = 768;

    /**
     * Get the mobile breakpoint value.
     */
    public function getBreakpoint(): int
    {
        return self::MOBILE_BREAKPOINT;
    }

    /**
     * Get the mobile menu toggle attributes as an array.
     *
     * @return array<string, string>
     */
    public function getToggleAttributes(): array
    {
        return [
            'class'          => 'jas-mobile-nav-toggle',
            'aria-label'     => __('Toggle menu', 'jasanika'),
            'aria-expanded'  => 'false',
            'data-jas-toggle' => 'mobile-nav',
            'data-jas-target' => '#jas-header-nav',
        ];
    }

    /**
     * Get mobile menu configuration for JavaScript.
     *
     * @return array<string, mixed>
     */
    public function getJsConfig(): array
    {
        return [
            'breakpoint'  => self::MOBILE_BREAKPOINT,
            'toggleClass' => 'jas-mobile-nav-toggle',
            'navSelector' => '#jas-header-nav',
            'activeClass' => 'jas-mobile-nav--open',
            'bodyClass'   => 'jas-nav-open',
        ];
    }
}