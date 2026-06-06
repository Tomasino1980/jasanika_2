<?php

declare(strict_types=1);

use Jasanika\Core\ThemeRenderer;

/**
 * Content template.
 *
 * Renders the main content area with the WordPress Loop
 * and temporary framework branding output.
 */

$renderer = ThemeRenderer::getInstance();
$frameworkInfo = $renderer ? $renderer->getFrameworkInfo() : null;
?>
<div class="jas-content">
    <div class="jas-container">
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('jas-article'); ?>>
                    <h1 class="jas-article__title"><?php the_title(); ?></h1>
                    <div class="jas-article__content">
                        <?php the_content(); ?>
                    </div>
                </article>
            <?php endwhile; ?>
        <?php else : ?>
            <article class="jas-article">
                <div class="jas-article__content">
                    <?php if ($frameworkInfo) : ?>
                        <h1><?php echo esc_html($frameworkInfo->getName()); ?></h1>
                        <p><?php echo esc_html__('Version', 'jasanika'); ?> <?php echo esc_html($frameworkInfo->getVersion()); ?></p>
                    <?php endif; ?>
                </div>
            </article>
        <?php endif; ?>
    </div>
</div>
