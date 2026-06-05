<?php

declare(strict_types=1);

namespace Jasanika\Admin\Fields;

use Jasanika\Admin\SettingsManager;

final class ColorField implements FieldInterface
{
    private string $key;
    private string $label;
    private SettingsManager $settingsManager;
    private string $default;
    private string $description;

    public function __construct(
        string $key,
        string $label,
        SettingsManager $settingsManager,
        string $default,
        string $description
    ) {
        $this->key = $key;
        $this->label = $label;
        $this->settingsManager = $settingsManager;
        $this->default = $default;
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
            '<input type="text" id="%s" name="%s" value="%s" class="regular-text" />',
            esc_attr($this->key),
            esc_attr($this->key),
            esc_attr($current)
        );

        echo '<p class="description">' . esc_html($this->description) . '</p>';
    }

    public function sanitize(mixed $value): string
    {
        $value = is_string($value) ? sanitize_text_field($value) : '';

        $value = ltrim($value, '#');
        $value = preg_replace('/[^0-9a-fA-F]/', '', $value);

        if (strlen($value) !== 6) {
            return $this->default;
        }

        return '#' . strtolower($value);
    }
}