<?php

declare(strict_types=1);

namespace Jasanika\Config;

final class Config
{
    /**
     * @var array<string, mixed>
     */
    private array $items;

    /**
     * @param array<string, mixed> $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * Get a value using dot notation.
     *
     * @param string $key     Dot-notation key (e.g. "database.host").
     * @param mixed  $default Default value if key is not found.
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $keys = explode('.', $key);
        $value = $this->items;

        foreach ($keys as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }

            $value = $value[$segment];
        }

        return $value;
    }

    /**
     * Check whether a key exists using dot notation.
     *
     * @param string $key Dot-notation key.
     * @return bool
     */
    public function has(string $key): bool
    {
        $keys = explode('.', $key);
        $value = $this->items;

        foreach ($keys as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return false;
            }

            $value = $value[$segment];
        }

        return true;
    }

    /**
     * Return all items as an array.
     *
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->items;
    }
}