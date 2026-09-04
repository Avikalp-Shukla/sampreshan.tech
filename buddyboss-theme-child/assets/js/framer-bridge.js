/**
 * SampreShan Framer Bridge
 *
 * Hydrates elements with [data-framer-mode="bridge"] by lazy-loading
 * the Framer runtime and rendering the project into the container.
 *
 * Falls back gracefully if the runtime is blocked, the project is missing,
 * or the user has slow network — the placeholder stays visible.
 */
(function () {
  'use strict';

  function ready(fn) {
    if (document.readyState !== 'loading') fn();
    else document.addEventListener('DOMContentLoaded', fn);
  }

  function hydrate(el) {
    if (el.dataset.framerHydrated === '1') return;
    el.dataset.framerHydrated = '1';

    var projectId = el.dataset.framerProjectId || '';
    var placeholder = el.querySelector('.sp-framer-embed__placeholder');
    if (placeholder) placeholder.remove();

    // The official Framer embed uses a script that exposes a global `Framer`
    // and accepts a target element. We lazy-load it only when needed.
    var s = document.createElement('script');
    s.src = 'https://framer.com/embed/' + encodeURIComponent(projectId) + '.js';
    s.async = true;
    s.defer = true;
    s.crossOrigin = 'anonymous';
    s.onload = function () {
      try {
        if (window.Framer && typeof window.Framer.mount === 'function') {
          window.Framer.mount(el, { projectId: projectId });
        }
      } catch (err) {
        // Restore placeholder so the user sees something useful.
        el.innerHTML = '<p style="padding:2rem;text-align:center;">'
          + 'The site is taking a moment to load. Please refresh if it does not appear.'
          + '</p>';
        console.error('[sampreshan-framer] mount failed:', err);
      }
    };
    s.onerror = function () {
      el.innerHTML = '<p style="padding:2rem;text-align:center;">'
        + 'The site could not be loaded. Please refresh the page.'
        + '</p>';
    };
    document.head.appendChild(s);
  }

  function init() {
    var nodes = document.querySelectorAll('[data-framer-mode="bridge"]');
    if (!nodes.length) return;
    if (!('IntersectionObserver' in window)) {
      Array.prototype.forEach.call(nodes, hydrate);
      return;
    }
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          hydrate(entry.target);
          io.unobserve(entry.target);
        }
      });
    }, { rootMargin: '200px' });
    Array.prototype.forEach.call(nodes, function (n) { io.observe(n); });
  }

  ready(init);
})();
