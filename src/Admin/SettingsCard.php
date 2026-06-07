<?php

declare(strict_types=1);

namespace Jasanika\Admin;

/**
 * Settings Card Component.
 *
 * A reusable card wrapper for the Settings UI. Each card contains:
 * - A header with title and optional badge
 * - An optional description paragraph
 * - Settings content (fields)
 * - Optional action buttons
 *
 * All settings are rendered inside these cards — no raw field lists.
 * Cards follow the admin design token system for consistent spacing,
 * borders, and visual rhythm.
 *
 * M29 — Settings UI Refactor & Design System.
 *
 * Usage:
 *   $card = new SettingsCard('Header Title', 'Optional description');
 *   $card->start();
 *   // ... render field HTML ...
 *   $card->end();
 *
 *   // With actions:
 *   $card = new SettingsCard('Title', 'Desc', ['<button>...</button>']);
 */
final class SettingsCard
{
    private string $title;
    private string $description;
    private string $id;
    /** @var string[] */
    private array $actions;
    private bool $started = false;

    /**
     * @param string   $title       Card title displayed in the header.
     * @param string   $description Optional description shown below the title.
     * @param string[] $actions     Optional action HTML strings (e.g. buttons, links).
     * @param string   $id          Optional HTML ID for the card. Auto-generated if empty.
     */
    public function __construct(
        string $title,
        string $description = '',
        array $actions = [],
        string $id = ''
    ) {
        $this->title       = $title;
        $this->description = $description;
        $this->actions     = $actions;
        $this->id          = $id ?: 'jas-settings-card--' . sanitize_title_with_dashes($title);
    }

    /**
     * Render the card opening tags including header and description.
     */
    public function start(): void
    {
        if ($this->started) {
            return;
        }

        $this->started = true;

        echo '<div class="jas-settings-card" id="' . esc_attr($this->id) . '">';
        echo '<div class="jas-settings-card__header">';
        echo '<h3 class="jas-settings-card__title">' . esc_html($this->title) . '</h3>';

        if (!empty($this->actions)) {
            echo '<div class="jas-settings-card__actions">';
            foreach ($this->actions as $action) {
                echo $action; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            }
            echo '</div>';
        }

        echo '</div>'; // .jas-settings-card__header

        if ($this->description !== '') {
            echo '<p class="jas-settings-card__description">' . esc_html($this->description) . '</p>';
        }

        echo '<div class="jas-settings-card__body">';
    }

    /**
     * Render the card closing tags.
     */
    public function end(): void
    {
        echo '</div>'; // .jas-settings-card__body
        echo '</div>'; // .jas-settings-card
    }

    /**
     * Render a complete card with content in one call.
     *
     * Convenience method for simple cards.
     *
     * @param string $title       Card title.
     * @param string $description Optional description.
     * @param string $content     Inner HTML content of the card body.
     * @param string[] $actions   Optional actions array.
     */
    public static function renderSimple(
        string $title,
        string $description = '',
        string $content = '',
        array $actions = []
    ): void {
        $card = new self($title, $description, $actions);
        $card->start();
        echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        $card->end();
    }
}