<?php

declare(strict_types=1);

namespace Jasanika\Admin;

final class SettingsPage
{
    private string $version;
    private SettingsManager $settingsManager;

    /**
     * @var string[] Allowed values for the site_layout setting.
     */
    private const ALLOWED_LAYOUTS = ['full-width', 'boxed'];

    public function __construct(string $version, SettingsManager $settingsManager)
    {
        $this->version = $version;
        $this->settingsManager = $settingsManager;
    }

    /**
     * Register the setting, section, and field using the WordPress Settings API.
     *
     * Hook this into the admin_init action.
     */
    public function registerSettings(): void
    {
        register_setting(
            'jasanika_settings',
            'site_layout',
            [
                'type'              => 'string',
                'sanitize_callback'  => [$this, 'sanitizeSiteLayout'],
                'default'            => 'full-width',
                'show_in_rest'       => false,
            ]
        );

        add_settings_section(
            'jasanika_settings_section',
            __('General Settings', 'jasanika'),
            null,
            'jasanika_settings'
        );

        add_settings_field(
            'site_layout',
            __('Site Layout', 'jasanika'),
            [$this, 'renderSiteLayoutField'],
            'jasanika_settings',
            'jasanika_settings_section'
        );
    }

    /**
     * Sanitize the site_layout value.
     *
     * Only allows values from the ALLOWED_LAYOUTS list.
     * Falls back to 'full-width' on invalid input.
     */
    public function sanitizeSiteLayout(mixed $value): string
    {
        $value = is_string($value) ? sanitize_text_field($value) : '';

        if (!in_array($value, self::ALLOWED_LAYOUTS, true)) {
            return 'full-width';
        }

        return $value;
    }

    /**
     * Render the Site Layout select field.
     */
    public function renderSiteLayoutField(): void
    {
        $current = $this->settingsManager->get('site_layout');

        if (!is_string($current) || !in_array($current, self::ALLOWED_LAYOUTS, true)) {
            $current = 'full-width';
        }

        echo '<select id="site_layout" name="site_layout">';

        foreach (self::ALLOWED_LAYOUTS as $layout) {
            printf(
                '<option value="%s" %s>%s</option>',
                esc_attr($layout),
                selected($current, $layout, false),
                esc_html(ucfirst(str_replace('-', ' ', $layout)))
            );
        }

        echo '</select>';
        echo '<p class="description">' . esc_html__('Select the layout style for your site.', 'jasanika') . '</p>';
    }

    /**
     * Render the full Settings page.
     *
     * Displays the framework version and a Settings API-powered form.
     */
    public function render(): void
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        echo '<div class="wrap">';
        echo '<h1>' . esc_html__('Jasanika Settings', 'jasanika') . '</h1>';
        echo '<p>' . sprintf(
            /* translators: %s: framework version number */
            esc_html__('Framework Version: %s', 'jasanika'),
            esc_html($this->version)
        ) . '</p>';

        echo '<form action="options.php" method="post">';

        settings_fields('jasanika_settings');
        do_settings_sections('jasanika_settings');
        submit_button(__('Save Settings', 'jasanika'));

        echo '</form>';
        echo '</div>';
    }
}