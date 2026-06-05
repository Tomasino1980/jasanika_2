<?php

declare(strict_types=1);

namespace Jasanika\Contracts;

use Jasanika\Hooks\HookManager;

interface HookableInterface
{
    public function register(HookManager $hooks): void;
}