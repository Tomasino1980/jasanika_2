<?php

declare(strict_types=1);

namespace Jasanika\Admin\Search;

/**
 * Settings Search for the Jasanika Settings UI.
 *
 * Provides client-side real-time filtering that:
 * - Filters settings cards by title, description, and field content
 * - Highlights matching field labels and descriptions
 * - Displays result count (visible / total)
 * - Hides irrelevant sections (sub-tabs and cards with no matches)
 * - Shows matching parent sections when a child field matches
 *
 * M29 — Settings UI Refactor & Design System.
 * Enhanced with group filtering, section visibility, and better UX.
 *
 * Rendering flow:
 * 1. Render search input with clear icon
 * 2. Render inline JavaScript for real-time filtering
 * 3. JS filters settings cards and shows/hides sub-navigation items
 */
final class SettingsSearch
{
    /**
     * Render the search field and initialization script.
     */
    public static function render(): void
    {
        ?>
        <div class="jas-settings-search">
            <input
                type="search"
                id="jas-settings-search-input"
                class="jas-settings-search__input"
                placeholder="Search settings..."
                aria-label="Search settings"
                autocomplete="off"
            >
            <span id="jas-settings-search-count" class="jas-settings-search__count"></span>
        </div>
        <?php

        self::renderScript();
    }

    /**
     * Render the inline JavaScript for client-side filtering.
     *
     * M29: Enhanced to filter whole cards, show/hide sub-nav items,
     * and provide better visual feedback.
     */
    private static function renderScript(): void
    {
        ?>
<script>
(function() {
    'use strict';

    var input = document.getElementById('jas-settings-search-input');
    if (!input) return;

    var countEl = document.getElementById('jas-settings-search-count');

    /**
     * Debounce utility to avoid excessive filtering on rapid input.
     */
    function debounce(fn, delay) {
        var timer = null;
        return function() {
            var context = this;
            var args = arguments;
            if (timer) clearTimeout(timer);
            timer = setTimeout(function() {
                fn.apply(context, args);
            }, delay);
        };
    }

    /**
     * Escape regex special characters for safe search highlighting.
     */
    function escapeRegex(str) {
        return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    /**
     * Restore original text in an element by removing highlight marks.
     */
    function restoreOriginal(el) {
        var marks = el.querySelectorAll('.jas-search-highlight');
        marks.forEach(function(mark) {
            var parent = mark.parentNode;
            if (parent) {
                parent.replaceChild(document.createTextNode(mark.textContent), mark);
                parent.normalize();
            }
        });
    }

    /**
     * Highlight all occurrences of query in the given text node parent.
     */
    function highlightText(el, query) {
        if (!el || el.querySelector('.jas-search-highlight')) return;

        var text = el.textContent || '';
        var lower = text.toLowerCase();
        var idx = lower.indexOf(query);
        if (idx === -1) return;

        var regex = new RegExp('(' + escapeRegex(query) + ')', 'gi');
        el.innerHTML = text.replace(regex, '<mark class="jas-search-highlight">$1</mark>');
    }

    var doFilter = debounce(function() {
        var query = input.value.toLowerCase().trim();
        var cards = document.querySelectorAll('.jas-settings-card');
        var visibleCount = 0;

        cards.forEach(function(card) {
            var text = (card.textContent || '').toLowerCase();
            var title = card.querySelector('.jas-settings-card__title');
            var desc  = card.querySelector('.jas-settings-card__description');

            // Restore original title and description before re-checking
            if (title) restoreOriginal(title);
            if (desc)  restoreOriginal(desc);

            var match = query === '' || text.indexOf(query) !== -1;

            card.style.display = match ? '' : 'none';

            if (match) {
                visibleCount++;

                // Highlight title
                if (title && query !== '' && (title.textContent || '').toLowerCase().indexOf(query) !== -1) {
                    highlightText(title, query);
                }

                // Highlight description
                if (desc && query !== '' && (desc.textContent || '').toLowerCase().indexOf(query) !== -1) {
                    highlightText(desc, query);
                }

                // Highlight field labels inside the card body
                if (query !== '') {
                    var labels = card.querySelectorAll('.jas-settings-card__body th label, .jas-settings-card__body .form-table th');
                    labels.forEach(function(label) {
                        restoreOriginal(label);
                        if ((label.textContent || '').toLowerCase().indexOf(query) !== -1) {
                            highlightText(label, query);
                        }
                    });
                }
            }
        });

        // Update sub-tab visibility — hide sub-tabs whose section card is hidden
        var subTabs = document.querySelectorAll('.jas-sub-tab');
        subTabs.forEach(function(tab) {
            var sectionSlug = tab.getAttribute('data-section');
            if (!sectionSlug) return;
            var card = document.querySelector('.jas-settings-card[id*="' + sectionSlug + '"]');
            if (!card) {
                var allCards = document.querySelectorAll('.jas-settings-card');
                var found = false;
                allCards.forEach(function(c) {
                    if (c.id && c.id.indexOf(sectionSlug) !== -1) found = true;
                });
                if (!found) return;
                tab.style.display = card && card.style.display === 'none' ? 'none' : '';
            } else {
                tab.style.display = card.style.display === 'none' ? 'none' : '';
            }
        });

        // Update result count
        if (countEl) {
            var total = cards.length;
            if (query === '') {
                countEl.textContent = '';
            } else {
                countEl.textContent = visibleCount + ' / ' + total;
            }
        }
    }, 150);

    input.addEventListener('input', doFilter);

    // Initial state — clear any stale highlights
    doFilter();
})();
</script>
        <?php
    }
}