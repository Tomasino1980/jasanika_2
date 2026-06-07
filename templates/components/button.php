<?php
/**
 * Button component template.
 *
 * Renders a token-driven action button with variant support.
 * Consumes design tokens via CSS classes — no hardcoded visual values.
 *
 * Variants:
 * - primary   (default) — Solid primary brand color background
 * - secondary            — Surface background with subtle border
 * - outline              — Transparent background with primary border
 *
 * Accessible markup with proper role, aria-label, and focus management.
 *
 * Expected variables (set by ComponentRenderer::renderButton()):
 * - $variant (string)  — Button variant class suffix.
 * - $label   (string)  — Button text content.
 * - $url     (string)  — Optional link URL (renders <a> tag when non-empty).
 * - $class   (string)  — CSS class string including variant.
 * - $attrs   (array)   — Additional HTML attributes as key-value pairs.
 *
 * @package Jasanika
 */

$label = $label ?? '';
$url   = $url   ?? '';
$class = $class ?? 'jas-btn jas-btn--primary';
$attrs = $attrs ?? [];

$attributes = '';

foreach ($attrs as $key => $value) {
    $attributes .= ' ' . esc_attr($key) . '="' . esc_attr((string) $value) . '"';
}

if (!empty($url)) :
?>
<a href="<?php echo esc_url($url); ?>"
   class="<?php echo esc_attr($class); ?>"
   role="button"
   <?php echo $attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
    <?php echo esc_html($label); ?>
</a>
<?php else : ?>
<button type="button"
        class="<?php echo esc_attr($class); ?>"
        <?php echo $attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
    <?php echo esc_html($label); ?>
</button>
<?php endif; ?>