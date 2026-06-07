<?php

declare(strict_types=1);

namespace Jasanika\Design;

use Jasanika\Admin\SettingsManager;

/**
 * Theme Preset Manager.
 *
 * Manages design presets that define token overrides for complete
 * theme appearance switching. Provides preset registration, active
 * preset resolution, token override retrieval, and preset application.
 *
 * M31: Added actual token definitions for all presets. Added
 * applyPresetToSettings() method for applying preset values to
 * the SettingsManager.
 *
 * Presets:
 * - default  — Standard Jasanika design
 * - modern   — Cleaner, more contemporary variant
 * - minimal  — Reduced visual complexity
 * - business — Professional business appearance
 * - custom   — Full manual control
 *
 * Each preset defines complete token overrides. Unspecified
 * tokens fall through to values from DesignSettingsManager defaults.
 *
 * M27: Added Business preset, Custom preset mode support, and
 * display helper methods for the Appearance Dashboard.
 */
final class ThemePresetManager
{
    /** @var array<string, array{name: string, label: string, description: string, tokens: array<string, string>}> */
    private array $presets = [];

    private string $activePreset = 'default';

    /**
     * Register a design preset.
     *
     * Presets cannot be unregistered. Duplicate registration by name
     * is silently ignored to prevent corruption.
     *
     * @param string               $name           Preset identifier (e.g. 'default').
     * @param string               $label          Human-readable label (e.g. 'Default').
     * @param string               $description    Human-readable description.
     * @param array<string, string> $tokenOverrides Token name → CSS value overrides.
     */
    public function registerPreset(string $name, string $label, string $description, array $tokenOverrides = []): void
    {
        if (isset($this->presets[$name])) {
            return;
        }

        $this->presets[$name] = [
            'name'        => $name,
            'label'       => $label,
            'description' => $description,
            'tokens'      => $tokenOverrides,
        ];
    }

    /**
     * Set the active preset by name.
     *
     * Returns true on success, false if the preset name is not registered.
     */
    public function setActivePreset(string $name): bool
    {
        if (!isset($this->presets[$name])) {
            return false;
        }

        $this->activePreset = $name;

        return true;
    }

    /**
     * Get the active preset identifier.
     */
    public function getActivePreset(): string
    {
        return $this->activePreset;
    }

    /**
     * Get a registered preset definition by name.
     *
     * @return array{name: string, label: string, description: string, tokens: array<string, string>}|null
     */
    public function getPreset(string $name): ?array
    {
        return $this->presets[$name] ?? null;
    }

    /**
     * Get the full active preset definition.
     *
     * @return array{name: string, label: string, description: string, tokens: array<string, string>}|null
     */
    public function getActivePresetData(): ?array
    {
        return $this->presets[$this->activePreset] ?? null;
    }

    /**
     * Get token overrides for the currently active preset.
     *
     * Returns an associative array of token name → CSS value.
     * Empty array when the active preset has no overrides.
     *
     * @return array<string, string>
     */
    public function getActiveTokenOverrides(): array
    {
        $preset = $this->getActivePresetData();

        return $preset['tokens'] ?? [];
    }

    /**
     * Get all registered presets.
     *
     * @return array<string, array{name: string, label: string, description: string, tokens: array<string, string>}>
     */
    public function getAllPresets(): array
    {
        return $this->presets;
    }

    /**
     * Get the human-readable label of the active preset.
     */
    public function getActivePresetLabel(): string
    {
        $data = $this->getActivePresetData();

        return $data['label'] ?? ucfirst($this->activePreset);
    }

    /**
     * Get the description of the active preset.
     */
    public function getActivePresetDescription(): string
    {
        $data = $this->getActivePresetData();

        return $data['description'] ?? '';
    }

    /**
     * Check whether the active preset is the Custom mode.
     */
    public function isCustomMode(): bool
    {
        return $this->activePreset === 'custom';
    }

    /**
     * Apply the active preset's token values to the SettingsManager.
     *
     * Writes preset color and typography values as WordPress options
     * so that DesignSettingsManager reads the correct values for the
     * active preset. When the active preset is 'custom', no values
     * are overwritten (full manual control).
     *
     * M31: New method for preset application engine.
     *
     * @param SettingsManager $settingsManager Settings manager to write to.
     */
    public function applyPresetToSettings(SettingsManager $settingsManager): void
    {
        if ($this->isCustomMode()) {
            return;
        }

        $overrides = $this->getActiveTokenOverrides();

        if (empty($overrides)) {
            return;
        }

        $colorMap = [
            '--jas-color-primary'    => 'primary_color',
            '--jas-color-secondary'  => 'secondary_color',
            '--jas-color-accent'     => 'accent_color',
            '--jas-color-background' => 'background_color',
            '--jas-color-surface'    => 'surface_color',
            '--jas-color-text'       => 'text_color',
            '--jas-color-heading'    => 'heading_color',
            '--jas-color-border'     => 'border_color',
        ];

        foreach ($colorMap as $token => $settingKey) {
            if (isset($overrides[$token])) {
                $settingsManager->set($settingKey, $overrides[$token]);
            }
        }
    }

    /**
     * Get the complete resolved token set for the active preset.
     *
     * Returns all token overrides merged with the default preset
     * values so frontend rendering receives a complete picture.
     *
     * M31: New method for preset application engine.
     *
     * @return array<string, string> Token name → CSS value.
     */
    public function getAppliedTokens(): array
    {
        if ($this->isCustomMode()) {
            return [];
        }

        return $this->getActiveTokenOverrides();
    }
}
