<?php

declare(strict_types=1);

namespace Jasanika\Admin;

use Jasanika\Contracts\SettingInterface;

final class SettingsManager
{
    /**
     * @var array<string, SettingInterface>
     */
    private array $settings = [];

    /**
     * Register a setting with its default value.
     */
    public function register(SettingInterface $setting): void
    {
        $this->settings[$setting->getKey()] = $setting;
    }

    /**
     * Get a setting value from the WordPress Options API.
     *
     * Uses get_option() with the registered default value as fallback.
     */
    public function get(string $key): mixed
    {
        $default = $this->getDefaultValue($key);

        return get_option($key, $default);
    }

    /**
     * Set a setting value using the WordPress Options API.
     *
     * Uses update_option() internally.
     */
    public function set(string $key, mixed $value): bool
    {
        return update_option($key, $value);
    }

    /**
     * Retrieve the default value for a registered setting.
     */
    private function getDefaultValue(string $key): mixed
    {
        if (isset($this->settings[$key])) {
            return $this->settings[$key]->getDefaultValue();
        }

        return null;
    }
}