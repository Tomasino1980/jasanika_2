<?php

declare(strict_types=1);

namespace Jasanika\Core;

use Jasanika\Admin\AdminMenu;
use Jasanika\Admin\AdminPage;
use Jasanika\Admin\Fields\FieldFactory;
use Jasanika\Admin\Pages\DashboardPage;
use Jasanika\Admin\Dashboard\AppearanceDashboard;
use Jasanika\Admin\Sections\Section;
use Jasanika\Admin\SettingsManager;
use Jasanika\Admin\SettingsPage;
use Jasanika\Assets\Asset;
use Jasanika\Assets\AssetManager;
use Jasanika\Components\ComponentRegistry;
use Jasanika\Components\ComponentRenderer;
use Jasanika\Config\ConfigRepository;
use Jasanika\Container\Container;
use Jasanika\Design\DesignSettingsManager;
use Jasanika\Design\DesignTokenGenerator;
use Jasanika\Design\DesignTokenRegistry;
use Jasanika\Design\ThemePresetManager;
use Jasanika\Footer\FooterManager;
use Jasanika\Footer\FooterRenderer;
use Jasanika\Header\HeaderLayout;
use Jasanika\Header\HeaderManager;
use Jasanika\Header\HeaderRenderer;
use Jasanika\Header\MobileMenu;
use Jasanika\Hero\HeroManager;
use Jasanika\Hero\HeroRenderer;
use Jasanika\Hooks\HookManager;
use Jasanika\Layout\LayoutManager;
use Jasanika\Layout\LayoutRenderer;
use Jasanika\Media\MediaManager;
use Jasanika\Modules\ModuleManager;
use Jasanika\Navigation\NavigationManager;
use Jasanika\Settings\Setting;
use Jasanika\Settings\ContainerWidthSetting;
use Jasanika\Settings\LogoSetting;
use Jasanika\Settings\PrimaryColorSetting;
use Jasanika\Settings\SettingsRegistry;
use Jasanika\Settings\SiteLayoutSetting;
use Jasanika\Settings\TypographySetting;
use Jasanika\Widgets\WidgetAreaManager;

/**
 * Jasanika Framework Bootstrap
 *
 * Central bootstrap class for the entire framework.
 * All systems are initialized here in dependency order.
 *
 * Architecture:
 *
 * Core
 * ├─ Settings
 * │   └─ SettingsRegistry
 * ├─ Admin
 * │   ├─ AdminMenu
 * │   ├─ SettingsPage
 * │   └─ AppearanceDashboard
 * ├─ Navigation
 * ├─ Site Identity
 * ├─ Widgets
 * ├─ Design
 * │   ├─ DesignTokenRegistry
 * │   ├─ ThemePresetManager
 * │   ├─ DesignSettingsManager
 * │   └─ DesignTokenGenerator
 * ├─ Layout
 * ├─ Components
 * │   ├─ ComponentRegistry
 * │   └─ ComponentRenderer
 * ├─ Header
 * │   ├─ HeaderLayout
 * │   ├─ MobileMenu
 * │   ├─ HeaderManager
 * │   └─ HeaderRenderer
 * ├─ Footer
 * │   ├─ FooterManager
 * │   └─ FooterRenderer
 * └─ Hero
 *     ├─ HeroManager
 *     └─ HeroRenderer
 *
 * Current Version:
 * 0.28
 *
 * @see FrameworkInfo
 * @see ThemeRenderer
 */
final class Application
{
    // ============================================================
    // CORE
    // ============================================================

    private Container $container;
    private ModuleManager $moduleManager;
    private ConfigRepository $configRepository;
    private HookManager $hookManager;
    private AssetManager $assetManager;
    private MediaManager $mediaManager;
    private FrameworkInfo $frameworkInfo;

    // ============================================================
    // SETTINGS
    // ============================================================

    private SettingsRegistry $settingsRegistry;
    private SettingsManager $settingsManager;

    // ============================================================
    // ADMIN
    // ============================================================

    private AdminMenu $adminMenu;

    // ============================================================
    // NAVIGATION
    // ============================================================

    private NavigationManager $navigationManager;

    // ============================================================
    // SITE IDENTITY
    // ============================================================

    private SiteIdentityRenderer $siteIdentityRenderer;

    // ============================================================
    // WIDGETS
    // ============================================================

    private WidgetAreaManager $widgetAreaManager;
    private LayoutRegionRenderer $layoutRegionRenderer;

    // ============================================================
    // DESIGN
    // ============================================================

    private DesignSettingsManager $designSettingsManager;
    private DesignTokenRegistry $tokenRegistry;
    private ThemePresetManager $presetManager;
    private DesignTokenGenerator $designTokenGenerator;

    // ============================================================
    // LAYOUT
    // ============================================================

    private LayoutManager $layoutManager;
    private LayoutRenderer $layoutRenderer;
    private ThemeRenderer $themeRenderer;

    // ============================================================
    // COMPONENTS
    // ============================================================

    private ComponentRegistry $componentRegistry;
    private ComponentRenderer $componentRenderer;

    // ============================================================
    // HEADER
    // ============================================================

    private HeaderLayout $headerLayout;
    private MobileMenu $mobileMenu;
    private HeaderManager $headerManager;
    private HeaderRenderer $headerRenderer;

    // ============================================================
    // FOOTER
    // ============================================================

    private FooterManager $footerManager;
    private FooterRenderer $footerRenderer;

    // ============================================================
    // HERO
    // ============================================================

    private HeroManager $heroManager;
    private HeroRenderer $heroRenderer;

    public function __construct()
    {
        // ============================================================
        // CORE SERVICES
        // ============================================================
        //
        // Depends on:
        // - Nothing (foundation layer)
        //
        // Provides:
        // - Container
        // - ModuleManager
        // - ConfigRepository
        // - HookManager
        // - AssetManager
        // - MediaManager
        // - FrameworkInfo
        //
        // These are framework prerequisites required by all other systems.
        //
        // M1 — Core Foundation
        // M2 — Service Container

        $this->frameworkInfo = new FrameworkInfo(
            'Jasanika 2',
            '0.28'
        );

        $this->container = new Container();
        $this->moduleManager = new ModuleManager();
        $this->configRepository = new ConfigRepository();
        $this->hookManager = new HookManager();
        $this->assetManager = new AssetManager();
        $this->mediaManager = new MediaManager();

        // ============================================================
        // SETTINGS SYSTEM
        // ============================================================
        //
        // Depends on:
        // - Container (via property storage)
        //
        // Provides:
        // - SettingsRegistry
        // - SettingsManager
        // - HeaderLayout
        // - MobileMenu
        //
        // Settings must be registered before SettingsManager is created
        // because SettingsManager reads from the registry at construction.
        //
        // M9-M11 — Settings Foundation
        // M14 — Registry Driven Settings
        // M26 — Site Builder Settings (Logo V2, Header, Footer, Hero, Layout)
        // M27 — Color Scheme Builder settings
        // M28 — Dynamic Header Builder settings

        $this->settingsRegistry = new SettingsRegistry();

        $this->headerLayout = new HeaderLayout();
        $this->mobileMenu = new MobileMenu();
        $this->registerSettings();

        $this->settingsManager = new SettingsManager($this->settingsRegistry);

        // ============================================================
        // ADMIN SYSTEM
        // ============================================================
        //
        // Depends on:
        // - FrameworkInfo
        // - HookManager
        //
        // Provides:
        // - AdminMenu
        // - Dashboard admin page
        //
        // Registers the framework admin menu structure and asset enqueuing
        // hooks. Asset definitions are registered here but enqueued later
        // via WordPress hooks.
        //
        // M7 — Admin Foundation
        // M8 — Admin Menu

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
        $this->registerAdminAssets();
        $this->registerFrontendAssets();

        // ============================================================
        // NAVIGATION SYSTEM
        // ============================================================
        //
        // Depends on:
        // - HookManager
        //
        // Provides:
        // - NavigationManager
        //
        // M20 — Navigation & Site Identity Architecture

        $this->navigationManager = new NavigationManager($this->hookManager);

        // ============================================================
        // SITE IDENTITY SYSTEM
        // ============================================================
        //
        // Depends on:
        // - SettingsManager
        // - MediaManager
        //
        // Provides:
        // - SiteIdentityRenderer
        //
        // M20 — Navigation & Site Identity Architecture

        $this->siteIdentityRenderer = new SiteIdentityRenderer(
            $this->settingsManager,
            $this->mediaManager
        );

        // ============================================================
        // WIDGET SYSTEM
        // ============================================================
        //
        // Depends on:
        // - HookManager
        //
        // Provides:
        // - WidgetAreaManager
        // - LayoutRegionRenderer
        //
        // M21 — Widget Areas & Layout Regions

        $this->widgetAreaManager = new WidgetAreaManager($this->hookManager);
        $this->widgetAreaManager->register();
        $this->layoutRegionRenderer = new LayoutRegionRenderer($this->widgetAreaManager);

        // ============================================================
        // DESIGN TOKEN SYSTEM
        // ============================================================
        //
        // Depends on:
        // - SettingsManager
        //
        // Provides:
        // - DesignSettingsManager
        // - DesignTokenRegistry
        // - ThemePresetManager
        // - DesignTokenGenerator
        //
        // Init order matters:
        // 1. DesignSettingsManager wraps SettingsManager
        // 2. DesignTokenRegistry defines all available tokens
        // 3. ThemePresetManager registers presets
        // 4. DesignTokenGenerator combines settings + tokens + presets
        //
        // M24 — Design Token Engine & Theme Preset Foundation
        // M27 — Color Scheme Builder tokens
        // M28 — Header CSS custom property token

        $this->designSettingsManager = new DesignSettingsManager($this->settingsManager);

        $this->tokenRegistry = new DesignTokenRegistry();
        $this->registerDesignTokens();

        $this->presetManager = new ThemePresetManager();
        $this->registerThemePresets();

        $this->designTokenGenerator = new DesignTokenGenerator(
            $this->designSettingsManager,
            $this->tokenRegistry,
            $this->presetManager
        );

        // ============================================================
        // LAYOUT SYSTEM
        // ============================================================
        //
        // Depends on:
        // - DesignSettingsManager
        // - LayoutRegionRenderer
        //
        // Provides:
        // - LayoutManager
        // - LayoutRenderer
        //
        // M23 — Dynamic Layout System

        $this->layoutManager = new LayoutManager($this->designSettingsManager);
        $this->layoutRenderer = new LayoutRenderer(
            $this->layoutManager,
            $this->layoutRegionRenderer
        );

        // ============================================================
        // COMPONENT SYSTEM
        // ============================================================
        //
        // Depends on:
        // - Nothing (standalone registry)
        //
        // Provides:
        // - ComponentRegistry
        // - ComponentRenderer
        //
        // Components must be registered before ComponentRenderer is created.
        //
        // M25 — Component Styling Framework

        $this->componentRegistry = new ComponentRegistry();
        $this->registerComponents();
        $this->componentRenderer = new ComponentRenderer(
            $this->componentRegistry
        );

        // ============================================================
        // HEADER BUILDER
        // ============================================================
        //
        // Depends on:
        // - SettingsManager
        // - HeaderLayout
        // - SiteIdentityRenderer
        // - NavigationManager
        // - ComponentRenderer
        // - MobileMenu
        //
        // Provides:
        // - HeaderManager
        // - HeaderRenderer
        //
        // HeaderManager reads settings. HeaderRenderer composes all
        // header output from sub-renderers.
        //
        // M26 — Site Builder Foundation (basic header)
        // M28 — Dynamic Header Builder (layouts, sticky, search, CTA, top bar)

        $this->headerManager = new HeaderManager($this->settingsManager, $this->headerLayout);
        $this->headerRenderer = new HeaderRenderer(
            $this->headerManager,
            $this->siteIdentityRenderer,
            $this->navigationManager,
            $this->componentRenderer,
            $this->mobileMenu
        );

        // ============================================================
        // FOOTER BUILDER
        // ============================================================
        //
        // Depends on:
        // - SettingsManager
        // - NavigationManager
        // - LayoutRegionRenderer
        // - LayoutManager
        //
        // Provides:
        // - FooterManager
        // - FooterRenderer
        //
        // M26 — Site Builder Foundation

        $this->footerManager = new FooterManager($this->settingsManager);
        $this->footerRenderer = new FooterRenderer(
            $this->footerManager,
            $this->navigationManager,
            $this->layoutRegionRenderer,
            $this->layoutManager
        );

        // ============================================================
        // HERO BUILDER
        // ============================================================
        //
        // Depends on:
        // - SettingsManager
        // - MediaManager
        // - ComponentRenderer
        //
        // Provides:
        // - HeroManager
        // - HeroRenderer
        //
        // M26 — Site Builder Foundation

        $this->heroManager = new HeroManager($this->settingsManager, $this->mediaManager);
        $this->heroRenderer = new HeroRenderer($this->heroManager, $this->componentRenderer);

        // ============================================================
        // THEME RENDERER
        // ============================================================
        //
        // Depends on:
        // - All preceding services
        //
        // Provides:
        // - ThemeRenderer (frontend rendering entry point)
        //
        // Must be initialized after all builder systems (Header, Footer, Hero).
        // ThemeRenderer owns the frontend rendering pipeline and delegates
        // to specific renderers.
        //
        // M18 — Frontend Foundation & Theme Rendering

        $this->initThemeRenderer();

        // Register asset lifecycle hooks for both admin and frontend.
        // These must be registered after ThemeRenderer is initialized.
        $this->hookManager->addAction('admin_enqueue_scripts', [$this->assetManager, 'registerWordPressAssets']);
        $this->hookManager->addAction('wp_enqueue_scripts', [$this->assetManager, 'registerWordPressAssets']);

        // ============================================================
        // SETTINGS UI
        // ============================================================
        //
        // Depends on:
        // - FrameworkInfo
        // - SettingsRegistry
        // - SettingsManager
        // - AssetManager
        // - ComponentRenderer
        // - ThemePresetManager
        //
        // Provides:
        // - SettingsPage (admin sub-page for all framework settings)
        // - FieldFactory
        //
        // Settings UI uses the Component Framework for consistent rendering.
        // Categories and sections define the tabbed settings organization.
        //
        // M9 — Settings Page Foundation
        // M26 — Site Builder Settings UI (tabbed categories)
        // M27 — Settings UX Framework (search, collapsible panels, presets)

        $fieldFactory = new FieldFactory($this->settingsManager, $this->assetManager);

        $settingsPage = new SettingsPage(
            $this->frameworkInfo,
            $this->settingsRegistry,
            $fieldFactory,
            $this->componentRenderer,
            $this->presetManager
        );

        $this->registerSettingsCategories($settingsPage);

        $this->hookManager->addAction('admin_init', [$settingsPage, 'registerSettings']);

        $settingsSubPage = new AdminPage(
            'Jasanika Settings',
            'jasanika-settings',
            [$settingsPage, 'render']
        );

        $this->adminMenu->registerSubPage($settingsSubPage);

        // ============================================================
        // ADMIN DASHBOARD
        // ============================================================
        //
        // Depends on:
        // - ThemePresetManager
        // - DesignSettingsManager
        // - ComponentRenderer
        // - HeaderManager
        // - FooterManager
        // - HeroManager
        // - LayoutManager
        // - SettingsManager
        //
        // Provides:
        // - AppearanceDashboard (Appearance → Overview)
        //
        // Read-only card-based view of the current theme configuration.
        //
        // M27 — Theme Presets & Settings UX Framework

        $appearanceDashboard = new AppearanceDashboard(
            $this->presetManager,
            $this->designSettingsManager,
            $this->componentRenderer,
            $this->headerManager,
            $this->footerManager,
            $this->heroManager,
            $this->layoutManager,
            $this->settingsManager
        );

        $appearanceSubPage = new AdminPage(
            'Appearance Overview',
            'jasanika-appearance',
            [$appearanceDashboard, 'render']
        );

        $this->adminMenu->registerSubPage($appearanceSubPage);

        // ============================================================
        // CONTAINER REGISTRATION
        // ============================================================
        //
        // Depends on:
        // - All previously initialized services
        //
        // Provides:
        // - DI Container with all services registered
        //
        // Must be the last initialization step so all services exist
        // before being registered in the container.
        //
        // M2 — Service Container

        $this->registerContainerServices();
    }

    // ============================================================
    // SETTINGS REGISTRATION
    // ============================================================

    /**
     * Register all framework settings in the SettingsRegistry.
     *
     * Responsibilities:
     * - Register foundational settings (SiteLayout, Logo, PrimaryColor, Typography, ContainerWidth)
     * - Register Logo V2 settings (desktop, mobile, retina logos with size/position)
     * - Register M28 Dynamic Header Builder settings (layout, heights, CTA, top bar)
     * - Register M26 legacy header settings (height, colors, sticky, search, top bar toggle)
     * - Register M26 footer settings (layout, colors, copyright, menu, social)
     * - Register M26 hero settings (type, content, background, slides)
     * - Register M26 layout control settings (widths, padding, margin)
     * - Register M27 theme preset and color scheme builder settings
     *
     * Dependencies:
     * - Uses $this->settingsRegistry (must be initialized before call)
     * - Uses $this->headerLayout for dynamic layout options
     *
     * Introduced:
     * - M9 (foundational settings)
     * - M26 (Logo V2, Header, Footer, Hero, Layout Control settings)
     * - M27 (Theme Preset, Color Scheme Builder settings)
     * - M28 (Dynamic Header Builder: layout, heights, CTA, top bar)
     */
    private function registerSettings(): void
    {
        $r = $this->settingsRegistry;

        // --- Original settings (M9-M11) ---
        $r->register(new SiteLayoutSetting());
        $r->register(new LogoSetting());
        $r->register(new PrimaryColorSetting());
        $r->register(new TypographySetting());
        $r->register(new ContainerWidthSetting());

        // --- Logo V2 (M26) ---
        $r->register(new Setting('logo_desktop', '', 'Desktop Logo', 'media'));
        $r->register(new Setting('logo_mobile', '', 'Mobile Logo', 'media'));
        $r->register(new Setting('logo_retina', '', 'Retina Logo', 'media'));
        $r->register(new Setting('logo_width', '200px', 'Logo Width', 'text'));
        $r->register(new Setting('logo_height', 'auto', 'Logo Height', 'text'));
        $r->register(new Setting('logo_position', 'left', 'Logo Position', 'select', [
            'left'   => 'Left',
            'center' => 'Center',
            'right'  => 'Right',
        ]));

        // --- M28: Dynamic Header Builder Settings ---
        $headerLayoutOptions = [];
        foreach ($this->headerLayout->getAllLayouts() as $slug => $config) {
            $headerLayoutOptions[$slug] = $config['label'];
        }
        $r->register(new Setting('header_layout', 'logo-left', 'Header Layout', 'select', $headerLayoutOptions));
        $r->register(new Setting('header_height_desktop', '80px', 'Desktop Header Height', 'text'));
        $r->register(new Setting('header_height_tablet', '72px', 'Tablet Header Height', 'text'));
        $r->register(new Setting('header_height_mobile', '64px', 'Mobile Header Height', 'text'));
        $r->register(new Setting('header_show_cta', 'no', 'Show CTA Button', 'select', [
            'yes' => 'Show',
            'no'  => 'Hide',
        ]));
        $r->register(new Setting('header_cta_label', 'Get Started', 'CTA Label', 'text'));
        $r->register(new Setting('header_cta_url', '#', 'CTA URL', 'text'));
        $r->register(new Setting('header_cta_style', 'primary', 'CTA Style', 'select', [
            'primary'   => 'Primary',
            'secondary' => 'Secondary',
            'outline'   => 'Outline',
        ]));
        $r->register(new Setting('header_top_bar_content', '', 'Top Bar Content', 'text'));
        $r->register(new Setting('header_top_bar_bg', '#24212b', 'Top Bar Background', 'color'));
        $r->register(new Setting('header_top_bar_text_color', '#b9b1c4', 'Top Bar Text Color', 'color'));

        // --- Header Settings (M26) ---
        $r->register(new Setting('header_height', '80px', 'Header Height', 'text'));
        $r->register(new Setting('header_bg_color', '#1b1a1f', 'Header Background Color', 'color'));
        $r->register(new Setting('header_text_color', '#f5f2f7', 'Header Text Color', 'color'));
        $r->register(new Setting('header_sticky', 'no', 'Sticky Header', 'select', [
            'yes' => 'Enabled',
            'no'  => 'Disabled',
        ]));
        $r->register(new Setting('header_show_search', 'no', 'Show Search', 'select', [
            'yes' => 'Show',
            'no'  => 'Hide',
        ]));
        $r->register(new Setting('header_show_top_bar', 'no', 'Show Top Bar', 'select', [
            'yes' => 'Show',
            'no'  => 'Hide',
        ]));

        // --- Footer Settings (M26) ---
        $r->register(new Setting('footer_layout', '3', 'Footer Layout', 'select', [
            '1' => '1 Column',
            '2' => '2 Columns',
            '3' => '3 Columns',
            '4' => '4 Columns',
        ]));
        $r->register(new Setting('footer_bg_color', '#1b1a1f', 'Footer Background Color', 'color'));
        $r->register(new Setting('footer_text_color', '#b9b1c4', 'Footer Text Color', 'color'));
        $r->register(new Setting('footer_copyright_text', '', 'Copyright Text', 'text'));
        $r->register(new Setting('footer_show_menu', 'yes', 'Show Footer Menu', 'select', [
            'yes' => 'Show',
            'no'  => 'Hide',
        ]));
        $r->register(new Setting('footer_show_social', 'no', 'Show Social Icons', 'select', [
            'yes' => 'Show',
            'no'  => 'Hide',
        ]));

        // --- Hero Settings (M26) ---
        $r->register(new Setting('hero_enabled', 'no', 'Enable Hero', 'select', [
            'yes' => 'Enabled',
            'no'  => 'Disabled',
        ]));
        $r->register(new Setting('hero_type', 'static', 'Hero Type', 'select', [
            'static' => 'Static',
            'slider' => 'Slider',
        ]));
        $r->register(new Setting('hero_height', '400px', 'Hero Height', 'text'));
        $r->register(new Setting('hero_title', '', 'Hero Title', 'text'));
        $r->register(new Setting('hero_subtitle', '', 'Hero Subtitle', 'text'));
        $r->register(new Setting('hero_background_image', '', 'Background Image', 'media'));
        $r->register(new Setting('hero_overlay_opacity', '0.3', 'Overlay Opacity', 'text'));
        $r->register(new Setting('hero_button_text', '', 'Button Text', 'text'));
        $r->register(new Setting('hero_button_url', '', 'Button URL', 'text'));

        // --- Hero Slides (M26) ---
        for ($i = 1; $i <= 3; $i++) {
            $r->register(new Setting('hero_slide_' . $i . '_title', '', 'Slide ' . $i . ' — Title', 'text'));
            $r->register(new Setting('hero_slide_' . $i . '_subtitle', '', 'Slide ' . $i . ' — Subtitle', 'text'));
            $r->register(new Setting('hero_slide_' . $i . '_image', '', 'Slide ' . $i . ' — Image', 'media'));
            $r->register(new Setting('hero_slide_' . $i . '_button_text', '', 'Slide ' . $i . ' — Button Text', 'text'));
            $r->register(new Setting('hero_slide_' . $i . '_button_url', '', 'Slide ' . $i . ' — Button URL', 'text'));
        }

        // --- Layout Controls (M26) ---
        $r->register(new Setting('layout_header_width', '1200px', 'Header Width', 'text'));
        $r->register(new Setting('layout_content_width', '1200px', 'Content Width', 'text'));
        $r->register(new Setting('layout_sidebar_width', '320px', 'Sidebar Width', 'text'));
        $r->register(new Setting('layout_footer_width', '1200px', 'Footer Width', 'text'));
        $r->register(new Setting('layout_section_padding', '2rem', 'Section Padding', 'text'));
        $r->register(new Setting('layout_section_margin', '1.5rem', 'Section Margin', 'text'));

        // --- M27: Theme Preset ---
        $r->register(new Setting('active_preset', 'default', 'Active Theme Preset', 'select', [
            'default' => 'Default',
            'modern'  => 'Modern',
            'minimal' => 'Minimal',
            'business'=> 'Business',
            'custom'  => 'Custom',
        ]));

        // --- M27: Color Scheme Builder ---
        $r->register(new Setting('secondary_color', '#24212b', 'Secondary Color', 'color'));
        $r->register(new Setting('accent_color', '#f1c95d', 'Accent Color', 'color'));
        $r->register(new Setting('background_color', '#1b1a1f', 'Background Color', 'color'));
        $r->register(new Setting('surface_color', '#24212b', 'Surface Color', 'color'));
        $r->register(new Setting('text_color', '#f5f2f7', 'Text Color', 'color'));
        $r->register(new Setting('heading_color', '#f5f2f7', 'Heading Color', 'color'));
        $r->register(new Setting('border_color', 'rgba(255,255,255,0.08)', 'Border Color', 'text'));
    }

    // ============================================================
    // SETTINGS CATEGORIES & SECTIONS
    // ============================================================

    /**
     * Register settings categories and sections on the SettingsPage.
     *
     * Responsibilities:
     * - Organize all settings into tabbed categories (General, Appearance, Content, Marketing, Advanced)
     * - Define Section groups with field references within each category
     * - Configure section labels, descriptions, and field membership
     *
     * Dependencies:
     * - Uses $this->settingsRegistry (registered settings must exist)
     *
     * Used by:
     * - SettingsPage (section rendering in admin)
     *
     * Introduced:
     * - M26 (tabbed categories with sections)
     * - M27 (Presets, Color Scheme, Typography sections)
     * - M28 (CTA Button, Top Bar sections)
     */
    private function registerSettingsCategories(SettingsPage $settingsPage): void
    {
        // --- General Category ---
        $settingsPage->registerSection(new Section(
            'general_site_identity',
            'Site Identity',
            'Site title, tagline, and basic site information.',
            'general',
            ['site_layout', 'container_width']
        ));

        $settingsPage->registerSection(new Section(
            'general_logo',
            'Logo',
            'Desktop, mobile, and retina logo variants with size and position controls.',
            'general',
            ['logo', 'logo_desktop', 'logo_mobile', 'logo_retina', 'logo_width', 'logo_height', 'logo_position']
        ));

        // --- Appearance Category ---
        $settingsPage->registerSection(new Section(
            'appearance_presets',
            'Presets',
            'Select a theme preset or switch to Custom mode for full control.',
            'appearance',
            ['active_preset']
        ));

        $settingsPage->registerSection(new Section(
            'appearance_color_scheme',
            'Color Scheme',
            'Primary, secondary, accent, background, text, and border colors.',
            'appearance',
            ['primary_color', 'secondary_color', 'accent_color', 'background_color', 'surface_color', 'text_color', 'heading_color', 'border_color']
        ));

        $settingsPage->registerSection(new Section(
            'appearance_typography',
            'Typography',
            'Font family selection and heading font configuration.',
            'appearance',
            ['typography']
        ));

        $settingsPage->registerSection(new Section(
            'appearance_header',
            'Header',
            'Header layout, height, colors, and feature toggles.',
            'appearance',
            [
                'header_layout', 'header_height', 'header_height_desktop', 'header_height_tablet', 'header_height_mobile',
                'header_bg_color', 'header_text_color', 'header_sticky', 'header_show_search', 'header_show_cta',
            ]
        ));

        $settingsPage->registerSection(new Section(
            'appearance_header_cta',
            'CTA Button',
            'Call-to-action button configuration.',
            'appearance',
            [
                'header_cta_label', 'header_cta_url', 'header_cta_style',
            ]
        ));

        $settingsPage->registerSection(new Section(
            'appearance_header_top_bar',
            'Top Bar',
            'Top bar content, colors, and visibility.',
            'appearance',
            [
                'header_show_top_bar', 'header_top_bar_content', 'header_top_bar_bg', 'header_top_bar_text_color',
            ]
        ));

        $settingsPage->registerSection(new Section(
            'appearance_hero',
            'Hero',
            'Hero section configuration including type, content, background, and slides.',
            'appearance',
            [
                'hero_enabled', 'hero_type', 'hero_height', 'hero_title', 'hero_subtitle',
                'hero_background_image', 'hero_overlay_opacity', 'hero_button_text', 'hero_button_url',
                'hero_slide_1_title', 'hero_slide_1_subtitle', 'hero_slide_1_image',
                'hero_slide_1_button_text', 'hero_slide_1_button_url',
                'hero_slide_2_title', 'hero_slide_2_subtitle', 'hero_slide_2_image',
                'hero_slide_2_button_text', 'hero_slide_2_button_url',
                'hero_slide_3_title', 'hero_slide_3_subtitle', 'hero_slide_3_image',
                'hero_slide_3_button_text', 'hero_slide_3_button_url',
            ]
        ));

        $settingsPage->registerSection(new Section(
            'appearance_footer',
            'Footer',
            'Footer layout, colors, copyright text, and feature toggles.',
            'appearance',
            ['footer_layout', 'footer_bg_color', 'footer_text_color', 'footer_copyright_text', 'footer_show_menu', 'footer_show_social']
        ));

        $settingsPage->registerSection(new Section(
            'appearance_layout',
            'Layout',
            'Width controls for header, content, sidebar, and footer regions.',
            'appearance',
            ['layout_header_width', 'layout_content_width', 'layout_sidebar_width', 'layout_footer_width', 'layout_section_padding', 'layout_section_margin']
        ));

        // --- Content Category (placeholder for future milestones) ---
        $settingsPage->registerSection(new Section(
            'content_blog',
            'Blog',
            'Blog and archive settings. (Coming in a future milestone)',
            'content',
            []
        ));

        // --- Marketing Category (placeholder) ---
        $settingsPage->registerSection(new Section(
            'marketing_social',
            'Social Media',
            'Social media links and sharing settings. (Coming in a future milestone)',
            'marketing',
            []
        ));

        // --- Advanced Category (placeholder) ---
        $settingsPage->registerSection(new Section(
            'advanced_development',
            'Development',
            'Performance, caching, and developer settings. (Coming in a future milestone)',
            'advanced',
            []
        ));
    }

    // ============================================================
    // ASSET REGISTRATION
    // ============================================================

    /**
     * Register the media-field.js asset definition.
     *
     * Responsibilities:
     * - Define the media-field.js script for WordPress Media Library integration
     * - Script is enqueued by MediaField when rendering on settings pages
     *
     * Dependencies:
     * - Uses $this->assetManager (must be initialized)
     *
     * Used by:
     * - MediaField (admin field rendering)
     *
     * Introduced:
     * - M17 (Media Infrastructure)
     */
    private function registerMediaFieldAsset(): void
    {
        $script = new Asset(
            'jasanika-media-field',
            get_template_directory_uri() . '/assets/admin/js/media-field.js',
            '0.28',
            ['jquery'],
            'all',
            true
        );

        $this->assetManager->registerScript($script);
    }

    /**
     * Register admin CSS assets for settings UI styling.
     *
     * Responsibilities:
     * - Register admin.css (settings UI, preset cards, collapsible panels, search, dashboard)
     * - Enqueue admin CSS and component CSS on Jasanika admin pages
     *
     * Dependencies:
     * - Uses $this->assetManager (must be initialized)
     * - Uses $this->hookManager (for admin_enqueue_scripts action)
     *
     * Introduced:
     * - M9 (basic admin styles)
     * - M27 (admin.css with settings UX framework styles)
     */
    private function registerAdminAssets(): void
    {
        $adminCss = new Asset(
            'jasanika-admin',
            get_template_directory_uri() . '/assets/css/admin.css',
            '0.28'
        );

        $this->assetManager->registerStyle($adminCss);

        // Enqueue admin CSS on Jasanika admin pages
        $this->hookManager->addAction('admin_enqueue_scripts', function (): void {
            $screen = get_current_screen();

            if ($screen && str_starts_with($screen->id, 'jasanika')) {
                $this->assetManager->enqueueStyle('jasanika-admin');
                $this->assetManager->enqueueStyle('jasanika-components');
            }
        });
    }

    /**
     * Register frontend CSS and JavaScript asset definitions.
     *
     * Responsibilities:
     * - Register frontend.css (core frontend styles)
     * - Register tokens.css (CSS custom properties)
     * - Register components.css (component styles)
     * - Register header.css (header and navigation styles)
     * - Register header.js (sticky header, search overlay, mobile nav)
     * - Register frontend.js (general frontend behavior)
     *
     * Dependencies:
     * - Uses $this->assetManager (must be initialized)
     *
     * Used by:
     * - ThemeRenderer::enqueueFrontendAssets() (wp_enqueue_scripts hook)
     *
     * Introduced:
     * - M18 (frontend.css, frontend.js)
     * - M24 (tokens.css)
     * - M25 (components.css)
     * - M28 (header.css, header.js)
     */
    private function registerFrontendAssets(): void
    {
        $style = new Asset(
            'jasanika-frontend',
            get_template_directory_uri() . '/assets/css/frontend.css',
            '0.28'
        );

        $this->assetManager->registerStyle($style);

        $tokens = new Asset(
            'jasanika-tokens',
            get_template_directory_uri() . '/assets/css/tokens.css',
            '0.28'
        );

        $this->assetManager->registerStyle($tokens);

        $components = new Asset(
            'jasanika-components',
            get_template_directory_uri() . '/assets/css/components.css',
            '0.28'
        );

        $this->assetManager->registerStyle($components);

        // M28: Header CSS
        $headerStyle = new Asset(
            'jasanika-header',
            get_template_directory_uri() . '/assets/css/header.css',
            '0.28'
        );

        $this->assetManager->registerStyle($headerStyle);

        // M28: Header JS
        $headerScript = new Asset(
            'jasanika-header',
            get_template_directory_uri() . '/assets/js/header.js',
            '0.28',
            [],
            'all',
            true
        );

        $this->assetManager->registerScript($headerScript);

        $script = new Asset(
            'jasanika-frontend',
            get_template_directory_uri() . '/assets/js/frontend.js',
            '0.28',
            [],
            'all',
            true
        );

        $this->assetManager->registerScript($script);
    }

    // ============================================================
    // THEME RENDERER INITIALIZATION
    // ============================================================

    /**
     * Initialize the ThemeRenderer for frontend rendering.
     *
     * Responsibilities:
     * - Create ThemeRenderer with all required service dependencies
     * - Call init() to register WordPress hooks and set the template pipeline
     *
     * Dependencies:
     * - All builder systems must be initialized first (Header, Footer, Hero)
     * - All rendering services must exist (Layout, Component, Design, Navigation)
     *
     * Used by:
     * - Application constructor (orchestration)
     *
     * Introduced:
     * - M18 (Frontend Foundation & Theme Rendering)
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
            $this->componentRenderer,
            $this->headerRenderer,
            $this->footerRenderer,
            $this->heroRenderer
        );

        $this->themeRenderer->init();
    }

    // ============================================================
    // CONTAINER SERVICES REGISTRATION
    // ============================================================

    /**
     * Register all framework services in the DI Container.
     *
     * Responsibilities:
     * - Map every service class to its corresponding property on this instance
     * - Register lazy-loading closures that return the already-initialized instance
     *
     * Dependencies:
     * - All services must be initialized before this method is called
     *
     * Used by:
     * - External code that resolves services via Container::get()
     *
     * Introduced:
     * - M2 (Service Container)
     *
     * @todo M30+: Consider splitting container registration into per-module
     *       service providers for better separation of concerns.
     */
    private function registerContainerServices(): void
    {
        $services = [
            ModuleManager::class       => 'moduleManager',
            ConfigRepository::class    => 'configRepository',
            HookManager::class         => 'hookManager',
            AssetManager::class        => 'assetManager',
            MediaManager::class        => 'mediaManager',
            SettingsManager::class     => 'settingsManager',
            AdminMenu::class           => 'adminMenu',
            SettingsRegistry::class    => 'settingsRegistry',
            FrameworkInfo::class       => 'frameworkInfo',
            NavigationManager::class   => 'navigationManager',
            SiteIdentityRenderer::class => 'siteIdentityRenderer',
            ThemeRenderer::class       => 'themeRenderer',
            WidgetAreaManager::class   => 'widgetAreaManager',
            LayoutRegionRenderer::class => 'layoutRegionRenderer',
            DesignSettingsManager::class => 'designSettingsManager',
            DesignTokenGenerator::class => 'designTokenGenerator',
            LayoutManager::class       => 'layoutManager',
            LayoutRenderer::class      => 'layoutRenderer',
            DesignTokenRegistry::class => 'tokenRegistry',
            ThemePresetManager::class  => 'presetManager',
            ComponentRegistry::class   => 'componentRegistry',
            ComponentRenderer::class   => 'componentRenderer',
            HeaderManager::class       => 'headerManager',
            HeaderRenderer::class      => 'headerRenderer',
            HeaderLayout::class        => 'headerLayout',
            MobileMenu::class          => 'mobileMenu',
            FooterManager::class       => 'footerManager',
            FooterRenderer::class      => 'footerRenderer',
            HeroManager::class         => 'heroManager',
            HeroRenderer::class        => 'heroRenderer',
        ];

        foreach ($services as $class => $property) {
            $this->container->register(
                $class,
                function (Container $container) use ($property): object {
                    return $this->$property;
                }
            );
        }
    }

    // ============================================================
    // DESIGN TOKEN REGISTRATION
    // ============================================================

    /**
     * Register all design tokens in the DesignTokenRegistry.
     *
     * Responsibilities:
     * - Color tokens (primary, secondary, accent, background, surface, text, heading, border)
     * - Legacy backward compatibility tokens (--jas-primary-color, --jas-primary-hover)
     * - Typography tokens (font family, font size scale xs-2xl)
     * - Spacing tokens (xs-xl rhythm scale)
     * - Layout tokens (container, header, content, sidebar, footer widths, section spacing)
     * - Border Radius tokens (sm, md, lg)
     *
     * Dependencies:
     * - Uses $this->tokenRegistry (must be initialized before call)
     *
     * Used by:
     * - DesignTokenGenerator (preset-aware token generation)
     *
     * Introduced:
     * - M24 (Design Token Engine)
     * - M26 (layout control tokens)
     * - M27 (secondary, accent, background, surface, text, heading, border, heading font tokens)
     * - M28 (--jas-header-bg token)
     */
    private function registerDesignTokens(): void
    {
        $r = $this->tokenRegistry;

        // --- Color tokens ---
        $r->registerToken('--jas-color-primary',       'Color', '#b78acb', 'Primary brand color');
        $r->registerToken('--jas-color-primary-hover', 'Color', '#c79cda', 'Primary brand color hover state');
        $r->registerToken('--jas-color-secondary',     'Color', '#24212b', 'Secondary brand color');
        $r->registerToken('--jas-color-accent',        'Color', '#f1c95d', 'Accent brand color');
        $r->registerToken('--jas-color-text',          'Color', '#f5f2f7', 'Body text color');
        $r->registerToken('--jas-color-heading',       'Color', '#f5f2f7', 'Heading text color');
        $r->registerToken('--jas-color-background',    'Color', '#1b1a1f', 'Main background color');
        $r->registerToken('--jas-color-surface',       'Color', '#24212b', 'Surface / secondary background color');
        $r->registerToken('--jas-color-border',        'Color', 'rgba(255,255,255,0.08)', 'Border and divider color');

        // --- Legacy backward compatibility tokens ---
        $r->registerToken('--jas-primary-color',       'Color', '#b78acb', 'Legacy primary color (use --jas-color-primary)');
        $r->registerToken('--jas-primary-hover',       'Color', '#c79cda', 'Legacy primary hover (use --jas-color-primary-hover)');

        // --- Typography tokens ---
        $r->registerToken('--jas-font-family',     'Typography', "'Inter', sans-serif", 'Body font family');
        $r->registerToken('--jas-font-family-heading', 'Typography', "'Playfair Display', serif", 'Heading font family');
        $r->registerToken('--jas-font-size-xs',    'Typography', '0.75rem', 'Extra small font size');
        $r->registerToken('--jas-font-size-sm',    'Typography', '0.875rem', 'Small font size');
        $r->registerToken('--jas-font-size-md',    'Typography', '1rem', 'Medium / base font size');
        $r->registerToken('--jas-font-size-lg',    'Typography', '1.125rem', 'Large font size');
        $r->registerToken('--jas-font-size-xl',    'Typography', '1.25rem', 'Extra large font size');
        $r->registerToken('--jas-font-size-2xl',   'Typography', '1.5rem', '2x large font size');

        // --- Spacing tokens ---
        $r->registerToken('--jas-space-xs', 'Spacing', '0.5rem', 'Extra small spacing');
        $r->registerToken('--jas-space-sm', 'Spacing', '1rem', 'Small spacing');
        $r->registerToken('--jas-space-md', 'Spacing', '1.5rem', 'Medium spacing');
        $r->registerToken('--jas-space-lg', 'Spacing', '2rem', 'Large spacing');
        $r->registerToken('--jas-space-xl', 'Spacing', '3rem', 'Extra large spacing');

        // --- Layout tokens ---
        $r->registerToken('--jas-container-width', 'Layout', '1200px', 'Maximum content container width');
        $r->registerToken('--jas-site-layout',     'Layout', 'full-width', 'Site layout mode (boxed or full-width)');
        $r->registerToken('--jas-header-width',    'Layout', '1200px', 'Header content width');
        $r->registerToken('--jas-content-width',   'Layout', '1200px', 'Main content area width');
        $r->registerToken('--jas-sidebar-width',   'Layout', '320px', 'Sidebar column width');
        $r->registerToken('--jas-footer-width',    'Layout', '1200px', 'Footer content width');
        $r->registerToken('--jas-section-padding', 'Layout', '2rem', 'Section padding');
        $r->registerToken('--jas-section-margin',  'Layout', '1.5rem', 'Section margin');

        // --- Border Radius tokens ---
        $r->registerToken('--jas-radius-sm', 'Border Radius', '0.25rem', 'Small border radius');
        $r->registerToken('--jas-radius-md', 'Border Radius', '0.5rem', 'Medium border radius');
        $r->registerToken('--jas-radius-lg', 'Border Radius', '0.75rem', 'Large border radius');
    }

    // ============================================================
    // THEME PRESETS REGISTRATION
    // ============================================================

    /**
     * Register theme presets in the ThemePresetManager.
     *
     * Responsibilities:
     * - Register all available theme presets (Default, Modern, Minimal, Business, Custom)
     * - Each preset has a slug, label, description, and optional configuration overrides
     *
     * Dependencies:
     * - Uses $this->presetManager (must be initialized before call)
     *
     * Used by:
     * - ThemePresetManager (preset resolution and active preset management)
     * - SettingsPage (preset selection UI)
     * - DesignTokenGenerator (preset-aware token generation)
     *
     * Introduced:
     * - M24 (Default, Modern, Minimal presets)
     * - M27 (Business, Custom presets)
     */
    private function registerThemePresets(): void
    {
        $this->presetManager->registerPreset(
            'default',
            'Default',
            'Standardni Jasanika design',
            []
        );

        $this->presetManager->registerPreset(
            'modern',
            'Modern',
            'Cistsi a soucasnejsi varianta',
            []
        );

        $this->presetManager->registerPreset(
            'minimal',
            'Minimal',
            'Redukovana vizualni komplexita',
            []
        );

        // --- M27 Presets ---
        $this->presetManager->registerPreset(
            'business',
            'Business',
            'Professionalni vzhled pro firemni prezentaci',
            []
        );

        $this->presetManager->registerPreset(
            'custom',
            'Custom',
            'Uplna kontrola nad kazdym detailem designu',
            []
        );
    }

    // ============================================================
    // COMPONENTS REGISTRATION
    // ============================================================

    /**
     * Register all frontend UI components in the ComponentRegistry.
     *
     * Responsibilities:
     * - Register the Button component (primary, secondary, outline variants)
     * - Register the Card component (header, body, footer sections)
     * - Register the Alert component (info, success, warning, error types)
     * - Register the Form Field component (text, email, search, textarea, select)
     *
     * Dependencies:
     * - Uses $this->componentRegistry (must be initialized before call)
     *
     * Used by:
     * - ComponentRenderer (component rendering in templates)
     * - SettingsPage (component-driven settings UI)
     * - HeaderRenderer (CTA button)
     * - HeroRenderer (hero button)
     *
     * Introduced:
     * - M25 (Component Styling Framework)
     *
     * @todo M30+: Consider extracting component registration into a dedicated
     *       ComponentServiceProvider when the number of components grows.
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

    // ============================================================
    // BOOT & PUBLIC ACCESSORS
    // ============================================================

    /**
     * Boot the framework.
     *
     * Currently reserved for post-construction initialization.
     * No logic is executed here yet.
     *
     * @todo M30+: Consider moving hook registration and post-init
     *       logic here for clearer separation between construction
     *       and boot phases.
     */
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

    public function getHeaderManager(): HeaderManager
    {
        return $this->headerManager;
    }

    public function getHeaderRenderer(): HeaderRenderer
    {
        return $this->headerRenderer;
    }

    public function getFooterManager(): FooterManager
    {
        return $this->footerManager;
    }

    public function getFooterRenderer(): FooterRenderer
    {
        return $this->footerRenderer;
    }

    public function getHeroManager(): HeroManager
    {
        return $this->heroManager;
    }

    public function getHeroRenderer(): HeroRenderer
    {
        return $this->heroRenderer;
    }
}
