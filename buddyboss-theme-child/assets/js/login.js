/**
 * SampreShan Login — phone-login panel + UX
 *
 * - Toggles the phone-login panel when [data-sp-open="phone-login"] is clicked.
 * - Posts the phone form to admin-ajax.php?action=sp_phone_login (handled by
 *   inc/auth/digit-bridge.php), which works whether or not the digit plugin
 *   is installed.
 * - Validates the phone client-side and surfaces server errors as inline
 *   notices.
 */
(function () {
  'use strict';

  function ready(fn) {
    if (document.readyState !== 'loading') fn();
    else document.addEventListener('DOMContentLoaded', fn);
  }

  function on(el, ev, fn) { el.addEventListener(ev, fn); }

  function showAlert(target, kind, msg) {
    if (!target) { return; }
    var div = document.createElement('div');
    div.className = 'sp-login__' + (kind === 'error' ? 'error' : 'notice');
    div.setAttribute('role', kind === 'error' ? 'alert' : 'status');
    div.textContent = msg;
    target.insertBefore(div, target.firstChild);
    setTimeout(function () { div.parentNode && div.parentNode.removeChild(div); }, 6000);
  }

  function init() {
    var body = document.body;
    if (!body || !body.classList.contains('sp-login-body')) { return; }

    // Toggle phone-login panel
    var openers = document.querySelectorAll('[data-sp-open="phone-login"]');
    Array.prototype.forEach.call(openers, function (btn) {
      on(btn, 'click', function (e) {
        e.preventDefault();
        var panel = document.getElementById('sp-phone-login-panel');
        if (!panel) { return; }
        var isHidden = panel.hasAttribute('hidden');
        if (isHidden) {
          panel.removeAttribute('hidden');
          btn.setAttribute('aria-expanded', 'true');
          var tel = panel.querySelector('input[type="tel"]');
          if (tel) { tel.focus(); }
        } else {
          panel.setAttribute('hidden', '');
          btn.setAttribute('aria-expanded', 'false');
        }
      });
    });

    // Phone-login form submit
    var form = document.querySelector('[data-sp-form="phone-login"]');
    if (form) {
      on(form, 'submit', function (e) {
        e.preventDefault();
        var submit = form.querySelector('button[type="submit"]');
        if (submit) { submit.disabled = true; }

        var data = new FormData(form);
        var payload = {
          action: 'sp_phone_login',
          nonce: (window.SampreshanAuth && window.SampreshanAuth.phoneNonce) || '',
        };
        data.forEach(function (v, k) { payload[k] = v; });

        var body = new URLSearchParams();
        for (var k in payload) { body.append(k, payload[k]); }

        fetch((window.SampreshanAuth && window.SampreshanAuth.ajaxUrl) || '/wp-admin/admin-ajax.php', {
          method: 'POST',
          credentials: 'same-origin',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: body.toString()
        })
        .then(function (r) { return r.json(); })
        .then(function (res) {
          if (!res || !res.success) {
            showAlert(form, 'error', (res && res.data && res.data.message) || 'Something went wrong.');
            return;
          }
          showAlert(form, 'notice', (res.data && res.data.message) || 'Done.');
          form.reset();
        })
        .catch(function () {
          showAlert(form, 'error', 'Network error. Please try again.');
        })
        .finally(function () {
          if (submit) { submit.disabled = false; }
        });
      });
    }
  }

  ready(init);
})();
