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
 * M30 — Admin UI Dark Card Layout (dark surface, subtle borders, radius standardization).
 *
 * Token categories:
 * - Radius    — Border radius scale (XS=2px inputs, SM=4px cards/buttons)
 * - Spacing   — 8px grid system (xs-xl)
 * - Colors    — Admin theme colors for dark card layout
 * - Shadows   — Minimal box-shadow definitions
 *
 * Dark Card Layout (M30):
 * - Surface: #24212b (dark card background)
 * - Background: #f0f0f1 (WordPress default page background — contrast with dark cards)
 * - Text: #f5f2f7 (light text on dark cards)
 * - Input bg: #1b1a1f (dark input fields within cards)
 * - Borders: rgba(255,255,255,0.08) (subtle)
 *
 * Usage:
 *   $tokens = AdminDesignRegistry::getAllTokens();
 */
final class AdminDesignRegistry
{
    /** @var array<string, array{category: string, value: string, description: string}> */
    private static array $tokens = [];

    private static bool $initialized = false;

    /**
     * Initialize default admin design tokens.
     *
     * Called automatically on first access via getAllTokens().
     */
    private static function init(): void
    {
        if (self::$initialized) {
            return;
        }

        self::$initialized = true;

        // --- Radius tokens ---
        // XS = 2px (inputs), SM = 4px (cards, buttons)
        self::registerToken('--jas-admin-radius-xs', 'Radius', '2px', 'Extra small border radius — inputs');
        self::registerToken('--jas-admin-radius-sm', 'Radius', '4px', 'Small border radius — cards, buttons');
        self::registerToken('--jas-admin-radius-md', 'Radius', '4px', 'Medium border radius (alias for SM)');

        // --- Spacing tokens (8px grid) ---
        self::registerToken('--jas-admin-space-xs', 'Spacing', '4px', 'Extra small spacing (0.5×)');
        self::registerToken('--jas-admin-space-sm', 'Spacing', '8px', 'Small spacing (1×) — label spacing');
        self::registerToken('--jas-admin-space-md', 'Spacing', '16px', 'Medium spacing (2×) — field spacing');
        self::registerToken('--jas-admin-space-lg', 'Spacing', '24px', 'Large spacing (3×) — card padding');
        self::registerToken('--jas-admin-space-xl', 'Spacing', '32px', 'Extra large spacing (4×)');

        // --- Color tokens ---
        // Dark Card Layout (M30):
        // Cards use dark surface (#24212b) against WordPress light page background (#f0f0f1).
        self::registerToken('--jas-admin-color-surface',        'Color', '#24212b', 'Card and panel surface background (dark)');
        self::registerToken('--jas-admin-color-surface-hover',  'Color', '#2a2733', 'Card surface hover state');
        self::registerToken('--jas-admin-color-bg',             'Color', '#f0f0f1', 'Page background color (WordPress default)');
        self::registerToken('--jas-admin-color-border',         'Color', 'rgba(255,255,255,0.08)', 'Border and divider color (subtle)');
        self::registerToken('--jas-admin-color-border-strong',  'Color', 'rgba(255,255,255,0.12)', 'Stronger border for interactive elements');
        self::registerToken('--jas-admin-color-text',           'Color', '#f5f2f7', 'Primary text color on dark surface');
        self::registerToken('--jas-admin-color-text-muted',     'Color', '#b9b1c4', 'Muted/secondary text color');
        self::registerToken('--jas-admin-color-accent',         'Color', '#b78acb', 'Primary accent (brand purple)');
        self::registerToken('--jas-admin-color-accent-hover',   'Color', '#c79cda', 'Primary accent hover state');
        self::registerToken('--jas-admin-color-input-bg',       'Color', '#1b1a1f', 'Input field background within dark cards');
        self::registerToken('--jas-admin-color-input-text',     'Color', '#f5f2f7', 'Input field text color');
        self::registerToken('--jas-admin-color-header-bg',      'Color', 'rgba(255,255,255,0.03)', 'Card header background (subtle light overlay)');
        self::registerToken('--jas-admin-color-divider',        'Color', 'rgba(255,255,255,0.06)', 'Subtle divider between header/body/description');

        // --- Shadow tokens ---
        self::registerToken('--jas-admin-shadow-sm', 'Shadow', '0 1px 3px rgba(0,0,0,0.12)', 'Small shadow for cards');
        self::registerToken('--jas-admin-shadow-md', 'Shadow', '0 2px 8px rgba(0,0,0,0.16)', 'Medium shadow for active/pressed states');
    }

    /**
     * Register a single admin design token.
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