<?php

declare(strict_types=1);

namespace Jasanika\Core;

use Jasanika\Admin\AdminMenu;
use Jasanika\Admin\AdminPage;
use Jasanika\Admin\Fields\FieldFactory;
use Jasanika\Admin\Pages\DashboardPage;
use Jasanika\Admin\SettingsManager;
use Jasanika\Admin\SettingsPage;
use Jasanika\Assets\Asset;
use Jasanika\Assets\AssetManager;
use Jasanika\Config\ConfigRepository;
use Jasanika\Container\Container;
use Jasanika\Core\ThemeRenderer;
use Jasanika\Hooks\HookManager;
use Jasanika\Media\MediaManager;
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
    private MediaManager $mediaManager;
    private SettingsRegistry $settingsRegistry;
    private SettingsManager $settingsManager;
    private AdminMenu $adminMenu;
    private FrameworkInfo $frameworkInfo;
    private ThemeRenderer $themeRenderer;

    public function __construct()
    {
        $this->frameworkInfo = new FrameworkInfo(
            'Jasanika 2',
            '0.19'
        );

        $this->container = new Container();
        $this->moduleManager = new ModuleManager();
        $this->configRepository = new ConfigRepository();
        $this->hookManager = new HookManager();
        $this->assetManager = new AssetManager();
        $this->mediaManager = new MediaManager();

        $this->settingsRegistry = new SettingsRegistry();
        $this->settingsRegistry->register(new SiteLayoutSetting());
        $this->settingsRegistry->register(new LogoSetting());
        $this->settingsRegistry->register(new PrimaryColorSetting());
        $this->settingsRegistry->register(new TypographySetting());
        $this->settingsRegistry->register(new ContainerWidthSetting());

        $this->settingsManager = new SettingsManager($this->settingsRegistry);

        $this->adminMenu = new AdminMenu();

        $dashboardPage = new DashboardPage($this->frameworkInfo);

        $dashboardAdminPage = new AdminPage(
            'Jasanika Framework',
            'jasanika',
            [$dashboardPage, 'render']
        );

        $this->adminMenu->registerPage($dashboardAdminPage);
        $this->adminMenu->register($this->hookManager);

        $this->registerMediaFieldAsset();
        $this->registerFrontendAssets();
        $this->initThemeRenderer();

        // Register asset lifecycle hooks.
        // WordPress registration (wp_register_script / wp_register_style)
        // must happen during proper enqueue hooks, not during framework bootstrap.
        $this->hookManager->addAction('admin_enqueue_scripts', [$this->assetManager, 'registerWordPressAssets']);
        $this->hookManager->addAction('wp_enqueue_scripts', [$this->assetManager, 'registerWordPressAssets']);

        $fieldFactory = new FieldFactory($this->settingsManager, $this->assetManager);

        $settingsPage = new SettingsPage(
            $this->frameworkInfo,
            $this->settingsRegistry,
            $fieldFactory
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
            MediaManager::class,
            function (Container $container): MediaManager {
                return $this->mediaManager;
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

        $this->container->register(
            FrameworkInfo::class,
            function (Container $container): FrameworkInfo {
                return $this->frameworkInfo;
            }
        );

        $this->container->register(
            ThemeRenderer::class,
            function (Container $container): ThemeRenderer {
                return $this->themeRenderer;
            }
        );
    }

    /**
     * Register the media-field.js asset definition.
     *
     * Stores the asset only. WordPress registration (wp_register_script)
     * is deferred to the registerWordPressAssets() call, which runs
     * during admin_enqueue_scripts / wp_enqueue_scripts hooks.
     */
    private function registerMediaFieldAsset(): void
    {
        $script = new Asset(
            'jasanika-media-field',
            get_template_directory_uri() . '/assets/admin/js/media-field.js',
            '0.17',
            ['jquery'],
            'all',
            true
        );

        $this->assetManager->registerScript($script);
    }

    /**
     * Register frontend CSS and JavaScript asset definitions.
     *
     * Stores the assets only. WordPress registration is deferred
     * to registerWordPressAssets() during the wp_enqueue_scripts hook.
     */
    private function registerFrontendAssets(): void
    {
        $style = new Asset(
            'jasanika-frontend',
            get_template_directory_uri() . '/assets/css/frontend.css',
            '0.18'
        );

        $this->assetManager->registerStyle($style);

        $script = new Asset(
            'jasanika-frontend',
            get_template_directory_uri() . '/assets/js/frontend.js',
            '0.18',
            [],
            'all',
            true
        );

        $this->assetManager->registerScript($script);
    }

    /**
     * Initialize the ThemeRenderer for frontend rendering.
     *
     * Creates the ThemeRenderer instance with all required dependencies,
     * registers it in the Container, and calls init() to set up hooks.
     */
    private function initThemeRenderer(): void
    {
        $this->themeRenderer = new ThemeRenderer(
            $this->frameworkInfo,
            $this->settingsManager,
            $this->assetManager,
            $this->hookManager
        );

        $this->themeRenderer->init();
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

    public function getMediaManager(): MediaManager
    {
        return $this->mediaManager;
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

    public function getFrameworkInfo(): FrameworkInfo
    {
        return $this->frameworkInfo;
    }

    public function getThemeRenderer(): ThemeRenderer
    {
        return $this->themeRenderer;
    }
}