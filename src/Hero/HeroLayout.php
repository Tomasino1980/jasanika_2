<?php

declare(strict_types=1);

namespace Jasanika\Hero;

/**
 * Hero Layout Presets — M33.
 *
 * Defines the available hero layout configurations. Each layout
 * specifies alignment, content width, and visual behavior.
 *
 * Layouts:
 * - centered     — Content centered horizontally and vertically
 * - left-aligned — Content aligned to the left side
 * - split        — Content on left, optional media/graphic on right
 * - minimal      — Slim hero with minimal padding, smaller text
 * - fullscreen   — Full viewport height hero with maximal impact
 *
 * Usage:
 *   $layout = HeroLayout::get('centered');
 *   $all = HeroLayout::getAll();
 */
final class HeroLayout
{
    /** @var array<string, array{label: string, align: string, class: string, description: string}> */
    private static array $layouts = [];

    private static bool $initialized = false;

    /**
     * Initialize layout definitions.
     */
    private static function init(): void
    {
        if (self::$initialized) {
            return;
        }

        self::$initialized = true;

        self::$layouts = [
            'centered' => [
                'label'       => __('Centered', 'jasanika'),
                'align'       => 'center',
                'class'       => 'jas-hero--centered',
                'description' => __('Content centered horizontally and vertically.', 'jasanika'),
            ],
            'left-aligned' => [
                'label'       => __('Left Aligned', 'jasanika'),
                'align'       => 'left',
                'class'       => 'jas-hero--left',
                'description' => __('Content aligned to the left side.', 'jasanika'),
            ],
            'split' => [
                'label'       => __('Split Content', 'jasanika'),
                'align'       => 'left',
                'class'       => 'jas-hero--split',
                'description' => __('Content on the left with space for media on the right.', 'jasanika'),
            ],
            'minimal' => [
                'label'       => __('Minimal', 'jasanika'),
                'align'       => 'center',
                'class'       => 'jas-hero--minimal',
                'description' => __('Slim hero with reduced padding and smaller text.', 'jasanika'),
            ],
            'fullscreen' => [
                'label'       => __('Fullscreen', 'jasanika'),
                'align'       => 'center',
                'class'       => 'jas-hero--fullscreen',
                'description' => __('Full viewport height with maximal visual impact.', 'jasanika'),
            ],
        ];
    }

    /**
     * Get a layout definition by slug.
     *
     * @return array{label: string, align: string, class: string, description: string}|null
     */
    public static function get(string $slug): ?array
    {
        self::init();

        return self::$layouts[$slug] ?? null;
    }

    /**
     * Get the CSS class for a layout slug.
     */
    public static function getClass(string $slug): string
    {
        $layout = self::get($slug);

        return $layout['class'] ?? 'jas-hero--centered';
    }

    /**
     * Get the alignment for a layout slug.
     */
    public static function getAlignment(string $slug): string
    {
        $layout = self::get($slug);

        return $layout['align'] ?? 'center';
    }

    /**
     * Get all registered layout definitions.
     *
     * @return array<string, array{label: string, align: string, class: string, description: string}>
     */
    public static function getAll(): array
    {
        self::init();

        return self::$layouts;
    }

    /**
     * Get all layout slugs and labels for use in a select field.
     *
     * @return array<string, string>
     */
    public static function getOptions(): array
    {
        $options = [];

        foreach (self::getAll() as $slug => $layout) {
            $options[$slug] = $layout['label'];
        }

        return $options;
    }

    /**
     * Get the default layout slug.
     */
    public static function getDefault(): string
    {
        return 'centered';
    }
}