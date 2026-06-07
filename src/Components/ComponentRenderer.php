<?php

declare(strict_types=1);

namespace Jasanika\Components;

/**
 * Component Renderer.
 *
 * Centralized rendering service for all reusable frontend UI components.
 * Standardizes HTML output and ensures consistent, token-driven markup.
 *
 * Rendering flow:
 * - Each method accepts typed parameters for the component variant and content.
 * - Data is passed as local variables to the component template file.
 * - Templates remain presentation-only — no business logic in templates.
 *
 * All components consume design tokens via CSS classes (see components.css).
 * No hardcoded colors, spacing, or visual values in templates.
 */
final class ComponentRenderer
{
    private ComponentRegistry $registry;

    public function __construct(ComponentRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Get the ComponentRegistry instance.
     */
    public function getRegistry(): ComponentRegistry
    {
        return $this->registry;
    }

    /**
     * Render a button component.
     *
     * Variants:
     * - primary   — Solid primary brand color background
     * - secondary — Surface background with border
     * - outline   — Transparent background with primary border
     *
     * @param string $variant  Button variant (primary, secondary, outline).
     * @param string $label    Button text content.
     * @param string $url      Button link URL (optional, renders <a> tag when provided).
     * @param array  $attrs    Additional HTML attributes as key-value pairs.
     */
    public function renderButton(
        string $variant = 'primary',
        string $label = '',
        string $url = '',
        array $attrs = []
    ): void {
        $template = $this->registry->getComponentTemplate('button');

        if (!$template || !file_exists($template)) {
            return;
        }

        $variant = in_array($variant, ['primary', 'secondary', 'outline'], true)
            ? $variant
            : 'primary';

        $class = 'jas-btn jas-btn--' . $variant;

        if (!empty($attrs['class'])) {
            $class .= ' ' . $attrs['class'];
            unset($attrs['class']);
        }

        include $template;
    }

    /**
     * Render a card component.
     *
     * Standardized content card for archives, search results, widgets, etc.
     *
     * @param string $title       Card title (optional).
     * @param string $body        Card body content (optional).
     * @param string $footer      Card footer content (optional).
     * @param array  $attrs       Additional HTML attributes as key-value pairs.
     */
    public function renderCard(
        string $title = '',
        string $body = '',
        string $footer = '',
        array $attrs = []
    ): void {
        $template = $this->registry->getComponentTemplate('card');

        if (!$template || !file_exists($template)) {
            return;
        }

        $class = 'jas-card';

        if (!empty($attrs['class'])) {
            $class .= ' ' . $attrs['class'];
            unset($attrs['class']);
        }

        include $template;
    }

    /**
     * Render an alert component.
     *
     * Types:
     * - info    — Informational message (primary color)
     * - success — Success message (green accent)
     * - warning — Warning message (gold accent)
     * - error   — Error message (red accent)
     *
     * @param string $type    Alert type (info, success, warning, error).
     * @param string $message Alert message content.
     * @param array  $attrs   Additional HTML attributes as key-value pairs.
     */
    public function renderAlert(
        string $type = 'info',
        string $message = '',
        array $attrs = []
    ): void {
        $template = $this->registry->getComponentTemplate('alert');

        if (!$template || !file_exists($template)) {
            return;
        }

        $type = in_array($type, ['info', 'success', 'warning', 'error'], true)
            ? $type
            : 'info';

        $class = 'jas-alert jas-alert--' . $type;

        if (!empty($attrs['class'])) {
            $class .= ' ' . $attrs['class'];
            unset($attrs['class']);
        }

        include $template;
    }

    /**
     * Render a form field component.
     *
     * Supported input types:
     * - text
     * - email
     * - search
     * - textarea
     * - select
     *
     * @param string $type        Field type (text, email, search, textarea, select).
     * @param string $name        Field name attribute.
     * @param string $label       Field label text.
     * @param string $value       Current field value (optional).
     * @param array  $options     Field options (used for select type — key-value pairs).
     * @param array  $attrs       Additional HTML attributes as key-value pairs.
     */
    public function renderFormField(
        string $type = 'text',
        string $name = '',
        string $label = '',
        string $value = '',
        array $options = [],
        array $attrs = []
    ): void {
        $template = $this->registry->getComponentTemplate('form-field');

        if (!$template || !file_exists($template)) {
            return;
        }

        $type = in_array($type, ['text', 'email', 'search', 'textarea', 'select'], true)
            ? $type
            : 'text';

        $class = 'jas-form-field jas-form-field--' . $type;

        if (!empty($attrs['class'])) {
            $class .= ' ' . $attrs['class'];
            unset($attrs['class']);
        }

        $placeholder = $attrs['placeholder'] ?? '';
        unset($attrs['placeholder']);

        include $template;
    }
}