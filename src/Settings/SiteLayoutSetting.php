<?php

declare(strict_types=1);

namespace Jasanika\Settings;

use Jasanika\Contracts\SettingInterface;

final class SiteLayoutSetting implements SettingInterface
{
    public function getKey(): string
    {
        return 'site_layout';
    }

    public function getDefaultValue(): mixed
    {
        return 'full-width';
    }
}