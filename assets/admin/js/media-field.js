/**
 * Media Field — WordPress Media Library integration.
 *
 * Provides image selection, preview, and removal functionality
 * for Jasanika media fields in the WordPress admin.
 *
 * Dependencies:
 * - jQuery (WordPress core)
 * - WordPress Media API (enqueued via wp_enqueue_media)
 *
 * Architecture:
 * MediaField (PHP)
 *     ↓
 * HTML markup
 *     ↓
 * This script
 *     ↓
 * WordPress Media API
 */
(function ($) {
    'use strict';

    var frame;
    var currentTarget;

    /**
     * Open the WordPress Media Library frame when the select button is clicked.
     *
     * The frame is created once and reused. The current target is stored
     * when the button is clicked so the select handler knows which field
     * to update.
     */
    $(document).on('click', '.jasanika-media-select', function (e) {
        e.preventDefault();

        var $button = $(this);
        currentTarget = $button.data('target');

        if (frame) {
            frame.open();
            return;
        }

        frame = wp.media({
            title: 'Select Image',
            button: {
                text: 'Use Image'
            },
            multiple: false
        });

        frame.on('select', function () {
            var target = currentTarget;
            var attachment = frame.state().get('selection').first().toJSON();
            var $input = $('#' + target);
            var $container = $input.closest('td');
            var $preview = $container.find('.jasanika-media-preview');
            var $selectBtn = $container.find('.jasanika-media-select');
            var $removeBtn = $container.find('.jasanika-media-remove');
            var imageUrl = attachment.sizes && attachment.sizes.medium
                ? attachment.sizes.medium.url
                : attachment.url;

            $input.val(attachment.id);

            $preview.html(
                '<img src="' + imageUrl + '" style="max-width: 200px; height: auto; border-radius: 4px;" class="jasanika-media-image" />'
            );

            if ($removeBtn.length === 0) {
                $selectBtn.after(
                    ' <button type="button" class="button jasanika-media-remove" data-target="' + target + '">Remove</button>'
                );
            }
        });

        frame.open();
    });

    /**
     * Clear the selected image and remove the preview.
     */
    $(document).on('click', '.jasanika-media-remove', function (e) {
        e.preventDefault();

        var target = $(this).data('target');
        var $container = $(this).closest('td');

        $('#' + target).val('');
        $container.find('.jasanika-media-preview').html('');
        $(this).remove();
    });

})(jQuery);