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
     * Register a style asset definition.
     *
     * Stores the asset only. WordPress registration happens later
     * via registerWordPressAssets() during the enqueue hooks.
     */
    public function registerStyle(Asset $asset): void
    {
        $this->styles[$asset->getHandle()] = $asset;
    }

    /**
     * Register a script asset definition.
     *
     * Stores the asset only. WordPress registration happens later
     * via registerWordPressAssets() during the enqueue hooks.
     */
    public function registerScript(Asset $asset): void
    {
        $this->scripts[$asset->getHandle()] = $asset;
    }

    /**
     * Register all stored assets with WordPress.
     *
     * Must be called during a proper WordPress enqueue hook
     * (admin_enqueue_scripts or wp_enqueue_scripts), never during
     * framework bootstrap.
     */
    public function registerWordPressAssets(): void
    {
        foreach ($this->styles as $handle => $asset) {
            wp_register_style(
                $handle,
                $asset->getSource(),
                $asset->getDependencies(),
                $asset->getVersion(),
                $asset->getMedia()
            );
        }

        foreach ($this->scripts as $handle => $asset) {
            wp_register_script(
                $handle,
                $asset->getSource(),
                $asset->getDependencies(),
                $asset->getVersion(),
                $asset->isInFooter()
            );
        }
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