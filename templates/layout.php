<?php

declare(strict_types=1);

use Jasanika\Core\ThemeRenderer;

/**
 * Main layout template.
 *
 * Orchestrates the complete page structure through ThemeRenderer.
 * Layout selection and rendering is delegated to LayoutManager and LayoutRenderer.
 *
 * Order:
 * 1. Header (HeaderRenderer)
 * 2. Hero (HeroRenderer — optional, configurable)
 * 3. Content (LayoutRenderer)
 * 4. Footer (FooterRenderer)
 */

ThemeRenderer::renderHeader();
echo '<div class="jas-site-wrapper">' . "\n";

// Hero section (optional, rendered only when enabled in settings)
ThemeRenderer::renderHero();

echo '<div class="jas-content">' . "\n";
echo '  <div class="jas-container">' . "\n";
ThemeRenderer::renderLayout();
echo '  </div>' . "\n";
echo '</div>' . "\n";

ThemeRenderer::renderFooter();
echo '</div>' . "\n";