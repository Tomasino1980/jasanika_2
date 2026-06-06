<?php

declare(strict_types=1);

namespace Jasanika\Admin\Fields;

use Jasanika\Admin\SettingsManager;

/**
 * Abstract base class for all field types.
 *
 * Consolidates shared state and constructor behavior that is duplicated
 * across multiple field implementations (TextField, ColorField, NumberField, SelectField).
 *
 * Justification:
 * Multiple field implementations already share common state and constructor behavior,
 * including key, label, default, description, and SettingsManager dependency injection.
 * This abstraction removes real duplication without introducing speculative patterns.
 */
abstract class AbstractField implements FieldInterface
{
    protected string $key;
    protected string $label;
    protected SettingsManager $settingsManager;
    protected ?string $default;
    protected string $description;

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
}