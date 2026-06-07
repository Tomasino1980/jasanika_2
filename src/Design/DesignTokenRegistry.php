<?php

declare(strict_types=1);

namespace Jasanika\Design;

/**
 * Design Token Registry.
 *
 * Single source of truth for all design token definitions.
 * Owns token metadata, categories, and default values.
 * Supports token registration, lookup by category, and bulk defaults retrieval.
 *
 * This registry is the foundation for:
 * - Theme Presets
 * - Color Systems
 * - Component Styling
 * - Design Variants
 * - Future Visual Builder
 *
 * Token categories:
 * - Color
 * - Typography
 * - Spacing
 * - Layout
 * - Border Radius
 *
 * No generated values exist here — only definitions and defaults.
 * Token generation (dynamic values, preset overrides) belongs in DesignTokenGenerator.
 */
final class DesignTokenRegistry
{
    /** @var array<string, array{name: string, category: string, default: string, description: string}> */
    private array $tokens = [];

    /**
     * Register a design token definition.
     *
     * Tokens can only be registered once. Duplicate registrations are silently ignored
     * to prevent registry corruption during bootstrapping.
     *
     * @param string $name        CSS custom property name (e.g. --jas-color-primary).
     * @param string $category    Token category (Color, Typography, Spacing, Layout, Border Radius).
     * @param string $default     Default CSS value for the token.
     * @param string $description Human-readable description of the token's purpose.
     */
    public function registerToken(string $name, string $category, string $default, string $description = ''): void
    {
        if (isset($this->tokens[$name])) {
            return;
        }

        $this->tokens[$name] = [
            'name'        => $name,
            'category'    => $category,
            'default'     => $default,
            'description' => $description,
        ];
    }

    /**
     * Get a single token definition by its CSS custom property name.
     *
     * Returns null when the token is not registered.
     *
     * @return array{name: string, category: string, default: string, description: string}|null
     */
    public function getToken(string $name): ?array
    {
        return $this->tokens[$name] ?? null;
    }

    /**
     * Get all token definitions belonging to a specific category.
     *
     * @return array<string, array{name: string, category: string, default: string, description: string}>
     */
    public function getTokensByCategory(string $category): array
    {
        return array_filter(
            $this->tokens,
            fn(array $token): bool => $token['category'] === $category
        );
    }

    /**
     * Get all registered token definitions.
     *
     * @return array<string, array{name: string, category: string, default: string, description: string}>
     */
    public function getAllTokens(): array
    {
        return $this->tokens;
    }

    /**
     * Get all token default values as a flat associative array.
     *
     * Key is the CSS custom property name, value is the default CSS value.
     * Useful for initializing the token generation pipeline.
     *
     * @return array<string, string>
     */
    public function getDefaults(): array
    {
        $defaults = [];

        foreach ($this->tokens as $name => $token) {
            $defaults[$name] = $token['default'];
        }

        return $defaults;
    }

    /**
     * Get all unique token categories present in the registry.
     *
     * @return string[]
     */
    public function getCategories(): array
    {
        $categories = [];

        foreach ($this->tokens as $token) {
            $categories[$token['category']] = true;
        }

        return array_keys($categories);
    }
}
