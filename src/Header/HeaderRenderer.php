<?php

declare(strict_types=1);

namespace Jasanika\Header;

use Jasanika\Components\ComponentRenderer;
use Jasanika\Core\SiteIdentityRenderer;
use Jasanika\Navigation\NavigationManager;

/**
 * Dynamic Header Renderer — M28.
 *
 * The single source of truth for all header rendering output.
 * No header HTML exists outside this class.
 *
 * Responsibilities:
 * - Render the complete <header> element with all configured zones
 * - Render top bar (optional)
 * - Render site branding with responsive logo V3
 * - Render primary navigation
 * - Render search toggle
 * - Render CTA button
 * - Apply header layout engine (logo-left, logo-center, etc.)
 * - Output debug information in WP_DEBUG mode
 *
 * Rendering pipeline:
 * 1. Top bar (if enabled)
 * 2. Header inner (branding, nav, search toggle, CTA — layout-driven)
 * 3. Sticky header class (if enabled)
 * 4. Responsive logo selection (desktop / mobile / retina)
 *
 * Architecture rules:
 * - HeaderManager owns settings
 * - HeaderLayout owns layout definitions
 * - HeaderRenderer owns rendering
 * - ThemeRenderer delegates rendering
 * - ComponentRenderer renders CTA button
 * - Design Tokens own styling via CSS custom properties
 */
final class HeaderRenderer
{
    private HeaderManager $headerManager;
    private SiteIdentityRenderer $siteIdentityRenderer;
    private NavigationManager $navigationManager;
    private ComponentRenderer $componentRenderer;
    private MobileMenu $mobileMenu;

    public function __construct(
        HeaderManager $headerManager,
        SiteIdentityRenderer $siteIdentityRenderer,
        NavigationManager $navigationManager,
        ComponentRenderer $componentRenderer,
        ?MobileMenu $mobileMenu = null
    ) {
        $this->headerManager = $headerManager;
        $this->siteIdentityRenderer = $siteIdentityRenderer;
        $this->navigationManager = $navigationManager;
        $this->componentRenderer = $componentRenderer;
        $this->mobileMenu = $mobileMenu ?? new MobileMenu();
    }

    /**
     * Render the complete header section.
     *
     * Outputs the <header> element with all configured components
     * driven by the active layout and settings.
     *
     * Called from templates/header.php via ThemeRenderer.
     */
    public function render(): void
    {
        $layout = $this->headerManager->getLayout();
        $classes = $this->buildHeaderClasses($layout);

        $bgColor = $this->headerManager->getHeaderBackgroundColor();
        $textColor = $this->headerManager->getHeaderTextColor();
        $height = $this->headerManager->getHeaderHeight();
        $desktopHeight = $this->headerManager->getDesktopHeaderHeight();
        $tabletHeight = $this->headerManager->getTabletHeaderHeight();
        $mobileHeight = $this->headerManager->getMobileHeaderHeight();

        // Start header element with CSS custom properties
        printf(
            '<header id="jas-header" class="%s" style="--jas-header-bg:%s;--jas-header-text:%s;--jas-header-height:%s;--jas-header-height-desktop:%s;--jas-header-height-tablet:%s;--jas-header-height-mobile:%s;">',
            esc_attr(implode(' ', $classes)),
            esc_attr($bgColor),
            esc_attr($textColor),
            esc_attr($height),
            esc_attr($desktopHeight),
            esc_attr($tabletHeight),
            esc_attr($mobileHeight)
        );

        // 1. Top bar (optional)
        $this->renderTopBar();

        // 2. Header inner (zones driven by layout)
        echo '<div class="jas-container">';
        echo '<div class="jas-header__inner">';

        $zones = $this->headerManager->getLayoutEngine()->getZones($layout);

        foreach ($zones as $zone) {
            $this->renderZone($zone, $layout);
        }

        echo '</div>'; // .jas-header__inner

        // 3. Search area (below inner, toggled by search button)
        if ($this->headerManager->showSearch()) {
            $this->renderSearchArea();
        }

        echo '</div>'; // .jas-container
        echo '</header>';

        // Debug output
        $this->renderDebug();
    }

    /**
     * Build the CSS classes for the header element.
     *
     * @return string[]
     */
    private function buildHeaderClasses(string $layout): array
    {
        $classes = ['jas-header'];

        // Layout class
        $classes[] = 'jas-header--' . $layout;

        // Sticky header
        if ($this->headerManager->isStickyEnabled()) {
            $classes[] = 'jas-header--sticky';
        }

        // Top bar
        if ($this->headerManager->showTopBar()) {
            $classes[] = 'jas-header--has-top-bar';
        }

        // Search
        if ($this->headerManager->showSearch()) {
            $classes[] = 'jas-header--has-search';
        }

        // CTA
        if ($this->headerManager->showCta()) {
            $classes[] = 'jas-header--has-cta';
        }

        return $classes;
    }

    /**
     * Render a single zone within the header inner.
     */
    private function renderZone(string $zone, string $layout): void
    {
        switch ($zone) {
            case 'branding':
                $this->renderBranding();
                break;

            case 'nav':
                $this->renderNav();
                break;

            case 'search':
                $this->renderSearchToggle();
                break;

            case 'cta':
                $this->renderCta();
                break;
        }
    }

    // ---------------------------------------------------------------
    //  Top Bar
    // ---------------------------------------------------------------

    /**
     * Render the top bar when enabled.
     */
    private function renderTopBar(): void
    {
        if (!$this->headerManager->showTopBar()) {
            return;
        }

        $content = $this->headerManager->getTopBarContent();
        $bgColor = $this->headerManager->getTopBarBackground();
        $textColor = $this->headerManager->getTopBarTextColor();

        echo '<div class="jas-top-bar" style="--jas-top-bar-bg:' . esc_attr($bgColor) . ';--jas-top-bar-text:' . esc_attr($textColor) . ';">';
        echo '<div class="jas-container">';
        echo '<div class="jas-top-bar__inner">';

        if ($content !== '') {
            echo '<div class="jas-top-bar__content">';
            echo wp_kses_post(wpautop($content));
            echo '</div>';
        }

        echo '</div>';
        echo '</div>';
        echo '</div>';
    }

    // ---------------------------------------------------------------
    //  Branding / Logo V3
    // ---------------------------------------------------------------

    /**
     * Render the site branding block with responsive logo V3.
     *
     * Priority:
     * 1. Desktop logo (always shown, replaced on mobile/retina via CSS)
     * 2. Mobile logo (shown on mobile breakpoint via CSS)
     * 3. Retina logo (shown on retina displays via CSS)
     * 4. Site title fallback
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
            // Desktop logo
            $this->renderLogoImage($logoId, 'jas-branding__logo jas-branding__logo--desktop', 'jas-branding__logo--desktop');
        }

        if ($mobileLogoId > 0 && $mobileLogoId !== $logoId) {
            // Mobile logo
            $this->renderLogoImage($mobileLogoId, 'jas-branding__logo jas-branding__logo--mobile', 'jas-branding__logo--mobile');
        }

        if ($retinaLogoId > 0 && $retinaLogoId !== $logoId && $retinaLogoId !== $mobileLogoId) {
            // Retina logo
            $this->renderLogoImage($retinaLogoId, 'jas-branding__logo jas-branding__logo--retina', 'jas-branding__logo--retina');
        }

        // Fallback to site title when no logo is set
        if ($logoId <= 0) {
            $this->siteIdentityRenderer->renderBranding();
        }

        echo '</div>';
    }

    /**
     * Render a logo image from attachment ID.
     */
    private function renderLogoImage(int $attachmentId, string $class, string $dataVariant): void
    {
        $width = $this->headerManager->getLogoWidth();
        $height = $this->headerManager->getLogoHeight();

        $attr = [
            'class'          => $class,
            'alt'            => get_bloginfo('name', 'display'),
            'loading'        => 'eager',
            'decoding'       => 'async',
            'data-logo-type' => $dataVariant,
            'style'          => sprintf('width:%s;height:%s;', $width, $height),
        ];

        $image = wp_get_attachment_image(
            $attachmentId,
            'full',
            false,
            $attr
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

    // ---------------------------------------------------------------
    //  Navigation
    // ---------------------------------------------------------------

    /**
     * Render the primary navigation with mobile support.
     */
    private function renderNav(): void
    {
        $menuExists = $this->navigationManager->hasMenu('primary');

        echo '<div id="jas-header-nav" class="jas-header-nav">';

        echo '<nav class="jas-primary-nav" aria-label="' . esc_attr__('Primary Navigation', 'jasanika') . '">';

        if ($menuExists) {
            $this->navigationManager->renderMenu('primary', 'Primary Navigation');
        } else {
            printf(
                '<button class="jas-mobile-nav-toggle" aria-label="%s" aria-expanded="false" data-jas-toggle="mobile-nav" data-jas-target="#jas-header-nav">',
                esc_attr__('Toggle menu', 'jasanika')
            );
            echo '<span class="jas-mobile-nav-toggle__icon"></span>';
            echo '</button>';
        }

        echo '</nav>';

        echo '</div>';
    }

    // ---------------------------------------------------------------
    //  Search Toggle & Area
    // ---------------------------------------------------------------

    /**
     * Render the search toggle icon/button.
     */
    private function renderSearchToggle(): void
    {
        echo '<div class="jas-header-actions">';

        printf(
            '<button class="jas-search-toggle" aria-label="%s" aria-expanded="false" data-jas-toggle="search">',
            esc_attr__('Toggle search', 'jasanika')
        );
        echo '<span class="jas-search-toggle__icon">';
        // Simple inline SVG search icon
        echo '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">';
        echo '<circle cx="8.5" cy="8.5" r="6" stroke="currentColor" stroke-width="1.5"/>';
        echo '<path d="M13 13L18 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>';
        echo '</svg>';
        echo '</span>';
        echo '</button>';

        echo '</div>';
    }

    /**
     * Render the search area below the header inner.
     */
    private function renderSearchArea(): void
    {
        echo '<div class="jas-header__search" hidden>';
        get_search_form();
        echo '</div>';
    }

    // ---------------------------------------------------------------
    //  CTA Button
    // ---------------------------------------------------------------

    /**
     * Render the CTA button using the Component Framework.
     */
    private function renderCta(): void
    {
        if (!$this->headerManager->showCta()) {
            return;
        }

        echo '<div class="jas-header-actions jas-header-actions--cta">';

        $this->componentRenderer->renderButton(
            $this->headerManager->getCtaStyle(),
            $this->headerManager->getCtaLabel(),
            $this->headerManager->getCtaUrl(),
            [
                'class' => 'jas-header-cta',
            ]
        );

        echo '</div>';
    }

    // ---------------------------------------------------------------
    //  Debug Support
    // ---------------------------------------------------------------

    /**
     * Render debug information as HTML comment when WP_DEBUG is enabled.
     */
    private function renderDebug(): void
    {
        if (!defined('WP_DEBUG') || !WP_DEBUG) {
            return;
        }

        $info = $this->headerManager->getDebugInfo();

        echo '<!--' . "\n";
        echo 'Header Layout: ' . esc_html($info['Layout']) . "\n";
        echo 'Sticky Header: ' . esc_html($info['Sticky Header']) . "\n";
        echo 'Search: ' . esc_html($info['Search']) . "\n";
        echo 'CTA: ' . esc_html($info['CTA']) . "\n";
        echo 'Desktop Logo: ' . esc_html($info['Desktop Logo']) . "\n";
        echo 'Mobile Logo: ' . esc_html($info['Mobile Logo']) . "\n";
        echo '-->' . "\n";
    }

    // ---------------------------------------------------------------
    //  Public Getters
    // ---------------------------------------------------------------

    public function getManager(): HeaderManager
    {
        return $this->headerManager;
    }

    public function getSiteIdentityRenderer(): SiteIdentityRenderer
    {
        return $this->siteIdentityRenderer;
    }

    public function getNavigationManager(): NavigationManager
    {
        return $this->navigationManager;
    }

    public function getComponentRenderer(): ComponentRenderer
    {
        return $this->componentRenderer;
    }

    public function getMobileMenu(): MobileMenu
    {
        return $this->mobileMenu;
    }
}