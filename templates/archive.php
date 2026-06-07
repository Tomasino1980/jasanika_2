<?php
/**
 * Archive template.
 *
 * Renders archive pages (categories, tags, authors, dates, custom post types)
 * using the unified Card Component for content presentation.
 * All components consume design tokens — no hardcoded visual values.
 *
 * @package Jasanika
 */

use Jasanika\Core\ContentRenderer;
use Jasanika\Core\ThemeRenderer;

$renderer = ThemeRenderer::getInstance();
$componentRenderer = $renderer ? $renderer->getComponentRenderer() : null;

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
                    <?php
                    if ($componentRenderer) {
                        $title = get_the_title();

                        ob_start();
                        ContentRenderer::renderExcerpt();
                        $body = ob_get_clean();

                        ob_start();
                        ContentRenderer::renderMeta(true);
                        ContentRenderer::renderReadMoreLink();
                        $footer = ob_get_clean();

                        $componentRenderer->renderCard(
                            $title,
                            $body,
                            $footer,
                            ['id' => 'post-' . get_the_ID()]
                        );
                    }
                    ?>
                <?php endwhile; ?>
            </div>

            <?php ContentRenderer::renderPagination(); ?>
        <?php else : ?>
            <?php ContentRenderer::renderEmptyState($type); ?>
        <?php endif; ?>
    </div>
</div>