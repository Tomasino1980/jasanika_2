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
use Jasanika\Components\ComponentRegistry;
use Jasanika\Components\ComponentRenderer;
use Jasanika\Config\ConfigRepository;
use Jasanika\Container\Container;
use Jasanika\Core\ThemeRenderer;
use Jasanika\Design\DesignSettingsManager;
use Jasanika\Design\DesignTokenGenerator;
use Jasanika\Design\DesignTokenRegistry;
use Jasanika\Design\ThemePresetManager;
use Jasanika\Hooks\HookManager;
use Jasanika\Layout\LayoutManager;
use Jasanika\Layout\LayoutRenderer;
use Jasanika\Media\MediaManager;
use Jasanika\Modules\ModuleManager;
use Jasanika\Navigation\NavigationManager;
use Jasanika\Widgets\WidgetAreaManager;
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
    private NavigationManager $navigationManager;
    private SiteIdentityRenderer $siteIdentityRenderer;
    private WidgetAreaManager $widgetAreaManager;
    private LayoutRegionRenderer $layoutRegionRenderer;
    private ThemeRenderer $themeRenderer;
    private DesignSettingsManager $designSettingsManager;
    private DesignTokenGenerator $designTokenGenerator;
    private DesignTokenRegistry $tokenRegistry;
    private ThemePresetManager $presetManager;
    private LayoutManager $layoutManager;
    private LayoutRenderer $layoutRenderer;
    private ComponentRegistry $componentRegistry;
    private ComponentRenderer $componentRenderer;

    public function __construct()
    {
        $this->frameworkInfo = new FrameworkInfo(
            'Jasanika 2',
            '0.25'
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

        // Initialize navigation and site identity services
        $this->navigationManager = new NavigationManager($this->hookManager);
        $this->siteIdentityRenderer = new SiteIdentityRenderer(
            $this->settingsManager,
            $this->mediaManager
        );

        // Initialize widget area and layout region services
        $this->widgetAreaManager = new WidgetAreaManager($this->hookManager);
        $this->widgetAreaManager->register();
        $this->layoutRegionRenderer = new LayoutRegionRenderer($this->widgetAreaManager);

        // Initialize design settings and token generation services
        $this->designSettingsManager = new DesignSettingsManager($this->settingsManager);

        // Initialize Design Token Registry — single source of truth for token definitions
        $this->tokenRegistry = new DesignTokenRegistry();
        $this->registerDesignTokens();

        // Initialize Theme Preset Manager — preset registration and resolution
        $this->presetManager = new ThemePresetManager();
        $this->registerThemePresets();

        // Initialize DesignTokenGenerator with expanded dependencies
        $this->designTokenGenerator = new DesignTokenGenerator(
            $this->designSettingsManager,
            $this->tokenRegistry,
            $this->presetManager
        );

        // Initialize layout services
        $this->layoutManager = new LayoutManager($this->designSettingsManager);
        $this->layoutRenderer = new LayoutRenderer(
            $this->layoutManager,
            $this->layoutRegionRenderer
        );

        // Initialize component system
        $this->componentRegistry = new ComponentRegistry();
        $this->registerComponents();
        $this->componentRenderer = new ComponentRenderer($this->componentRegistry);

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
            NavigationManager::class,
            function (Container $container): NavigationManager {
                return $this->navigationManager;
            }
        );

        $this->container->register(
            SiteIdentityRenderer::class,
            function (Container $container): SiteIdentityRenderer {
                return $this->siteIdentityRenderer;
            }
        );

        $this->container->register(
            ThemeRenderer::class,
            function (Container $container): ThemeRenderer {
                return $this->themeRenderer;
            }
        );

        $this->container->register(
            WidgetAreaManager::class,
            function (Container $container): WidgetAreaManager {
                return $this->widgetAreaManager;
            }
        );

        $this->container->register(
            LayoutRegionRenderer::class,
            function (Container $container): LayoutRegionRenderer {
                return $this->layoutRegionRenderer;
            }
        );

        $this->container->register(
            DesignSettingsManager::class,
            function (Container $container): DesignSettingsManager {
                return $this->designSettingsManager;
            }
        );

        $this->container->register(
            DesignTokenGenerator::class,
            function (Container $container): DesignTokenGenerator {
                return $this->designTokenGenerator;
            }
        );

        $this->container->register(
            LayoutManager::class,
            function (Container $container): LayoutManager {
                return $this->layoutManager;
            }
        );

        $this->container->register(
            LayoutRenderer::class,
            function (Container $container): LayoutRenderer {
                return $this->layoutRenderer;
            }
        );

        $this->container->register(
            DesignTokenRegistry::class,
            function (Container $container): DesignTokenRegistry {
                return $this->tokenRegistry;
            }
        );

        $this->container->register(
            ThemePresetManager::class,
            function (Container $container): ThemePresetManager {
                return $this->presetManager;
            }
        );

        $this->container->register(
            ComponentRegistry::class,
            function (Container $container): ComponentRegistry {
                return $this->componentRegistry;
            }
        );

        $this->container->register(
            ComponentRenderer::class,
            function (Container $container): ComponentRenderer {
                return $this->componentRenderer;
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
            '0.25'
        );

        $this->assetManager->registerStyle($style);

        $tokens = new Asset(
            'jasanika-tokens',
            get_template_directory_uri() . '/assets/css/tokens.css',
            '0.25'
        );

        $this->assetManager->registerStyle($tokens);

        $components = new Asset(
            'jasanika-components',
            get_template_directory_uri() . '/assets/css/components.css',
            '0.25'
        );

        $this->assetManager->registerStyle($components);

        $script = new Asset(
            'jasanika-frontend',
            get_template_directory_uri() . '/assets/js/frontend.js',
            '0.25',
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
            $this->hookManager,
            $this->navigationManager,
            $this->siteIdentityRenderer,
            $this->layoutRegionRenderer,
            $this->designTokenGenerator,
            $this->layoutManager,
            $this->layoutRenderer,
            $this->componentRenderer
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

    public function getNavigationManager(): NavigationManager
    {
        return $this->navigationManager;
    }

    public function getSiteIdentityRenderer(): SiteIdentityRenderer
    {
        return $this->siteIdentityRenderer;
    }

    public function getThemeRenderer(): ThemeRenderer
    {
        return $this->themeRenderer;
    }

    public function getWidgetAreaManager(): WidgetAreaManager
    {
        return $this->widgetAreaManager;
    }

    public function getLayoutRegionRenderer(): LayoutRegionRenderer
    {
        return $this->layoutRegionRenderer;
    }

    public function getDesignSettingsManager(): DesignSettingsManager
    {
        return $this->designSettingsManager;
    }

    public function getDesignTokenGenerator(): DesignTokenGenerator
    {
        return $this->designTokenGenerator;
    }

    public function getLayoutManager(): LayoutManager
    {
        return $this->layoutManager;
    }

    public function getLayoutRenderer(): LayoutRenderer
    {
        return $this->layoutRenderer;
    }

    public function getTokenRegistry(): DesignTokenRegistry
    {
        return $this->tokenRegistry;
    }

    public function getPresetManager(): ThemePresetManager
    {
        return $this->presetManager;
    }

    public function getComponentRegistry(): ComponentRegistry
    {
        return $this->componentRegistry;
    }

    public function getComponentRenderer(): ComponentRenderer
    {
        return $this->componentRenderer;
    }

    /**
     * Register all design tokens in the DesignTokenRegistry.
     *
     * Tokens are organized by category:
     * - Color          — Semantic color tokens
     * - Typography     — Font family and size scale
     * - Spacing        — Spacing rhythm scale
     * - Layout         — Container width, site layout
     * - Border Radius  — Border radius scale
     *
     * Dynamic values (primary color, font family, etc.) are set at
     * generation time in DesignTokenGenerator. The registry holds
     * the default/fallback values.
     */
    private function registerDesignTokens(): void
    {
        $r = $this->tokenRegistry;

        // --- Color tokens ---
        $r->registerToken('--jas-color-primary',       'Color', '#b78acb', 'Primary brand color');
        $r->registerToken('--jas-color-primary-hover', 'Color', '#c79cda', 'Primary brand color hover state');
        $r->registerToken('--jas-color-text',          'Color', '#f5f2f7', 'Body text color');
        $r->registerToken('--jas-color-heading',       'Color', '#f5f2f7', 'Heading text color');
        $r->registerToken('--jas-color-background',    'Color', '#1b1a1f', 'Main background color');
        $r->registerToken('--jas-color-surface',       'Color', '#24212b', 'Surface / secondary background color');
        $r->registerToken('--jas-color-border',        'Color', 'rgba(255,255,255,0.08)', 'Border and divider color');

        // --- Legacy backward compatibility tokens ---
        $r->registerToken('--jas-primary-color',       'Color', '#b78acb', 'Legacy primary color (use --jas-color-primary)');
        $r->registerToken('--jas-primary-hover',       'Color', '#c79cda', 'Legacy primary hover (use --jas-color-primary-hover)');

        // --- Typography tokens ---
        $r->registerToken('--jas-font-family',  'Typography', "'Inter', sans-serif", 'Body font family');
        $r->registerToken('--jas-font-size-xs', 'Typography', '0.75rem', 'Extra small font size');
        $r->registerToken('--jas-font-size-sm', 'Typography', '0.875rem', 'Small font size');
        $r->registerToken('--jas-font-size-md', 'Typography', '1rem', 'Medium / base font size');
        $r->registerToken('--jas-font-size-lg', 'Typography', '1.125rem', 'Large font size');
        $r->registerToken('--jas-font-size-xl', 'Typography', '1.25rem', 'Extra large font size');
        $r->registerToken('--jas-font-size-2xl', 'Typography', '1.5rem', '2x large font size');

        // --- Spacing tokens ---
        $r->registerToken('--jas-space-xs', 'Spacing', '0.5rem', 'Extra small spacing');
        $r->registerToken('--jas-space-sm', 'Spacing', '1rem', 'Small spacing');
        $r->registerToken('--jas-space-md', 'Spacing', '1.5rem', 'Medium spacing');
        $r->registerToken('--jas-space-lg', 'Spacing', '2rem', 'Large spacing');
        $r->registerToken('--jas-space-xl', 'Spacing', '3rem', 'Extra large spacing');

        // --- Layout tokens ---
        $r->registerToken('--jas-container-width', 'Layout', '1200px', 'Maximum content container width');
        $r->registerToken('--jas-site-layout',     'Layout', 'full-width', 'Site layout mode (boxed or full-width)');

        // --- Border Radius tokens ---
        $r->registerToken('--jas-radius-sm', 'Border Radius', '0.25rem', 'Small border radius');
        $r->registerToken('--jas-radius-md', 'Border Radius', '0.5rem', 'Medium border radius');
        $r->registerToken('--jas-radius-lg', 'Border Radius', '0.75rem', 'Large border radius');
    }

    /**
     * Register theme presets in the ThemePresetManager.
     *
     * Each preset defines token overrides. Unspecified tokens fall
     * through to DesignTokenRegistry defaults and DesignSettingsManager values.
     *
     * Initial presets (foundation only, no admin UI):
     * - default  — Standard Jasanika design
     * - modern   — Cleaner contemporary variant
     * - minimal  — Reduced visual complexity
     *
     * @todo M26 - Theme Presets UI: Admin interface for preset selection.
     */
    private function registerThemePresets(): void
    {
        $this->presetManager->registerPreset(
            'default',
            'Default',
            'Standardni Jasanika design',
            [
                // Default preset has no overrides — uses base token values.
            ]
        );

        $this->presetManager->registerPreset(
            'modern',
            'Modern',
            'Cistsi a soucasnejsi varianta',
            [
                // Modern token overrides will be defined in a future milestone.
            ]
        );

        $this->presetManager->registerPreset(
            'minimal',
            'Minimal',
            'Redukovana vizualni komplexita',
            [
                // Minimal token overrides will be defined in a future milestone.
            ]
        );
    }

    /**
     * Register all frontend UI components in the ComponentRegistry.
     *
     * Each component defines:
     * - slug        — Component identifier used in templates
     * - name        — Human-readable name
     * - description — Component purpose
     * - template    — Template file path relative to theme root
     *
     * Initial components (M25):
     * - button     — Action button with variant support
     * - card       - Content presentation card
     * - alert      — Semantic alert/message banner
     * - form-field — Standardized form field
     *
     * Additional components will be registered in future milestones
     * (M29 - Component Library Expansion).
     */
    private function registerComponents(): void
    {
        $r = $this->componentRegistry;

        $r->registerComponent(
            'button',
            'Button',
            'Action button with primary, secondary, and outline variants.',
            get_template_directory() . '/templates/components/button.php'
        );

        $r->registerComponent(
            'card',
            'Card',
            'Content presentation card for archives, search results, and widgets.',
            get_template_directory() . '/templates/components/card.php'
        );

        $r->registerComponent(
            'alert',
            'Alert',
            'Semantic alert banner with info, success, warning, and error types.',
            get_template_directory() . '/templates/components/alert.php'
        );

        $r->registerComponent(
            'form-field',
            'Form Field',
            'Standardized form field with label and input (text, email, search, textarea, select).',
            get_template_directory() . '/templates/components/form-field.php'
        );
    }
}