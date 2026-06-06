<?php

declare(strict_types=1);

namespace Jasanika\Admin\Fields;

use Jasanika\Admin\SettingsManager;

final class SelectField extends AbstractField
{
    /** @var string[] */
    private array $options;

    /**
     * @param string[]    $options          Allowed option values.
     * @param string|null $default          Default value. If null, resolved from SettingsRegistry.
     */
    public function __construct(
        string $key,
        string $label,
        SettingsManager $settingsManager,
        array $options,
        ?string $default = null,
        string $description = ''
    ) {
        parent::__construct($key, $label, $settingsManager, $default, $description);

        $this->options = $options;
    }

    public function getDefault(): string
    {
        if ($this->default !== null) {
            return $this->default;
        }

        $resolved = $this->settingsManager->get($this->key);

        if (is_string($resolved) && in_array($resolved, $this->options, true)) {
            return $resolved;
        }

        return $this->options[0] ?? '';
    }

    public function render(): void
    {
        $current = $this->settingsManager->get($this->key);

        if (!is_string($current) || !in_array($current, $this->options, true)) {
            $current = $this->getDefault();
        }

        echo '<select id="' . esc_attr($this->key) . '" name="' . esc_attr($this->key) . '">';

        foreach ($this->options as $option) {
            printf(
                '<option value="%s" %s>%s</option>',
                esc_attr($option),
                selected($current, $option, false),
                esc_html(ucfirst(str_replace('-', ' ', $option)))
            );
        }

        echo '</select>';
        echo '<p class="description">' . esc_html($this->description) . '</p>';
    }

    public function sanitize(mixed $value): string
    {
        $value = is_string($value) ? sanitize_text_field($value) : '';

        if (!in_array($value, $this->options, true)) {
            return $this->getDefault();
        }

        return $value;
    }
}