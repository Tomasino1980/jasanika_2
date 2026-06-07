<?php
/**
 * Main footer template.
 *
 * Renders the site footer with widget regions, navigation, copyright, and
 * optional framework version information (development mode only).
 *
 * Footer widget regions are rendered first, followed by navigation and
 * copyright. Regions with no active widgets produce no output.
 */
?>
<footer id="jas-footer" class="jas-footer">
    <div class="jas-container">
        <?php
        // Render footer widget regions only for non-landing page layouts
        $renderer = \Jasanika\Core\ThemeRenderer::getInstance();
        if ($renderer) {
            $layoutManager = $renderer->getLayoutManager();
            if ($layoutManager && $layoutManager->getActiveLayout() !== 'landing-page') {
                $layoutRenderer = $renderer->getLayoutRegionRenderer();
                if ($layoutRenderer) {
                    $layoutRenderer->renderFooterRegions();
                }
            }
        }
        ?>
        <?php get_template_part('templates/components/footer-navigation'); ?>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>