<?php
/**
 * Search results template.
 *
 * Displays search query, result count, and matching posts
 * using the component system (Card, Button, Form components).
 * All components consume design tokens — no hardcoded visual values.
 *
 * @package Jasanika
 */

use Jasanika\Core\ContentRenderer;
use Jasanika\Core\ThemeRenderer;

$renderer = ThemeRenderer::getInstance();
$componentRenderer = $renderer ? $renderer->getComponentRenderer() : null;

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
            <div class="jas-empty-state">
                <?php ContentRenderer::renderEmptyState('search'); ?>
                <form role="search" method="get" class="jas-search-form" action="<?php echo esc_url(home_url('/')); ?>">
                    <?php
                    if ($componentRenderer) {
                        $componentRenderer->renderFormField(
                            'search',
                            's',
                            '',
                            $query,
                            [],
                            [
                                'placeholder' => __('Search again...', 'jasanika'),
                                'aria-label'  => __('Search for', 'jasanika'),
                            ]
                        );

                        $componentRenderer->renderButton(
                            'primary',
                            __('Search', 'jasanika')
                        );
                    }
                    ?>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>