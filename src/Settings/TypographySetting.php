<?php

declare(strict_types=1);

namespace Jasanika\Settings;

use Jasanika\Contracts\SettingInterface;

final class TypographySetting implements SettingInterface
{
    public function getKey(): string
    {
        return 'typography';
    }

    public function getDefaultValue(): mixed
    {
        return 'system';
    }
}
