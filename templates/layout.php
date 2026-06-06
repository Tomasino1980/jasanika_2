<?php

declare(strict_types=1);

use Jasanika\Core\ThemeRenderer;

/**
 * Main layout template.
 *
 * Assembles the complete page structure through ThemeRenderer.
 * No direct template includes outside ThemeRenderer.
 */

ThemeRenderer::renderHeader();
ThemeRenderer::renderContent();
ThemeRenderer::renderFooter();
