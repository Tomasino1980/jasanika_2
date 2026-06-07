<?php

declare(strict_types=1);

namespace Jasanika\Core;

use Jasanika\Admin\SettingsManager;
use Jasanika\Assets\AssetManager;
use Jasanika\Design\DesignTokenGenerator;
use Jasanika\Hooks\HookManager;
use Jasanika\Layout\LayoutManager;
use Jasanika\Layout\LayoutRenderer;
use Jasanika\Navigation\NavigationManager;

/**
 * Single frontend rendering entry point.
 *
 * Responsibilities:
 * - Orchestrate page rendering through LayoutManager and LayoutRenderer
 * - Render header with site branding and navigation
 * - Render footer with navigation and copyright
 * - Resolve content template based on WordPress hierarchy
 * - Register frontend asset enqueuing
 * - Expose rendering services to templates (orchestration-only)
 *
 * Layout selection is delegated to LayoutManager.
 * Layout rendering is delegated to LayoutRenderer.
 * No direct layout conditionals in this class.
 * No frontend rendering logic in Application.
 *
 * @todo Template Context Refactor: Current template resolution uses
 *       WordPress conditional tags inside ThemeRenderer. Extract into
 *       a dedicated TemplateResolver service when frontend architecture
 *       stabilizes. Do NOT remove static instance pattern yet —
 *       frontend is still evolving.
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
        LayoutRenderer $layoutRenderer
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
        $this->assetManager->enqueueScript('jasanika-frontend');
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
     * Render the header template.
     *
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
     * Render the footer template.
     *
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
     *
     * @todo Template Context Refactor: Extract template resolution into a
     *       dedicated TemplateResolver service when frontend architecture stabilizes.
     *       The current static access pattern is intentional to prevent
     *       architectural churn while frontend is still evolving.
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
     *
     * Maps WordPress template hierarchy to framework template files.
     * Falls back to content.php for uncategorized or future template types.
     *
     * @todo Template Context Refactor: Move to dedicated TemplateResolver
     *       when implementing ThemeRenderer refactor milestone.
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
}