<?php

declare(strict_types=1);

namespace Jasanika\Design;

/**
 * Design Token Generator.
 *
 * Converts design settings, registered token definitions, and preset
 * overrides into CSS custom properties for frontend consumption.
 *
 * This is the central token generation engine for the Jasanika design system.
 * It owns no token definitions — those belong to DesignTokenRegistry.
 * It owns no preset logic — that belongs to ThemePresetManager.
 * It orchestrates token generation from all sources.
 *
 * Flow:
 * DesignSettingsManager (dynamic values)
 *   + DesignTokenRegistry (defaults & definitions)
 *   + ThemePresetManager (overrides)
 *   ↓
 * DesignTokenGenerator::getAllTokens()
 *   ↓
 * renderInlineStyles() → <style id="jasanika-design-tokens"> in <head>
 * renderDebugComment() → HTML comment in WP_DEBUG mode
 *
 * Generates:
 * - Semantic color tokens  (--jas-color-*)
 * - Typography scale       (--jas-font-size-*)
 * - Spacing system         (--jas-space-*)
 * - Layout tokens          (--jas-container-width, --jas-site-layout, etc.)
 * - Border radius tokens   (--jas-radius-*)
 * - Legacy tokens          (--jas-primary-color, --jas-primary-hover)
 *
 * M26 additions:
 * - --jas-header-width
 * - --jas-content-width
 * - --jas-sidebar-width
 * - --jas-footer-width
 * - --jas-section-padding
 * - --jas-section-margin
 */
final class DesignTokenGenerator
{
    private DesignSettingsManager $designSettingsManager;
    private DesignTokenRegistry $tokenRegistry;
    private ThemePresetManager $presetManager;

    public function __construct(
        DesignSettingsManager $designSettingsManager,
        DesignTokenRegistry $tokenRegistry,
        ThemePresetManager $presetManager
    ) {
        $this->designSettingsManager = $designSettingsManager;
        $this->tokenRegistry = $tokenRegistry;
        $this->presetManager = $presetManager;
    }

    /**
     * Generate the complete token set.
     *
     * Resolution order (later overrides earlier):
     * 1. DesignTokenRegistry defaults
     * 2. Dynamic values from DesignSettingsManager
     * 3. Legacy backward compatibility tokens
     * 4. Layout control tokens from SettingsManager
     * 5. Preset overrides
     *
     * M27: Added color scheme tokens (secondary, accent, background,
     * surface, text, heading, border) and heading font family token.
     *
     * @return array<string, string> Token name → CSS value.
     */
    public function getAllTokens(): array
    {
        // 1. Start with registry defaults
        $tokens = $this->tokenRegistry->getDefaults();

        // 2. Override with dynamic design settings
        $tokens['--jas-primary-color']       = $this->designSettingsManager->getPrimaryColor();
        $tokens['--jas-color-primary']       = $this->designSettingsManager->getPrimaryColor();
        $tokens['--jas-primary-hover']       = $this->designSettingsManager->getPrimaryColorHover();
        $tokens['--jas-color-primary-hover'] = $this->designSettingsManager->getPrimaryColorHover();
        $tokens['--jas-color-secondary']     = $this->designSettingsManager->getSecondaryColor();
        $tokens['--jas-color-accent']        = $this->designSettingsManager->getAccentColor();
        $tokens['--jas-color-background']    = $this->designSettingsManager->getBackgroundColor();
        $tokens['--jas-color-surface']       = $this->designSettingsManager->getSurfaceColor();
        $tokens['--jas-color-text']          = $this->designSettingsManager->getTextColor();
        $tokens['--jas-color-heading']       = $this->designSettingsManager->getHeadingColor();
        $tokens['--jas-color-border']        = $this->designSettingsManager->getBorderColor();
        $tokens['--jas-font-family']         = $this->designSettingsManager->getFontFamily();
        $tokens['--jas-container-width']     = $this->designSettingsManager->getContainerWidth();
        $tokens['--jas-site-layout']         = $this->designSettingsManager->getSiteLayout();

        // M28: Header layout tokens
        $tokens['--jas-header-bg']   = $this->designSettingsManager->getPrimaryColor(); // placeholder, overridden by inline style

        // 3. Compute semantic color values
        // For now, design-system-fixed values are the defaults in the registry.
        // Dynamic derived color logic will be expanded in future milestones.

        // 4. Apply layout control values from SettingsManager
        $layoutTokens = [
            '--jas-header-width'   => 'layout_header_width',
            '--jas-content-width'  => 'layout_content_width',
            '--jas-sidebar-width'  => 'layout_sidebar_width',
            '--jas-footer-width'   => 'layout_footer_width',
            '--jas-section-padding' => 'layout_section_padding',
            '--jas-section-margin'  => 'layout_section_margin',
        ];

        foreach ($layoutTokens as $tokenName => $settingKey) {
            $value = $this->designSettingsManager->getLayoutSetting($settingKey);
            if ($value !== '') {
                $tokens[$tokenName] = $value;
            }
        }

        // 5. Apply preset overrides
        $overrides = $this->presetManager->getActiveTokenOverrides();

        foreach ($overrides as $name => $value) {
            $tokens[$name] = $value;
        }

        return $tokens;
    }

    /**
     * Generate and output the inline <style> block with CSS custom properties.
     *
     * Called via the wp_head action hook.
     * Outputs :root-level and .jas-theme-level CSS custom properties.
     */
    public function renderInlineStyles(): void
    {
        $tokens = $this->getAllTokens();

        echo '<style id="jasanika-design-tokens">' . "\n";

        // :root group — base token context
        echo ':root {' . "\n";

        foreach ($tokens as $name => $value) {
            echo '  ' . $name . ': ' . esc_attr($value) . ';' . "\n";
        }

        echo '}' . "\n";

        // .jas-theme group — prepared for future theme switching.
        // When a theme class is applied to a container, these tokens
        // override the :root-level values within that scope.
        echo '.jas-theme {' . "\n";

        foreach ($tokens as $name => $value) {
            echo '  ' . $name . ': ' . esc_attr($value) . ';' . "\n";
        }

        echo '}' . "\n";

        echo '</style>' . "\n";
    }

    /**
     * Generate debug HTML comment when WP_DEBUG is enabled.
     *
     * Outputs current design token values, active preset, and token count
     * as an HTML comment. Never visible in production environments.
     */
    public function renderDebugComment(): void
    {
        if (!defined('WP_DEBUG') || !WP_DEBUG) {
            return;
        }

        $color    = $this->designSettingsManager->getPrimaryColor();
        $font     = $this->designSettingsManager->getTypographyKey();
        $width    = $this->designSettingsManager->getContainerWidth();
        $layout   = $this->designSettingsManager->getSiteLayout();
        $preset   = $this->presetManager->getActivePreset();
        $tokens   = $this->getAllTokens();

        echo '<!--' . "\n";
        echo 'Jasanika Design Tokens' . "\n";
        echo 'Preset: ' . esc_attr($preset) . "\n";
        echo 'Primary Color: ' . esc_attr($color) . "\n";
        echo 'Font Family: ' . esc_attr(ucfirst($font)) . "\n";
        echo 'Container Width: ' . esc_attr($width) . "\n";
        echo 'Layout: ' . esc_attr($layout) . "\n";
        echo '--' . "\n";
        echo 'Token Count: ' . count($tokens) . "\n";
        echo '-->' . "\n";
    }

    /**
     * Get the CSS class name for the current site layout.
     *
     * Returns "jas-site--boxed" or "jas-site--full-width"
     * based on the site layout setting.
     */
    public function getSiteLayoutClass(): string
    {
        $layout = $this->designSettingsManager->getSiteLayout();

        return 'jas-site--' . $layout;
    }

    /**
     * Filter WordPress body classes to include the site layout class.
     *
     * @param string[] $classes Existing body classes.
     * @return string[] Modified body classes.
     */
    public function filterBodyClass(array $classes): array
    {
        $classes[] = $this->getSiteLayoutClass();

        return $classes;
    }
}