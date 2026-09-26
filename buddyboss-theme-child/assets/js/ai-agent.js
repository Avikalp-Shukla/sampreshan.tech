/**
 * Sampreshan Sahayak — floating AI help widget.
 * @package SampreShan_Child
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        var root = document.getElementById('sp-sahayak');
        if (!root || typeof window.SampreshanSahayak === 'undefined') return;

        var config = window.SampreshanSahayak;
        var launcher = root.querySelector('[data-sahayak-launcher]');
        var closeBtn = root.querySelector('[data-sahayak-close]');
        var body = root.querySelector('[data-sahayak-body]');
        var form = root.querySelector('[data-sahayak-form]');
        var input = root.querySelector('[data-sahayak-input]');
        var sendBtn = root.querySelector('[data-sahayak-send]');
        var history = [];
        var busy = false;

        function esc(s) {
            return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
                return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
            });
        }

        function appendMessage(role, text, extraClass) {
            var el = document.createElement('div');
            el.className = 'sp-sahayak__msg sp-sahayak__msg--' + role + (extraClass ? ' ' + extraClass : '');
            el.textContent = text;
            body.appendChild(el);
            body.scrollTop = body.scrollHeight;
            return el;
        }

        function openPanel() {
            root.classList.add('is-open');
            launcher.setAttribute('aria-expanded', 'true');
            setTimeout(function () { input.focus(); }, 50);
        }

        function closePanel() {
            root.classList.remove('is-open');
            launcher.setAttribute('aria-expanded', 'false');
            launcher.focus();
        }

        launcher.addEventListener('click', function () {
            if (root.classList.contains('is-open')) { closePanel(); } else { openPanel(); }
        });
        closeBtn.addEventListener('click', closePanel);
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && root.classList.contains('is-open')) { closePanel(); }
        });

        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                form.requestSubmit ? form.requestSubmit() : form.dispatchEvent(new Event('submit', { cancelable: true }));
            }
        });

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            if (busy) return;

            var message = input.value.trim();
            if (!message) return;

            appendMessage('user', message);
            history.push({ role: 'user', content: message });
            input.value = '';
            input.style.height = 'auto';

            busy = true;
            sendBtn.disabled = true;
            var pending = appendMessage('assistant', 'Sochte hue…', 'sp-sahayak__msg--pending');

            var formData = new FormData();
            formData.append('action', 'sp_ai_agent_chat');
            formData.append('nonce', config.nonce);
            formData.append('message', message);
            formData.append('history', JSON.stringify(history.slice(0, -1)));

            fetch(config.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: formData })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    pending.remove();
                    if (data && data.success && data.data && data.data.reply) {
                        appendMessage('assistant', data.data.reply);
                        history.push({ role: 'assistant', content: data.data.reply });
                    } else {
                        var msg = (data && data.data && data.data.message) || 'Something went wrong. Please try again.';
                        appendMessage('assistant', msg, 'sp-sahayak__msg--error');
                    }
                })
                .catch(function () {
                    pending.remove();
                    appendMessage('assistant', 'Network issue. Please try again.', 'sp-sahayak__msg--error');
                })
                .finally(function () {
                    busy = false;
                    sendBtn.disabled = false;
                });
        });

        input.addEventListener('input', function () {
            input.style.height = 'auto';
            input.style.height = Math.min(input.scrollHeight, 110) + 'px';
        });
    });
})();
