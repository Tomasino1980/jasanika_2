<?php

declare(strict_types=1);

namespace Jasanika\Navigation;

use Jasanika\Hooks\HookManager;

/**
 * Centralized navigation architecture.
 *
 * Responsibilities:
 * - Register WordPress menu locations (primary, footer)
 * - Resolve active menu for a given location
 * - Provide menu rendering HTML with accessible markup
 *
 * Menu locations:
 * - primary: Main site navigation
 * - footer: Footer utility navigation
 *
 * Template files must not call wp_nav_menu() directly.
 * All navigation rendering goes through this service.
 */
final class NavigationManager
{
    /**
     * Registered menu locations.
     */
    private const MENU_LOCATIONS = [
        'primary' => 'Primary Navigation',
        'footer'  => 'Footer Navigation',
    ];

    private HookManager $hookManager;

    public function __construct(HookManager $hookManager)
    {
        $this->hookManager = $hookManager;
    }

    /**
     * Register navigation menu locations with WordPress.
     *
     * Must be called during theme setup, typically during
     * ThemeRenderer initialization or a dedicated hook.
     */
    public function registerMenuLocations(): void
    {
        $this->hookManager->addAction('after_setup_theme', [$this, 'registerMenus']);
    }

    /**
     * Register menus with WordPress.
     *
     * Callback for after_setup_theme action.
     * Internal — do not call directly.
     */
    public function registerMenus(): void
    {
        register_nav_menus(self::MENU_LOCATIONS);
    }

    /**
     * Check whether a given menu location has a menu assigned.
     */
    public function hasMenu(string $location): bool
    {
        if (!isset(self::MENU_LOCATIONS[$location])) {
            return false;
        }

        return has_nav_menu($location);
    }

    /**
     * Render the menu HTML for a given location.
     *
     * Outputs an accessible <nav> element with the WordPress menu.
     * Returns early if no menu is assigned to the location.
     *
     * @param string $location  Menu location key ('primary', 'footer').
     * @param string $ariaLabel Accessible label for the nav element.
     */
    public function renderMenu(string $location, string $ariaLabel = ''): void
    {
        if (!isset(self::MENU_LOCATIONS[$location])) {
            return;
        }

        if (!$this->hasMenu($location)) {
            return;
        }

        if (empty($ariaLabel)) {
            $ariaLabel = self::MENU_LOCATIONS[$location];
        }

        $args = [
            'theme_location' => $location,
            'container'      => false,
            'menu_class'     => 'jas-nav__menu',
            'echo'           => true,
            'depth'          => 2,
            'fallback_cb'    => '__return_false',
            'items_wrap'     => '<ul class="jas-nav__menu">%3$s</ul>',
        ];

        printf(
            '<nav class="jas-nav" aria-label="%s">',
            esc_attr($ariaLabel)
        );

        wp_nav_menu($args);

        echo '</nav>';
    }

    /**
     * Get the registered menu locations.
     *
     * @return array<string, string>
     */
    public function getMenuLocations(): array
    {
        return self::MENU_LOCATIONS;
    }
}
