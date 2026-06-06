<?php

declare(strict_types=1);

namespace Jasanika\Admin\Fields;

use Jasanika\Admin\SettingsManager;

final class NumberField extends AbstractField
{
    private int $min;
    private int $max;

    /**
     * @param string|null $default Default value. If null, resolved from SettingsRegistry.
     */
    public function __construct(
        string $key,
        string $label,
        SettingsManager $settingsManager,
        ?string $default = null,
        int $min = 1,
        int $max = 9999,
        string $description = ''
    ) {
        parent::__construct($key, $label, $settingsManager, $default, $description);

        $this->min = $min;
        $this->max = $max;
    }

    public function getDefault(): string
    {
        if ($this->default !== null) {
            return $this->default;
        }

        $resolved = $this->settingsManager->get($this->key);

        if (is_string($resolved) && $resolved !== '') {
            return $resolved;
        }

        return (string) $this->min;
    }

    public function render(): void
    {
        $current = $this->settingsManager->get($this->key);

        if (!is_string($current)) {
            $current = $this->getDefault();
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
            return $this->getDefault();
        }

        return $value;
    }
}