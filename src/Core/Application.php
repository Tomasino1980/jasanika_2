<?php

declare(strict_types=1);

namespace Jasanika\Core;

use Jasanika\Admin\SettingsManager;
use Jasanika\Assets\AssetManager;
use Jasanika\Config\ConfigRepository;
use Jasanika\Container\Container;
use Jasanika\Hooks\HookManager;
use Jasanika\Modules\ModuleManager;

final class Application
{
    private Container $container;
    private ModuleManager $moduleManager;
    private ConfigRepository $configRepository;
    private HookManager $hookManager;
    private AssetManager $assetManager;
    private SettingsManager $settingsManager;

    public function __construct()
    {
        $this->container = new Container();
        $this->moduleManager = new ModuleManager();
        $this->configRepository = new ConfigRepository();
        $this->hookManager = new HookManager();
        $this->assetManager = new AssetManager();
        $this->settingsManager = new SettingsManager();

        $this->container->register(
            ModuleManager::class,
            function (Container $container): ModuleManager {
                return $this->moduleManager;
            }
        );

        $this->container->register(
            ConfigRepository::class,
            function (Container $container): ConfigRepository {
                return $this->configRepository;
            }
        );

        $this->container->register(
            HookManager::class,
            function (Container $container): HookManager {
                return $this->hookManager;
            }
        );

        $this->container->register(
            AssetManager::class,
            function (Container $container): AssetManager {
                return $this->assetManager;
            }
        );

        $this->container->register(
            SettingsManager::class,
            function (Container $container): SettingsManager {
                return $this->settingsManager;
            }
        );
    }

    public function boot(): void
    {
        // Configuration system initialized
    }

    public function getContainer(): Container
    {
        return $this->container;
    }

    public function getModuleManager(): ModuleManager
    {
        return $this->moduleManager;
    }

    public function getConfigRepository(): ConfigRepository
    {
        return $this->configRepository;
    }

    public function getHookManager(): HookManager
    {
        return $this->hookManager;
    }

    public function getAssetManager(): AssetManager
    {
        return $this->assetManager;
    }

    public function getSettingsManager(): SettingsManager
    {
        return $this->settingsManager;
    }
}