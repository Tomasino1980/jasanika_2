<?php

declare(strict_types=1);

namespace Jasanika\Admin\Search;

/**
 * Settings Search for the Jasanika Settings UI.
 *
 * Provides a client-side search field that filters settings sections
 * and fields by label, description, and category. No page reload required.
 *
 * M27 — Theme Presets & Settings UX Framework.
 *
 * Rendering flow:
 * 1. Render search input at the top of the settings page
 * 2. Enqueue inline JavaScript that filters section cards in real time
 * 3. Matching sections are shown; non-matching sections are hidden
 * 4. Search highlights matched labels
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
                placeholder="Search Settings..."
                aria-label="Search settings"
                autocomplete="off"
            >
            <span class="jas-settings-search__icon" aria-hidden="true">&#128269;</span>
            <span id="jas-settings-search-count" class="jas-settings-search__count"></span>
        </div>
        <?php

        self::renderScript();
    }

    /**
     * Render the inline JavaScript for client-side filtering.
     */
    private static function renderScript(): void
    {
        ?>
<script>
(function() {
    var input = document.getElementById('jas-settings-search-input');
    if (!input) return;

    var countEl = document.getElementById('jas-settings-search-count');

    input.addEventListener('input', function() {
        var query = this.value.toLowerCase().trim();
        var sections = document.querySelectorAll('.jas-settings__section');
        var visibleCount = 0;

        sections.forEach(function(section) {
            var card = section.querySelector('.jas-card');
            if (!card) return;

            var text = card.textContent.toLowerCase();
            var match = query === '' || text.indexOf(query) !== -1;

            section.style.display = match ? '' : 'none';
            if (match) visibleCount++;

            // Highlight matching text in labels
            if (query !== '') {
                var labels = section.querySelectorAll('.jas-card__body label, .jas-card__body th, .jas-card__body .jas-form-field__label');
                labels.forEach(function(label) {
                    var original = label.getAttribute('data-jas-original');
                    if (!original) {
                        original = label.innerHTML;
                        label.setAttribute('data-jas-original', original);
                    }
                    if (original.toLowerCase().indexOf(query) !== -1) {
                        var regex = new RegExp('(' + query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi');
                        label.innerHTML = original.replace(regex, '<mark class="jas-search-highlight">$1</mark>');
                    } else {
                        label.innerHTML = original;
                    }
                });
            } else {
                // Restore original text
                var labels = section.querySelectorAll('[data-jas-original]');
                labels.forEach(function(label) {
                    label.innerHTML = label.getAttribute('data-jas-original');
                });
            }
        });

        if (countEl) {
            var total = sections.length;
            if (query === '') {
                countEl.textContent = '';
            } else {
                countEl.textContent = visibleCount + ' / ' + total;
            }
        }
    });
})();
</script>
        <?php
    }
}