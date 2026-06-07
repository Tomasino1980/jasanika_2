<?php

declare(strict_types=1);

namespace Jasanika\Core;

/**
 * Single source of truth for framework metadata.
 *
 * Provides framework name and version information.
 * Use this service instead of duplicating version strings across classes.
 *
 * Responsibilities:
 * - Store framework name
 * - Store framework version
 * - Provide read-only access to both values
 *
 * Dependencies:
 * - None (pure value container)
 *
 * Used by:
 * - Application (bootstrap)
 * - DashboardPage (admin display)
 * - ThemeRenderer (template rendering)
 * - SettingsPage (settings header)
 * - FooterRenderer (copyright output)
 *
 * Introduced:
 * - M1 (Core Foundation)
 *
 * @todo M30+: Consider caching version from style.css header for
 *       automatic version synchronization.
 */
final class FrameworkInfo
{
    private string $name;
    private string $version;

    /**
     * @param string $name    Framework display name (e.g. "Jasanika 2")
     * @param string $version Semantic version string (e.g. "0.28")
     */
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
