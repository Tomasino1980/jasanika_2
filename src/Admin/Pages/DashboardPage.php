<?php

declare(strict_types=1);

namespace Jasanika\Admin\Pages;

use Jasanika\Core\FrameworkInfo;

/**
 * Dashboard page renderer for the Jasanika Framework admin.
 *
 * Responsible for rendering the main Jasanika Dashboard page content.
 * This class is used as the callback for the top-level admin menu page.
 */
final class DashboardPage
{
    private FrameworkInfo $frameworkInfo;

    public function __construct(FrameworkInfo $frameworkInfo)
    {
        $this->frameworkInfo = $frameworkInfo;
    }

    /**
     * Render the Dashboard page.
     *
     * Outputs the framework name and version within a WordPress admin wrap.
     */
    public function render(): void
    {
        echo '<div class="wrap">';
        echo '<h1>' . esc_html__('Jasanika Framework', 'jasanika') . '</h1>';
        echo '<p>' . sprintf(
            /* translators: %s: framework version number */
            esc_html__('Version: %s', 'jasanika'),
            esc_html($this->frameworkInfo->getVersion())
        ) . '</p>';
        echo '</div>';
    }
}
