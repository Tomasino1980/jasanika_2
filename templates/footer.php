<?php
/**
 * Main footer template.
 *
 * Renders the site footer with navigation, copyright, and
 * optional framework version information (development mode only).
 */
?>
<footer id="jas-footer" class="jas-footer">
    <div class="jas-container">
        <?php get_template_part('templates/components/footer-navigation'); ?>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>