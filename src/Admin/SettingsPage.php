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

    /**
     * @var string[] Allowed values for the typography setting.
     */
    private const ALLOWED_TYPOGRAPHY = ['system', 'playfair', 'inter', 'monospace'];

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

        register_setting(
            'jasanika_settings',
            'primary_color',
            [
                'type'              => 'string',
                'sanitize_callback'  => [$this, 'sanitizePrimaryColor'],
                'default'            => '#2c3e50',
                'show_in_rest'       => false,
            ]
        );

        register_setting(
            'jasanika_settings',
            'typography',
            [
                'type'              => 'string',
                'sanitize_callback'  => [$this, 'sanitizeTypography'],
                'default'            => 'system',
                'show_in_rest'       => false,
            ]
        );

        register_setting(
            'jasanika_settings',
            'container_width',
            [
                'type'              => 'string',
                'sanitize_callback'  => [$this, 'sanitizeContainerWidth'],
                'default'            => '1200',
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

        add_settings_field(
            'primary_color',
            __('Primary Color', 'jasanika'),
            [$this, 'renderPrimaryColorField'],
            'jasanika_settings',
            'jasanika_settings_section'
        );

        add_settings_field(
            'typography',
            __('Typography', 'jasanika'),
            [$this, 'renderTypographyField'],
            'jasanika_settings',
            'jasanika_settings_section'
        );

        add_settings_field(
            'container_width',
            __('Container Width', 'jasanika'),
            [$this, 'renderContainerWidthField'],
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
     * Sanitize the primary_color value.
     *
     * Strips non-hex characters and ensures a valid hex color with hash prefix.
     * Falls back to the default on invalid input.
     */
    public function sanitizePrimaryColor(mixed $value): string
    {
        $value = is_string($value) ? sanitize_text_field($value) : '';

        $value = ltrim($value, '#');
        $value = preg_replace('/[^0-9a-fA-F]/', '', $value);

        if (strlen($value) !== 6) {
            return '#2c3e50';
        }

        return '#' . strtolower($value);
    }

    /**
     * Render the Primary Color text field.
     */
    public function renderPrimaryColorField(): void
    {
        $current = $this->settingsManager->get('primary_color');

        if (!is_string($current)) {
            $current = '#2c3e50';
        }

        printf(
            '<input type="text" id="primary_color" name="primary_color" value="%s" class="regular-text" />',
            esc_attr($current)
        );
        echo '<p class="description">' . esc_html__('Enter a hex color for the primary theme color (e.g. #2c3e50).', 'jasanika') . '</p>';
    }

    /**
     * Sanitize the typography value.
     *
     * Only allows values from the ALLOWED_TYPOGRAPHY list.
     * Falls back to 'system' on invalid input.
     */
    public function sanitizeTypography(mixed $value): string
    {
        $value = is_string($value) ? sanitize_text_field($value) : '';

        if (!in_array($value, self::ALLOWED_TYPOGRAPHY, true)) {
            return 'system';
        }

        return $value;
    }

    /**
     * Render the Typography select field.
     */
    public function renderTypographyField(): void
    {
        $current = $this->settingsManager->get('typography');

        if (!is_string($current) || !in_array($current, self::ALLOWED_TYPOGRAPHY, true)) {
            $current = 'system';
        }

        echo '<select id="typography" name="typography">';

        foreach (self::ALLOWED_TYPOGRAPHY as $option) {
            printf(
                '<option value="%s" %s>%s</option>',
                esc_attr($option),
                selected($current, $option, false),
                esc_html(ucfirst($option))
            );
        }

        echo '</select>';
        echo '<p class="description">' . esc_html__('Choose the typography style for your site.', 'jasanika') . '</p>';
    }

    /**
     * Sanitize the container_width value.
     *
     * Ensures a positive numeric string with a maximum reasonable value.
     * Falls back to '1200' on invalid input.
     */
    public function sanitizeContainerWidth(mixed $value): string
    {
        $value = is_string($value) ? sanitize_text_field($value) : '';

        $value = preg_replace('/[^0-9]/', '', $value);

        if ($value === '' || (int) $value < 1 || (int) $value > 9999) {
            return '1200';
        }

        return $value;
    }

    /**
     * Render the Container Width text field.
     */
    public function renderContainerWidthField(): void
    {
        $current = $this->settingsManager->get('container_width');

        if (!is_string($current)) {
            $current = '1200';
        }

        printf(
            '<input type="text" id="container_width" name="container_width" value="%s" class="small-text" />',
            esc_attr($current)
        );
        echo '<p class="description">' . esc_html__('Set the maximum container width in pixels (e.g. 1200).', 'jasanika') . '</p>';
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