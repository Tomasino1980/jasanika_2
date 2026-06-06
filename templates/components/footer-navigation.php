<?php
/**
 * Footer navigation component.
 *
 * Renders the footer WordPress menu with accessible markup.
 * Includes copyright information and framework version in development mode.
 *
 * @package Jasanika
 */

use Jasanika\Core\ThemeRenderer;

$renderer = ThemeRenderer::getInstance();
$nav = $renderer ? $renderer->getNavigationManager() : null;
$frameworkInfo = $renderer ? $renderer->getFrameworkInfo() : null;
?>
<div class="jas-footer-content">
    <div class="jas-footer-content__inner">
        <?php if ($nav && $nav->hasMenu('footer')) : ?>
            <div class="jas-footer-nav">
                <?php $nav->renderMenu('footer', 'Footer Navigation'); ?>
            </div>
        <?php endif; ?>

        <div class="jas-footer-info">
            <p class="jas-footer-info__copyright">
                &copy; <?php echo esc_html(gmdate('Y')); ?>
                <a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a>
            </p>

            <?php if ($frameworkInfo && defined('WP_DEBUG') && WP_DEBUG) : ?>
                <p class="jas-footer-info__version">
                    <?php echo esc_html($frameworkInfo->getName()); ?> v<?php echo esc_html($frameworkInfo->getVersion()); ?>
                </p>
            <?php endif; ?>
        </div>
    </div>
</div>