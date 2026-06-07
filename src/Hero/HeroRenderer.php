<?php

declare(strict_types=1);

namespace Jasanika\Hero;

use Jasanika\Components\ComponentRenderer;

/**
 * Hero Renderer V2 — M33.
 *
 * Full layout-driven hero rendering with content, background,
 * overlay, and dual button support.
 *
 * Responsibilities:
 * - Render hero based on active layout preset
 * - Render content (title, subtitle, description)
 * - Render primary and secondary buttons using Component Framework
 * - Render background (color, image, gradient)
 * - Render overlay (enabled/disabled with color and opacity)
 * - Render slider type with multiple slides
 * - Apply height modes (auto, medium, large, fullscreen)
 *
 * Layouts:
 * - centered     — Content centered horizontally and vertically
 * - left-aligned — Content aligned to left
 * - split        — Content on left, media placeholder on right
 * - minimal      - Reduced padding and font sizes
 * - fullscreen   - Full viewport height
 *
 * Rendering flow:
 * 1. Read settings via HeroManager
 * 2. Build section element with layout class and background
 * 3. Render overlay (if enabled)
 * 4. Render content area with layout-specific markup
 * 5. Render buttons (primary and/or secondary)
 * 6. Debug output in WP_DEBUG mode
 *
 * Dependencies:
 * - HeroManager (settings access, layout, background, overlay)
 * - ComponentRenderer (button rendering)
 *
 * Used by:
 * - ThemeRenderer (delegated rendering from templates/layout.php)
 *
 * Introduced:
 * - M26 (basic static and slider hero)
 * - M33 (full layout-driven hero with content, background, overlay, buttons)
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
        $layoutClass = $this->heroManager->getLayoutClass();
        $heightCss = $this->heroManager->getHeroHeight();
        $bgCss = $this->heroManager->getBackgroundCss();

        // Build section element with layout classes and inline styles
        $sectionClass = 'jas-hero ' . $layoutClass . ' jas-hero--' . $type;
        $sectionStyle = '';

        if ($heightCss !== 'auto') {
            $sectionStyle .= 'min-height:' . $heightCss . ';';
        }

        if ($bgCss !== '') {
            $sectionStyle .= $bgCss;
        }

        printf(
            '<section id="jas-hero" class="%s" style="%s">',
            esc_attr($sectionClass),
            esc_attr($sectionStyle)
        );

        // Inner wrapper for flex layout
        echo '<div class="jas-hero__inner">';

        if ($type === 'slider') {
            $this->renderSlider();
        } else {
            $this->renderStatic();
        }

        echo '</div>';
        echo '</section>';

        $this->renderDebug();
    }

    /**
     * Render the static hero with layout-driven markup.
     */
    private function renderStatic(): void
    {
        // Overlay
        $this->renderOverlay();

        $layout = $this->heroManager->getLayout();

        echo '<div class="jas-hero__content jas-container">';

        if ($layout === 'split') {
            $this->renderSplitLayout();
        } else {
            $this->renderStandardContent();
        }

        echo '</div>';
    }

    /**
     * Render the standard content block (centered, left-aligned, minimal, fullscreen).
     */
    private function renderStandardContent(): void
    {
        $title = $this->heroManager->getHeroTitle();
        $subtitle = $this->heroManager->getHeroSubtitle();
        $description = $this->heroManager->getHeroDescription();

        echo '<div class="jas-hero__content-block">';

        if ($title !== '') {
            printf('<h1 class="jas-hero__title">%s</h1>', esc_html($title));
        }

        if ($subtitle !== '') {
            printf('<p class="jas-hero__subtitle">%s</p>', esc_html($subtitle));
        }

        if ($description !== '') {
            printf('<div class="jas-hero__description">%s</div>', wp_kses_post(wpautop($description)));
        }

        $this->renderButtons();

        echo '</div>';
    }

    /**
     * Render the split layout (content on left, media placeholder on right).
     */
    private function renderSplitLayout(): void
    {
        $title = $this->heroManager->getHeroTitle();
        $subtitle = $this->heroManager->getHeroSubtitle();
        $description = $this->heroManager->getHeroDescription();

        echo '<div class="jas-hero__split">';
        echo '<div class="jas-hero__split-content">';

        if ($title !== '') {
            printf('<h1 class="jas-hero__title">%s</h1>', esc_html($title));
        }

        if ($subtitle !== '') {
            printf('<p class="jas-hero__subtitle">%s</p>', esc_html($subtitle));
        }

        if ($description !== '') {
            printf('<div class="jas-hero__description">%s</div>', wp_kses_post(wpautop($description)));
        }

        $this->renderButtons();

        echo '</div>'; // .jas-hero__split-content

        // Media/graphic placeholder for split layout
        echo '<div class="jas-hero__split-media">';
        echo '<div class="jas-hero__split-placeholder"></div>';
        echo '</div>';

        echo '</div>'; // .jas-hero__split
    }

    /**
     * Render the overlay (if enabled).
     */
    private function renderOverlay(): void
    {
        if (!$this->heroManager->isOverlayEnabled()) {
            return;
        }

        $css = $this->heroManager->getOverlayCss();

        if ($css !== '') {
            printf(
                '<div class="jas-hero__overlay" style="%s"></div>',
                esc_attr($css)
            );
        }
    }

    /**
     * Render primary and secondary buttons using the Component Framework.
     */
    private function renderButtons(): void
    {
        $btnStyle = $this->heroManager->getButtonStyle();
        $primaryLabel = $this->heroManager->getPrimaryButtonLabel();
        $primaryUrl = $this->heroManager->getPrimaryButtonUrl();
        $secondaryLabel = $this->heroManager->getSecondaryButtonLabel();
        $secondaryUrl = $this->heroManager->getSecondaryButtonUrl();

        if ($primaryLabel === '' && $secondaryLabel === '') {
            return;
        }

        echo '<div class="jas-hero__actions">';

        if ($primaryLabel !== '' && $primaryUrl !== '') {
            $this->componentRenderer->renderButton(
                $btnStyle,
                $primaryLabel,
                $primaryUrl,
                ['class' => 'jas-hero__btn jas-hero__btn--primary']
            );
        }

        if ($secondaryLabel !== '' && $secondaryUrl !== '') {
            $secondaryBtnStyle = $btnStyle === 'primary' ? 'outline' : 'primary';
            $this->componentRenderer->renderButton(
                $secondaryBtnStyle,
                $secondaryLabel,
                $secondaryUrl,
                ['class' => 'jas-hero__btn jas-hero__btn--secondary']
            );
        }

        echo '</div>';
    }

    /**
     * Render the slider hero.
     *
     * @todo M33+: Add slider navigation (arrows, dots) and autoplay support.
     *       Current implementation renders slides stacked for foundation only.
     */
    private function renderSlider(): void
    {
        $slides = $this->heroManager->getSlides();

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

            // Overlay for each slide
            if ($this->heroManager->isOverlayEnabled()) {
                $css = $this->heroManager->getOverlayCss();
                if ($css !== '') {
                    printf('<div class="jas-hero__overlay" style="%s"></div>', esc_attr($css));
                }
            }

            echo '<div class="jas-hero__content jas-container">';
            echo '<div class="jas-hero__content-block">';

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
                    $this->heroManager->getButtonStyle(),
                    $btnText,
                    $btnUrl,
                    ['class' => 'jas-hero__btn']
                );
            }

            echo '</div>';
            echo '</div>';
            echo '</div>';
        }

        echo '</div>';

        // Slider navigation placeholder
        echo '<div class="jas-hero__slider-nav"></div>';
    }

    /**
     * Render debug information when WP_DEBUG is enabled.
     */
    private function renderDebug(): void
    {
        if (!defined('WP_DEBUG') || !WP_DEBUG) {
            return;
        }

        $info = $this->heroManager->getDebugInfo();

        echo '<!--' . "\n";
        echo 'Jasanika Hero Builder' . "\n";
        echo 'Layout: ' . esc_html($info['Layout']) . "\n";
        echo 'Height Mode: ' . esc_html($info['Height Mode']) . "\n";
        echo 'Background Type: ' . esc_html($info['Background Type']) . "\n";
        echo 'Overlay: ' . esc_html($info['Overlay']) . "\n";
        echo 'Title: ' . esc_html($info['Title'] !== '' ? $info['Title'] : '—') . "\n";
        echo 'Primary Button: ' . esc_html($info['Primary Button']) . "\n";
        echo 'Secondary Button: ' . esc_html($info['Secondary Button']) . "\n";
        echo '-->' . "\n";
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