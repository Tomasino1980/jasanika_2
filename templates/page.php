<?php
/**
 * Page template.
 *
 * Renders a single WordPress Page with the standard Loop.
 * Uses ContentRenderer for all content output.
 *
 * @package Jasanika
 */

use Jasanika\Core\ContentRenderer;

?>
<div class="jas-content">
    <div class="jas-container">
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('jas-page'); ?>>
                    <?php ContentRenderer::renderTitle('h1'); ?>
                    <?php ContentRenderer::renderContent(); ?>
                </article>
            <?php endwhile; ?>
        <?php else : ?>
            <?php ContentRenderer::renderEmptyState('default'); ?>
        <?php endif; ?>
    </div>
</div>