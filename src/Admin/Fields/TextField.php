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

    /**
     * Render only the input HTML.
     * Label and description are wrapped by FormRow in SettingsPage.
     *
     * M34: Unified input styling with jas-form-input class.
     */
    public function render(): void
    {
        $current = $this->settingsManager->get($this->key);

        if (!is_string($current)) {
            $current = $this->getDefault();
        }

        printf(
            '<input type="text" id="%s" name="%s" value="%s" class="jas-form-input" />',
            esc_attr($this->key),
            esc_attr($this->key),
            esc_attr($current)
        );
    }

    public function sanitize(mixed $value): string
    {
        if (!is_string($value)) {
            return $this->getDefault();
        }

        return sanitize_text_field($value);
    }
}