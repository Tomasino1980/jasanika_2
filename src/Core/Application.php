<?php

declare(strict_types=1);

namespace Jasanika\Core;

use Jasanika\Admin\AdminMenu;
use Jasanika\Admin\AdminPage;
use Jasanika\Admin\Fields\ColorField;
use Jasanika\Admin\Fields\NumberField;
use Jasanika\Admin\Fields\SelectField;
use Jasanika\Admin\SettingsManager;
use Jasanika\Admin\SettingsPage;
use Jasanika\Assets\AssetManager;
use Jasanika\Config\ConfigRepository;
use Jasanika\Container\Container;
use Jasanika\Hooks\HookManager;
use Jasanika\Modules\ModuleManager;
use Jasanika\Settings\ContainerWidthSetting;
use Jasanika\Settings\LogoSetting;
use Jasanika\Settings\PrimaryColorSetting;
use Jasanika\Settings\SettingsRegistry;
use Jasanika\Settings\SiteLayoutSetting;
use Jasanika\Settings\TypographySetting;

final class Application
{
    private Container $container;
    private ModuleManager $moduleManager;
    private ConfigRepository $configRepository;
    private HookManager $hookManager;
    private AssetManager $assetManager;
    private SettingsRegistry $settingsRegistry;
    private SettingsManager $settingsManager;
    private AdminMenu $adminMenu;

    public function __construct()
    {
        $this->container = new Container();
        $this->moduleManager = new ModuleManager();
        $this->configRepository = new ConfigRepository();
        $this->hookManager = new HookManager();
        $this->assetManager = new AssetManager();

        $this->settingsRegistry = new SettingsRegistry();
        $this->settingsRegistry->register(new SiteLayoutSetting());
        $this->settingsRegistry->register(new LogoSetting());
        $this->settingsRegistry->register(new PrimaryColorSetting());
        $this->settingsRegistry->register(new TypographySetting());
        $this->settingsRegistry->register(new ContainerWidthSetting());

        $this->settingsManager = new SettingsManager($this->settingsRegistry);

        $this->adminMenu = new AdminMenu(
            $this->configRepository->get('app.version', '0.12')
        );

        $dashboardPage = new AdminPage(
            'Jasanika Framework',
            'jasanika',
            [$this->adminMenu, 'renderDashboard']
        );

        $this->adminMenu->registerPage($dashboardPage);
        $this->adminMenu->register($this->hookManager);

        $settingsPage = new SettingsPage(
            $this->configRepository->get('app.version', '0.12'),
            new SelectField(
                'site_layout',
                __('Site Layout', 'jasanika'),
                $this->settingsManager,
                ['full-width', 'boxed'],
                'full-width',
                __('Select the layout style for your site.', 'jasanika')
            ),
            new ColorField(
                'primary_color',
                __('Primary Color', 'jasanika'),
                $this->settingsManager,
                '#2c3e50',
                __('Enter a hex color for the primary theme color (e.g. #2c3e50).', 'jasanika')
            ),
            new SelectField(
                'typography',
                __('Typography', 'jasanika'),
                $this->settingsManager,
                ['system', 'playfair', 'inter', 'monospace'],
                'system',
                __('Choose the typography style for your site.', 'jasanika')
            ),
            new NumberField(
                'container_width',
                __('Container Width', 'jasanika'),
                $this->settingsManager,
                '1200',
                1,
                9999,
                __('Set the maximum container width in pixels (e.g. 1200).', 'jasanika')
            )
        );

        $this->hookManager->addAction('admin_init', [$settingsPage, 'registerSettings']);

        $settingsSubPage = new AdminPage(
            'Jasanika Settings',
            'jasanika-settings',
            [$settingsPage, 'render']
        );

        $this->adminMenu->registerSubPage($settingsSubPage);

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

        $this->container->register(
            AdminMenu::class,
            function (Container $container): AdminMenu {
                return $this->adminMenu;
            }
        );

        $this->container->register(
            SettingsRegistry::class,
            function (Container $container): SettingsRegistry {
                return $this->settingsRegistry;
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

    public function getSettingsRegistry(): SettingsRegistry
    {
        return $this->settingsRegistry;
    }

    public function getAdminMenu(): AdminMenu
    {
        return $this->adminMenu;
    }
}