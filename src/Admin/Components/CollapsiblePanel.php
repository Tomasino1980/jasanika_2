<?php

declare(strict_types=1);

namespace Jasanika\Admin\Components;

/**
 * Reusable collapsible section panel for the Settings UI.
 *
 * Wraps any content in a toggle-able panel with a header button
 * that expands or collapses the body content. State is preserved
 * via a data attribute and optional URL hash for tab navigation.
 *
 * M27 — Theme Presets & Settings UX Framework.
 *
 * Usage:
 *   $panel = new CollapsiblePanel('header-settings', 'Header', true);
 *   $panel->start();
 *   // ... content ...
 *   $panel->end();
 */
final class CollapsiblePanel
{
    private string $id;
    private string $title;
    private bool $startOpen;
    private string $badge;

    /**
     * @param string $id        Unique panel identifier (used as HTML id and data attribute).
     * @param string $title     Panel header title.
     * @param bool   $startOpen Whether the panel starts expanded.
     * @param string $badge     Optional badge text (e.g. field count).
     */
    public function __construct(
        string $id,
        string $title,
        bool $startOpen = true,
        string $badge = ''
    ) {
        $this->id        = $id;
        $this->title     = $title;
        $this->startOpen = $startOpen;
        $this->badge     = $badge;
    }

    /**
     * Render the panel opening tag and header.
     */
    public function start(): void
    {
        $expanded = $this->startOpen ? 'true' : 'false';
        $bodyClass = $this->startOpen ? '' : ' style="display:none;"';

        ?>
        <div class="jas-collapsible" data-panel-id="<?php echo esc_attr($this->id); ?>">
            <button
                type="button"
                class="jas-collapsible__toggle"
                aria-expanded="<?php echo esc_attr($expanded); ?>"
                aria-controls="jas-panel-body-<?php echo esc_attr($this->id); ?>"
            >
                <span class="jas-collapsible__icon" aria-hidden="true">&#9660;</span>
                <span class="jas-collapsible__title"><?php echo esc_html($this->title); ?></span>
                <?php if ($this->badge !== '') : ?>
                    <span class="jas-collapsible__badge"><?php echo esc_html($this->badge); ?></span>
                <?php endif; ?>
            </button>
            <div
                id="jas-panel-body-<?php echo esc_attr($this->id); ?>"
                class="jas-collapsible__body"
                <?php echo $bodyClass; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            >
        <?php
    }

/**
 * Render the panel closing tag.
 */
public function end(): void
{
    ?>
            </div>
        </div>
    <?php
}

/**
 * Render the collapsible panel initialization script once.
 *
 * Call this once per page (e.g. in the SettingsPage render method).
 */
public static function renderScript(): void
{
    static $rendered = false;

    if ($rendered) {
        return;
    }

    $rendered = true;

    ?>
<script>
(function() {
    var toggles = document.querySelectorAll('.jas-collapsible__toggle');
    toggles.forEach(function(toggle) {
        toggle.addEventListener('click', function() {
            var expanded = this.getAttribute('aria-expanded') === 'true';
            var bodyId = this.getAttribute('aria-controls');
            var body = document.getElementById(bodyId);

            this.setAttribute('aria-expanded', expanded ? 'false' : 'true');
            if (body) {
                body.style.display = expanded ? 'none' : '';
                // Preserve state in sessionStorage for tab navigation
                try {
                    sessionStorage.setItem('jas-panel-' + bodyId, expanded ? 'collapsed' : 'expanded');
                } catch(e) {}
            }
        });

        // Restore state from sessionStorage
        var bodyId = toggle.getAttribute('aria-controls');
        if (bodyId) {
            try {
                var saved = sessionStorage.getItem('jas-panel-' + bodyId);
                if (saved === 'collapsed') {
                    toggle.setAttribute('aria-expanded', 'false');
                    var body = document.getElementById(bodyId);
                    if (body) body.style.display = 'none';
                }
            } catch(e) {}
        }
    });
})();
</script>
    <?php
}
}