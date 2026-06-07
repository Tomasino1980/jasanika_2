<?php

declare(strict_types=1);

namespace Jasanika\Admin\Components;

/**
 * Preset Card component for the Theme Presets UI.
 *
 * Renders a visually distinct preset selection card with label,
 * description, active state indicator, and a selection button.
 *
 * M27 — Theme Presets & Settings UX Framework.
 *
 * Usage:
 *   $card = new PresetCard('default', 'Default', 'Standard design', true);
 *   $card->render();
 */
final class PresetCard
{
    private string $name;
    private string $label;
    private string $description;
    private bool $isActive;

    public function __construct(
        string $name,
        string $label,
        string $description,
        bool $isActive = false
    ) {
        $this->name        = $name;
        $this->label       = $label;
        $this->description = $description;
        $this->isActive    = $isActive;
    }

    /**
     * Render the preset card HTML.
     */
    public function render(): void
    {
        $activeClass = $this->isActive ? ' jas-preset-card--active' : '';
        $checked     = $this->isActive ? ' checked' : '';

        ?>
        <label class="jas-preset-card<?php echo esc_attr($activeClass); ?>">
            <input
                type="radio"
                name="jasanika_active_preset"
                value="<?php echo esc_attr($this->name); ?>"
                class="jas-preset-card__input"
                <?php echo $checked; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            >
            <span class="jas-preset-card__indicator"></span>
            <span class="jas-preset-card__content">
                <strong class="jas-preset-card__label"><?php echo esc_html($this->label); ?></strong>
                <?php if ($this->description !== '') : ?>
                    <span class="jas-preset-card__desc"><?php echo esc_html($this->description); ?></span>
                <?php endif; ?>
            </span>
        </label>
        <?php
    }
}