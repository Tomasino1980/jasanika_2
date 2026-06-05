<?php

declare(strict_types=1);

namespace Jasanika\Config;

final class ConfigRepository
{
    /**
     * @var array<string, Config>
     */
    private array $configs = [];

    private string $configPath;

    public function __construct(?string $configPath = null)
    {
        $this->configPath = $configPath ?? dirname(__DIR__, 2) . '/config';

        $this->load();
    }

    /**
     * Load all PHP configuration files from config directory.
     *
     * Each file is expected to return an array and is stored
     * under its filename (without extension) as a Config instance.
     */
    private function load(): void
    {
        $files = glob($this->configPath . '/*.php');

        if ($files === false) {
            return;
        }

        foreach ($files as $file) {
            $key = basename($file, '.php');
            $config = require $file;

            if (is_array($config)) {
                $this->configs[$key] = new Config($config);
            }
        }
    }

    /**
     * Get a configuration value using dot notation.
     *
     * The first segment specifies the configuration file,
     * remaining segments target nested keys within that file.
     *
     * Examples:
     *   get('app')          -> all app.php config
     *   get('app.debug')    -> value of debug key in app.php
     *
     * @param string $key     Dot-notation key.
     * @param mixed  $default Default value if key is not found.
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $segments = explode('.', $key, 2);
        $file = $segments[0];

        if (!isset($this->configs[$file])) {
            return $default;
        }

        if (count($segments) === 1) {
            return $this->configs[$file]->all();
        }

        return $this->configs[$file]->get($segments[1], $default);
    }

    /**
     * Check whether a configuration key exists using dot notation.
     *
     * @param string $key Dot-notation key.
     * @return bool
     */
    public function has(string $key): bool
    {
        $segments = explode('.', $key, 2);
        $file = $segments[0];

        if (!isset($this->configs[$file])) {
            return false;
        }

        if (count($segments) === 1) {
            return true;
        }

        return $this->configs[$file]->has($segments[1]);
    }

    /**
     * Return all configuration data as a nested array.
     *
     * @return array<string, array<string, mixed>>
     */
    public function all(): array
    {
        $result = [];

        foreach ($this->configs as $key => $config) {
            $result[$key] = $config->all();
        }

        return $result;
    }
}