<?php
/**
 * 404 template.
 *
 * Renders a user-friendly error page with:
 * - Clear error message
 * - Navigation back to homepage
 * - Consistent framework styling
 *
 * @package Jasanika
 */

use Jasanika\Core\ContentRenderer;

?>
<div class="jas-content">
    <div class="jas-container">
        <div class="jas-error-404">
            <h1 class="jas-content__title"><?php esc_html_e('Page Not Found', 'jasanika'); ?></h1>

            <div class="jas-content__body jas-error-404__body">
                <p><?php esc_html_e('The page you are looking for does not exist. It may have been moved, deleted, or the URL may be incorrect.', 'jasanika'); ?></p>
            </div>

            <?php ContentRenderer::renderHomeLink(); ?>
        </div>
    </div>
</div>