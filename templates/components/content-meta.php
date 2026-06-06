<?php
/**
 * Content meta component.
 *
 * Renders post metadata (date, author, categories).
 * Must be called inside the WordPress Loop.
 *
 * @package Jasanika
 */

use Jasanika\Core\ContentRenderer;

ContentRenderer::renderMeta(true);