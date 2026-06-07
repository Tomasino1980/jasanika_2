<?php

declare(strict_types=1);

namespace Jasanika\Hero;

/**
 * Hero slide value object.
 *
 * Represents a single slide in the hero slider.
 * Pure data — no rendering or business logic.
 */
final class HeroSlide
{
    private string $title;
    private string $subtitle;
    private int $imageId;
    private string $buttonText;
    private string $buttonUrl;
    private int $index;

    public function __construct(
        int $index,
        string $title = '',
        string $subtitle = '',
        int $imageId = 0,
        string $buttonText = '',
        string $buttonUrl = ''
    ) {
        $this->index = $index;
        $this->title = $title;
        $this->subtitle = $subtitle;
        $this->imageId = $imageId;
        $this->buttonText = $buttonText;
        $this->buttonUrl = $buttonUrl;
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSubtitle(): string
    {
        return $this->subtitle;
    }

    public function getImageId(): int
    {
        return $this->imageId;
    }

    public function getButtonText(): string
    {
        return $this->buttonText;
    }

    public function getButtonUrl(): string
    {
        return $this->buttonUrl;
    }

    /**
     * Whether this slide has any content.
     */
    public function hasContent(): bool
    {
        return $this->title !== '' || $this->subtitle !== '' || $this->imageId > 0;
    }
}