<?php
/**
 * Card component template.
 *
 * Standardized content card for archives, search results, widgets,
 * and future custom post types.
 *
 * Consumes design tokens via CSS classes — no hardcoded visual values.
 * Consistent spacing, radius, and interactive states.
 *
 * Expected variables (set by ComponentRenderer::renderCard()):
 * - $title  (string)  — Card title content.
 * - $body   (string)  — Card body content.
 * - $footer (string)  — Card footer content.
 * - $class  (string)  — CSS class string.
 * - $attrs  (array)   — Additional HTML attributes as key-value pairs.
 *
 * @package Jasanika
 */

$title  = $title  ?? '';
$body   = $body   ?? '';
$footer = $footer ?? '';
$class  = $class  ?? 'jas-card';
$attrs  = $attrs  ?? [];

$attributes = '';

foreach ($attrs as $key => $value) {
    $attributes .= ' ' . esc_attr($key) . '="' . esc_attr((string) $value) . '"';
}
?>
<article class="<?php echo esc_attr($class); ?>" <?php echo $attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
    <?php if (!empty($title)) : ?>
    <header class="jas-card__header">
        <h3 class="jas-card__title"><?php echo esc_html($title); ?></h3>
    </header>
    <?php endif; ?>

    <?php if (!empty($body)) : ?>
    <div class="jas-card__body">
        <?php echo wp_kses_post($body); ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($footer)) : ?>
    <footer class="jas-card__footer">
        <?php echo wp_kses_post($footer); ?>
    </footer>
    <?php endif; ?>
</article>