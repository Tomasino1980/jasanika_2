<?php

declare(strict_types=1);

namespace Jasanika\Core;

use Jasanika\Admin\SettingsManager;
use Jasanika\Assets\AssetManager;
use Jasanika\Components\ComponentRenderer;
use Jasanika\Design\DesignTokenGenerator;
use Jasanika\Footer\FooterRenderer;
use Jasanika\Header\HeaderRenderer;
use Jasanika\Hero\HeroRenderer;
use Jasanika\Hooks\HookManager;
use Jasanika\Layout\LayoutManager;
use Jasanika\Layout\LayoutRenderer;
use Jasanika\Navigation\NavigationManager;

/**
 * Single frontend rendering entry point.
 *
 * Responsibilities:
 * - Orchestrate page rendering through LayoutManager and LayoutRenderer
 * - Render header via HeaderRenderer
 * - Render hero via HeroRenderer (optional)
 * - Render footer via FooterRenderer
 * - Render content area
 * - Resolve content template based on WordPress hierarchy
 * - Register frontend asset enqueuing
 * - Expose rendering services to templates (orchestration-only)
 *
 * Layout selection is delegated to LayoutManager.
 * Layout rendering is delegated to LayoutRenderer.
 * No direct layout conditionals in this class.
 * No frontend rendering logic in Application.
 *
 * Dependencies:
 * - All builder systems (Header, Footer, Hero)
 * - All rendering services (Layout, Component, Design, Navigation, Site Identity)
 *
 * Used by:
 * - Application (constructor via initThemeRenderer)
 * - Template files (via getInstance() static accessor)
 *
 * Introduced:
 * - M18 (Frontend Foundation & Theme Rendering)
 * - M26 (Header, Footer, Hero delegation)
 * - M28 (Header CSS/JS enqueuing)
 *
 * @todo M30+: Consider replacing the static singleton pattern with
 *       explicit dependency injection in template files. The static
 *       instance was introduced to avoid modifying the WordPress
 *       template hierarchy.
 */
final class ThemeRenderer
{
    private static ?self $instance = null;

    private FrameworkInfo $frameworkInfo;
    private SettingsManager $settingsManager;
    private AssetManager $assetManager;
    private HookManager $hookManager;
    private NavigationManager $navigationManager;
    private SiteIdentityRenderer $siteIdentityRenderer;
    private LayoutRegionRenderer $layoutRegionRenderer;
    private DesignTokenGenerator $designTokenGenerator;
    private LayoutManager $layoutManager;
    private LayoutRenderer $layoutRenderer;
    private ComponentRenderer $componentRenderer;
    private HeaderRenderer $headerRenderer;
    private FooterRenderer $footerRenderer;
    private HeroRenderer $heroRenderer;

    public function __construct(
        FrameworkInfo $frameworkInfo,
        SettingsManager $settingsManager,
        AssetManager $assetManager,
        HookManager $hookManager,
        NavigationManager $navigationManager,
        SiteIdentityRenderer $siteIdentityRenderer,
        LayoutRegionRenderer $layoutRegionRenderer,
        DesignTokenGenerator $designTokenGenerator,
        LayoutManager $layoutManager,
        LayoutRenderer $layoutRenderer,
        ComponentRenderer $componentRenderer,
        HeaderRenderer $headerRenderer,
        FooterRenderer $footerRenderer,
        HeroRenderer $heroRenderer
    ) {
        $this->frameworkInfo = $frameworkInfo;
        $this->settingsManager = $settingsManager;
        $this->assetManager = $assetManager;
        $this->hookManager = $hookManager;
        $this->navigationManager = $navigationManager;
        $this->siteIdentityRenderer = $siteIdentityRenderer;
        $this->layoutRegionRenderer = $layoutRegionRenderer;
        $this->designTokenGenerator = $designTokenGenerator;
        $this->layoutManager = $layoutManager;
        $this->layoutRenderer = $layoutRenderer;
        $this->componentRenderer = $componentRenderer;
        $this->headerRenderer = $headerRenderer;
        $this->footerRenderer = $footerRenderer;
        $this->heroRenderer = $heroRenderer;
    }

    /**
     * Initialize the ThemeRenderer.
     *
     * Stores the instance for template access, registers frontend hooks,
     * and sets up the rendering pipeline. Must be called once during
     * Application bootstrap.
     */
    public function init(): void
    {
        self::$instance = $this;

        // Override WordPress template selection with our layout
        $this->hookManager->addFilter('template_include', [$this, 'overrideTemplate']);

        // Output dynamic CSS custom properties in <head> via DesignTokenGenerator
        $this->hookManager->addAction('wp_head', [$this->designTokenGenerator, 'renderInlineStyles']);

        // Output debug comment in <head> when WP_DEBUG is enabled
        $this->hookManager->addAction('wp_head', [$this->designTokenGenerator, 'renderDebugComment']);

        // Add site layout class to WordPress body class
        $this->hookManager->addFilter('body_class', [$this->designTokenGenerator, 'filterBodyClass']);

        // Output component debug information in <head> when WP_DEBUG is enabled
        $this->hookManager->addAction('wp_head', [$this->componentRenderer->getRegistry(), 'renderDebugComment']);

        // Output Site Builder debug information in <head> when WP_DEBUG is enabled
        $this->hookManager->addAction('wp_head', [$this, 'renderSiteBuilderDebugComment']);

        // Enqueue frontend assets during the wp_enqueue_scripts hook
        $this->hookManager->addAction('wp_enqueue_scripts', [$this, 'enqueueFrontendAssets']);

        // Register navigation menu locations
        $this->navigationManager->registerMenuLocations();
    }

    /**
     * Get the stored ThemeRenderer instance.
     *
     * Provides access from template files without requiring global state
     * or breaking dependency injection patterns. The instance is set once
     * during init() and never replaced.
     */
    public static function getInstance(): ?self
    {
        return self::$instance;
    }

    /**
     * Override the WordPress template with our layout.
     *
     * Called via the template_include filter.
     * Also triggers frontend asset enqueuing.
     */
    public function overrideTemplate(string $template): string
    {
        return get_template_directory() . '/templates/layout.php';
    }

    /**
     * Enqueue frontend CSS and JavaScript.
     *
     * Assets must be registered as definitions in Application constructor
     * via AssetManager::registerStyle() / registerScript().
     * This method only enqueues already-registered handles.
     */
    public function enqueueFrontendAssets(): void
    {
        $this->assetManager->enqueueStyle('jasanika-frontend');
        $this->assetManager->enqueueStyle('jasanika-tokens');
        $this->assetManager->enqueueStyle('jasanika-components');
        $this->assetManager->enqueueStyle('jasanika-header');
        $this->assetManager->enqueueScript('jasanika-frontend');
        $this->assetManager->enqueueScript('jasanika-header');
    }

    /**
     * Get the DesignTokenGenerator instance.
     */
    public function getDesignTokenGenerator(): DesignTokenGenerator
    {
        return $this->designTokenGenerator;
    }

    /**
     * Get the LayoutManager instance.
     */
    public function getLayoutManager(): LayoutManager
    {
        return $this->layoutManager;
    }

    /**
     * Get the LayoutRenderer instance.
     */
    public function getLayoutRenderer(): LayoutRenderer
    {
        return $this->layoutRenderer;
    }

    /**
     * Get the ComponentRenderer instance.
     */
    public function getComponentRenderer(): ComponentRenderer
    {
        return $this->componentRenderer;
    }

    /**
     * Get the HeaderRenderer instance.
     */
    public function getHeaderRenderer(): HeaderRenderer
    {
        return $this->headerRenderer;
    }

    /**
     * Get the FooterRenderer instance.
     */
    public function getFooterRenderer(): FooterRenderer
    {
        return $this->footerRenderer;
    }

    /**
     * Get the HeroRenderer instance.
     */
    public function getHeroRenderer(): HeroRenderer
    {
        return $this->heroRenderer;
    }

    /**
     * Render the header template.
     *
     * Delegates to HeaderRenderer for full configuration-aware header output.
     * Called from templates/layout.php via ThemeRenderer::getInstance().
     */
    public static function renderHeader(): void
    {
        $instance = self::$instance;

        if (!$instance) {
            return;
        }

        include get_template_directory() . '/templates/header.php';
    }

    /**
     * Render the hero section.
     *
     * Delegates to HeroRenderer.
     * Called from templates/layout.php via ThemeRenderer::getInstance().
     */
    public static function renderHero(): void
    {
        $instance = self::$instance;

        if (!$instance) {
            return;
        }

        $instance->heroRenderer->render();
    }

    /**
     * Render the footer template.
     *
     * Delegates to FooterRenderer for full configuration-aware footer output.
     * Called from templates/layout.php via ThemeRenderer::getInstance().
     */
    public static function renderFooter(): void
    {
        $instance = self::$instance;

        if (!$instance) {
            return;
        }

        include get_template_directory() . '/templates/footer.php';
    }

    /**
     * Render the content area.
     *
     * Resolves the correct content template based on WordPress conditional tags
     * (page, single, archive, search, 404) and includes it.
     *
     * Called from templates/layout.php.
     */
    public static function renderContent(): void
    {
        $instance = self::$instance;

        if (!$instance) {
            return;
        }

        $template = $instance->resolveContentTemplate();

        include $template;
    }

    /**
     * Resolve the content template path based on WordPress conditional tags.
     */
    private function resolveContentTemplate(): string
    {
        $dir = get_template_directory() . '/templates/';

        if (is_404()) {
            return $dir . '404.php';
        }

        if (is_search()) {
            return $dir . 'search.php';
        }

        if (is_page()) {
            return $dir . 'page.php';
        }

        if (is_single()) {
            return $dir . 'single.php';
        }

        if (is_archive()) {
            return $dir . 'archive.php';
        }

        // Fallback to the original content template
        return $dir . 'content.php';
    }

    /**
     * Render the layout area.
     *
     * Delegates to LayoutRenderer for complete layout rendering.
     * Layout selection is handled by LayoutManager.
     * No layout conditionals exist in this class.
     *
     * Called from templates/layout.php.
     */
    public static function renderLayout(): void
    {
        $instance = self::$instance;

        if (!$instance) {
            return;
        }

        $instance->layoutRenderer->render();
    }

    /**
     * Get the NavigationManager instance.
     */
    public function getNavigationManager(): NavigationManager
    {
        return $this->navigationManager;
    }

    /**
     * Get the SiteIdentityRenderer instance.
     */
    public function getSiteIdentityRenderer(): SiteIdentityRenderer
    {
        return $this->siteIdentityRenderer;
    }

    /**
     * Get the LayoutRegionRenderer instance.
     */
    public function getLayoutRegionRenderer(): LayoutRegionRenderer
    {
        return $this->layoutRegionRenderer;
    }

    /**
     * Render the sidebar widget region.
     *
     * Delegates to LayoutRegionRenderer for accessible markup.
     * Called from templates/sidebar.php.
     */
    public static function renderSidebar(): void
    {
        $instance = self::$instance;

        if (!$instance) {
            return;
        }

        $instance->layoutRegionRenderer->renderSidebar();
    }

    /**
     * Get the SettingsManager instance.
     */
    public function getSettingsManager(): SettingsManager
    {
        return $this->settingsManager;
    }

    /**
     * Get the FrameworkInfo instance.
     */
    public function getFrameworkInfo(): FrameworkInfo
    {
        return $this->frameworkInfo;
    }

    /**
     * Render Site Builder debug comment when WP_DEBUG is enabled.
     *
     * Outputs the status of Header, Footer, Hero builders and active
     * settings category information.
     */
    public function renderSiteBuilderDebugComment(): void
    {
        if (!defined('WP_DEBUG') || !WP_DEBUG) {
            return;
        }

        $headerEnabled = $this->headerRenderer !== null ? 'Enabled' : 'Disabled';
        $footerEnabled = $this->footerRenderer !== null ? 'Enabled' : 'Disabled';
        $heroStatus = $this->heroRenderer !== null && $this->heroRenderer->getManager()->isEnabled()
            ? 'Enabled'
            : 'Disabled';

        echo '<!--' . "\n";
        echo 'Jasanika Site Builder' . "\n";
        echo 'Header: ' . $headerEnabled . "\n";
        echo 'Footer: ' . $footerEnabled . "\n";
        echo 'Hero: ' . $heroStatus . "\n";
        echo '-->' . "\n";
    }
}