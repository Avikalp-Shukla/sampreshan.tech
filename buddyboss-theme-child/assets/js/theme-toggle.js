/**
 * SampreShan Theme Toggle — Dashboard Dark Mode ("Ratri")
 *
 * Toggles `sp-dark` on <body> and persists the choice in localStorage
 * so every page follows the user's preference. Default (no stored value)
 * is the regular light dashboard.
 *
 * Button contract (rendered by template-dashboard.php):
 *   <button id="sp-theme-toggle" class="sp-theme-toggle" ...>
 *     <span class="sp-theme-toggle__label">…</span>
 *   </button>
 *
 * @package SampreShan_Child
 */
(function () {
    'use strict';

    var KEY = 'sp-theme';
    var DARK = 'dark';

    function isDark() {
        try {
            return window.localStorage.getItem(KEY) === DARK;
        } catch (e) {
            return document.body.classList.contains('sp-dark');
        }
    }

    function labelFor(dark) {
        return dark ? 'Light mode' : 'Dark mode';
    }

    function paint(btn, dark) {
        if (!btn) { return; }
        btn.setAttribute('aria-pressed', dark ? 'true' : 'false');
        var label = btn.querySelector('.sp-theme-toggle__label');
        if (label) { label.textContent = labelFor(dark); }
        btn.title = labelFor(dark);
    }

    function apply(dark, btn) {
        document.body.classList.toggle('sp-dark', dark);
        try {
            if (dark) { window.localStorage.setItem(KEY, DARK); }
            else { window.localStorage.removeItem(KEY); }
        } catch (e) { /* private mode — session only */ }
        paint(btn, dark);
    }

    function init() {
        var btn = document.getElementById('sp-theme-toggle');
        // Apply stored preference on every page (script loads globally).
        apply(isDark(), btn);
        if (!btn) { return; }
        btn.addEventListener('click', function () {
            apply(!document.body.classList.contains('sp-dark'), btn);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
