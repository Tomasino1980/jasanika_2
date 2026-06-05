<?php

declare(strict_types=1);

namespace Jasanika\Contracts;

interface ModuleInterface
{
    public function register(): void;

    public function boot(): void;
}