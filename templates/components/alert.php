<?php
/**
 * Alert component template.
 *
 * Renders a token-driven semantic alert/message banner.
 * Consumes design tokens via CSS classes — no hardcoded visual values.
 *
 * Types:
 * - info    (default) — Primary brand accent for informational messages
 * - success           — Green accent for success confirmations
 * - warning           — Gold accent for warnings
 * - error             — Red accent for errors
 *
 * Accessible markup with role="alert" for screen reader support.
 *
 * Expected variables (set by ComponentRenderer::renderAlert()):
 * - $type    (string)  — Alert type class suffix.
 * - $message (string)  — Alert message content.
 * - $class   (string)  — CSS class string including type.
 * - $attrs   (array)   — Additional HTML attributes as key-value pairs.
 *
 * @package Jasanika
 */

$message = $message ?? '';
$class   = $class   ?? 'jas-alert jas-alert--info';
$attrs   = $attrs   ?? [];

$attributes = '';

foreach ($attrs as $key => $value) {
    $attributes .= ' ' . esc_attr($key) . '="' . esc_attr((string) $value) . '"';
}

if (empty($message)) {
    return;
}
?>
<div class="<?php echo esc_attr($class); ?>"
     role="alert"
     <?php echo $attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
    <p class="jas-alert__message"><?php echo wp_kses_post($message); ?></p>
</div>