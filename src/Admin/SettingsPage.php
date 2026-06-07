<?php

declare(strict_types=1);

namespace Jasanika\Admin;

use Jasanika\Admin\Fields\FieldFactory;
use Jasanika\Admin\Sections\Section;
use Jasanika\Components\ComponentRenderer;
use Jasanika\Core\FrameworkInfo;
use Jasanika\Settings\SettingsRegistry;

/**
 * Settings page coordinator — V2 (Site Builder UI).
 *
 * Tabbed settings interface with category-based organization.
 * Uses the ComponentRenderer from M25 for consistent UI rendering.
 *
 * Categories:
 * - General     — Site identity, logo, basic info
 * - Appearance  — Header, Footer, Hero, Colors, Typography, Layout
 * - Content     — Blog, archive, post settings (future)
 * - Marketing   — Social, SEO, analytics (future)
 * - Advanced    — Development, performance, code (future)
 *
 * Rendering flow:
 * 1. Render tab navigation (ComponentRenderer Tabs)
 * 2. Determine active tab from URL parameter
 * 3. Render sections for active category
 * 4. Each section renders its settings fields
 */
final class SettingsPage
{
    private FrameworkInfo $frameworkInfo;
    private SettingsRegistry $settingsRegistry;
    private FieldFactory $fieldFactory;
    private ComponentRenderer $componentRenderer;
    /** @var array<string, array{name: string, sections: Section[]}> */
    private array $categories = [];
    /** @var array<string, Section> */
    private array $sections = [];

    public function __construct(
        FrameworkInfo $frameworkInfo,
        SettingsRegistry $settingsRegistry,
        FieldFactory $fieldFactory,
        ComponentRenderer $componentRenderer
    ) {
        $this->frameworkInfo = $frameworkInfo;
        $this->settingsRegistry = $settingsRegistry;
        $this->fieldFactory = $fieldFactory;
        $this->componentRenderer = $componentRenderer;
    }

    /**
     * Register a settings category with its sections.
     *
     * @param string    $slug     Category slug.
     * @param string    $name     Human-readable category name.
     * @param Section[] $sections Sections belonging to this category.
     */
    public function registerCategory(string $slug, string $name, array $sections = []): void
    {
        $this->categories[$slug] = [
            'name'     => $name,
            'sections' => $sections,
        ];

        foreach ($sections as $section) {
            $this->sections[$section->getSlug()] = $section;
        }
    }

    /**
     * Register a setting section under a category.
     */
    public function registerSection(Section $section): void
    {
        $cat = $section->getCategory();

        if (!isset($this->categories[$cat])) {
            $this->categories[$cat] = [
                'name'     => ucfirst($cat),
                'sections' => [],
            ];
        }

        $this->categories[$cat]['sections'][] = $section;
        $this->sections[$section->getSlug()] = $section;
    }

    /**
     * Register settings, sections, and fields using the WordPress Settings API.
     *
     * Hook this into the admin_init action.
     */
    public function registerSettings(): void
    {
        foreach ($this->sections as $section) {
            $sectionSlug = 'jasanika_section_' . $section->getSlug();

            add_settings_section(
                $sectionSlug,
                $section->getName(),
                function () use ($section): void {
                    $desc = $section->getDescription();
                    if ($desc !== '') {
                        echo '<p class="jas-section__description">' . esc_html($desc) . '</p>';
                    }
                },
                'jasanika_settings'
            );

            foreach ($section->getSettingKeys() as $key) {
                $setting = $this->settingsRegistry->get($key);

                if ($setting === null) {
                    continue;
                }

                $field = $this->fieldFactory->create($setting);

                register_setting(
                    'jasanika_settings',
                    $field->getKey(),
                    [
                        'type'              => 'string',
                        'sanitize_callback'  => [$field, 'sanitize'],
                        'default'            => $field->getDefault(),
                        'show_in_rest'       => false,
                    ]
                );

                add_settings_field(
                    $field->getKey(),
                    $field->getLabel(),
                    [$field, 'render'],
                    'jasanika_settings',
                    $sectionSlug
                );
            }
        }
    }

    /**
     * Render the full Settings page with tabs.
     */
    public function render(): void
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        $activeTab = $this->getActiveTab();

        echo '<div class="wrap jas-settings">';
        echo '<h1>' . esc_html__('Jasanika Settings', 'jasanika') . '</h1>';
        echo '<p class="jas-settings__version">' . sprintf(
            /* translators: %s: framework version number */
            esc_html__('Framework Version: %s', 'jasanika'),
            esc_html($this->frameworkInfo->getVersion())
        ) . '</p>';

        // Tab navigation
        $this->renderTabs($activeTab);

        echo '<form action="options.php" method="post" class="jas-settings__form">';

        settings_fields('jasanika_settings');

        // Render sections for the active category
        $this->renderActiveSections($activeTab);

        submit_button(__('Save Settings', 'jasanika'));

        echo '</form>';
        echo '</div>';
    }

    /**
     * Render tab navigation using ComponentRenderer.
     */
    private function renderTabs(string $activeTab): void
    {
        echo '<nav class="jas-settings__tabs nav-tab-wrapper">';

        foreach ($this->categories as $slug => $category) {
            $activeClass = ($slug === $activeTab) ? ' nav-tab-active' : '';
            $url = add_query_arg('tab', $slug);

            printf(
                '<a href="%s" class="nav-tab%s" data-tab="%s">%s</a>',
                esc_url($url),
                esc_attr($activeClass),
                esc_attr($slug),
                esc_html($category['name'])
            );
        }

        echo '</nav>';
    }

    /**
     * Render sections for the active category.
     *
     * Groups settings fields under section headings and uses
     * ComponentRenderer Card component for each section.
     */
    private function renderActiveSections(string $activeTab): void
    {
        $category = $this->categories[$activeTab] ?? null;

        if ($category === null) {
            echo '<p>' . esc_html__('Select a settings category.', 'jasanika') . '</p>';
            return;
        }

        if (empty($category['sections'])) {
            echo '<p>' . esc_html__('No settings in this category yet.', 'jasanika') . '</p>';
            return;
        }

        foreach ($category['sections'] as $section) {
            $sectionSlug = 'jasanika_section_' . $section->getSlug();

            echo '<div class="jas-settings__section">';

            // Use card component for section wrapping
            ob_start();
            do_settings_fields('jasanika_settings', $sectionSlug);
            $fieldsHtml = ob_get_clean();

            $this->componentRenderer->renderCard(
                $section->getName(),
                $fieldsHtml
            );

            echo '</div>';
        }
    }

    /**
     * Get the active tab from the URL parameter.
     *
     * Defaults to the first registered category.
     * Falls back to 'general' when no categories exist.
     */
    private function getActiveTab(): string
    {
        $tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : '';

        if ($tab !== '' && isset($this->categories[$tab])) {
            return $tab;
        }

        $keys = array_keys($this->categories);

        return !empty($keys) ? $keys[0] : 'general';
    }

    /**
     * Get all registered categories.
     *
     * @return array<string, array{name: string, sections: Section[]}>
     */
    public function getCategories(): array
    {
        return $this->categories;
    }
}