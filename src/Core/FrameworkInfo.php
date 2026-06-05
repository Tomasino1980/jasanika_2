<?php

declare(strict_types=1);

namespace Jasanika\Core;

/**
 * Single source of truth for framework metadata.
 *
 * Provides framework name and version information.
 * Use this service instead of duplicating version strings across classes.
 */
final class FrameworkInfo
{
    private string $name;
    private string $version;

    public function __construct(string $name, string $version)
    {
        $this->name = $name;
        $this->version = $version;
    }

    /**
     * Get the framework name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the framework version.
     */
    public function getVersion(): string
    {
        return $this->version;
    }
}
