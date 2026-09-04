/**
 * SampreShan Petition Sign — Premium UI handler
 *
 * Listens for clicks on `.sp-sign-button[data-petition-id]` and posts to
 * admin-ajax.php with the SampreshanPetition nonce exposed in the page.
 * On success, swaps the button for a "Signed ✓" state and updates the
 * signature count badge. Shows toast notifications instead of alerts.
 *
 * @package SampreShan_Child
 */
(function () {
    'use strict';

    function ready(fn) {
        if (document.readyState !== 'loading') fn();
        else document.addEventListener('DOMContentLoaded', fn);
    }

    /* ── Toast notification ── */
    function showToast(message, type) {
        type = type || 'info';
        var existing = document.querySelector('.sp-toast');
        if (existing) { existing.remove(); }

        var toast = document.createElement('div');
        toast.className = 'sp-toast sp-toast--' + type;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'polite');

        var icons = {
            success: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
            error: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
            info: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>'
        };

        toast.innerHTML =
            '<span class="sp-toast__icon">' + (icons[type] || icons.info) + '</span>' +
            '<span class="sp-toast__message">' + message + '</span>' +
            '<button class="sp-toast__close" aria-label="Dismiss">&times;</button>';

        document.body.appendChild(toast);

        // Trigger animation
        requestAnimationFrame(function () {
            toast.classList.add('is-visible');
        });

        // Close button
        toast.querySelector('.sp-toast__close').addEventListener('click', function () {
            toast.classList.remove('is-visible');
            setTimeout(function () { toast.remove(); }, 300);
        });

        // Auto-dismiss
        setTimeout(function () {
            if (toast.parentNode) {
                toast.classList.remove('is-visible');
                setTimeout(function () { toast.remove(); }, 300);
            }
        }, 4000);
    }

    /* ── AJAX helper ── */
    function post(action, data) {
        var body = new URLSearchParams();
        for (var k in data) { body.append(k, data[k]); }
        body.append('action', action);
        body.append('nonce', (window.SampreshanPetition && window.SampreshanPetition.nonce) || '');
        return fetch((window.SampreshanPetition && window.SampreshanPetition.ajaxUrl) || '/wp-admin/admin-ajax.php', {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: body.toString()
        }).then(function (r) { return r.json(); });
    }

    function fmt(n) {
        try { return new Intl.NumberFormat().format(n); } catch (e) { return String(n); }
    }

    /* ── Bind sign/unsign button ── */
    function bindButton(btn) {
        if (btn.dataset.spBound === '1') { return; }
        btn.dataset.spBound = '1';
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            if (btn.disabled) { return; }

            // Check for comment field
            var card = btn.closest('.petition-card, .sp-sign-card, [data-petition-card], .sp-petition-single__actions');
            var commentField = card ? card.querySelector('.sp-sign-comment') : null;
            var comment = commentField ? commentField.value : '';

            btn.disabled = true;
            var oldHTML = btn.innerHTML;
            var label = (window.SampreshanPetition && window.SampreshanPetition.i18n && window.SampreshanPetition.i18n.signing) || 'Signing\u2026';
            btn.innerHTML = '<span class="sp-spinner sp-spinner--sm"></span> ' + label;

            var action = btn.dataset.signed === '1' ? 'sp_unsign_petition' : 'sp_sign_petition';
            var payload = { petition_id: btn.dataset.petitionId };
            if (comment) { payload.comment = comment; }

            post(action, payload)
                .then(function (res) {
                    if (!res || !res.success) {
                        btn.disabled = false;
                        btn.innerHTML = oldHTML;
                        var msg = (res && res.data && res.data.message) || 'Something went wrong. Please try again.';
                        showToast(msg, 'error');
                        return;
                    }

                    // Update button state
                    var isSigned = action === 'sp_sign_petition';
                    btn.dataset.signed = isSigned ? '1' : '0';
                    btn.classList.toggle('is-signed', isSigned);

                    if (isSigned) {
                        btn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> ' +
                            ((window.SampreshanPetition && window.SampreshanPetition.i18n && window.SampreshanPetition.i18n.signed) || 'Signed \u2713');
                        showToast('Thank you for your support!', 'success');
                    } else {
                        btn.innerHTML = oldHTML;
                        showToast('Signature removed.', 'info');
                    }

                    // Update count badge
                    var count = (res.data && typeof res.data.count !== 'undefined') ? res.data.count : null;
                    if (count !== null) {
                        var parentCard = btn.closest('.petition-card, .sp-sign-card, [data-petition-card], .sp-petition-single__hero');
                        if (parentCard) {
                            var badges = parentCard.querySelectorAll('[data-sp-signature-count]');
                            badges.forEach(function (badge) {
                                badge.textContent = fmt(count);
                            });
                        }
                    }

                    // Clear comment field
                    if (commentField) { commentField.value = ''; }

                    btn.disabled = false;
                })
                .catch(function () {
                    btn.disabled = false;
                    btn.innerHTML = oldHTML;
                    showToast('Network error. Please try again.', 'error');
                });
        });
    }

    /* ── Share buttons ── */
    function bindShareButtons() {
        document.querySelectorAll('.sp-share-btn').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                var url = btn.dataset.url || window.location.href;
                var title = btn.dataset.title || document.title;

                if (navigator.share) {
                    navigator.share({ title: title, url: url }).catch(function () {});
                } else {
                    navigator.clipboard.writeText(url).then(function () {
                        showToast('Link copied to clipboard!', 'success');
                    }).catch(function () {
                        // Fallback
                        var input = document.createElement('input');
                        input.value = url;
                        document.body.appendChild(input);
                        input.select();
                        document.execCommand('copy');
                        document.body.removeChild(input);
                        showToast('Link copied to clipboard!', 'success');
                    });
                }
            });
        });
    }

    /* ── Initialize ── */
    function init() {
        var nodes = document.querySelectorAll('.sp-sign-button[data-petition-id]');
        if (nodes.length) {
            Array.prototype.forEach.call(nodes, bindButton);
        }
        bindShareButtons();
    }

    ready(init);
})();
