<?php

declare(strict_types=1);

namespace Jasanika\Header;

use Jasanika\Admin\SettingsManager;

/**
 * Header configuration manager.
 *
 * Owns all header-related settings and provides typed accessors
 * for the rendering pipeline. No rendering logic in this class.
 *
 * M28: Expanded with header layout, responsive heights, top bar,
 * CTA button settings, and tablet/mobile height variants.
 *
 * Settings are managed by the Settings Framework and stored in
 * the WordPress Options API via SettingsManager.
 */
final class HeaderManager
{
    private SettingsManager $settingsManager;
    private HeaderLayout $headerLayout;

    public function __construct(SettingsManager $settingsManager, ?HeaderLayout $headerLayout = null)
    {
        $this->settingsManager = $settingsManager;
        $this->headerLayout = $headerLayout ?? new HeaderLayout();
    }

    /**
     * Get the HeaderLayout instance.
     */
    public function getLayoutEngine(): HeaderLayout
    {
        return $this->headerLayout;
    }

    // ---------------------------------------------------------------
    //  Logo Settings (V3)
    // ---------------------------------------------------------------

    /**
     * Get the desktop logo attachment ID.
     */
    public function getDesktopLogoId(): int
    {
        return (int) $this->settingsManager->get('logo_desktop');
    }

    /**
     * Get the mobile logo attachment ID.
     */
    public function getMobileLogoId(): int
    {
        return (int) $this->settingsManager->get('logo_mobile');
    }

    /**
     * Get the retina logo attachment ID.
     */
    public function getRetinaLogoId(): int
    {
        return (int) $this->settingsManager->get('logo_retina');
    }

    /**
     * Get the logo width CSS value.
     */
    public function getLogoWidth(): string
    {
        $width = $this->settingsManager->get('logo_width');
        return is_string($width) && $width !== '' ? $width : '200px';
    }

    /**
     * Get the logo height CSS value.
     */
    public function getLogoHeight(): string
    {
        $height = $this->settingsManager->get('logo_height');
        return is_string($height) && $height !== '' ? $height : 'auto';
    }

    /**
     * Get the logo position.
     *
     * Returns 'left', 'center', or 'right'.
     */
    public function getLogoPosition(): string
    {
        $position = $this->settingsManager->get('logo_position');
        if (in_array($position, ['left', 'center', 'right'], true)) {
            return $position;
        }
        return 'left';
    }

    /**
     * Get desktop logo URL or empty string.
     */
    public function getDesktopLogoUrl(): string
    {
        $id = $this->getDesktopLogoId();
        if ($id <= 0) {
            return '';
        }
        $src = wp_get_attachment_image_url($id, 'full');
        return is_string($src) ? $src : '';
    }

    /**
     * Get mobile logo URL or empty string.
     */
    public function getMobileLogoUrl(): string
    {
        $id = $this->getMobileLogoId();
        if ($id <= 0) {
            return '';
        }
        $src = wp_get_attachment_image_url($id, 'full');
        return is_string($src) ? $src : '';
    }

    /**
     * Get retina logo URL or empty string.
     */
    public function getRetinaLogoUrl(): string
    {
        $id = $this->getRetinaLogoId();
        if ($id <= 0) {
            return '';
        }
        $src = wp_get_attachment_image_url($id, 'full');
        return is_string($src) ? $src : '';
    }

    // ---------------------------------------------------------------
    //  Header Layout
    // ---------------------------------------------------------------

    /**
     * Get the active header layout slug.
     */
    public function getLayout(): string
    {
        $layout = $this->settingsManager->get('header_layout');
        return is_string($layout) && $layout !== '' ? $layout : 'logo-left';
    }

    // ---------------------------------------------------------------
    //  Header Height (responsive)
    // ---------------------------------------------------------------

    /**
     * Get the header height CSS value (base/default).
     */
    public function getHeaderHeight(): string
    {
        $height = $this->settingsManager->get('header_height');
        return is_string($height) && $height !== '' ? $height : '80px';
    }

    /**
     * Get the desktop header height CSS value.
     */
    public function getDesktopHeaderHeight(): string
    {
        $height = $this->settingsManager->get('header_height_desktop');
        return is_string($height) && $height !== '' ? $height : $this->getHeaderHeight();
    }

    /**
     * Get the tablet header height CSS value.
     */
    public function getTabletHeaderHeight(): string
    {
        $height = $this->settingsManager->get('header_height_tablet');
        return is_string($height) && $height !== '' ? $height : $this->getDesktopHeaderHeight();
    }

    /**
     * Get the mobile header height CSS value.
     */
    public function getMobileHeaderHeight(): string
    {
        $height = $this->settingsManager->get('header_height_mobile');
        return is_string($height) && $height !== '' ? $height : '64px';
    }

    // ---------------------------------------------------------------
    //  Header Colors
    // ---------------------------------------------------------------

    public function getHeaderBackgroundColor(): string
    {
        $color = $this->settingsManager->get('header_bg_color');
        return is_string($color) && $color !== '' ? $color : '#1b1a1f';
    }

    public function getHeaderTextColor(): string
    {
        $color = $this->settingsManager->get('header_text_color');
        return is_string($color) && $color !== '' ? $color : '#f5f2f7';
    }

    // ---------------------------------------------------------------
    //  Sticky Header
    // ---------------------------------------------------------------

    public function isStickyEnabled(): bool
    {
        return $this->settingsManager->get('header_sticky') === 'yes';
    }

    // ---------------------------------------------------------------
    //  Search Toggle
    // ---------------------------------------------------------------

    public function showSearch(): bool
    {
        $show = $this->settingsManager->get('header_show_search');
        return $show === 'yes';
    }

    // ---------------------------------------------------------------
    //  CTA Button
    // ---------------------------------------------------------------

    public function showCta(): bool
    {
        return $this->settingsManager->get('header_show_cta') === 'yes';
    }

    public function getCtaLabel(): string
    {
        $label = $this->settingsManager->get('header_cta_label');
        return is_string($label) && $label !== '' ? $label : __('Get Started', 'jasanika');
    }

    public function getCtaUrl(): string
    {
        $url = $this->settingsManager->get('header_cta_url');
        return is_string($url) ? $url : '#';
    }

    public function getCtaStyle(): string
    {
        $style = $this->settingsManager->get('header_cta_style');
        return in_array($style, ['primary', 'secondary', 'outline'], true) ? $style : 'primary';
    }

    // ---------------------------------------------------------------
    //  Top Bar
    // ---------------------------------------------------------------

    public function showTopBar(): bool
    {
        return $this->settingsManager->get('header_show_top_bar') === 'yes';
    }

    public function getTopBarContent(): string
    {
        $content = $this->settingsManager->get('header_top_bar_content');
        return is_string($content) ? $content : '';
    }

    public function getTopBarBackground(): string
    {
        $color = $this->settingsManager->get('header_top_bar_bg');
        return is_string($color) && $color !== '' ? $color : '#24212b';
    }

    public function getTopBarTextColor(): string
    {
        $color = $this->settingsManager->get('header_top_bar_text_color');
        return is_string($color) && $color !== '' ? $color : '#b9b1c4';
    }

    // ---------------------------------------------------------------
    //  Debug Info
    // ---------------------------------------------------------------

    /**
     * Get all header settings for debug output.
     *
     * @return array<string, mixed>
     */
    public function getDebugInfo(): array
    {
        return [
            'Layout'              => $this->getLayout(),
            'Sticky Header'       => $this->isStickyEnabled() ? 'enabled' : 'disabled',
            'Search'              => $this->showSearch() ? 'enabled' : 'disabled',
            'CTA'                 => $this->showCta() ? 'enabled' : 'disabled',
            'Desktop Logo'        => $this->getDesktopLogoId() > 0 ? 'loaded' : 'not set',
            'Mobile Logo'         => $this->getMobileLogoId() > 0 ? 'loaded' : 'not set',
            'Top Bar'             => $this->showTopBar() ? 'enabled' : 'disabled',
            'Header Height'       => $this->getHeaderHeight(),
            'Desktop Height'      => $this->getDesktopHeaderHeight(),
            'Tablet Height'       => $this->getTabletHeaderHeight(),
            'Mobile Height'       => $this->getMobileHeaderHeight(),
            'Header BG Color'     => $this->getHeaderBackgroundColor(),
            'Header Text Color'   => $this->getHeaderTextColor(),
            'CTA Label'           => $this->showCta() ? $this->getCtaLabel() : '—',
            'Top Bar Content'     => $this->showTopBar() ? $this->getTopBarContent() : '—',
        ];
    }
}