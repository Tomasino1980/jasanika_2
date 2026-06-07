<?php

declare(strict_types=1);

namespace Jasanika\Header;

use Jasanika\Core\SiteIdentityRenderer;
use Jasanika\Navigation\NavigationManager;

/**
 * Header renderer.
 *
 * Owns all header rendering logic. Delegates site identity and navigation
 * to dedicated services. Uses HeaderManager for configuration values.
 *
 * Produces:
 * - Top bar (optional)
 * - Site branding + navigation
 * - Search toggle (optional)
 * - Sticky header class (optional)
 */
final class HeaderRenderer
{
    private HeaderManager $headerManager;
    private SiteIdentityRenderer $siteIdentityRenderer;
    private NavigationManager $navigationManager;

    public function __construct(
        HeaderManager $headerManager,
        SiteIdentityRenderer $siteIdentityRenderer,
        NavigationManager $navigationManager
    ) {
        $this->headerManager = $headerManager;
        $this->siteIdentityRenderer = $siteIdentityRenderer;
        $this->navigationManager = $navigationManager;
    }

    /**
     * Render the complete header section.
     *
     * Outputs the <header> element with all configured components.
     * Called from templates/header.php via ThemeRenderer.
     */
    public function render(): void
    {
        $classes = ['jas-header'];

        if ($this->headerManager->isStickyEnabled()) {
            $classes[] = 'jas-header--sticky';
        }

        $logoPosition = $this->headerManager->getLogoPosition();
        $classes[] = 'jas-header--logo-' . $logoPosition;

        $bgColor = $this->headerManager->getHeaderBackgroundColor();
        $textColor = $this->headerManager->getHeaderTextColor();

        printf(
            '<header id="jas-header" class="%s" style="--jas-header-bg:%s;--jas-header-text:%s;--jas-header-height:%s;">',
            esc_attr(implode(' ', $classes)),
            esc_attr($bgColor),
            esc_attr($textColor),
            esc_attr($this->headerManager->getHeaderHeight())
        );

        echo '<div class="jas-container">';
        echo '<div class="jas-header__inner">';

        // Site branding with logo settings
        $this->renderBranding();

        // Primary navigation
        $this->renderNav();

        echo '</div>';

        // Search toggle
        if ($this->headerManager->showSearch()) {
            echo '<div class="jas-header__search">';
            get_search_form();
            echo '</div>';
        }

        echo '</div>';
        echo '</header>';
    }

    /**
     * Render the site branding block respecting logo V2 settings.
     */
    private function renderBranding(): void
    {
        $logoId = $this->headerManager->getDesktopLogoId();
        $mobileLogoId = $this->headerManager->getMobileLogoId();
        $retinaLogoId = $this->headerManager->getRetinaLogoId();

        echo '<div class="jas-site-branding" style="';
        printf('text-align:%s;', esc_attr($this->headerManager->getLogoPosition()));
        echo '">';

        if ($logoId > 0) {
            $this->renderLogoImage($logoId, 'jas-branding__logo jas-branding__logo--desktop');
        }

        if ($mobileLogoId > 0 && $mobileLogoId !== $logoId) {
            $this->renderLogoImage($mobileLogoId, 'jas-branding__logo jas-branding__logo--mobile');
        }

        if ($retinaLogoId > 0 && $retinaLogoId !== $logoId) {
            $this->renderLogoImage($retinaLogoId, 'jas-branding__logo jas-branding__logo--retina');
        }

        // Fallback to existing site identity when no logo is set
        if ($logoId <= 0) {
            $this->siteIdentityRenderer->renderBranding();
        }

        echo '</div>';
    }

    /**
     * Render a logo image from attachment ID.
     */
    private function renderLogoImage(int $attachmentId, string $class): void
    {
        $width = $this->headerManager->getLogoWidth();
        $height = $this->headerManager->getLogoHeight();

        $image = wp_get_attachment_image(
            $attachmentId,
            'full',
            false,
            [
                'class'   => $class,
                'alt'     => get_bloginfo('name', 'display'),
                'loading' => 'eager',
                'decoding' => 'async',
                'style'   => sprintf('width:%s;height:%s;', $width, $height),
            ]
        );

        if (empty($image)) {
            return;
        }

        printf(
            '<a href="%s" class="jas-branding__link" rel="home">%s</a>',
            esc_url(home_url('/')),
            $image // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        );
    }

    /**
     * Render the primary navigation.
     */
    private function renderNav(): void
    {
        echo '<nav class="jas-primary-nav" aria-label="' . esc_attr__('Primary Navigation', 'jasanika') . '">';

        if ($this->navigationManager->hasMenu('primary')) {
            $this->navigationManager->renderMenu('primary', 'Primary Navigation');
        } else {
            printf(
                '<button class="jas-mobile-nav-toggle" aria-label="%s" aria-expanded="false">',
                esc_attr__('Toggle menu', 'jasanika')
            );
            echo '<span class="jas-mobile-nav-toggle__icon"></span>';
            echo '</button>';
        }

        echo '</nav>';
    }

    /**
     * Get the HeaderManager instance.
     */
    public function getManager(): HeaderManager
    {
        return $this->headerManager;
    }

    /**
     * Get the SiteIdentityRenderer instance.
     */
    public function getSiteIdentityRenderer(): SiteIdentityRenderer
    {
        return $this->siteIdentityRenderer;
    }

    /**
     * Get the NavigationManager instance.
     */
    public function getNavigationManager(): NavigationManager
    {
        return $this->navigationManager;
    }
}