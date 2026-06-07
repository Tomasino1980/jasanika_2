<?php

declare(strict_types=1);

namespace Jasanika\Admin\UI\Form;

/**
 * Form Row — M34 Unified Form Layout System.
 *
 * Renders a single form row with label, description, and input
 * arranged in a CSS grid: 280px | 1fr on desktop, stacked on mobile.
 *
 * Each row contains:
 * - Label section: label text + optional description
 * - Input section: the rendered field HTML
 *
 * Usage:
 *   FormRow::render('Setting Label', $inputHtml, 'Optional description');
 *   FormRow::render('Site Title', '<input type="text" ... />', 'The site title');
 */
final class FormRow
{
    /**
     * Render a form row.
     *
     * @param string $label       Field label text.
     * @param string $inputHtml   Pre-rendered input HTML (from Field::render()).
     * @param string $description Optional description shown below the label.
     * @param string $key         Optional setting key for HTML id association.
     */
    public static function render(string $label, string $inputHtml, string $description = '', string $key = ''): void
    {
        echo '<div class="jas-form-row"' . ($key !== '' ? ' data-key="' . esc_attr($key) . '"' : '') . '>';

        // Label column
        echo '<div class="jas-form-row__label">';
        echo '<label class="jas-form-row__label-text">' . esc_html($label) . '</label>';

        if ($description !== '') {
            echo '<span class="jas-form-row__desc">' . esc_html($description) . '</span>';
        }

        echo '</div>';

        // Input column
        echo '<div class="jas-form-row__input">';
        echo $inputHtml; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo '</div>';

        echo '</div>';
    }

    /**
     * Render a form row from field parts (label, input callback).
     *
     * Convenience method for inline use.
     *
     * @param string   $label       Field label.
     * @param callable $inputCallback Callable that echoes the input HTML.
     * @param string   $description  Optional description.
     * @param string   $key          Optional setting key.
     */
    public static function renderFromCallback(string $label, callable $inputCallback, string $description = '', string $key = ''): void
    {
        ob_start();
        $inputCallback();
        $inputHtml = ob_get_clean();

        self::render($label, $inputHtml, $description, $key);
    }
}