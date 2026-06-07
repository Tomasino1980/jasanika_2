<?php
/**
 * Form field component template.
 *
 * Renders a standardized form field with label and input element.
 * Consumes design tokens via CSS classes — no hardcoded visual values.
 *
 * Supported input types:
 * - text     — Single-line text input
 * - email    — Email input with validation
 * - search   — Search input
 * - textarea — Multi-line textarea
 * - select   — Dropdown select
 *
 * Consistent label positioning, spacing, and token-driven focus states.
 *
 * Expected variables (set by ComponentRenderer::renderFormField()):
 * - $type        (string)  — Field type (text, email, search, textarea, select).
 * - $name        (string)  — Input name attribute.
 * - $label       (string)  — Field label text.
 * - $value       (string)  — Current field value.
 * - $options     (array)   — Select options (key-value pairs, used for select type).
 * - $placeholder (string)  — Placeholder text.
 * - $class       (string)  — CSS class string including type.
 * - $attrs       (array)   — Additional HTML attributes as key-value pairs.
 *
 * @package Jasanika
 */

$type        = $type        ?? 'text';
$name        = $name        ?? '';
$label       = $label       ?? '';
$value       = $value       ?? '';
$options     = $options     ?? [];
$placeholder = $placeholder ?? '';
$class       = $class       ?? 'jas-form-field jas-form-field--text';
$attrs       = $attrs       ?? [];

$attributes = '';

foreach ($attrs as $key => $val) {
    $attributes .= ' ' . esc_attr($key) . '="' . esc_attr((string) $val) . '"';
}

if (empty($name)) {
    return;
}
?>
<div class="<?php echo esc_attr($class); ?>">
    <?php if (!empty($label)) : ?>
    <label class="jas-form-field__label" for="jas-field-<?php echo esc_attr($name); ?>">
        <?php echo esc_html($label); ?>
    </label>
    <?php endif; ?>

    <?php if ($type === 'textarea') : ?>
    <textarea class="jas-form-field__input"
              id="jas-field-<?php echo esc_attr($name); ?>"
              name="<?php echo esc_attr($name); ?>"
              placeholder="<?php echo esc_attr($placeholder); ?>"
              <?php echo $attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_textarea($value); ?></textarea>

    <?php elseif ($type === 'select') : ?>
    <select class="jas-form-field__input"
            id="jas-field-<?php echo esc_attr($name); ?>"
            name="<?php echo esc_attr($name); ?>"
            <?php echo $attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
        <?php foreach ($options as $optValue => $optLabel) : ?>
        <option value="<?php echo esc_attr($optValue); ?>" <?php selected($value, $optValue); ?>>
            <?php echo esc_html($optLabel); ?>
        </option>
        <?php endforeach; ?>
    </select>

    <?php else : ?>
    <input class="jas-form-field__input"
           type="<?php echo esc_attr($type); ?>"
           id="jas-field-<?php echo esc_attr($name); ?>"
           name="<?php echo esc_attr($name); ?>"
           value="<?php echo esc_attr($value); ?>"
           placeholder="<?php echo esc_attr($placeholder); ?>"
           <?php echo $attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> />
    <?php endif; ?>
</div>