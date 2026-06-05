<?php

declare(strict_types=1);

namespace Jasanika\Core;

use Jasanika\Container\Container;
use Jasanika\Modules\ModuleManager;

final class Application
{
    private Container $container;
    private ModuleManager $moduleManager;

    public function __construct()
    {
        $this->container = new Container();
        $this->moduleManager = new ModuleManager();

        $this->container->register(
            ModuleManager::class,
            function (Container $container): ModuleManager {
                return $this->moduleManager;
            }
        );
    }

    public function boot(): void
    {
        // Framework initialized
    }

    public function getContainer(): Container
    {
        return $this->container;
    }

    public function getModuleManager(): ModuleManager
    {
        return $this->moduleManager;
    }
}