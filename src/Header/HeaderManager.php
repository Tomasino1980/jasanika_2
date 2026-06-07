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
 * Settings are managed by the Settings Framework and stored in
 * the WordPress Options API via SettingsManager.
 */
final class HeaderManager
{
    private SettingsManager $settingsManager;

    public function __construct(SettingsManager $settingsManager)
    {
        $this->settingsManager = $settingsManager;
    }

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
     * Get the header height CSS value.
     */
    public function getHeaderHeight(): string
    {
        $height = $this->settingsManager->get('header_height');
        return is_string($height) && $height !== '' ? $height : '80px';
    }

    /**
     * Get the header background color.
     */
    public function getHeaderBackgroundColor(): string
    {
        $color = $this->settingsManager->get('header_bg_color');
        return is_string($color) && $color !== '' ? $color : '#1b1a1f';
    }

    /**
     * Get the header text color.
     */
    public function getHeaderTextColor(): string
    {
        $color = $this->settingsManager->get('header_text_color');
        return is_string($color) && $color !== '' ? $color : '#f5f2f7';
    }

    /**
     * Whether sticky header is enabled.
     */
    public function isStickyEnabled(): bool
    {
        return $this->settingsManager->get('header_sticky') === 'yes';
    }

    /**
     * Whether search is shown in header.
     */
    public function showSearch(): bool
    {
        return $this->settingsManager->get('header_show_search') === 'yes';
    }

    /**
     * Whether top bar is shown.
     */
    public function showTopBar(): bool
    {
        return $this->settingsManager->get('header_show_top_bar') === 'yes';
    }

    /**
     * Get all header settings for debug output.
     *
     * @return array<string, mixed>
     */
    public function getDebugInfo(): array
    {
        return [
            'Logo Desktop'      => $this->getDesktopLogoId(),
            'Logo Width'        => $this->getLogoWidth(),
            'Logo Height'       => $this->getLogoHeight(),
            'Logo Position'     => $this->getLogoPosition(),
            'Header Height'     => $this->getHeaderHeight(),
            'Header BG Color'   => $this->getHeaderBackgroundColor(),
            'Header Text Color' => $this->getHeaderTextColor(),
            'Sticky Header'     => $this->isStickyEnabled() ? 'yes' : 'no',
            'Show Search'       => $this->showSearch() ? 'yes' : 'no',
            'Show Top Bar'      => $this->showTopBar() ? 'yes' : 'no',
        ];
    }
}