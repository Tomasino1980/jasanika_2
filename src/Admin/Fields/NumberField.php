<?php

declare(strict_types=1);

namespace Jasanika\Admin\Fields;

use Jasanika\Admin\SettingsManager;

final class NumberField implements FieldInterface
{
    private string $key;
    private string $label;
    private SettingsManager $settingsManager;
    private string $default;
    private string $description;
    private int $min;
    private int $max;

    public function __construct(
        string $key,
        string $label,
        SettingsManager $settingsManager,
        string $default,
        int $min,
        int $max,
        string $description
    ) {
        $this->key = $key;
        $this->label = $label;
        $this->settingsManager = $settingsManager;
        $this->default = $default;
        $this->min = $min;
        $this->max = $max;
        $this->description = $description;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getDefault(): string
    {
        return $this->default;
    }

    public function render(): void
    {
        $current = $this->settingsManager->get($this->key);

        if (!is_string($current)) {
            $current = $this->default;
        }

        printf(
            '<input type="text" id="%s" name="%s" value="%s" class="small-text" />',
            esc_attr($this->key),
            esc_attr($this->key),
            esc_attr($current)
        );

        echo '<p class="description">' . esc_html($this->description) . '</p>';
    }

    public function sanitize(mixed $value): string
    {
        $value = is_string($value) ? sanitize_text_field($value) : '';

        $value = preg_replace('/[^0-9]/', '', $value);

        if ($value === '' || (int) $value < $this->min || (int) $value > $this->max) {
            return $this->default;
        }

        return $value;
    }
}