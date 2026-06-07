<?php

declare(strict_types=1);

use Jasanika\Core\ThemeRenderer;

/**
 * Main layout template.
 *
 * Assembles the complete page structure through ThemeRenderer.
 * Content area supports optional sidebar layout when widgets are active.
 * No direct template includes outside ThemeRenderer.
 */

ThemeRenderer::renderHeader();
echo '<div class="jas-site-wrapper">' . "\n";
echo '<div class="jas-content">' . "\n";
echo '  <div class="jas-container">' . "\n";
ThemeRenderer::renderContentArea();
echo '  </div>' . "\n";
echo '</div>' . "\n";
ThemeRenderer::renderFooter();
echo '</div>' . "\n";
