<?php

declare(strict_types=1);

namespace Jasanika\Core;

/**
 * Centralized content renderer.
 *
 * Responsibilities:
 * - Render page/post titles
 * - Render post content (the_content)
 * - Render excerpts
 * - Render post metadata (date, author, categories, tags)
 * - Handle empty content states
 *
 * All rendering methods use WordPress native APIs.
 * No direct WordPress Loop logic outside this class.
 * Templates call these static methods from within the Loop.
 */
final class ContentRenderer
{
    /**
     * Render the post title with an appropriate heading tag.
     *
     * @param string $tag HTML heading tag (h1, h2, h3).
     */
    public static function renderTitle(string $tag = 'h1'): void
    {
        if (!in_the_loop() && !is_singular()) {
            return;
        }

        $title = get_the_title();

        if (empty($title)) {
            return;
        }

        printf(
            '<%1$s class="jas-content__title">%2$s</%1$s>',
            esc_attr($tag),
            esc_html($title)
        );
    }

    /**
     * Render the post content with a wrapper div.
     *
     * Calls the_content() which triggers WordPress content filters
     * (wpautop, shortcodes, oEmbed, etc.).
     */
    public static function renderContent(): void
    {
        echo '<div class="jas-content__body">';
        the_content(
            sprintf(
                '<span class="jas-content__read-more">%s</span>',
                esc_html__('Read more', 'jasanika')
            )
        );
        echo '</div>';
    }

    /**
     * Render the post excerpt.
     *
     * Falls back to a trimmed version of the content if no explicit excerpt exists.
     */
    public static function renderExcerpt(): void
    {
        if (has_excerpt()) {
            $excerpt = get_the_excerpt();
        } else {
            $excerpt = wp_trim_words(
                get_the_content(),
                30,
                '&hellip;'
            );
        }

        echo '<div class="jas-content__excerpt">';
        echo wp_kses_post($excerpt);
        echo '</div>';
    }

    /**
     * Render post metadata (date, author, categories).
     *
     * Outputs a structured metadata block with:
     * - Published date
     * - Author name
     * - Category list (if available)
     *
     * @param bool $showCategories Whether to display category links.
     */
    public static function renderMeta(bool $showCategories = true): void
    {
        $time = get_the_time('U');
        $date = get_the_date();
        $author = get_the_author();

        if (empty($time) || empty($date)) {
            return;
        }

        echo '<div class="jas-content__meta">';

        // Date
        printf(
            '<time class="jas-content__meta-date" datetime="%s">%s</time>',
            esc_attr(gmdate('c', $time)),
            esc_html($date)
        );

        // Author
        if (!empty($author)) {
            echo '<span class="jas-content__meta-separator" aria-hidden="true">·</span>';
            printf(
                '<span class="jas-content__meta-author">%s %s</span>',
                esc_html__('by', 'jasanika'),
                esc_html($author)
            );
        }

        // Categories
        if ($showCategories) {
            $categories = get_the_category();

            if (!empty($categories)) {
                echo '<span class="jas-content__meta-separator" aria-hidden="true">·</span>';
                echo '<span class="jas-content__meta-categories">';
                $categoryLinks = [];

                foreach ($categories as $category) {
                    $categoryLinks[] = sprintf(
                        '<a href="%s" rel="category tag">%s</a>',
                        esc_url(get_category_link($category->term_id)),
                        esc_html($category->name)
                    );
                }

                echo implode(', ', $categoryLinks); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                echo '</span>';
            }
        }

        echo '</div>';
    }

    /**
     * Render pagination for archive-like pages.
     *
     * Uses WordPress paginate_links() with framework-compatible markup.
     */
    public static function renderPagination(): void
    {
        $pagination = paginate_links([
            'prev_text' => '&laquo;',
            'next_text' => '&raquo;',
            'type'      => 'list',
        ]);

        if (empty($pagination)) {
            return;
        }

        echo '<nav class="jas-pagination" aria-label="' . esc_attr__('Posts navigation', 'jasanika') . '">';
        echo wp_kses_post($pagination);
        echo '</nav>';
    }

    /**
     * Render an empty state message.
     *
     * Handles various empty content scenarios with appropriate messaging.
     *
     * @param string $type The type of empty state: 'default', 'search', 'archive', '404'.
     */
    public static function renderEmptyState(string $type = 'default'): void
    {
        $message = self::getEmptyStateMessage($type);

        if (empty($message)) {
            return;
        }

        echo '<div class="jas-empty-state">';
        echo '<p class="jas-empty-state__message">' . wp_kses_post($message) . '</p>';
        echo '</div>';
    }

    /**
     * Get the empty state message for a given type.
     *
     * @param string $type The empty state type.
     * @return string The translated message.
     */
    private static function getEmptyStateMessage(string $type): string
    {
        return match ($type) {
            'search' => __('No results found. Try a different search term.', 'jasanika'),
            'archive' => __('No posts found in this archive.', 'jasanika'),
            '404' => __('The page you are looking for does not exist.', 'jasanika'),
            default => __('No content found.', 'jasanika'),
        };
    }

    /**
     * Render a "Back to homepage" link.
     *
     * Typically used on 404 or error pages.
     */
    public static function renderHomeLink(): void
    {
        printf(
            '<a href="%s" class="jas-content__home-link">%s</a>',
            esc_url(home_url('/')),
            esc_html__('Back to homepage', 'jasanika')
        );
    }

    /**
     * Render search query and result count.
     *
     * Displays the search term and number of results found.
     *
     * @param int $resultCount Number of results.
     */
    public static function renderSearchInfo(int $resultCount): void
    {
        $query = get_search_query();

        echo '<div class="jas-search-info">';

        if (!empty($query)) {
            printf(
                '<p class="jas-search-info__query">%s <strong>%s</strong></p>',
                esc_html__('Search results for:', 'jasanika'),
                esc_html($query)
            );
        }

        printf(
            '<p class="jas-search-info__count">%s</p>',
            sprintf(
                esc_html(
                    _n(
                        '%d result found',
                        '%d results found',
                        $resultCount,
                        'jasanika'
                    )
                ),
                $resultCount
            )
        );

        echo '</div>';
    }

    /**
     * Render the archive title.
     *
     * Uses WordPress archive title functions for:
     * - Category archives
     * - Tag archives
     * - Author archives
     * - Date archives
     * - Post type archives
     */
    public static function renderArchiveTitle(): void
    {
        $title = get_the_archive_title();

        if (empty($title)) {
            return;
        }

        printf(
            '<h1 class="jas-content__archive-title">%s</h1>',
            wp_kses_post($title)
        );
    }

    /**
     * Render the archive description.
     *
     * Uses get_the_archive_description() for category/tag descriptions.
     */
    public static function renderArchiveDescription(): void
    {
        $description = get_the_archive_description();

        if (empty($description)) {
            return;
        }

        echo '<div class="jas-content__archive-description">';
        echo wp_kses_post($description);
        echo '</div>';
    }

    /**
     * Render a "Read more" link for card-style listings.
     */
    public static function renderReadMoreLink(): void
    {
        printf(
            '<a href="%s" class="jas-content__read-more-link">%s</a>',
            esc_url(get_permalink()),
            esc_html__('Read more', 'jasanika')
        );
    }
}
