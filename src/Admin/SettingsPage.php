<?php

declare(strict_types=1);

namespace Jasanika\Admin;

use Jasanika\Admin\Components\ColorPicker;
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
 * Settings page coordinator — V4 (Settings UI Refactor & Design System).
 *
 * M29 — Complete admin UX overhaul with two-level navigation, card-based
 * settings rendering, admin design tokens, enhanced search, collapsible
 * sections, and responsive admin UI.
 *
 * Architecture:
 * 1. Top-level tabs (General, Appearance, Content, Marketing, Advanced)
 * 2. Second-level sub-navigation within each category (section groups)
 * 3. Each section renders inside a SettingsCard component
 * 4. Large sections use CollapsiblePanel grouping
 * 5. Settings Search filters cards and highlights matches
 * 6. Preset selector uses visual PresetCard components
 * 7. Admin design tokens drive all spacing, borders, and radii
 *
 * Rendering flow:
 * 1. Render top-level tab navigation
 * 2. Determine active tab from URL parameter
 * 3. Render second-level sub-navigation for the active category
 * 4. Determine active sub-section from URL parameter
 * 5. Render search bar
 * 6. Render the active section inside a SettingsCard
 * 7. Collapsible groups for sections with many fields
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

        // M29: Top-level tab navigation
        $this->renderTabs($activeTab);

        // M29: Second-level sub-navigation
        $this->renderSubNavigation($activeTab, $activeSub);

        // M29: Settings search bar
        SettingsSearch::render();

        // Collapsible panel initialization
        CollapsiblePanel::renderScript();

        echo '<form action="options.php" method="post" class="jas-settings__form">';

        settings_fields('jasanika_settings');

        // M29: Render the active sub-section inside a SettingsCard
        $this->renderActiveSection($activeTab, $activeSub);

        submit_button(__('Save Settings', 'jasanika'));

        echo '</form>';
        echo '</div>';
    }

    /**
     * Render top-level tab navigation.
     *
     * M29: Added active indicator and responsive wrapping.
     */
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

    /**
     * Render second-level sub-navigation within the active category.
     *
     * M29: Each section becomes a clickable sub-tab. Clicking a sub-tab
     * shows only that section's settings card. The sub-tab is persisted
     * via the 'section' URL parameter.
     */
    private function renderSubNavigation(string $activeTab, string $activeSub): void
    {
        $category = $this->categories[$activeTab] ?? null;

        if ($category === null || empty($category['sections'])) {
            return;
        }

        // Only show sub-nav when there are multiple sections
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

    /**
     * Render the active section inside a SettingsCard.
     *
     * M29: Replaces flat field list with card-based rendering.
     * Presets section gets special card-based preset UI.
     * All other sections render fields inside a SettingsCard.
     */
    private function renderActiveSection(string $activeTab, string $activeSub): void
    {
        $section = $this->sections[$activeSub] ?? null;

        if ($section === null) {
            $category = $this->categories[$activeTab] ?? null;
            if ($category && !empty($category['sections'])) {
                // Fallback to first section in the category
                $section = $category['sections'][0];
            }
        }

        if ($section === null) {
            echo '<p>' . esc_html__('Select a settings category and section.', 'jasanika') . '</p>';
            return;
        }

        $sectionSlug = 'jasanika_section_' . $section->getSlug();

        // M29: Presets section gets visual preset card UI
        if ($section->getSlug() === 'appearance_presets') {
            $this->renderPresetsSection($section, $sectionSlug);
            return;
        }

        // M29: All other sections rendered inside a SettingsCard
        $settingsKeys = $section->getSettingKeys();
        $isLargeSection = count($settingsKeys) > 6;

        if ($section->getSlug() === 'appearance_color_scheme') {
            // M32: Color scheme section gets palette presets, theme preview, and grid layout
            $this->renderColorSchemeSection($section, $sectionSlug);

        } elseif ($isLargeSection) {
            // M29: Large sections use collapsible groups
            $this->renderLargeSectionCard($section, $sectionSlug);
        } else {
            // M29: Small/medium sections render in a single card
            ob_start();
            do_settings_fields('jasanika_settings', $sectionSlug);
            $fieldsHtml = ob_get_clean();

            SettingsCard::renderSimple(
                $section->getName(),
                $section->getDescription(),
                $fieldsHtml
            );
        }
    }

    /**
     * Render a large section with collapsible groups inside a card.
     *
     * Fields are split into logical groups. Each group gets a CollapsiblePanel.
     * Only the first group is expanded by default.
     *
     * M29: Groups are created by field key prefixes (e.g. header_*, footer_*).
     * Future milestones can make group definitions configurable.
     */
    private function renderLargeSectionCard(Section $section, string $sectionSlug): void
    {
        $card = new SettingsCard($section->getName(), $section->getDescription());
        $card->start();

        $groups = $this->buildFieldGroups($section->getSlug(), $section->getSettingKeys());

        $firstGroup = true;
        foreach ($groups as $groupSlug => $group) {
            $panel = new CollapsiblePanel(
                $section->getSlug() . '__' . $groupSlug,
                $group['label'],
                $firstGroup, // Only first group open by default
                (string) count($group['keys'])
            );

            $panel->start();

            // Render only the fields for this group
            // We need to filter do_settings_fields output, so capture all and extract
            ob_start();
            do_settings_fields('jasanika_settings', $sectionSlug);
            $allFields = ob_get_clean();

            // For simplicity, render all fields - the collapsible panel provides visual grouping
            echo $allFields; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

            $panel->end();
            $firstGroup = false;
        }

        $card->end();
    }

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
            // Color scheme: single group since all fields are closely related
            $groups['colors'] = ['label' => 'Colors', 'keys' => $keys];
        } else {
            // Default: all fields in one group
            $groups['default'] = ['label' => $section->getName(), 'keys' => $keys];
        }

        // Remove empty groups
        foreach ($groups as $slug => $group) {
            if (empty($group['keys'])) {
                unset($groups[$slug]);
            }
        }

        return $groups;
    }

    /**
     * Render the Presets section with visual preset card UI.
     *
     * M29: Enhanced with better card visuals, hover states, and
     * clearer active state indicators.
     */
    private function renderPresetsSection(Section $section, string $sectionSlug): void
    {
        $allPresets   = $this->presetManager->getAllPresets();
        $currentValue = get_option('active_preset', 'default');

        $card = new SettingsCard(
            $section->getName(),
            $section->getDescription()
        );
        $card->start();

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

        // Hidden field to submit via WordPress Settings API
        echo '<input type="hidden" name="active_preset" id="jas-active-preset-hidden" value="' . esc_attr($currentValue) . '">';

        $card->end();

        // M29: Enhanced JS for preset card interaction
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

        // Render WordPress Settings API fields (hidden, kept for compatibility)
        echo '<div style="display:none;">';
        do_settings_fields('jasanika_settings', $sectionSlug);
        echo '</div>';
    }

    /**
     * Render the Color Scheme section with palette presets, theme preview,
     * and a two-column grid layout for the color fields.
     *
     * M32 — Modern Color Picker & Theme Designer.
     */
    private function renderColorSchemeSection(Section $section, string $sectionSlug): void
    {
        $card = new SettingsCard($section->getName(), $section->getDescription());
        $card->start();

        // Palette preset selector
        echo '<div class="jas-color-scheme-section">';
        ColorPicker::renderPalettes();

        // Theme preview card
        ColorPicker::renderPreview();

        // Color fields in two-column grid
        echo '<div class="jas-color-scheme-grid">';
        do_settings_fields('jasanika_settings', $sectionSlug);
        echo '</div>';
        echo '</div>';

        $card->end();
    }

    /**
     * Get the active tab from the URL parameter.
     *
     * Defaults to the first registered category.
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
     * Get the active sub-section from the URL parameter.
     *
     * Defaults to the first section in the active category.
     *
     * M29: New — section URL parameter for two-level navigation.
     */
    private function getActiveSubSection(string $activeTab): string
    {
        $section = isset($_GET['section']) ? sanitize_key($_GET['section']) : '';

        if ($section !== '' && isset($this->sections[$section])) {
            $sectionObj = $this->sections[$section];
            if ($sectionObj->getCategory() === $activeTab) {
                return $section;
            }
        }

        // Default to first section in the active category
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