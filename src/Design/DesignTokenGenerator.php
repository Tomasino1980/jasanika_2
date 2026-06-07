<?php

declare(strict_types=1);

namespace Jasanika\Design;

/**
 * Design token generator for frontend CSS custom properties.
 *
 * Converts design settings into CSS custom properties and generates
 * the inline style block in the document head. Also provides a
 * debug comment block visible only when WP_DEBUG is enabled.
 *
 * Responsibilities:
 * - Convert settings into CSS custom properties
 * - Generate frontend design token set
 * - Output debug information in development mode
 *
 * Flow:
 * ThemeRenderer → DesignTokenGenerator → <style> block in <head>
 *
 * No settings lookups occur directly in templates.
 */
final class DesignTokenGenerator
{
    private DesignSettingsManager $designSettingsManager;

    public function __construct(DesignSettingsManager $designSettingsManager)
    {
        $this->designSettingsManager = $designSettingsManager;
    }

    /**
     * Generate and output the inline <style> block with CSS custom properties.
     *
     * Called via the wp_head action hook.
     * Outputs :root-level CSS custom properties for frontend consumption.
     */
    public function renderInlineStyles(): void
    {
        $tokens = $this->designSettingsManager->getAllTokens();

        echo '<style id="jasanika-design-tokens">' . "\n";
        echo ':root {' . "\n";

        foreach ($tokens as $name => $value) {
            echo '  ' . $name . ': ' . esc_attr($value) . ';' . "\n";
        }

        echo '}' . "\n";
        echo '</style>' . "\n";
    }

    /**
     * Generate debug HTML comment when WP_DEBUG is enabled.
     *
     * Outputs current design token values as an HTML comment.
     * Never visible in production environments.
     */
    public function renderDebugComment(): void
    {
        if (!defined('WP_DEBUG') || !WP_DEBUG) {
            return;
        }

        $debugInfo = $this->designSettingsManager->getDebugInfo();

        echo '<!--' . "\n";
        echo 'Jasanika Design Tokens' . "\n";

        foreach ($debugInfo as $label => $value) {
            echo $label . ': ' . esc_attr((string) $value) . "\n";
        }

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