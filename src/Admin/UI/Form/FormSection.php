<?php

declare(strict_types=1);

namespace Jasanika\Admin\UI\Form;

/**
 * Form Section — M34 Unified Form Layout System.
 *
 * Renders a settings panel with header, divider, and content body.
 * Each section is a visually distinct card with:
 * - Dark background (no frost glass, no blur)
 * - Subtle gradient overlay on header
 * - Light border (1px solid rgba)
 * - Title + description + divider before content
 * - Content body for form rows
 *
 * Desktop layout:
 * +--------------------------------------------------+
 * | Title                                       badge |
 * | Description                                       |
 * |--------------------------------------------------|
 * | Form Row 1:  [Label + Desc | Input]              |
 * | Form Row 2:  [Label + Desc | Input]              |
 * +--------------------------------------------------+
 *
 * Usage:
 *   $section = new FormSection('Section Title', 'Description');
 *   $section->start();
 *   // ... render form rows ...
 *   $section->end();
 */
final class FormSection
{
    private string $title;
    private string $description;
    private string $id;
    private string $badge;
    private bool $started = false;

    /**
     * @param string $title       Section title displayed in the header.
     * @param string $description Optional description shown below the title.
     * @param string $id          Optional HTML ID. Auto-generated if empty.
     * @param string $badge       Optional badge text (e.g. field count).
     */
    public function __construct(
        string $title,
        string $description = '',
        string $id = '',
        string $badge = ''
    ) {
        $this->title = $title;
        $this->description = $description;
        $this->id = $id ?: 'jas-form-section--' . sanitize_title_with_dashes($title);
        $this->badge = $badge;
    }

    /**
     * Render the section opening tags.
     */
    public function start(): void
    {
        if ($this->started) {
            return;
        }

        $this->started = true;

        echo '<div class="jas-form-section" id="' . esc_attr($this->id) . '">';

        // Header with title and optional badge
        echo '<div class="jas-form-section__header">';
        echo '<h3 class="jas-form-section__title">' . esc_html($this->title) . '</h3>';

        if ($this->badge !== '') {
            echo '<span class="jas-form-section__badge">' . esc_html($this->badge) . '</span>';
        }

        echo '</div>';

        // Description
        if ($this->description !== '') {
            echo '<p class="jas-form-section__description">' . esc_html($this->description) . '</p>';
        }

        // Divider
        echo '<div class="jas-form-section__divider"></div>';

        // Content body
        echo '<div class="jas-form-section__content">';
    }

    /**
     * Render the section closing tags.
     */
    public function end(): void
    {
        echo '</div>'; // .jas-form-section__content
        echo '</div>'; // .jas-form-section
    }

    /**
     * Render a complete section in one call.
     *
     * @param string $title       Section title.
     * @param string $description Optional description.
     * @param string $content     Inner HTML content (form rows).
     */
    public static function renderSimple(string $title, string $description = '', string $content = ''): void
    {
        $section = new self($title, $description);
        $section->start();
        echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        $section->end();
    }
}