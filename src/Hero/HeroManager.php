<?php

declare(strict_types=1);

namespace Jasanika\Hero;

use Jasanika\Admin\SettingsManager;
use Jasanika\Media\MediaManager;

/**
 * Hero configuration manager.
 *
 * Owns all hero-related settings and provides typed accessors
 * for the HeroRenderer. Supports static hero and slider modes.
 *
 * Slider slides are stored as WordPress options (not CPT, not Gutenberg).
 * Foundation only — no visual builder.
 */
final class HeroManager
{
    private SettingsManager $settingsManager;
    private MediaManager $mediaManager;

    public function __construct(SettingsManager $settingsManager, MediaManager $mediaManager)
    {
        $this->settingsManager = $settingsManager;
        $this->mediaManager = $mediaManager;
    }

    /**
     * Whether the hero section is enabled.
     */
    public function isEnabled(): bool
    {
        return $this->settingsManager->get('hero_enabled') === 'yes';
    }

    /**
     * Get the hero type.
     *
     * Returns 'static' or 'slider'.
     */
    public function getHeroType(): string
    {
        $type = $this->settingsManager->get('hero_type');
        return in_array($type, ['static', 'slider'], true) ? $type : 'static';
    }

    /**
     * Get the hero height CSS value.
     */
    public function getHeroHeight(): string
    {
        $height = $this->settingsManager->get('hero_height');
        return is_string($height) && $height !== '' ? $height : '400px';
    }

    /**
     * Get the static hero title.
     */
    public function getHeroTitle(): string
    {
        $title = $this->settingsManager->get('hero_title');
        return is_string($title) ? $title : '';
    }

    /**
     * Get the static hero subtitle.
     */
    public function getHeroSubtitle(): string
    {
        $subtitle = $this->settingsManager->get('hero_subtitle');
        return is_string($subtitle) ? $subtitle : '';
    }

    /**
     * Get the hero background image attachment ID.
     */
    public function getHeroBackgroundImageId(): int
    {
        return (int) $this->settingsManager->get('hero_background_image');
    }

    /**
     * Get the hero background image URL.
     */
    public function getHeroBackgroundImageUrl(): string
    {
        $id = $this->getHeroBackgroundImageId();
        if ($id <= 0) {
            return '';
        }
        return $this->mediaManager->getAttachmentUrl($id);
    }

    /**
     * Get the overlay opacity (0.0 - 1.0).
     */
    public function getOverlayOpacity(): float
    {
        $opacity = $this->settingsManager->get('hero_overlay_opacity');
        $opacity = (float) $opacity;
        return max(0.0, min(1.0, $opacity));
    }

    /**
     * Get the hero button text.
     */
    public function getButtonText(): string
    {
        $text = $this->settingsManager->get('hero_button_text');
        return is_string($text) ? $text : '';
    }

    /**
     * Get the hero button URL.
     */
    public function getButtonUrl(): string
    {
        $url = $this->settingsManager->get('hero_button_url');
        return is_string($url) ? $url : '';
    }

    /**
     * Get all slides for the slider type.
     *
     * @return HeroSlide[]
     */
    public function getSlides(): array
    {
        $slides = [];
        $count = 3;

        for ($i = 1; $i <= $count; $i++) {
            $slide = new HeroSlide(
                $i,
                $this->getSlideSetting('hero_slide_' . $i . '_title'),
                $this->getSlideSetting('hero_slide_' . $i . '_subtitle'),
                (int) $this->getSlideSetting('hero_slide_' . $i . '_image'),
                $this->getSlideSetting('hero_slide_' . $i . '_button_text'),
                $this->getSlideSetting('hero_slide_' . $i . '_button_url')
            );

            if ($slide->hasContent()) {
                $slides[] = $slide;
            }
        }

        return $slides;
    }

    /**
     * Get a single slide by index (1-based).
     */
    public function getSlide(int $index): ?HeroSlide
    {
        $slides = $this->getSlides();

        foreach ($slides as $slide) {
            if ($slide->getIndex() === $index) {
                return $slide;
            }
        }

        return null;
    }

    /**
     * Get a setting value for a slide field.
     */
    private function getSlideSetting(string $key): string
    {
        $value = $this->settingsManager->get($key);
        return is_string($value) ? $value : '';
    }

    /**
     * Get all hero settings for debug output.
     *
     * @return array<string, mixed>
     */
    public function getDebugInfo(): array
    {
        $info = [
            'Enabled'    => $this->isEnabled() ? 'yes' : 'no',
            'Type'       => $this->getHeroType(),
            'Height'     => $this->getHeroHeight(),
            'Title'      => $this->getHeroTitle(),
            'Subtitle'   => $this->getHeroSubtitle(),
            'BG Image'   => $this->getHeroBackgroundImageId(),
            'Overlay'    => (string) $this->getOverlayOpacity(),
            'Button'     => $this->getButtonText() . ' → ' . $this->getButtonUrl(),
        ];

        if ($this->getHeroType() === 'slider') {
            $slides = $this->getSlides();
            $info['Slides'] = count($slides);
            foreach ($slides as $i => $slide) {
                $info['Slide ' . $slide->getIndex()] = $slide->getTitle();
            }
        }

        return $info;
    }
}