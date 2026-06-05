<?php

declare(strict_types=1);

namespace Jasanika\Assets;

final class AssetManager
{
    /**
     * @var array<string, Asset>
     */
    private array $styles = [];

    /**
     * @var array<string, Asset>
     */
    private array $scripts = [];

    /**
     * Register a style asset.
     */
    public function registerStyle(Asset $asset, array $dependencies = [], string $media = 'all'): void
    {
        $this->styles[$asset->getHandle()] = $asset;

        wp_register_style(
            $asset->getHandle(),
            $asset->getSource(),
            $dependencies,
            $asset->getVersion(),
            $media
        );
    }

    /**
     * Register a script asset.
     */
    public function registerScript(Asset $asset, array $dependencies = [], bool $inFooter = false): void
    {
        $this->scripts[$asset->getHandle()] = $asset;

        wp_register_script(
            $asset->getHandle(),
            $asset->getSource(),
            $dependencies,
            $asset->getVersion(),
            $inFooter
        );
    }

    /**
     * Enqueue a registered style by handle.
     */
    public function enqueueStyle(string $handle): void
    {
        if (isset($this->styles[$handle])) {
            wp_enqueue_style($handle);
        }
    }

    /**
     * Enqueue a registered script by handle.
     */
    public function enqueueScript(string $handle): void
    {
        if (isset($this->scripts[$handle])) {
            wp_enqueue_script($handle);
        }
    }
}