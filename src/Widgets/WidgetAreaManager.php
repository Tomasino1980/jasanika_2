<?php

declare(strict_types=1);

namespace Jasanika\Widgets;

use Jasanika\Hooks\HookManager;

/**
 * Centralized widget area registration.
 *
 * Responsibilities:
 * - Register widget area definitions with WordPress
 * - Manage sidebar configuration as a single source of truth
 * - Hook widget area registration into the widgets_init action
 *
 * Widget areas:
 * - primary-sidebar: Main sidebar widget area
 * - footer-left:     Left footer widget column
 * - footer-center:   Center footer widget column
 * - footer-right:    Right footer widget column
 *
 * Template files must not call register_sidebar() directly.
 * All widget area definitions go through this service.
 */
final class WidgetAreaManager
{
    /**
     * Registered widget area definitions.
     *
     * Each entry is registered as a WordPress sidebar via register_sidebar().
     * The 'id' key corresponds to the dynamic_sidebar() lookup key.
     *
     * @var array<string, array<string, string>>
     */
    private const WIDGET_AREAS = [
        'primary-sidebar' => [
            'name'        => 'Primary Sidebar',
            'id'          => 'primary-sidebar',
            'description' => 'Main sidebar widget area displayed alongside content.',
            'before_widget' => '<section id="%1$s" class="jas-widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h3 class="jas-widget__title">',
            'after_title'   => '</h3>',
        ],
        'footer-left' => [
            'name'        => 'Footer Left',
            'id'          => 'footer-left',
            'description' => 'Left column footer widget area.',
            'before_widget' => '<section id="%1$s" class="jas-widget jas-footer-widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h3 class="jas-widget__title jas-footer-widget__title">',
            'after_title'   => '</h3>',
        ],
        'footer-center' => [
            'name'        => 'Footer Center',
            'id'          => 'footer-center',
            'description' => 'Center column footer widget area.',
            'before_widget' => '<section id="%1$s" class="jas-widget jas-footer-widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h3 class="jas-widget__title jas-footer-widget__title">',
            'after_title'   => '</h3>',
        ],
        'footer-right' => [
            'name'        => 'Footer Right',
            'id'          => 'footer-right',
            'description' => 'Right column footer widget area.',
            'before_widget' => '<section id="%1$s" class="jas-widget jas-footer-widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h3 class="jas-widget__title jas-footer-widget__title">',
            'after_title'   => '</h3>',
        ],
    ];

    private HookManager $hookManager;

    public function __construct(HookManager $hookManager)
    {
        $this->hookManager = $hookManager;
    }

    /**
     * Register all widget areas with WordPress.
     *
     * Hooks the registerSidebars() method into the widgets_init action.
     * Must be called during framework bootstrap, typically from Application.
     */
    public function register(): void
    {
        $this->hookManager->addAction('widgets_init', [$this, 'registerSidebars']);
    }

    /**
     * Register individual sidebar definitions with WordPress.
     *
     * Callback for widgets_init action.
     * Internal — do not call directly.
     */
    public function registerSidebars(): void
    {
        foreach (self::WIDGET_AREAS as $id => $args) {
            register_sidebar($args);
        }
    }

    /**
     * Check whether a given widget area has active widgets.
     */
    public function hasActiveWidgets(string $id): bool
    {
        if (!isset(self::WIDGET_AREAS[$id])) {
            return false;
        }

        return is_active_sidebar($id);
    }

    /**
     * Render a widget area.
     *
     * Outputs the dynamic sidebar for the given area ID.
     * Returns early if the area has no active widgets.
     *
     * Debug note: When WP_DEBUG is true and no widgets exist,
     * no placeholder output is rendered. The frontend remains clean.
     */
    public function renderWidgetArea(string $id): void
    {
        if (!$this->hasActiveWidgets($id)) {
            return;
        }

        dynamic_sidebar($id);
    }

    /**
     * Get all registered widget area IDs.
     *
     * @return string[]
     */
    public function getAreaIds(): array
    {
        return array_keys(self::WIDGET_AREAS);
    }

    /**
     * Get a single widget area configuration by ID.
     *
     * @return array<string, string>|null
     */
    public function getAreaConfig(string $id): ?array
    {
        return self::WIDGET_AREAS[$id] ?? null;
    }
}
