<?php

declare(strict_types=1);

namespace Jasanika\Settings;

use Jasanika\Contracts\SettingInterface;

final class PrimaryColorSetting implements SettingInterface
{
    public function getKey(): string
    {
        return 'primary_color';
    }

    public function getDefaultValue(): mixed
    {
        return '#2c3e50';
    }

    public function getLabel(): string
    {
        return 'Primary Color';
    }

    public function getFieldType(): string
    {
        return 'color';
    }

    /**
     * @return string[]
     */
    public function getOptions(): array
    {
        return [];
    }
}
