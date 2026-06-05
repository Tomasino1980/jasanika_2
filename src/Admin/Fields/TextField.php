<?php

declare(strict_types=1);

namespace Jasanika\Admin\Fields;

use Jasanika\Admin\SettingsManager;

final class TextField implements FieldInterface
{
    private string $key;
    private string $label;
    private SettingsManager $settingsManager;
    private ?string $default;
    private string $description;

    /**
     * @param string|null $default Default value. If null, resolved from SettingsRegistry.
     */
    public function __construct(
        string $key,
        string $label,
        SettingsManager $settingsManager,
        ?string $default = null,
        string $description = ''
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
        if ($this->default !== null) {
            return $this->default;
        }

        $resolved = $this->settingsManager->get($this->key);

        if (is_string($resolved)) {
            return $resolved;
        }

        return '';
    }

    public function render(): void
    {
        $current = $this->settingsManager->get($this->key);

        if (!is_string($current)) {
            $current = $this->getDefault();
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
        if (!is_string($value)) {
            return $this->getDefault();
        }

        return sanitize_text_field($value);
    }
}
