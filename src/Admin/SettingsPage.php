<?php

declare(strict_types=1);

namespace Jasanika\Admin;

use Jasanika\Admin\Components\CollapsiblePanel;
use Jasanika\Admin\Components\PresetCard;
use Jasanika\Admin\Fields\FieldFactory;
use Jasanika\Admin\Sections\Section;
use Jasanika\Admin\Search\SettingsSearch;
use Jasanika\Components\ComponentRenderer;
use Jasanika\Core\FrameworkInfo;
use Jasanika\Design\ThemePresetManager;
use Jasanika\Settings\SettingsRegistry;

/**
 * Settings page coordinator — V3 (Theme Presets & Settings UX Framework).
 *
 * M27: Added Search, Collapsible Sections, Preset UI, Color Scheme Builder,
 * Appearance Dashboard integration, and rendering cleanup.
 *
 * Tabbed settings interface with category-based organization.
 * Uses the ComponentRenderer from M25 for consistent UI rendering.
 *
 * Categories:
 * - General     — Site identity, logo, basic info
 * - Appearance  — Presets, Color Scheme, Typography, Header, Footer, Hero, Layout
 * - Content     — Blog, archive, post settings (future)
 * - Marketing   — Social, SEO, analytics (future)
 * - Advanced    — Development, performance, code (future)
 *
 * Rendering flow:
 * 1. Render tab navigation
 * 2. Determine active tab from URL parameter
 * 3. Render search bar
 * 4. Render sections for active category
 * 5. Each section renders its settings fields inside collapsible panels
 */
final class SettingsPage
{
    private FrameworkInfo $frameworkInfo;
    private SettingsRegistry $settingsRegistry;
    private FieldFactory $fieldFactory;
    private ComponentRenderer $componentRenderer;
    private ThemePresetManager $presetManager;
    /** @var array<string, array{name: string, sections: Section[]}> */
    private array $categories = [];
    /** @var array<string, Section> */
    private array $sections = [];

    public function __construct(
        FrameworkInfo $frameworkInfo,
        SettingsRegistry $settingsRegistry,
        FieldFactory $fieldFactory,
        ComponentRenderer $componentRenderer,
        ThemePresetManager $presetManager
    ) {
        $this->frameworkInfo = $frameworkInfo;
        $this->settingsRegistry = $settingsRegistry;
        $this->fieldFactory = $fieldFactory;
        $this->componentRenderer = $componentRenderer;
        $this->presetManager = $presetManager;
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

        // M27: Settings search bar
        SettingsSearch::render();

        // M27: Collapsible panel initialization
        CollapsiblePanel::renderScript();

        echo '<form action="options.php" method="post" class="jas-settings__form">';

        settings_fields('jasanika_settings');

        // Render sections for the active category
        $this->renderActiveSections($activeTab);

        submit_button(__('Save Settings', 'jasanika'));

        echo '</form>';
        echo '</div>';
    }

    /**
     * Render tab navigation.
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
     * M27: Uses collapsible panels, preset cards for the presets section,
     * and consistent card-based field grouping for all other sections.
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

            echo '<div class="jas-settings__section" data-section="' . esc_attr($section->getSlug()) . '">';

            if ($section->getSlug() === 'appearance_presets') {
                // M27: Custom preset card UI instead of standard select field
                $this->renderPresetsSection($section, $sectionSlug);
            } else {
                // M27: Standard section rendered inside collapsible panel
                $panel = new CollapsiblePanel(
                    $section->getSlug(),
                    $section->getName(),
                    true,
                    (string) count($section->getSettingKeys())
                );

                $panel->start();

                ob_start();
                do_settings_fields('jasanika_settings', $sectionSlug);
                $fieldsHtml = ob_get_clean();

                $this->componentRenderer->renderCard(
                    $section->getName(),
                    $fieldsHtml
                );

                $panel->end();
            }

            echo '</div>';
        }
    }

    /**
     * Render the Presets section with custom preset card UI.
     *
     * M27: Replaces standard select field with visual PresetCard components.
     */
    private function renderPresetsSection(Section $section, string $sectionSlug): void
    {
        $activePreset  = $this->presetManager->getActivePreset();
        $allPresets    = $this->presetManager->getAllPresets();
        $currentValue  = get_option('active_preset', 'default');

        echo '<div class="jas-presets-section">';

        echo '<p class="jas-section__description">' . esc_html($section->getDescription()) . '</p>';
        echo '<div class="jas-presets-grid">';

        foreach ($allPresets as $name => $preset) {
            $isActive = ($name === $currentValue);
            $card = new PresetCard(
                $name,
                $preset['label'],
                $preset['description'],
                $isActive
            );
            $card->render();
        }

        echo '</div>'; // .jas-presets-grid

        // Hidden field to submit via WordPress Settings API
        echo '<input type="hidden" name="active_preset" id="jas-active-preset-hidden" value="' . esc_attr($currentValue) . '">';

        echo '</div>'; // .jas-presets-section

        // M27: Inline JS to sync preset radio selection with hidden field
        ?>
<script>
(function() {
    var radios = document.querySelectorAll('.jas-preset-card__input');
    var hidden = document.getElementById('jas-active-preset-hidden');
    if (!radios.length || !hidden) return;

    radios.forEach(function(radio) {
        radio.addEventListener('change', function() {
            if (this.checked) {
                hidden.value = this.value;
                // Update visual active state
                document.querySelectorAll('.jas-preset-card').forEach(function(card) {
                    card.classList.remove('jas-preset-card--active');
                });
                this.closest('.jas-preset-card').classList.add('jas-preset-card--active');
            }
        });
    });
})();
</script>
        <?php

        // Render WordPress Settings API fields (hidden via CSS, kept for compatibility)
        echo '<div style="display:none;">';
        do_settings_fields('jasanika_settings', $sectionSlug);
        echo '</div>';
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