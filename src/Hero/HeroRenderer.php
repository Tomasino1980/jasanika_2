<?php

declare(strict_types=1);

namespace Jasanika\Hero;

use Jasanika\Components\ComponentRenderer;

/**
 * Hero renderer.
 *
 * Owns all hero rendering logic. Uses HeroManager for configuration
 * values and ComponentRenderer for button rendering.
 *
 * Responsibilities:
 * - Render static hero with title, subtitle, background, overlay, and CTA button
 * - Render slider hero with multiple slides (stacked, no carousel JS yet)
 * - Skip output when hero is disabled
 *
 * Rendering modes:
 * - Static: Single hero with optional background image and overlay
 * - Slider: Multiple slides with per-slide content, background, and CTA
 *
 * Dependencies:
 * - HeroManager (settings access, slide data)
 * - ComponentRenderer (CTA button rendering)
 *
 * Used by:
 * - ThemeRenderer (delegated rendering from templates/layout.php)
 *
 * Introduced:
 * - M26 (Site Builder Foundation)
 *
 * @todo M30+: Add slider navigation (arrows, dots) and autoplay support.
 *       Current implementation renders slides stacked for foundation only.
 * @todo M30+: Consider extracting slide rendering into a dedicated
 *       HeroSlideRenderer for Single Responsibility compliance.
 */
final class HeroRenderer
{
    private HeroManager $heroManager;
    private ComponentRenderer $componentRenderer;

    public function __construct(HeroManager $heroManager, ComponentRenderer $componentRenderer)
    {
        $this->heroManager = $heroManager;
        $this->componentRenderer = $componentRenderer;
    }

    /**
     * Render the hero section.
     *
     * Outputs nothing if hero is disabled.
     * Called from templates/layout.php via ThemeRenderer.
     */
    public function render(): void
    {
        if (!$this->heroManager->isEnabled()) {
            return;
        }

        $type = $this->heroManager->getHeroType();

        echo '<section id="jas-hero" class="jas-hero jas-hero--' . esc_attr($type) . '">';
        echo '<div class="jas-hero__inner" style="min-height:' . esc_attr($this->heroManager->getHeroHeight()) . ';">';

        if ($type === 'slider') {
            $this->renderSlider();
        } else {
            $this->renderStatic();
        }

        echo '</div>';
        echo '</section>';
    }

    /**
     * Render the static hero.
     */
    private function renderStatic(): void
    {
        $bgUrl = $this->heroManager->getHeroBackgroundImageUrl();
        $overlay = $this->heroManager->getOverlayOpacity();
        $title = $this->heroManager->getHeroTitle();
        $subtitle = $this->heroManager->getHeroSubtitle();
        $buttonText = $this->heroManager->getButtonText();
        $buttonUrl = $this->heroManager->getButtonUrl();

        $bgStyle = '';

        if ($bgUrl !== '') {
            $bgStyle = sprintf(
                'background-image:url(%s);background-size:cover;background-position:center;',
                esc_url($bgUrl)
            );
        }

        echo '<div class="jas-hero__slide" style="' . $bgStyle . '">';

        if ($overlay > 0) {
            printf(
                '<div class="jas-hero__overlay" style="opacity:%s;"></div>',
                esc_attr((string) $overlay)
            );
        }

        echo '<div class="jas-hero__content jas-container">';

        if ($title !== '') {
            printf('<h1 class="jas-hero__title">%s</h1>', esc_html($title));
        }

        if ($subtitle !== '') {
            printf('<p class="jas-hero__subtitle">%s</p>', esc_html($subtitle));
        }

        if ($buttonText !== '' && $buttonUrl !== '') {
            $this->componentRenderer->renderButton(
                'primary',
                $buttonText,
                $buttonUrl,
                ['class' => 'jas-hero__btn']
            );
        }

        echo '</div>';
        echo '</div>';
    }

    /**
     * Render the slider hero.
     *
     * @todo M30+: Add slider navigation (arrows, dots) and autoplay support.
     *       Current implementation renders slides stacked for foundation only.
     */
    private function renderSlider(): void
    {
        $slides = $this->heroManager->getSlides();
        $overlay = $this->heroManager->getOverlayOpacity();

        if (empty($slides)) {
            $this->renderStatic();
            return;
        }

        echo '<div class="jas-hero__slider">';

        foreach ($slides as $slide) {
            $bgUrl = '';

            if ($slide->getImageId() > 0) {
                $bgUrl = wp_get_attachment_url($slide->getImageId());
            }

            $bgStyle = '';

            if ($bgUrl !== '' && is_string($bgUrl)) {
                $bgStyle = sprintf(
                    'background-image:url(%s);background-size:cover;background-position:center;',
                    esc_url($bgUrl)
                );
            }

            printf(
                '<div class="jas-hero__slide" data-slide="%d" style="%s">',
                $slide->getIndex(),
                $bgStyle
            );

            if ($overlay > 0) {
                printf(
                    '<div class="jas-hero__overlay" style="opacity:%s;"></div>',
                    esc_attr((string) $overlay)
                );
            }

            echo '<div class="jas-hero__content jas-container">';

            $slideTitle = $slide->getTitle();
            if ($slideTitle !== '') {
                printf('<h2 class="jas-hero__title">%s</h2>', esc_html($slideTitle));
            }

            $slideSubtitle = $slide->getSubtitle();
            if ($slideSubtitle !== '') {
                printf('<p class="jas-hero__subtitle">%s</p>', esc_html($slideSubtitle));
            }

            $btnText = $slide->getButtonText();
            $btnUrl = $slide->getButtonUrl();
            if ($btnText !== '' && $btnUrl !== '') {
                $this->componentRenderer->renderButton(
                    'primary',
                    $btnText,
                    $btnUrl,
                    ['class' => 'jas-hero__btn']
                );
            }

            echo '</div>';
            echo '</div>';
        }

        echo '</div>';

        // Slider navigation placeholder
        echo '<div class="jas-hero__slider-nav"></div>';
    }

    /**
     * Get the HeroManager instance.
     */
    public function getManager(): HeroManager
    {
        return $this->heroManager;
    }

    /**
     * Get the ComponentRenderer instance.
     */
    public function getComponentRenderer(): ComponentRenderer
    {
        return $this->componentRenderer;
    }
}