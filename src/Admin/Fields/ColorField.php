<?php

declare(strict_types=1);

namespace Jasanika\Admin\Fields;

use Jasanika\Admin\Components\ColorPicker;

/**
 * Color Field — M32.
 *
 * Renders a modern color picker field using the ColorPicker component.
 * The user sees a swatch + HEX input combo that opens a floating
 * picker panel with saturation square, hue slider, and HEX/RGB inputs.
 *
 * No WordPress default color picker.
 * No jQuery.
 * Vanilla JavaScript only.
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

    public function render(): void
    {
        $current = $this->settingsManager->get($this->key);

        if (!is_string($current)) {
            $current = $this->getDefault();
        }

        ColorPicker::render($this->key, $current, $this->label);

        if ($this->description !== '') {
            echo '<p class="description">' . esc_html($this->description) . '</p>';
        }
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