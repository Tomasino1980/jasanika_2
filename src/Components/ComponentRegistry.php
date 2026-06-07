<?php

declare(strict_types=1);

namespace Jasanika\Components;

/**
 * Component Registry.
 *
 * Single source of truth for all reusable frontend UI components.
 * Owns component definitions, metadata, and provides component
 * inventory for rendering and debug purposes.
 *
 * Components registered:
 * - button     — Action button with variants (primary, secondary, outline)
 * - card       — Content card with consistent spacing and radius
 * - alert      — Semantic alert/message display (info, success, warning, error)
 * - form-field — Standardized form field with label and input
 *
 * Registry-driven pattern (see architecture-rules.md):
 * - ComponentRegistry owns component definitions
 * - ComponentRenderer owns component rendering
 * - Templates remain presentation-only
 * - All components consume design tokens
 */
final class ComponentRegistry
{
    /** @var array<string, array{name: string, description: string, template: string}> */
    private array $components = [];

    /**
     * Register a component definition.
     *
     * Duplicate registrations are silently ignored.
     *
     * @param string $slug        Component identifier (e.g. 'button').
     * @param string $name        Human-readable component name.
     * @param string $description Component purpose description.
     * @param string $template    Relative path to the component template file.
     */
    public function registerComponent(
        string $slug,
        string $name,
        string $description,
        string $template
    ): void {
        if (isset($this->components[$slug])) {
            return;
        }

        $this->components[$slug] = [
            'name'        => $name,
            'description' => $description,
            'template'    => $template,
        ];
    }

    /**
     * Get a single component definition by its slug.
     *
     * Returns null when the component is not registered.
     *
     * @return array{name: string, description: string, template: string}|null
     */
    public function getComponent(string $slug): ?array
    {
        return $this->components[$slug] ?? null;
    }

    /**
     * Get all registered component definitions.
     *
     * @return array<string, array{name: string, description: string, template: string}>
     */
    public function getAllComponents(): array
    {
        return $this->components;
    }

    /**
     * Get all registered component slugs.
     *
     * @return string[]
     */
    public function getComponentSlugs(): array
    {
        return array_keys($this->components);
    }

    /**
     * Check whether a specific component is registered.
     */
    public function hasComponent(string $slug): bool
    {
        return isset($this->components[$slug]);
    }

    /**
     * Get the template path for a registered component.
     *
     * Returns null when the component is not registered.
     */
    public function getComponentTemplate(string $slug): ?string
    {
        return $this->components[$slug]['template'] ?? null;
    }

    /**
     * Render debug information as an HTML comment.
     *
     * Outputs a list of registered components with metadata.
     * Only visible when WP_DEBUG is enabled.
     * Never visible in production environments.
     */
    public function renderDebugComment(): void
    {
        if (!defined('WP_DEBUG') || !WP_DEBUG) {
            return;
        }

        echo '<!--' . "\n";
        echo 'Jasanika Components' . "\n";

        foreach ($this->components as $slug => $component) {
            echo '  ' . esc_html($slug) . ' — ' . esc_html($component['name']) . "\n";
        }

        echo '-->' . "\n";
    }
}