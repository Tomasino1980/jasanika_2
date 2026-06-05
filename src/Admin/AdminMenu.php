<?php

declare(strict_types=1);

namespace Jasanika\Admin;

use Jasanika\Hooks\HookManager;

final class AdminMenu
{
    private string $version;

    /** @var AdminPage[] */
    private array $pages = [];

    public function __construct(string $version)
    {
        $this->version = $version;
    }

    public function registerPage(AdminPage $page): void
    {
        $this->pages[] = $page;
    }

    public function register(HookManager $hooks): void
    {
        $hooks->addAction('admin_menu', [$this, 'registerPages']);
    }

    /**
     * WordPress admin_menu hook callback.
     *
     * Registers all stored pages using the native WordPress Admin Menu API.
     */
    public function registerPages(): void
    {
        foreach ($this->pages as $page) {
            add_menu_page(
                $page->getPageTitle(),
                $page->getPageTitle(),
                'manage_options',
                $page->getSlug(),
                $page->getCallback(),
                '',
                3
            );
        }
    }

    /**
     * Default dashboard page callback.
     *
     * Renders the Jasanika Dashboard with framework name and version.
     */
    public function renderDashboard(): void
    {
        echo '<div class="wrap">';
        echo '<h1>' . esc_html__('Jasanika Framework', 'jasanika') . '</h1>';
        echo '<p>' . sprintf(
            /* translators: %s: framework version number */
            esc_html__('Version: %s', 'jasanika'),
            esc_html($this->version)
        ) . '</p>';
        echo '</div>';
    }
}