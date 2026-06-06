<?php

declare(strict_types=1);

/**
 * Media configuration.
 *
 * Central location for media-related configuration values.
 * Keep lightweight. No speculative or future-only options.
 *
 * @see \Jasanika\Media\MediaManager
 */
return [
    /**
     * Default WordPress image size used for admin previews.
     *
     * Supported values: 'thumbnail', 'medium', 'medium_large', 'large', 'full'.
     */
    'preview_size' => 'medium',
];