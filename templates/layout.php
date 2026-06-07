<?php

declare(strict_types=1);

use Jasanika\Core\ThemeRenderer;

/**
 * Main layout template.
 *
 * Orchestrates the complete page structure through ThemeRenderer.
 * Layout selection and rendering is delegated to LayoutManager and LayoutRenderer.
 * No direct layout conditionals in this template.
 */

ThemeRenderer::renderHeader();
echo '<div class="jas-site-wrapper">' . "\n";
echo '<div class="jas-content">' . "\n";
echo '  <div class="jas-container">' . "\n";
ThemeRenderer::renderLayout();
echo '  </div>' . "\n";
echo '</div>' . "\n";
ThemeRenderer::renderFooter();
echo '</div>' . "\n";
