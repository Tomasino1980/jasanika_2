<?php
/**
 * Site branding component.
 *
 * Renders the site logo (or fallback title) and tagline.
 * Delegates to SiteIdentityRenderer through ThemeRenderer.
 *
 * @package Jasanika
 */

use Jasanika\Core\ThemeRenderer;

$renderer = ThemeRenderer::getInstance();
$branding = $renderer ? $renderer->getSiteIdentityRenderer() : null;

if (!$branding) {
    return;
}
?>
<div class="jas-site-branding">
    <?php $branding->renderBranding(); ?>
    <?php $branding->renderSiteTagline(); ?>
</div>