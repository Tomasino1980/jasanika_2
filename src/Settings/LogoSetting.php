<?php

declare(strict_types=1);

namespace Jasanika\Settings;

use Jasanika\Contracts\SettingInterface;

final class LogoSetting implements SettingInterface
{
    public function getKey(): string
    {
        return 'logo';
    }

    public function getDefaultValue(): mixed
    {
        return '';
    }

    public function getLabel(): string
    {
        return 'Logo';
    }

    public function getFieldType(): string
    {
        return 'text';
    }

    /**
     * @return string[]
     */
    public function getOptions(): array
    {
        return [];
    }
}
