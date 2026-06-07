<?php
/**
 * Sidebar template.
 *
 * Renders the primary sidebar widget region.
 * Uses LayoutRegionRenderer through ThemeRenderer for consistent markup.
 *
 * Returns early with no output if no widgets are active.
 * No placeholder text is visible to visitors.
 *
 * @package Jasanika
 */

use Jasanika\Core\ThemeRenderer;

$renderer = ThemeRenderer::getInstance();

if (!$renderer) {
    return;
}

$layoutRegionRenderer = $renderer->getLayoutRegionRenderer();

if (!$layoutRegionRenderer || !$layoutRegionRenderer->hasSidebar()) {
    return;
}

$layoutRegionRenderer->renderSidebar();
