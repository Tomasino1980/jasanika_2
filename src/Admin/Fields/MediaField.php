<?php

declare(strict_types=1);

namespace Jasanika\Admin\Fields;

/**
 * Media selection field for the WordPress Settings API.
 *
 * Renders a media selection field using the WordPress Media Library.
 * Stores attachment IDs only. URLs, file paths, and image metadata
 * are never stored — WordPress attachment IDs are the single source of truth.
 *
 * Architecture:
 * FieldInterface
 *     ↑
 * AbstractField
 *     ↑
 * MediaField
 */
final class MediaField extends AbstractField
{
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
     */
    public function render(): void
    {
        $current = $this->settingsManager->get($this->key);

        if (!is_string($current)) {
            $current = $this->getDefault();
        }

        $attachmentId = absint($current);

        wp_enqueue_media();

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

        $this->renderMediaScript();
    }

    /**
     * Inline JavaScript for WordPress Media Library integration.
     *
     * Uses the WordPress Media Frame API to allow image selection.
     * jQuery is used because it is required by the WordPress Media Library API.
     */
    private function renderMediaScript(): void
    {
        ?>
        <script type="text/javascript">
        (function($) {
            var frame;
            var target = '<?php echo esc_js($this->key); ?>';

            $('.jasanika-media-select[data-target="' + target + '"]').on('click', function(e) {
                e.preventDefault();

                if (frame) {
                    frame.open();
                    return;
                }

                frame = wp.media({
                    title: '<?php echo esc_js(__('Select Image', 'jasanika')); ?>',
                    button: {
                        text: '<?php echo esc_js(__('Use Image', 'jasanika')); ?>'
                    },
                    multiple: false
                });

                frame.on('select', function() {
                    var attachment = frame.state().get('selection').first().toJSON();
                    var $input = $('#' + target);
                    var $preview = $input.closest('td').find('.jasanika-media-preview');
                    var $removeBtn = $input.closest('td').find('.jasanika-media-remove');

                    $input.val(attachment.id);

                    var imageUrl = attachment.url;
                    if (attachment.sizes && attachment.sizes.medium) {
                        imageUrl = attachment.sizes.medium.url;
                    }

                    $preview.html(
                        '<img src="' + imageUrl + '" style="max-width: 200px; height: auto; border-radius: 4px;" class="jasanika-media-image" />'
                    );

                    if ($removeBtn.length === 0) {
                        $input.closest('td').find('.jasanika-media-select').after(
                            ' <button type="button" class="button jasanika-media-remove" data-target="' + target + '"><?php echo esc_js(__('Remove', 'jasanika')); ?></button>'
                        );

                        $('.jasanika-media-remove[data-target="' + target + '"]').on('click', function() {
                            $('#' + target).val('');
                            $input.closest('td').find('.jasanika-media-preview').html('');
                            $(this).remove();
                        });
                    }
                });

                frame.open();
            });

            $('.jasanika-media-remove[data-target="' + target + '"]').on('click', function() {
                $('#' + target).val('');
                $('#' + target).closest('td').find('.jasanika-media-preview').html('');
                $(this).remove();
            });
        })(jQuery);
        </script>
        <?php
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