<?php

declare(strict_types=1);

namespace Jasanika\Admin;

use Jasanika\Contracts\SettingInterface;
use Jasanika\Settings\SettingsRegistry;

final class SettingsManager
{
    private SettingsRegistry $registry;

    /**
     * @param SettingsRegistry $registry The settings registry to manage.
     */
    public function __construct(SettingsRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Register a setting via the registry.
     */
    public function register(SettingInterface $setting): void
    {
        $this->registry->register($setting);
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
     * Retrieve the default value for a registered setting from the registry.
     */
    private function getDefaultValue(string $key): mixed
    {
        $setting = $this->registry->get($key);

        if ($setting instanceof SettingInterface) {
            return $setting->getDefaultValue();
        }

        return null;
    }

    /**
     * Get the underlying settings registry.
     */
    public function getRegistry(): SettingsRegistry
    {
        return $this->registry;
    }
}