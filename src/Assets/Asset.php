<?php

declare(strict_types=1);

namespace Jasanika\Assets;

final class Asset
{
    /**
     * @param string   $handle       Unique asset handle.
     * @param string   $source       Asset URL or path.
     * @param string   $version      Asset version string.
     * @param string[] $dependencies Handles of registered dependencies.
     * @param string   $media        CSS media type (for styles only).
     * @param bool     $inFooter     Whether to enqueue in footer (for scripts only).
     */
    public function __construct(
        private readonly string $handle,
        private readonly string $source,
        private readonly string $version = '1.0.0',
        private readonly array $dependencies = [],
        private readonly string $media = 'all',
        private readonly bool $inFooter = false,
    ) {
    }

    public function getHandle(): string
    {
        return $this->handle;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    public function getMedia(): string
    {
        return $this->media;
    }

    public function isInFooter(): bool
    {
        return $this->inFooter;
    }
}
