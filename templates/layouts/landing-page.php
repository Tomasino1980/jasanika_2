<?php

declare(strict_types=1);

use Jasanika\Core\ThemeRenderer;

/**
 * Landing Page layout template.
 *
 * Minimal layout optimized for marketing pages.
 * No sidebar, no footer widget regions, no unnecessary wrappers.
 * Preserves header and footer navigation.
 *
 * Called from LayoutRenderer::renderLandingPage().
 * No direct layout logic in this template.
 */
$renderer = ThemeRenderer::getInstance();
$layoutRenderer = $renderer ? $renderer->getLayoutRenderer() : null;

if ($layoutRenderer) {
    $layoutRenderer->renderLandingPage();
}
