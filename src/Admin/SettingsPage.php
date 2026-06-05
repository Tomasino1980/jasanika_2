<?php

declare(strict_types=1);

namespace Jasanika\Admin;

use Jasanika\Admin\Fields\FieldInterface;

final class SettingsPage
{
    private string $version;

    /** @var FieldInterface[] */
    private array $fields;

    /**
     * @param FieldInterface ...$fields Field objects for rendering and sanitization.
     */
    public function __construct(string $version, FieldInterface ...$fields)
    {
        $this->version = $version;
        $this->fields = $fields;
    }

    /**
     * Register settings, sections, and fields using the WordPress Settings API.
     *
     * Delegates rendering and sanitization to field objects.
     * Hook this into the admin_init action.
     */
    public function registerSettings(): void
    {
        add_settings_section(
            'jasanika_settings_section',
            __('General Settings', 'jasanika'),
            null,
            'jasanika_settings'
        );

        foreach ($this->fields as $field) {
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
                'jasanika_settings_section'
            );
        }
    }

    /**
     * Render the full Settings page.
     *
     * Displays the framework version and a Settings API-powered form.
     * Field rendering is delegated to individual field objects.
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