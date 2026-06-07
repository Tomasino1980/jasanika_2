<?php

declare(strict_types=1);

namespace Jasanika\Footer;

use Jasanika\Admin\SettingsManager;

/**
 * Footer configuration manager.
 *
 * Owns all footer-related settings and provides typed accessors
 * for the rendering pipeline. No rendering logic in this class.
 */
final class FooterManager
{
    private SettingsManager $settingsManager;

    public function __construct(SettingsManager $settingsManager)
    {
        $this->settingsManager = $settingsManager;
    }

    /**
     * Get the footer layout (number of columns).
     *
     * Returns 1, 2, 3, or 4.
     */
    public function getFooterLayout(): int
    {
        $layout = $this->settingsManager->get('footer_layout');
        $layout = (int) $layout;
        return in_array($layout, [1, 2, 3, 4], true) ? $layout : 3;
    }

    /**
     * Get the footer background color.
     */
    public function getFooterBackgroundColor(): string
    {
        $color = $this->settingsManager->get('footer_bg_color');
        return is_string($color) && $color !== '' ? $color : '#1b1a1f';
    }

    /**
     * Get the footer text color.
     */
    public function getFooterTextColor(): string
    {
        $color = $this->settingsManager->get('footer_text_color');
        return is_string($color) && $color !== '' ? $color : '#b9b1c4';
    }

    /**
     * Get the copyright text.
     */
    public function getCopyrightText(): string
    {
        $text = $this->settingsManager->get('footer_copyright_text');
        return is_string($text) && $text !== '' ? $text : '';
    }

    /**
     * Whether the footer menu is shown.
     */
    public function showFooterMenu(): bool
    {
        return $this->settingsManager->get('footer_show_menu') === 'yes';
    }

    /**
     * Whether social icons are shown.
     */
    public function showSocialIcons(): bool
    {
        return $this->settingsManager->get('footer_show_social') === 'yes';
    }

    /**
     * Get all footer settings for debug output.
     *
     * @return array<string, mixed>
     */
    public function getDebugInfo(): array
    {
        return [
            'Footer Layout'      => $this->getFooterLayout() . ' columns',
            'Footer BG Color'    => $this->getFooterBackgroundColor(),
            'Footer Text Color'  => $this->getFooterTextColor(),
            'Copyright Text'     => $this->getCopyrightText(),
            'Show Footer Menu'   => $this->showFooterMenu() ? 'yes' : 'no',
            'Show Social Icons'  => $this->showSocialIcons() ? 'yes' : 'no',
        ];
    }
}