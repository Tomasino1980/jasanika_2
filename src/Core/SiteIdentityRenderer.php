<?php

declare(strict_types=1);

namespace Jasanika\Core;

use Jasanika\Admin\SettingsManager;
use Jasanika\Media\MediaManager;

/**
 * Centralized site identity rendering.
 *
 * Responsibilities:
 * - Render site logo (from LogoSetting)
 * - Render site title (bloginfo)
 * - Render site tagline (bloginfo)
 * - Handle fallback behaviour
 *
 * Priority:
 * 1. Logo Setting → Image from Media Library
 * 2. Site Title (bloginfo name)
 * 3. Fallback text
 *
 * Templates call into this service through ThemeRenderer.
 * No direct bloginfo() calls outside this class.
 */
final class SiteIdentityRenderer
{
    private SettingsManager $settingsManager;
    private MediaManager $mediaManager;

    public function __construct(
        SettingsManager $settingsManager,
        MediaManager $mediaManager
    ) {
        $this->settingsManager = $settingsManager;
        $this->mediaManager = $mediaManager;
    }

    /**
     * Render complete site branding block.
     *
     * Outputs logo (or fallback site title) with site tagline.
     * Priority: Logo → Site Title → Fallback Text
     */
    public function renderBranding(): void
    {
        echo '<div class="jas-branding">';

        $this->renderLogoOrTitle();

        echo '</div>';
    }

    /**
     * Render the site logo or fallback title.
     *
     * Priority:
     * 1. Uploaded logo (LogoSetting attachment ID)
     * 2. Site title (bloginfo name)
     */
    private function renderLogoOrTitle(): void
    {
        $logoId = (int) $this->settingsManager->get('logo');

        if ($logoId > 0 && $this->mediaManager->isAttachmentValid($logoId)) {
            $this->renderLogoImage($logoId);
            return;
        }

        $this->renderSiteTitle();
    }

    /**
     * Render the logo image from a Media Library attachment.
     *
     * Uses WordPress wp_get_attachment_image() for responsive
     * image markup with proper alt attribute handling.
     */
    private function renderLogoImage(int $attachmentId): void
    {
        $image = wp_get_attachment_image(
            $attachmentId,
            'full',
            false,
            [
                'class'   => 'jas-branding__logo',
                'alt'     => get_bloginfo('name', 'display'),
                'loading' => 'eager',
                'decoding' => 'async',
            ]
        );

        if (empty($image)) {
            $this->renderSiteTitle();
            return;
        }

        printf(
            '<a href="%s" class="jas-branding__link" rel="home">%s</a>',
            esc_url(home_url('/')),
            $image // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        );
    }

    /**
     * Render the site title as a text fallback.
     *
     * Uses bloginfo('name') for the site title.
     * Wraps in an h1 on the front page, otherwise a p.
     */
    public function renderSiteTitle(): void
    {
        $name = get_bloginfo('name', 'display');

        if (empty($name)) {
            $name = 'Jasanika';
        }

        $tag = is_front_page() && is_home() ? 'h1' : 'p';

        printf(
            '<%1$s class="jas-branding__title">' .
            '<a href="%2$s" class="jas-branding__link" rel="home">%3$s</a>' .
            '</%1$s>',
            esc_attr($tag),
            esc_url(home_url('/')),
            esc_html($name)
        );
    }

    /**
     * Render the site tagline.
     *
     * Uses bloginfo('description').
     * Outputs nothing if the tagline is empty.
     */
    public function renderSiteTagline(): void
    {
        $description = get_bloginfo('description', 'display');

        if (empty($description)) {
            return;
        }

        printf(
            '<p class="jas-branding__tagline">%s</p>',
            esc_html($description)
        );
    }
}
