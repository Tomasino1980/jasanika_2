<?php
/**
 * Main footer template.
 *
 * Renders the site footer via FooterRenderer with widget regions,
 * navigation, copyright, and optional social icons.
 *
 * Footer widget regions are rendered by FooterRenderer. Regions with
 * no active widgets produce no output.
 */
?>
<?php
use Jasanika\Core\ThemeRenderer;

$renderer = ThemeRenderer::getInstance();

if ($renderer) {
    $renderer->getFooterRenderer()->render();
}
?>

<?php wp_footer(); ?>
</body>
</html>