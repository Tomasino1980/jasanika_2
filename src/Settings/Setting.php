<?php

declare(strict_types=1);

namespace Jasanika\Settings;

use Jasanika\Contracts\SettingInterface;

/**
 * Generic reusable setting value object.
 *
 * Implements SettingInterface so that settings can be registered inline
 * without creating a dedicated class for each setting. This is useful
 * for the large number of Site Builder settings introduced in M26.
 *
 * Usage:
 *   $registry->register(new Setting('header_height', '80px', 'Header Height', 'text'));
 *
 * @todo M30+: If settings require custom validation or computed defaults,
 *       create dedicated setting classes for those exceptions.
 */
final class Setting implements SettingInterface
{
    private string $key;
    private mixed $default;
    private string $label;
    private string $fieldType;
    /** @var string[] */
    private array $options;

    /**
     * @param string   $key       Setting key used in WordPress Options API.
     * @param mixed    $default   Default value.
     * @param string   $label     Human-readable label in admin UI.
     * @param string   $fieldType Field type (text, color, select, number, media).
     * @param string[] $options   Options for select fields (key => label).
     */
    public function __construct(
        string $key,
        mixed $default,
        string $label,
        string $fieldType,
        array $options = []
    ) {
        $this->key = $key;
        $this->default = $default;
        $this->label = $label;
        $this->fieldType = $fieldType;
        $this->options = $options;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getDefaultValue(): mixed
    {
        return $this->default;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getFieldType(): string
    {
        return $this->fieldType;
    }

    /**
     * @return string[]
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}