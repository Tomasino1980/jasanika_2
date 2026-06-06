<?php

declare(strict_types=1);

namespace Jasanika\Admin\Fields;

use Jasanika\Admin\SettingsManager;
use Jasanika\Assets\AssetManager;

/**
 * Media selection field for the WordPress Settings API.
 *
 * Renders a media selection field using the WordPress Media Library.
 * Stores attachment IDs only. URLs, file paths, and image metadata
 * are never stored — WordPress attachment IDs are the single source of truth.
 *
 * JavaScript for Media Library integration is loaded from a dedicated
 * asset file via the AssetManager.
 *
 * Architecture:
 * FieldInterface
 *     ↑
 * AbstractField
 *     ↑
 * MediaField
 *
 * Flow:
 * MediaField::render()
 *     ↓
 * wp_enqueue_media() — WordPress Media API
 *     ↓
 * AssetManager::enqueueScript() — media-field.js
 *     ↓
 * HTML output (hidden input, preview, buttons)
 */
final class MediaField extends AbstractField
{
    private AssetManager $assetManager;

    public function __construct(
        string $key,
        string $label,
        SettingsManager $settingsManager,
        AssetManager $assetManager,
        ?string $default = null,
        string $description = ''
    ) {
        parent::__construct($key, $label, $settingsManager, $default, $description);

        $this->assetManager = $assetManager;
    }

    public function getDefault(): string
    {
        if ($this->default !== null) {
            return $this->default;
        }

        $resolved = $this->settingsManager->get($this->key);

        if (is_string($resolved) && $resolved !== '') {
            return $resolved;
        }

        return '';
    }

    /**
     * Render the media selection field.
     *
     * Outputs a hidden input for the attachment ID,
     * a preview of the selected image (if one exists),
     * and a "Select Image" button that opens the WordPress Media Library.
     *
     * Media Library JavaScript is loaded via AssetManager,
     * not inline — ensuring separation of concerns.
     */
    public function render(): void
    {
        $current = $this->settingsManager->get($this->key);

        if (!is_string($current)) {
            $current = $this->getDefault();
        }

        $attachmentId = absint($current);

        // WordPress Media API must be available before the external script runs.
        wp_enqueue_media();

        // Load the dedicated Media Library integration script.
        $this->assetManager->enqueueScript('jasanika-media-field');

        printf(
            '<input type="hidden" id="%s" name="%s" value="%s" class="jasanika-media-input" />',
            esc_attr($this->key),
            esc_attr($this->key),
            esc_attr((string) $attachmentId)
        );

        echo '<div class="jasanika-media-preview" style="margin-bottom: 8px;">';

        if ($attachmentId > 0 && wp_get_attachment_image($attachmentId)) {
            echo wp_get_attachment_image(
                $attachmentId,
                'medium',
                false,
                [
                    'style' => 'max-width: 200px; height: auto; border-radius: 4px;',
                    'class' => 'jasanika-media-image',
                ]
            );
        }

        echo '</div>';

        printf(
            '<button type="button" class="button jasanika-media-select" data-target="%s">%s</button>',
            esc_attr($this->key),
            esc_html__('Select Image', 'jasanika')
        );

        if ($attachmentId > 0) {
            printf(
                ' <button type="button" class="button jasanika-media-remove" data-target="%s">%s</button>',
                esc_attr($this->key),
                esc_html__('Remove', 'jasanika')
            );
        }

        echo '<p class="description">' . esc_html($this->description) . '</p>';
    }

    /**
     * Sanitize the attachment ID value.
     *
     * Ensures only valid numeric attachment IDs are stored.
     * Returns an empty string for invalid values.
     */
    public function sanitize(mixed $value): string
    {
        $attachmentId = absint($value);

        if ($attachmentId === 0) {
            return $this->getDefault();
        }

        return (string) $attachmentId;
    }
}