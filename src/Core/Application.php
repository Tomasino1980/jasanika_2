<?php

declare(strict_types=1);

namespace Jasanika\Core;

use Jasanika\Container\Container;

final class Application
{
    private Container $container;

    public function __construct()
    {
        $this->container = new Container();
    }

    public function boot(): void
    {
        // Framework initialized
    }

    public function getContainer(): Container
    {
        return $this->container;
    }
}