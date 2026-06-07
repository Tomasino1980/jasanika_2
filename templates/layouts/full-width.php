<?php

declare(strict_types=1);

use Jasanika\Core\ThemeRenderer;

/**
 * Full Width layout template.
 *
 * Renders content at full width without sidebar references.
 * Used for pages that need maximum content width.
 *
 * Called from LayoutRenderer::renderFullWidth().
 * No direct layout logic in this template.
 */
$renderer = ThemeRenderer::getInstance();
$layoutRenderer = $renderer ? $renderer->getLayoutRenderer() : null;

if ($layoutRenderer) {
    $layoutRenderer->renderFullWidth();
}
