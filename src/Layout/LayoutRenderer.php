<?php

declare(strict_types=1);

namespace Jasanika\Layout;

use Jasanika\Core\LayoutRegionRenderer;
use Jasanika\Core\ThemeRenderer;

/**
 * Centralized layout rendering.
 *
 * Renders layout structures by delegating to dedicated layout template files.
 * Keeps layout logic centralized and prevents layout conditionals inside
 * individual templates.
 *
 * Responsibilities:
 * - Render content-sidebar layout (content + sidebar)
 * - Render full-width layout (content only)
 * - Render landing-page layout (minimal, no sidebar, no widgets)
 * - Output layout debug information when WP_DEBUG is enabled
 *
 * Flow:
 * LayoutManager → LayoutRenderer → Layout Templates
 */
final class LayoutRenderer
{
    private LayoutManager $layoutManager;
    private LayoutRegionRenderer $layoutRegionRenderer;

    public function __construct(
        LayoutManager $layoutManager,
        LayoutRegionRenderer $layoutRegionRenderer
    ) {
        $this->layoutManager = $layoutManager;
        $this->layoutRegionRenderer = $layoutRegionRenderer;
    }

    /**
     * Render the active layout.
     *
     * Resolves the layout via LayoutManager and includes the
     * corresponding template file. Outputs debug information
     * when WP_DEBUG is enabled.
     */
    public function render(): void
    {
        $layout = $this->layoutManager->getActiveLayout();

        $this->renderDebugComment($layout);

        $template = get_template_directory() . '/templates/layouts/' . $layout . '.php';

        if (file_exists($template)) {
            include $template;
        }
    }

    /**
     * Render the content-sidebar layout.
     *
     * Content area with right sidebar when sidebar widgets are active.
     * Falls back to content-only when no sidebar widgets exist.
     */
    public function renderContentSidebar(): void
    {
        echo '<div class="jas-layout jas-layout--sidebar">' . "\n";

        if ($this->layoutRegionRenderer->hasSidebar()) {
            echo '  <div class="jas-content-with-sidebar">' . "\n";
            echo '    <main class="jas-content-main jas-content-main--with-sidebar">' . "\n";
            ThemeRenderer::renderContent();
            echo '    </main>' . "\n";
            $this->layoutRegionRenderer->renderSidebar();
            echo '  </div>' . "\n";
        } else {
            echo '  <div class="jas-content-full">' . "\n";
            ThemeRenderer::renderContent();
            echo '  </div>' . "\n";
        }

        echo '</div>' . "\n";
    }

    /**
     * Render the full-width layout.
     *
     * Content only, no sidebar references.
     * Used for pages that need maximum content width.
     */
    public function renderFullWidth(): void
    {
        echo '<div class="jas-layout jas-layout--full-width">' . "\n";
        echo '  <div class="jas-content-full">' . "\n";
        ThemeRenderer::renderContent();
        echo '  </div>' . "\n";
        echo '</div>' . "\n";
    }

    /**
     * Render the landing-page layout.
     *
     * Minimal layout optimized for marketing pages.
     * No sidebar, no footer widget regions.
     * Preserves header and footer navigation.
     */
    public function renderLandingPage(): void
    {
        echo '<div class="jas-layout jas-layout--landing">' . "\n";
        echo '  <div class="jas-content-full jas-content-full--landing">' . "\n";
        ThemeRenderer::renderContent();
        echo '  </div>' . "\n";
        echo '</div>' . "\n";
    }

    /**
     * Output layout debug information as an HTML comment.
     *
     * Only visible when WP_DEBUG is enabled.
     * Never visible in production environments.
     */
    private function renderDebugComment(string $layout): void
    {
        if (!defined('WP_DEBUG') || !WP_DEBUG) {
            return;
        }

        echo '<!--' . "\n";
        echo 'Jasanika Layout' . "\n";
        echo 'Active Layout: ' . esc_html($layout) . "\n";
        echo '-->' . "\n";
    }

    /**
     * Get the LayoutManager instance.
     */
    public function getLayoutManager(): LayoutManager
    {
        return $this->layoutManager;
    }
}