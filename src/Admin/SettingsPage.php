<?php

declare(strict_types=1);

namespace Jasanika\Admin;

use Jasanika\Admin\Components\ColorPicker;
use Jasanika\Admin\Components\CollapsiblePanel;
use Jasanika\Admin\Components\PresetCard;
use Jasanika\Admin\Fields\FieldFactory;
use Jasanika\Admin\Sections\Section;
use Jasanika\Admin\Search\SettingsSearch;
use Jasanika\Admin\UI\Form\FormRow;
use Jasanika\Admin\UI\Form\FormSection;
use Jasanika\Components\ComponentRenderer;
use Jasanika\Core\FrameworkInfo;
use Jasanika\Design\ThemePresetManager;
use Jasanika\Settings\SettingsRegistry;

/**
 * Settings page coordinator — V5 (Unified Form Layout System).
 *
 * M34 — All sections use the unified FormSection + FormRow layout system.
 * Every setting is rendered as a consistent label|input grid row inside
 * a visually distinct section panel.
 *
 * Architecture:
 * 1. Top-level tabs (General, Appearance, Content, Marketing, Advanced)
 * 2. Second-level sub-navigation within each category (section groups)
 * 3. Each section renders inside a FormSection panel
 * 4. Each setting renders as a FormRow (280px label | 1fr input grid)
 * 5. Large sections use CollapsiblePanel grouping
 * 6. Settings Search filters sections and highlights matches
 * 7. Preset selector uses visual PresetCard components
 *
 * Rendering flow:
 * 1. Render top-level tab navigation
 * 2. Determine active tab from URL parameter
 * 3. Render second-level sub-navigation for the active category
 * 4. Determine active sub-section from URL parameter
 * 5. Render search bar
 * 6. Render the active section inside a FormSection
 * 7. Each field wrapped in FormRow with label + description
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
                '__return_empty_string',
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
     * Render the full Settings page with two-level navigation.
     */
    public function render(): void
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        $activeTab = $this->getActiveTab();
        $activeSub = $this->getActiveSubSection($activeTab);

        echo '<div class="wrap jas-settings">';
        echo '<div class="jas-settings__header">';
        echo '<h1>' . esc_html__('Jasanika Settings', 'jasanika') . '</h1>';
        echo '<span class="jas-settings__version">v' . esc_html($this->frameworkInfo->getVersion()) . '</span>';
        echo '</div>';

        // Top-level tab navigation
        $this->renderTabs($activeTab);

        // Second-level sub-navigation
        $this->renderSubNavigation($activeTab, $activeSub);

        // Settings search bar
        SettingsSearch::render();

        // Collapsible panel initialization
        CollapsiblePanel::renderScript();

        echo '<form action="options.php" method="post" class="jas-settings__form">';

        settings_fields('jasanika_settings');

        // Render the active section
        $this->renderActiveSection($activeTab, $activeSub);

        submit_button(__('Save Settings', 'jasanika'));

        echo '</form>';
        echo '</div>';
    }

    // ---------------------------------------------------------------
    //  Navigation
    // ---------------------------------------------------------------

    private function renderTabs(string $activeTab): void
    {
        echo '<nav class="jas-settings__tabs" role="tablist">';

        foreach ($this->categories as $slug => $category) {
            $activeClass = ($slug === $activeTab) ? ' jas-tab--active' : '';
            $url = add_query_arg('tab', $slug);

            printf(
                '<a href="%s" class="jas-tab%s" role="tab" aria-selected="%s" data-tab="%s">%s</a>',
                esc_url($url),
                esc_attr($activeClass),
                $slug === $activeTab ? 'true' : 'false',
                esc_attr($slug),
                esc_html($category['name'])
            );
        }

        echo '</nav>';
    }

    private function renderSubNavigation(string $activeTab, string $activeSub): void
    {
        $category = $this->categories[$activeTab] ?? null;

        if ($category === null || empty($category['sections'])) {
            return;
        }

        if (count($category['sections']) <= 1) {
            return;
        }

        echo '<nav class="jas-settings__sub-tabs">';

        foreach ($category['sections'] as $section) {
            $slug = $section->getSlug();
            $activeClass = ($slug === $activeSub) ? ' jas-sub-tab--active' : '';
            $url = add_query_arg([
                'tab'     => $activeTab,
                'section' => $slug,
            ]);

            $fieldCount = count($section->getSettingKeys());

            printf(
                '<a href="%s" class="jas-sub-tab%s" data-section="%s">%s<span class="jas-sub-tab__count">%d</span></a>',
                esc_url($url),
                esc_attr($activeClass),
                esc_attr($slug),
                esc_html($section->getName()),
                esc_html($fieldCount)
            );
        }

        echo '</nav>';
    }

    // ---------------------------------------------------------------
    //  Section Rendering (M34 Unified Form Layout)
    // ---------------------------------------------------------------

    /**
     * Render the active section using the unified FormSection + FormRow layout.
     *
     * M34: All sections use FormSection panels. Fields inside are
     * rendered as FormRow elements (280px label | 1fr input grid).
     */
    private function renderActiveSection(string $activeTab, string $activeSub): void
    {
        $section = $this->sections[$activeSub] ?? null;

        if ($section === null) {
            $category = $this->categories[$activeTab] ?? null;
            if ($category && !empty($category['sections'])) {
                $section = $category['sections'][0];
            }
        }

        if ($section === null) {
            echo '<p>' . esc_html__('Select a settings category and section.', 'jasanika') . '</p>';
            return;
        }

        $sectionSlug = $section->getSlug();

        // Special sections
        if ($sectionSlug === 'appearance_presets') {
            $this->renderPresetsSection($section);
            return;
        }

        if ($sectionSlug === 'appearance_color_scheme') {
            $this->renderColorSchemeSection($section);
            return;
        }

        // Unified FormSection layout for all other sections
        $settingsKeys = $section->getSettingKeys();
        $isLargeSection = count($settingsKeys) > 6;

        if ($isLargeSection) {
            $this->renderLargeSection($section);
        } else {
            $this->renderStandardSection($section);
        }
    }

    /**
     * Render a standard section (≤ 6 fields) in a FormSection panel.
     */
    private function renderStandardSection(Section $section): void
    {
        $sec = new FormSection($section->getName(), $section->getDescription());
        $sec->start();

        foreach ($section->getSettingKeys() as $key) {
            $setting = $this->settingsRegistry->get($key);

            if ($setting === null) {
                continue;
            }

            $field = $this->fieldFactory->create($setting);

            // Render the field input, then wrap in FormRow
            ob_start();
            $field->render();
            $inputHtml = ob_get_clean();

            FormRow::render(
                $setting->getLabel(),
                $inputHtml,
                '',
                $key
            );
        }

        $sec->end();
    }

    /**
     * Render a large section (> 6 fields) with collapsible groups.
     */
    private function renderLargeSection(Section $section): void
    {
        $sec = new FormSection($section->getName(), $section->getDescription());
        $sec->start();

        $groups = $this->buildFieldGroups($section->getSlug(), $section->getSettingKeys());

        $firstGroup = true;
        foreach ($groups as $groupSlug => $group) {
            $panel = new CollapsiblePanel(
                $section->getSlug() . '__' . $groupSlug,
                $group['label'],
                $firstGroup,
                (string) count($group['keys'])
            );

            $panel->start();

            foreach ($group['keys'] as $key) {
                $setting = $this->settingsRegistry->get($key);

                if ($setting === null) {
                    continue;
                }

                $field = $this->fieldFactory->create($setting);

                ob_start();
                $field->render();
                $inputHtml = ob_get_clean();

                FormRow::render(
                    $setting->getLabel(),
                    $inputHtml,
                    '',
                    $key
                );
            }

            $panel->end();
            $firstGroup = false;
        }

        $sec->end();
    }

    /**
     * Render the Presets section with visual preset card UI.
     */
    private function renderPresetsSection(Section $section): void
    {
        $allPresets   = $this->presetManager->getAllPresets();
        $currentValue = get_option('active_preset', 'default');

        $sec = new FormSection($section->getName(), $section->getDescription());
        $sec->start();

        echo '<div class="jas-presets-grid">';

        foreach ($allPresets as $name => $preset) {
            $isActive = ($name === $currentValue);
            $presetCard = new PresetCard(
                $name,
                $preset['label'],
                $preset['description'],
                $isActive
            );
            $presetCard->render();
        }

        echo '</div>';

        echo '<input type="hidden" name="active_preset" id="jas-active-preset-hidden" value="' . esc_attr($currentValue) . '">';

        $sec->end();

        // Preset card interaction JS
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
                document.querySelectorAll('.jas-preset-card').forEach(function(card) {
                    card.classList.remove('jas-preset-card--active');
                });
                var parent = this.closest('.jas-preset-card');
                if (parent) {
                    parent.classList.add('jas-preset-card--active');
                }
            }
        });
    });
})();
</script>
        <?php
    }

    /**
     * Render the Color Scheme section with palette presets, theme preview,
     * and two-column grid layout.
     */
    private function renderColorSchemeSection(Section $section): void
    {
        $sec = new FormSection($section->getName(), $section->getDescription());
        $sec->start();

        echo '<div class="jas-color-scheme-section">';
        ColorPicker::renderPalettes();
        ColorPicker::renderPreview();

        echo '<div class="jas-color-scheme-grid">';

        foreach ($section->getSettingKeys() as $key) {
            $setting = $this->settingsRegistry->get($key);

            if ($setting === null) {
                continue;
            }

            $field = $this->fieldFactory->create($setting);

            ob_start();
            $field->render();
            $inputHtml = ob_get_clean();

            // Color scheme uses a compact row without description
            FormRow::render(
                $setting->getLabel(),
                $inputHtml,
                '',
                $key
            );
        }

        echo '</div>';
        echo '</div>';

        $sec->end();
    }

    // ---------------------------------------------------------------
    //  Field Groups
    // ---------------------------------------------------------------

    /**
     * Build field groups for a large section based on field key prefixes.
     *
     * @param string   $sectionSlug Section identifier.
     * @param string[] $keys        Setting keys in the section.
     *
     * @return array<string, array{label: string, keys: string[]}>
     */
    private function buildFieldGroups(string $sectionSlug, array $keys): array
    {
        $groups = [];

        if (str_contains($sectionSlug, 'header') && !str_contains($sectionSlug, 'cta') && !str_contains($sectionSlug, 'top_bar')) {
            $groups['general']     = ['label' => 'General',     'keys' => []];
            $groups['layout']      = ['label' => 'Layout',      'keys' => []];
            $groups['navigation']  = ['label' => 'Navigation',  'keys' => []];
            $groups['mobile']      = ['label' => 'Mobile',      'keys' => []];

            foreach ($keys as $key) {
                if (str_contains($key, 'height')) {
                    $groups['layout']['keys'][] = $key;
                } elseif (str_contains($key, 'mobile')) {
                    $groups['mobile']['keys'][] = $key;
                } elseif (str_contains($key, 'search') || str_contains($key, 'sticky') || str_contains($key, 'show_')) {
                    $groups['navigation']['keys'][] = $key;
                } else {
                    $groups['general']['keys'][] = $key;
                }
            }
        } elseif (str_contains($sectionSlug, 'hero')) {
            $groups['content']    = ['label' => 'Content',    'keys' => []];
            $groups['background'] = ['label' => 'Background', 'keys' => []];
            $groups['slides']     = ['label' => 'Slides',     'keys' => []];

            foreach ($keys as $key) {
                if (str_contains($key, 'slide')) {
                    $groups['slides']['keys'][] = $key;
                } elseif (str_contains($key, 'background') || str_contains($key, 'overlay') || str_contains($key, 'image')) {
                    $groups['background']['keys'][] = $key;
                } else {
                    $groups['content']['keys'][] = $key;
                }
            }
        } elseif (str_contains($sectionSlug, 'color_scheme')) {
            $groups['colors'] = ['label' => 'Colors', 'keys' => $keys];
        } else {
            $groups['default'] = ['label' => $section->getName(), 'keys' => $keys];
        }

        foreach ($groups as $slug => $group) {
            if (empty($group['keys'])) {
                unset($groups[$slug]);
            }
        }

        return $groups;
    }

    // ---------------------------------------------------------------
    //  Tab/Sub Navigation Helpers
    // ---------------------------------------------------------------

    private function getActiveTab(): string
    {
        $tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : '';

        if ($tab !== '' && isset($this->categories[$tab])) {
            return $tab;
        }

        $keys = array_keys($this->categories);

        return !empty($keys) ? $keys[0] : 'general';
    }

    private function getActiveSubSection(string $activeTab): string
    {
        $section = isset($_GET['section']) ? sanitize_key($_GET['section']) : '';

        if ($section !== '' && isset($this->sections[$section])) {
            $sectionObj = $this->sections[$section];
            if ($sectionObj->getCategory() === $activeTab) {
                return $section;
            }
        }

        $category = $this->categories[$activeTab] ?? null;

        if ($category && !empty($category['sections'])) {
            return $category['sections'][0]->getSlug();
        }

        return '';
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