<?php
/**
 * Archive template.
 *
 * Renders archive pages (categories, tags, authors, dates, custom post types)
 * using a unified card layout. Supports all archive types through
 * the same rendering pipeline.
 *
 * @package Jasanika
 */

use Jasanika\Core\ContentRenderer;

$foundPosts = (int) $wp_query->found_posts;
$type       = $foundPosts > 0 ? 'default' : 'archive';
?>
<div class="jas-content">
    <div class="jas-container">
        <header class="jas-archive-header">
            <?php ContentRenderer::renderArchiveTitle(); ?>
            <?php ContentRenderer::renderArchiveDescription(); ?>
        </header>

        <?php if (have_posts()) : ?>
            <div class="jas-archive-grid">
                <?php while (have_posts()) : the_post(); ?>
                    <?php get_template_part('templates/components/content-card'); ?>
                <?php endwhile; ?>
            </div>

            <?php ContentRenderer::renderPagination(); ?>
        <?php else : ?>
            <?php ContentRenderer::renderEmptyState($type); ?>
        <?php endif; ?>
    </div>
</div>