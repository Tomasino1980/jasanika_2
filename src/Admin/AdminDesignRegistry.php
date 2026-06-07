<?php

declare(strict_types=1);

namespace Jasanika\Admin;

/**
 * Admin Design Token Registry.
 *
 * Defines all design tokens used in the Jasanika admin interface.
 * These tokens control spacing, radii, colors, and shadows across
 * all admin UI components for consistent visual design.
 *
 * M29 — Settings UI Refactor & Design System.
 *
 * Token categories:
 * - Radius    — Border radius scale (XS=2px, SM=4px, MD=6px)
 * - Spacing   — 8px grid system (xs-xl)
 * - Colors    — Admin theme colors (border, surface, background, text, muted, accent)
 * - Shadows   — Minimal box-shadow definitions
 *
 * Usage:
 *   $registry = AdminDesignRegistry::getInstance();
 *   $tokens   = $registry->getAllTokens();
 */
final class AdminDesignRegistry
{
    /** @var array<string, array{category: string, value: string, description: string}> */
    private static array $tokens = [];

    private static bool $initialized = false;

    /**
     * Initialize default admin design tokens.
     *
     * Called automatically on first access via getInstance().
     */
    private static function init(): void
    {
        if (self::$initialized) {
            return;
        }

        self::$initialized = true;

        // --- Radius tokens ---
        self::registerToken('--jas-admin-radius-xs', 'Radius', '2px', 'Extra small border radius');
        self::registerToken('--jas-admin-radius-sm', 'Radius', '4px', 'Small border radius');
        self::registerToken('--jas-admin-radius-md', 'Radius', '6px', 'Medium border radius');

        // --- Spacing tokens (8px grid) ---
        self::registerToken('--jas-admin-space-xs', 'Spacing', '4px', 'Extra small spacing (0.5×)');
        self::registerToken('--jas-admin-space-sm', 'Spacing', '8px', 'Small spacing (1×)');
        self::registerToken('--jas-admin-space-md', 'Spacing', '16px', 'Medium spacing (2×)');
        self::registerToken('--jas-admin-space-lg', 'Spacing', '24px', 'Large spacing (3×)');
        self::registerToken('--jas-admin-space-xl', 'Spacing', '32px', 'Extra large spacing (4×)');

        // --- Color tokens ---
        self::registerToken('--jas-admin-color-border',     'Color', 'rgba(255,255,255,0.08)', 'Border and divider color');
        self::registerToken('--jas-admin-color-surface',    'Color', '#ffffff', 'Card and panel surface background');
        self::registerToken('--jas-admin-color-background', 'Color', '#f0f0f1', 'Page background color');
        self::registerToken('--jas-admin-color-text',       'Color', '#1d2327', 'Primary text color');
        self::registerToken('--jas-admin-color-text-muted', 'Color', '#8c8f94', 'Muted/secondary text color');
        self::registerToken('--jas-admin-color-accent',     'Color', '#b78acb', 'Primary accent (brand purple)');
        self::registerToken('--jas-admin-color-accent-hover', 'Color', '#c79cda', 'Primary accent hover state');

        // --- Shadow tokens ---
        self::registerToken('--jas-admin-shadow-sm', 'Shadow', '0 1px 2px rgba(0,0,0,0.04)', 'Small shadow');
        self::registerToken('--jas-admin-shadow-md', 'Shadow', '0 2px 6px rgba(0,0,0,0.06)', 'Medium shadow');
    }

    /**
     * Register a single admin design token.
     *
     * @param string $token       CSS custom property name (e.g. --jas-admin-space-sm).
     * @param string $category    Token category (Radius, Spacing, Color, Shadow).
     * @param string $value       CSS value of the token.
     * @param string $description Human-readable description of the token.
     */
    public static function registerToken(string $token, string $category, string $value, string $description = ''): void
    {
        self::init();

        self::$tokens[$token] = [
            'category'    => $category,
            'value'       => $value,
            'description' => $description,
        ];
    }

    /**
     * Get a single token value by name.
     *
     * Returns null if the token is not registered.
     */
    public static function getToken(string $token): ?string
    {
        self::init();

        return self::$tokens[$token]['value'] ?? null;
    }

    /**
     * Get all registered admin design tokens.
     *
     * @return array<string, array{category: string, value: string, description: string}>
     */
    public static function getAllTokens(): array
    {
        self::init();

        return self::$tokens;
    }

    /**
     * Get all tokens grouped by category.
     *
     * @return array<string, array<string, array{category: string, value: string, description: string}>>
     */
    public static function getTokensByCategory(): array
    {
        self::init();

        $grouped = [];

        foreach (self::$tokens as $name => $data) {
            $category = $data['category'];
            $grouped[$category][$name] = $data;
        }

        return $grouped;
    }

    /**
     * Generate CSS custom property declarations from all registered tokens.
     *
     * Useful for inline style injection when an external CSS file is not preferred.
     *
     * @return string CSS declarations (without selector).
     */
    public static function toCss(): string
    {
        self::init();

        $css = '';

        foreach (self::$tokens as $name => $data) {
            $css .= '    ' . $name . ': ' . $data['value'] . ";\n";
        }

        return $css;
    }
}