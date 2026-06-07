<?php

declare(strict_types=1);

namespace Jasanika\Admin\Dashboard;

use Jasanika\Admin\SettingsManager;
use Jasanika\Components\ComponentRenderer;
use Jasanika\Design\DesignSettingsManager;
use Jasanika\Design\ThemePresetManager;
use Jasanika\Footer\FooterManager;
use Jasanika\Header\HeaderManager;
use Jasanika\Hero\HeroManager;
use Jasanika\Layout\LayoutManager;

/**
 * Appearance Overview Dashboard.
 *
 * Read-only summary page displaying the current theme appearance
 * configuration. Uses Card components from the Component Framework
 * for consistent UI rendering.
 *
 * M27 — Theme Presets & Settings UX Framework.
 */
final class AppearanceDashboard
{
    private ThemePresetManager $presetManager;
    private DesignSettingsManager $designSettingsManager;
    private ComponentRenderer $componentRenderer;
    private HeaderManager $headerManager;
    private FooterManager $footerManager;
    private HeroManager $heroManager;
    private LayoutManager $layoutManager;
    private SettingsManager $settingsManager;

    public function __construct(
        ThemePresetManager $presetManager,
        DesignSettingsManager $designSettingsManager,
        ComponentRenderer $componentRenderer,
        HeaderManager $headerManager,
        FooterManager $footerManager,
        HeroManager $heroManager,
        LayoutManager $layoutManager,
        SettingsManager $settingsManager
    ) {
        $this->presetManager         = $presetManager;
        $this->designSettingsManager = $designSettingsManager;
        $this->componentRenderer     = $componentRenderer;
        $this->headerManager         = $headerManager;
        $this->footerManager         = $footerManager;
        $this->heroManager           = $heroManager;
        $this->layoutManager         = $layoutManager;
        $this->settingsManager       = $settingsManager;
    }

    /**
     * Render the Appearance Dashboard page.
     */
    public function render(): void
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        echo '<div class="wrap jas-appearance-dashboard">';
        echo '<h1>' . esc_html__('Appearance Overview', 'jasanika') . '</h1>';
        echo '<p class="jas-appearance-dashboard__subtitle">' . esc_html__('Current theme appearance configuration summary.', 'jasanika') . '</p>';

        echo '<div class="jas-appearance-dashboard__grid">';
        $this->renderPresetCard();
        $this->renderColorCard();
        $this->renderTypographyCard();
        $this->renderHeaderCard();
        $this->renderHeroCard();
        $this->renderFooterCard();
        $this->renderLayoutCard();
        $this->renderLogoCard();
        echo '</div>';

        echo '</div>';
    }

    private function renderPresetCard(): void
    {
        $presetName  = $this->presetManager->getActivePresetLabel();
        $description = $this->presetManager->getActivePresetDescription();

        $body = '<dl class="jas-dashboard__list">';
        $body .= '<dt>' . esc_html__('Active Preset', 'jasanika') . '</dt>';
        $body .= '<dd>' . esc_html($presetName) . '</dd>';

        if ($description !== '') {
            $body .= '<dt>' . esc_html__('Description', 'jasanika') . '</dt>';
            $body .= '<dd>' . esc_html($description) . '</dd>';
        }

        $body .= '<dt>' . esc_html__('Mode', 'jasanika') . '</dt>';
        $body .= '<dd>' . ($this->presetManager->isCustomMode() ? 'Custom' : 'Preset') . '</dd>';
        $body .= '</dl>';

        $this->componentRenderer->renderCard(
            __('Theme Preset', 'jasanika'),
            $body
        );
    }

    private function renderColorCard(): void
    {
        $primaryColor = $this->designSettingsManager->getPrimaryColor();

        $body = '<dl class="jas-dashboard__list">';
        $body .= '<dt>' . esc_html__('Primary Color', 'jasanika') . '</dt>';
        $body .= '<dd><span class="jas-dashboard__swatch" style="background:' . esc_attr($primaryColor) . '"></span> ' . esc_html($primaryColor) . '</dd>';
        $body .= '</dl>';

        $this->componentRenderer->renderCard(
            __('Color Scheme', 'jasanika'),
            $body
        );
    }

    private function renderTypographyCard(): void
    {
        $fontFamily = $this->designSettingsManager->getFontFamily();
        $fontKey    = $this->designSettingsManager->getTypographyKey();

        $body = '<dl class="jas-dashboard__list">';
        $body .= '<dt>' . esc_html__('Font Family', 'jasanika') . '</dt>';
        $body .= '<dd style="font-family:' . esc_attr($fontFamily) . '">' . esc_html(ucfirst($fontKey)) . '</dd>';
        $body .= '</dl>';

        $this->componentRenderer->renderCard(
            __('Typography', 'jasanika'),
            $body
        );
    }

    private function renderHeaderCard(): void
    {
        $height = $this->settingsManager->get('header_height');
        $sticky = $this->headerManager->isStickyEnabled();

        $body = '<dl class="jas-dashboard__list">';
        $body .= '<dt>' . esc_html__('Height', 'jasanika') . '</dt>';
        $body .= '<dd>' . esc_html($height ?: '80px') . '</dd>';
        $body .= '<dt>' . esc_html__('Sticky', 'jasanika') . '</dt>';
        $body .= '<dd>' . ($sticky ? __('Enabled', 'jasanika') : __('Disabled', 'jasanika')) . '</dd>';
        $body .= '</dl>';

        $this->componentRenderer->renderCard(
            __('Header', 'jasanika'),
            $body
        );
    }

    private function renderHeroCard(): void
    {
        $enabled = $this->heroManager->isEnabled();
        $type    = $this->heroManager->getHeroType();

        $body = '<dl class="jas-dashboard__list">';
        $body .= '<dt>' . esc_html__('Status', 'jasanika') . '</dt>';
        $body .= '<dd>' . ($enabled ? __('Enabled', 'jasanika') : __('Disabled', 'jasanika')) . '</dd>';

        if ($enabled) {
            $body .= '<dt>' . esc_html__('Type', 'jasanika') . '</dt>';
            $body .= '<dd>' . esc_html(ucfirst($type ?: 'static')) . '</dd>';
            $height = $this->settingsManager->get('hero_height');
            $title  = $this->settingsManager->get('hero_title');
            if ($height) {
                $body .= '<dt>' . esc_html__('Height', 'jasanika') . '</dt>';
                $body .= '<dd>' . esc_html($height) . '</dd>';
            }
            if ($title) {
                $body .= '<dt>' . esc_html__('Title', 'jasanika') . '</dt>';
                $body .= '<dd>' . esc_html($title) . '</dd>';
            }
        }
        $body .= '</dl>';

        $this->componentRenderer->renderCard(
            __('Hero Section', 'jasanika'),
            $body
        );
    }

    private function renderFooterCard(): void
    {
        $layout  = $this->settingsManager->get('footer_layout');
        $columns = $layout ? (int) $layout : 3;
        $menu    = $this->settingsManager->get('footer_show_menu');

        $body = '<dl class="jas-dashboard__list">';
        $body .= '<dt>' . esc_html__('Columns', 'jasanika') . '</dt>';
        $body .= '<dd>' . esc_html((string) $columns) . '</dd>';
        $body .= '<dt>' . esc_html__('Footer Menu', 'jasanika') . '</dt>';
        $body .= '<dd>' . ($menu === 'yes' ? __('Visible', 'jasanika') : __('Hidden', 'jasanika')) . '</dd>';
        $body .= '</dl>';

        $this->componentRenderer->renderCard(
            __('Footer', 'jasanika'),
            $body
        );
    }

    private function renderLayoutCard(): void
    {
        $layout  = $this->designSettingsManager->getSiteLayout();
        $content = $this->designSettingsManager->getLayoutSetting('layout_content_width');

        $body = '<dl class="jas-dashboard__list">';
        $body .= '<dt>' . esc_html__('Site Layout', 'jasanika') . '</dt>';
        $body .= '<dd>' . esc_html(ucfirst(str_replace('-', ' ', $layout))) . '</dd>';

        if ($content !== '') {
            $body .= '<dt>' . esc_html__('Content Width', 'jasanika') . '</dt>';
            $body .= '<dd>' . esc_html($content) . '</dd>';
        }
        $body .= '</dl>';

        $this->componentRenderer->renderCard(
            __('Layout', 'jasanika'),
            $body
        );
    }

    private function renderLogoCard(): void
    {
        $desktopLogo = $this->settingsManager->get('logo_desktop');
        $position    = $this->settingsManager->get('logo_position');

        $body = '<dl class="jas-dashboard__list">';
        $body .= '<dt>' . esc_html__('Desktop Logo', 'jasanika') . '</dt>';
        $body .= '<dd>' . ($desktopLogo ? __('Uploaded', 'jasanika') : __('Not set', 'jasanika')) . '</dd>';
        $body .= '<dt>' . esc_html__('Position', 'jasanika') . '</dt>';
        $body .= '<dd>' . esc_html(ucfirst($position ?: 'left')) . '</dd>';
        $body .= '</dl>';

        $this->componentRenderer->renderCard(
            __('Logo', 'jasanika'),
            $body
        );
    }
}