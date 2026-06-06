<?php
/**
 * Search results template.
 *
 * Displays search query, result count, and matching posts.
 * Shows an empty state with a search form when no results are found.
 *
 * @package Jasanika
 */

use Jasanika\Core\ContentRenderer;

$query      = get_search_query();
$foundPosts = (int) $wp_query->found_posts;
?>
<div class="jas-content">
    <div class="jas-container">
        <header class="jas-search-header">
            <?php ContentRenderer::renderSearchInfo($foundPosts); ?>
        </header>

        <?php if (have_posts()) : ?>
            <div class="jas-search-results">
                <?php while (have_posts()) : the_post(); ?>
                    <?php get_template_part('templates/components/content-card'); ?>
                <?php endwhile; ?>
            </div>

            <?php ContentRenderer::renderPagination(); ?>
        <?php else : ?>
            <?php ContentRenderer::renderEmptyState('search'); ?>
        <?php endif; ?>
    </div>
</div>