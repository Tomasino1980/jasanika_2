<?php

declare(strict_types=1);

namespace Jasanika\Events;

/**
 * Frontend Refresh Event.
 *
 * Foundation for the Live Preview System (M28).
 *
 * This event represents a setting change that should trigger a
 * frontend refresh. No live preview implementation exists yet.
 * This is the event-driven architecture foundation only.
 *
 * M27 — Preview Architecture Foundation (prepares for M28).
 *
 * Flow (future):
 * Settings Change → SettingsChangeEvent → FrontendRefreshEvent → Frontend Refresh
 */
final class FrontendRefreshEvent
{
    private string $source;
    private string $settingKey;
    private mixed $oldValue;
    private mixed $newValue;
    private int $timestamp;

    public function __construct(
        string $source,
        string $settingKey,
        mixed $oldValue = null,
        mixed $newValue = null
    ) {
        $this->source     = $source;
        $this->settingKey = $settingKey;
        $this->oldValue   = $oldValue;
        $this->newValue   = $newValue;
        $this->timestamp  = time();
    }

    /**
     * Get the event source identifier.
     */
    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * Get the setting key that changed.
     */
    public function getSettingKey(): string
    {
        return $this->settingKey;
    }

    /**
     * Get the old value before the change.
     */
    public function getOldValue(): mixed
    {
        return $this->oldValue;
    }

    /**
     * Get the new value after the change.
     */
    public function getNewValue(): mixed
    {
        return $this->newValue;
    }

    /**
     * Get the event creation timestamp.
     */
    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    /**
     * Check whether this event should trigger a frontend refresh.
     *
     * Only appearance-related settings trigger a refresh.
     * Administrative settings (slug, page titles, etc.) do not.
     */
    public function shouldRefresh(): bool
    {
        $refreshKeys = [
            'active_preset',
            'primary_color',
            'secondary_color',
            'accent_color',
            'background_color',
            'surface_color',
            'text_color',
            'heading_color',
            'border_color',
            'typography',
            'font_family',
            'container_width',
            'site_layout',
            'header_height',
            'header_bg_color',
            'header_text_color',
            'header_sticky',
            'footer_layout',
            'footer_bg_color',
            'footer_text_color',
            'hero_enabled',
            'hero_type',
            'hero_height',
            'logo_width',
            'logo_height',
            'logo_position',
        ];

        return in_array($this->settingKey, $refreshKeys, true);
    }
}