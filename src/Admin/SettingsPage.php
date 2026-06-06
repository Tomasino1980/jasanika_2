<?php

declare(strict_types=1);

namespace Jasanika\Admin;

use Jasanika\Admin\Fields\FieldFactory;
use Jasanika\Admin\Fields\FieldInterface;
use Jasanika\Core\FrameworkInfo;
use Jasanika\Settings\SettingsRegistry;

/**
 * Settings page coordinator.
 *
 * Iterates over all registered settings from SettingsRegistry,
 * creates FieldInterface instances via FieldFactory,
 * and registers them with the WordPress Settings API.
 *
 * No hardcoded field definitions exist in this class.
 */
final class SettingsPage
{
    private FrameworkInfo $frameworkInfo;
    private SettingsRegistry $settingsRegistry;
    private FieldFactory $fieldFactory;

    public function __construct(
        FrameworkInfo $frameworkInfo,
        SettingsRegistry $settingsRegistry,
        FieldFactory $fieldFactory
    ) {
        $this->frameworkInfo = $frameworkInfo;
        $this->settingsRegistry = $settingsRegistry;
        $this->fieldFactory = $fieldFactory;
    }

    /**
     * Register settings, sections, and fields using the WordPress Settings API.
     *
     * Iterates over all registered settings, creates field objects via FieldFactory,
     * and registers each with WordPress.
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

        foreach ($this->settingsRegistry->all() as $setting) {
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
            esc_html($this->frameworkInfo->getVersion())
        ) . '</p>';

        echo '<form action="options.php" method="post">';

        settings_fields('jasanika_settings');
        do_settings_sections('jasanika_settings');
        submit_button(__('Save Settings', 'jasanika'));

        echo '</form>';
        echo '</div>';
    }
}