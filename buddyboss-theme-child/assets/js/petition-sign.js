/**
 * SampreShan Petition Sign — Premium UI handler
 *
 * Listens for clicks on `.sp-sign-button[data-petition-id]` and posts to
 * admin-ajax.php with the SampreshanPetition nonce exposed in the page.
 * On success, swaps the button for a "Supported ✓" state and updates the
 * I count badge. Shows toast notifications instead of alerts.
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
            var label = (window.SampreshanPetition && window.SampreshanPetition.i18n && window.SampreshanPetition.i18n.signing) || 'Supporting\u2026';
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
                            ((window.SampreshanPetition && window.SampreshanPetition.i18n && window.SampreshanPetition.i18n.signed) || 'Supported \u2713');
                        showToast('Thank you — your I is counted!', 'success');
                    } else {
                        btn.innerHTML = oldHTML;
                        showToast('I removed.', 'info');
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

    /* ── Report toggle + submit ── */
    function bindReport() {
        document.querySelectorAll('.sp-report-toggle').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var wrap = btn.closest('.sp-petition-single__actions, .sp-report') || btn.parentNode;
                var form = wrap ? wrap.parentNode.querySelector('.sp-report-form') : null;
                if (!form && btn.nextElementSibling && btn.nextElementSibling.classList.contains('sp-report-form')) {
                    form = btn.nextElementSibling;
                }
                if (!form) { return; }
                var open = !form.hidden;
                form.hidden = open;
                btn.setAttribute('aria-expanded', open ? 'false' : 'true');
                if (!open) {
                    var sel = form.querySelector('select');
                    if (sel) { sel.focus(); }
                }
            });
        });
        document.querySelectorAll('.sp-report-form[data-petition-id]').forEach(function (form) {
            if (form.dataset.spBound === '1') { return; }
            form.dataset.spBound = '1';
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                var submit = form.querySelector('button[type="submit"]');
                var reason = form.querySelector('select[name="reason"]');
                if (submit) { submit.disabled = true; }
                post('sp_report_petition', {
                    petition_id: form.dataset.petitionId,
                    reason: reason ? reason.value : ''
                })
                    .then(function (res) {
                        if (!res || !res.success) {
                            var msg = (res && res.data && res.data.message) || 'Something went wrong. Please try again.';
                            showToast(msg, 'error');
                            return;
                        }
                        showToast((res.data && res.data.message) || 'Report received. Thank you.', 'success');
                        form.hidden = true;
                        var toggle = document.querySelector('.sp-report-toggle');
                        if (toggle) { toggle.disabled = true; toggle.style.opacity = '0.55'; }
                    })
                    .catch(function () {
                        showToast('Network error. Please try again.', 'error');
                    })
                    .finally(function () {
                        if (submit) { submit.disabled = false; }
                    });
            });
        });
    }

    /* ── Save / unsave toggle ── */
    function bindSave() {
        document.querySelectorAll('.sp-save-btn[data-petition-id]').forEach(function (btn) {
            if (btn.dataset.spBound === '1') { return; }
            btn.dataset.spBound = '1';
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                if (btn.disabled) { return; }
                btn.disabled = true;
                var saving = btn.dataset.saved !== '1';
                post(saving ? 'sp_save_petition' : 'sp_unsave_petition', {
                    petition_id: btn.dataset.petitionId
                })
                    .then(function (res) {
                        if (!res || !res.success) {
                            var msg = (res && res.data && res.data.message) || 'Something went wrong. Please try again.';
                            showToast(msg, 'error');
                            return;
                        }
                        btn.dataset.saved = saving ? '1' : '0';
                        btn.classList.toggle('is-saved', saving);
                        btn.setAttribute('aria-pressed', saving ? 'true' : 'false');
                        var label = btn.querySelector('span');
                        if (label && (label.textContent.trim() === 'Save' || label.textContent.trim() === 'Saved')) {
                            label.textContent = saving ? 'Saved' : 'Save';
                        }
                        showToast((res.data && res.data.message) || (saving ? 'Saved.' : 'Removed.'), 'success');
                        if (!saving && btn.dataset.behavior === 'remove') {
                            var li = btn.closest('li');
                            if (li) { li.remove(); }
                        }
                    })
                    .catch(function () {
                        showToast('Network error. Please try again.', 'error');
                    })
                    .finally(function () {
                        btn.disabled = false;
                    });
            });
        });
    }

    /* ── Starter updates ── */
    function bindUpdates() {
        document.querySelectorAll('.sp-update-form[data-petition-id]').forEach(function (form) {
            if (form.dataset.spBound === '1') { return; }
            form.dataset.spBound = '1';
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                var field = form.querySelector('textarea[name="text"]');
                var submit = form.querySelector('button[type="submit"]');
                var text = field ? field.value.trim() : '';
                if (text.length < 10) {
                    showToast('Please write at least a sentence.', 'error');
                    return;
                }
                if (submit) { submit.disabled = true; }
                post('sp_post_update', {
                    petition_id: form.dataset.petitionId,
                    text: text
                })
                    .then(function (res) {
                        if (!res || !res.success) {
                            var msg = (res && res.data && res.data.message) || 'Something went wrong. Please try again.';
                            showToast(msg, 'error');
                            return;
                        }
                        showToast((res.data && res.data.message) || 'Update posted.', 'success');
                        if (field) { field.value = ''; }
                        var list = document.querySelector('.sp-updates-list');
                        if (list) {
                            var li = document.createElement('li');
                            li.className = 'sp-updates-list__item';
                            var p = document.createElement('p');
                            p.className = 'sp-updates-list__text';
                            p.textContent = text;
                            var time = document.createElement('time');
                            time.className = 'sp-updates-list__time';
                            time.textContent = 'Just now';
                            li.appendChild(p);
                            li.appendChild(time);
                            list.insertBefore(li, list.firstChild);
                        } else {
                            window.location.reload();
                        }
                    })
                    .catch(function () {
                        showToast('Network error. Please try again.', 'error');
                    })
                    .finally(function () {
                        if (submit) { submit.disabled = false; }
                    });
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
        bindReport();
        bindSave();
        bindUpdates();
    }

    ready(init);
})();
