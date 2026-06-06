<?php
/**
 * Empty state component.
 *
 * Renders a fallback message when no content is found.
 * Used by archive, search, and default templates.
 *
 * @package Jasanika
 */

use Jasanika\Core\ContentRenderer;

$type = isset($type) ? $type : 'default';
?>
<div class="jas-empty-state">
    <?php ContentRenderer::renderEmptyState($type); ?>

    <?php if ($type === '404') : ?>
        <?php ContentRenderer::renderHomeLink(); ?>
    <?php elseif ($type === 'search') : ?>
        <?php get_search_form(); ?>
    <?php endif; ?>
</div>