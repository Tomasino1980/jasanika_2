<?php

declare(strict_types=1);

namespace Jasanika\Footer;

use Jasanika\Core\LayoutRegionRenderer;
use Jasanika\Layout\LayoutManager;
use Jasanika\Navigation\NavigationManager;

/**
 * Footer renderer.
 *
 * Owns all footer rendering logic. Uses FooterManager for configuration
 * values and delegates navigation/widget rendering to dedicated services.
 *
 * Produces:
 * - Footer widget regions (column layout, skipped for landing-page)
 * - Footer navigation (optional)
 * - Copyright text (optional)
 * - Social icons (optional, placeholder)
 */
final class FooterRenderer
{
    private FooterManager $footerManager;
    private NavigationManager $navigationManager;
    private LayoutRegionRenderer $layoutRegionRenderer;
    private LayoutManager $layoutManager;

    public function __construct(
        FooterManager $footerManager,
        NavigationManager $navigationManager,
        LayoutRegionRenderer $layoutRegionRenderer,
        LayoutManager $layoutManager
    ) {
        $this->footerManager = $footerManager;
        $this->navigationManager = $navigationManager;
        $this->layoutRegionRenderer = $layoutRegionRenderer;
        $this->layoutManager = $layoutManager;
    }

    /**
     * Render the complete footer section.
     *
     * Outputs the <footer> element with all configured components.
     * Called from templates/footer.php via ThemeRenderer.
     */
    public function render(): void
    {
        $bgColor = $this->footerManager->getFooterBackgroundColor();
        $textColor = $this->footerManager->getFooterTextColor();
        $columns = $this->footerManager->getFooterLayout();

        printf(
            '<footer id="jas-footer" class="jas-footer jas-footer--cols-%d" style="--jas-footer-bg:%s;--jas-footer-text:%s;">',
            $columns,
            esc_attr($bgColor),
            esc_attr($textColor)
        );

        echo '<div class="jas-container">';

        // Render footer widget regions
        $this->renderFooterWidgets($columns);

        // Footer navigation
        if ($this->footerManager->showFooterMenu()) {
            $this->renderFooterNav();
        }

        // Copyright text
        $this->renderCopyright();

        // Social icons (placeholder)
        if ($this->footerManager->showSocialIcons()) {
            $this->renderSocialIcons();
        }

        echo '</div>';
        echo '</footer>';
    }

    /**
     * Render footer widget columns.
     *
     * Skipped for landing-page layouts.
     */
    private function renderFooterWidgets(int $columns): void
    {
        if ($this->layoutManager->getActiveLayout() === 'landing-page') {
            return;
        }

        echo '<div class="jas-footer__widgets">';
        $this->layoutRegionRenderer->renderFooterRegions();
        echo '</div>';
    }

    /**
     * Render the footer navigation menu.
     */
    private function renderFooterNav(): void
    {
        echo '<div class="jas-footer__nav">';
        if ($this->navigationManager->hasMenu('footer')) {
            $this->navigationManager->renderMenu('footer', 'Footer Navigation');
        }
        echo '</div>';
    }

    /**
     * Render the copyright text.
     */
    private function renderCopyright(): void
    {
        $text = $this->footerManager->getCopyrightText();

        if (empty($text)) {
            $text = sprintf(
                /* translators: %s: site name */
                '&copy; %s %s',
                date('Y'),
                get_bloginfo('name', 'display')
            );
        }

        // Replace dynamic tags
        $text = str_replace(
            ['{year}', '{sitename}'],
            [date('Y'), get_bloginfo('name', 'display')],
            $text
        );

        printf(
            '<div class="jas-footer__copyright"><p>%s</p></div>',
            wp_kses_post($text)
        );
    }

    /**
     * Render social icons.
     *
     * @todo M30+: Implement proper social icon management with URL settings.
     *       Currently renders a placeholder for the icon area.
     */
    private function renderSocialIcons(): void
    {
        echo '<div class="jas-footer__social">';
        echo '<span class="jas-footer__social-label">' . esc_html__('Follow us:', 'jasanika') . '</span>';
        echo '</div>';
    }

    /**
     * Get the FooterManager instance.
     */
    public function getManager(): FooterManager
    {
        return $this->footerManager;
    }

    /**
     * Get the NavigationManager instance.
     */
    public function getNavigationManager(): NavigationManager
    {
        return $this->navigationManager;
    }

    /**
     * Get the LayoutRegionRenderer instance.
     */
    public function getLayoutRegionRenderer(): LayoutRegionRenderer
    {
        return $this->layoutRegionRenderer;
    }
}