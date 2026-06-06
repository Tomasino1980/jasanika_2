<?php
/**
 * Content card component.
 *
 * Renders a single post card for archive/search listings.
 * Displays title, excerpt, metadata, and a read more link.
 *
 * Must be called inside the WordPress Loop.
 *
 * @package Jasanika
 */

use Jasanika\Core\ContentRenderer;

$postId = get_the_ID();

if (empty($postId)) {
    return;
}
?>
<article id="post-<?php echo esc_attr($postId); ?>" <?php post_class('jas-card'); ?>>
    <header class="jas-card__header">
        <?php ContentRenderer::renderTitle('h2'); ?>
    </header>

    <div class="jas-card__body">
        <?php ContentRenderer::renderExcerpt(); ?>
    </div>

    <footer class="jas-card__footer">
        <?php ContentRenderer::renderMeta(true); ?>
        <?php ContentRenderer::renderReadMoreLink(); ?>
    </footer>
</article>