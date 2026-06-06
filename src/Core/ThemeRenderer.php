<?php

declare(strict_types=1);

namespace Jasanika\Core;

use Jasanika\Admin\SettingsManager;
use Jasanika\Assets\AssetManager;
use Jasanika\Hooks\HookManager;

/**
 * Single frontend rendering entry point.
 *
 * Responsibilities:
 * - Render complete page layout
 * - Render header
 * - Render footer
 * - Render content area
 * - Integrate frontend settings (container width, typography)
 * - Register frontend asset enqueuing
 *
 * No direct template includes outside this class.
 * No frontend rendering logic in Application.
 */
final class ThemeRenderer
{
    private static ?self $instance = null;

    private FrameworkInfo $frameworkInfo;
    private SettingsManager $settingsManager;
    private AssetManager $assetManager;
    private HookManager $hookManager;

    public function __construct(
        FrameworkInfo $frameworkInfo,
        SettingsManager $settingsManager,
        AssetManager $assetManager,
        HookManager $hookManager
    ) {
        $this->frameworkInfo = $frameworkInfo;
        $this->settingsManager = $settingsManager;
        $this->assetManager = $assetManager;
        $this->hookManager = $hookManager;
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

        // Output dynamic CSS custom properties in <head>
        $this->hookManager->addAction('wp_head', [$this, 'renderInlineStyles']);

        // Enqueue frontend assets during the wp_enqueue_scripts hook
        $this->hookManager->addAction('wp_enqueue_scripts', [$this, 'enqueueFrontendAssets']);
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
        $this->assetManager->enqueueScript('jasanika-frontend');
    }

    /**
     * Render inline CSS custom properties in <head>.
     *
     * Reads current settings from SettingsManager and generates
     * :root-level CSS custom properties for frontend consumption.
     */
    public function renderInlineStyles(): void
    {
        $containerWidth = $this->getContainerWidthValue();
        $fontFamily = $this->getFontFamilyValue();

        echo '<style id="jasanika-custom-properties">' . "\n";
        echo ':root {' . "\n";
        echo '  --jas-container-width: ' . esc_attr($containerWidth) . ';' . "\n";
        echo '  --jas-font-family: ' . esc_attr($fontFamily) . ';' . "\n";
        echo '}' . "\n";
        echo '</style>' . "\n";
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

        $frameworkInfo = $instance->frameworkInfo;

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

        $frameworkInfo = $instance->frameworkInfo;

        include get_template_directory() . '/templates/footer.php';
    }

    /**
     * Render the content area.
     *
     * Uses the standard WordPress Loop.
     * Called from templates/layout.php.
     */
    public static function renderContent(): void
    {
        $instance = self::$instance;

        if (!$instance) {
            return;
        }

        $frameworkInfo = $instance->frameworkInfo;

        include get_template_directory() . '/templates/content.php';
    }

    /**
     * Get the container width as a CSS length value.
     */
    private function getContainerWidthValue(): string
    {
        $width = $this->settingsManager->get('container_width');

        if (empty($width)) {
            $width = '1200';
        }

        return $width . 'px';
    }

    /**
     * Get the font-family CSS value based on the typography setting.
     */
    private function getFontFamilyValue(): string
    {
        $typography = $this->settingsManager->get('typography');

        return match ($typography) {
            'inter'   => '"Inter", sans-serif',
            'roboto'  => '"Roboto", sans-serif',
            default   => 'system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
        };
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