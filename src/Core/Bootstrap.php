<?php

declare(strict_types=1);

namespace Jasanika\Core;

final class Bootstrap
{
    public static function init(): void
    {
        $application = new Application();

        $application->boot();
    }
}