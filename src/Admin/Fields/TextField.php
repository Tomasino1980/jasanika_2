<?php

declare(strict_types=1);

namespace Jasanika\Admin\Fields;

final class TextField extends AbstractField
{
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