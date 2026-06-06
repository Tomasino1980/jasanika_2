<?php
/**
 * Page header component.
 *
 * Renders the page-level title, archive title, or search header
 * above the main content loop. Used by template files to display
 * contextual headings.
 *
 * No business logic — delegates to ContentRenderer.
 *
 * @package Jasanika
 */

use Jasanika\Core\ContentRenderer;

if (is_search()) {
    ContentRenderer::renderSearchInfo((int) $wp_query->found_posts ?? 0);
} elseif (is_archive()) {
    ContentRenderer::renderArchiveTitle();
    ContentRenderer::renderArchiveDescription();
} elseif (is_singular()) {
    ContentRenderer::renderTitle('h1');
} elseif (is_404()) {
    echo '<h1 class="jas-content__title">' . esc_html__('Page Not Found', 'jasanika') . '</h1>';
}