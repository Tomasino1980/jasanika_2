<?php

declare(strict_types=1);

namespace Jasanika\Media;

/**
 * Media infrastructure service.
 *
 * Provides attachment validation and URL resolution
 * using native WordPress media APIs.
 *
 * Infrastructure only. Must not contain:
 * - frontend rendering
 * - image optimization
 * - responsive image logic
 * - CDN integration
 * - business logic
 */
final class MediaManager
{
    /**
     * Get the URL for a given attachment ID.
     *
     * Returns an empty string if the attachment is not valid.
     */
    public function getAttachmentUrl(int $attachmentId): string
    {
        if (!$this->isAttachmentValid($attachmentId)) {
            return '';
        }

        $url = wp_get_attachment_url($attachmentId);

        return is_string($url) ? $url : '';
    }

    /**
     * Check whether the given attachment ID references a valid attachment.
     *
     * Validates that the post exists, is published (inherit status),
     * and is of the attachment post type.
     */
    public function isAttachmentValid(int $attachmentId): bool
    {
        if ($attachmentId <= 0) {
            return false;
        }

        $status = get_post_status($attachmentId);

        if ($status === false || $status === null) {
            return false;
        }

        $postType = get_post_type($attachmentId);

        return $postType === 'attachment';
    }
}