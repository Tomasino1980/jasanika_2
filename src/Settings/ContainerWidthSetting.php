<?php

declare(strict_types=1);

namespace Jasanika\Settings;

use Jasanika\Contracts\SettingInterface;

final class ContainerWidthSetting implements SettingInterface
{
    public function getKey(): string
    {
        return 'container_width';
    }

    public function getDefaultValue(): mixed
    {
        return '1200';
    }
}
