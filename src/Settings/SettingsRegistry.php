<?php

declare(strict_types=1);

namespace Jasanika\Settings;

use Jasanika\Contracts\SettingInterface;

final class SettingsRegistry
{
    /**
     * @var array<string, SettingInterface>
     */
    private array $settings = [];

    /**
     * Register a setting in the registry.
     */
    public function register(SettingInterface $setting): void
    {
        $this->settings[$setting->getKey()] = $setting;
    }

    /**
     * Get a registered setting by key.
     *
     * Returns null if the setting is not registered.
     */
    public function get(string $key): ?SettingInterface
    {
        return $this->settings[$key] ?? null;
    }

    /**
     * Get all registered settings.
     *
     * @return array<string, SettingInterface>
     */
    public function all(): array
    {
        return $this->settings;
    }
}