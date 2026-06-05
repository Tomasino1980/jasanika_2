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
}
