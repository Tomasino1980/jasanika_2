<?php

declare(strict_types=1);

namespace Jasanika\Design;

/**
 * Theme Preset Manager.
 *
 * Manages design presets that define token overrides for complete
 * theme appearance switching. Provides preset registration, active
 * preset resolution, and token override retrieval.
 *
 * Initial presets:
 * - default  — Standard Jasanika design
 * - modern   — Cleaner, more contemporary variant
 * - minimal  — Reduced visual complexity
 *
 * Each preset can override any subset of design tokens. Unspecified
 * tokens fall through to values from DesignSettingsManager and
 * DesignTokenRegistry defaults.
 *
 * No admin UI exists yet — this is the foundation layer.
 * The active preset is always 'default' by default.
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
}