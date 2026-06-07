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
        // Render footer widget regions (footer-left, footer-center, footer-right)
        $renderer = \Jasanika\Core\ThemeRenderer::getInstance();
        if ($renderer) {
            $layoutRenderer = $renderer->getLayoutRegionRenderer();
            if ($layoutRenderer) {
                $layoutRenderer->renderFooterRegions();
            }
        }
        ?>
        <?php get_template_part('templates/components/footer-navigation'); ?>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>