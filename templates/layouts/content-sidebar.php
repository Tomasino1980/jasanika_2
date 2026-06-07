<?php

declare(strict_types=1);

use Jasanika\Core\ThemeRenderer;

/**
 * Content + Sidebar layout template.
 *
 * Renders the main content area with an optional right sidebar.
 * When the primary sidebar has active widgets, a two-column
 * grid layout is displayed. Otherwise content renders full width.
 *
 * Called from LayoutRenderer::renderContentSidebar().
 * No direct layout logic in this template.
 */
$renderer = ThemeRenderer::getInstance();
$layoutRenderer = $renderer ? $renderer->getLayoutRenderer() : null;

if ($layoutRenderer) {
    $layoutRenderer->renderContentSidebar();
}
