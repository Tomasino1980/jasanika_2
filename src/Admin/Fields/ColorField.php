<?php

declare(strict_types=1);

namespace Jasanika\Admin\Fields;

use Jasanika\Admin\Components\ColorPicker;

/**
 * Color Field — M32/M34.
 *
 * Renders a modern color picker field using the ColorPicker component.
 * Only the input HTML is rendered here; label and description are
 * wrapped by FormRow in SettingsPage.
 *
 * M34: Removed inline description rendering (handled by FormRow).
 */
final class ColorField extends AbstractField
{
    public function getDefault(): string
    {
        if ($this->default !== null) {
            return $this->default;
        }

        $resolved = $this->settingsManager->get($this->key);

        if (is_string($resolved) && $resolved !== '') {
            return $resolved;
        }

        return '#2c3e50';
    }

    /**
     * Render only the color picker input HTML.
     * Label and description are wrapped by FormRow in SettingsPage.
     */
    public function render(): void
    {
        $current = $this->settingsManager->get($this->key);

        if (!is_string($current)) {
            $current = $this->getDefault();
        }

        ColorPicker::render($this->key, $current, $this->label);
    }

    public function sanitize(mixed $value): string
    {
        $value = is_string($value) ? sanitize_text_field($value) : '';

        $value = ltrim($value, '#');
        $value = preg_replace('/[^0-9a-fA-F]/', '', $value);

        if (strlen($value) !== 6) {
            return $this->getDefault();
        }

        return '#' . strtolower($value);
    }
}