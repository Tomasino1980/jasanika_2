<?php
/**
 * Primary navigation component.
 *
 * Renders the primary WordPress menu with accessible markup.
 * Delegates to NavigationManager through ThemeRenderer.
 *
 * @package Jasanika
 */

use Jasanika\Core\ThemeRenderer;

$renderer = ThemeRenderer::getInstance();
$nav = $renderer ? $renderer->getNavigationManager() : null;

if (!$nav) {
    return;
}
?>
<div class="jas-primary-nav">
    <?php if ($nav->hasMenu('primary')) : ?>
        <?php $nav->renderMenu('primary', 'Primary Navigation'); ?>
    <?php else : ?>
        <button class="jas-mobile-nav-toggle" aria-label="<?php esc_attr_e('Toggle menu', 'jasanika'); ?>" aria-expanded="false">
            <span class="jas-mobile-nav-toggle__icon"></span>
        </button>
    <?php endif; ?>
</div>