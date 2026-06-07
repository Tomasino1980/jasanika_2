<?php

declare(strict_types=1);

namespace Jasanika\Admin;

/**
 * Design Style Registry.
 *
 * Registers UI style presets for the admin interface.
 * These are purely architectural — no visual implementation yet.
 * Future milestones will implement the visual rendering.
 *
 * M29 — Settings UI Refactor & Design System.
 *
 * Initial styles:
 * - Classic  — Traditional WordPress admin appearance
 * - Modern   — Clean, minimal, flat design
 * - Glass    — Foundation only, transparent/glass-morphism style (future)
 *
 * Usage:
 *   $registry = DesignStyleRegistry::getInstance();
 *   $styles   = $registry->getAllStyles();
 *   $active   = $registry->getActiveStyle();
 */
final class DesignStyleRegistry
{
    /** @var array<string, array{label: string, description: string, css_class: string}> */
    private static array $styles = [];

    private static bool $initialized = false;
    private static string $activeStyle = 'modern';

    /**
     * Initialize default design styles.
     */
    private static function init(): void
    {
        if (self::$initialized) {
            return;
        }

        self::$initialized = true;

        self::registerStyle(
            'classic',
            'Classic',
            'Traditional WordPress admin appearance with standard form layouts and familiar visual hierarchy.',
            'jas-admin-style--classic'
        );

        self::registerStyle(
            'modern',
            'Modern',
            'Clean, minimal, flat design with card-based layouts, subtle borders, and generous spacing.',
            'jas-admin-style--modern'
        );

        self::registerStyle(
            'glass',
            'Glass (Foundation)',
            'Transparent glass-morphism style with blurred backgrounds and layered depth. Visual implementation coming in a future milestone.',
            'jas-admin-style--glass'
        );
    }

    /**
     * Register a new admin design style.
     *
     * @param string $name        Style identifier (e.g. 'modern').
     * @param string $label       Human-readable label.
     * @param string $description Human-readable description.
     * @param string $cssClass    CSS class to apply when the style is active.
     */
    public static function registerStyle(string $name, string $label, string $description, string $cssClass): void
    {
        self::init();

        if (isset(self::$styles[$name])) {
            return;
        }

        self::$styles[$name] = [
            'label'       => $label,
            'description' => $description,
            'css_class'   => $cssClass,
        ];
    }

    /**
     * Set the active design style.
     */
    public static function setActiveStyle(string $name): bool
    {
        self::init();

        if (!isset(self::$styles[$name])) {
            return false;
        }

        self::$activeStyle = $name;

        return true;
    }

    /**
     * Get the active style name.
     */
    public static function getActiveStyle(): string
    {
        self::init();

        return self::$activeStyle;
    }

    /**
     * Get the CSS class for the active style.
     */
    public static function getActiveStyleCssClass(): string
    {
        self::init();

        return self::$styles[self::$activeStyle]['css_class'] ?? 'jas-admin-style--modern';
    }

    /**
     * Get all registered design styles.
     *
     * @return array<string, array{label: string, description: string, css_class: string}>
     */
    public static function getAllStyles(): array
    {
        self::init();

        return self::$styles;
    }
}