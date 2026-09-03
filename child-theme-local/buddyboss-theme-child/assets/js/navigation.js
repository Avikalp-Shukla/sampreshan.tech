/**
 * SampreShan Navigation JS
 * Mobile tab nav, smooth scroll, header behavior
 * @package SampreShan_Child
 */

(function () {
    'use strict';

    // === Mobile Tab Nav (injected on small screens) ===
    function initMobileTabNav() {
        if (document.querySelector('.mobile-tab-nav')) return;

        const nav = document.createElement('nav');
        nav.className = 'mobile-tab-nav';
        nav.setAttribute('aria-label', 'Primary mobile navigation');
        nav.innerHTML = [
            { href: '/',         label: 'Home',       icon: homeIcon() },
            { href: '/petitions', label: 'Petitions',  icon: petitionIcon() },
            { href: '/feed',     label: 'Feed',       icon: feedIcon() },
            { href: '/members',  label: 'Network',    icon: networkIcon() },
            { href: '/account',  label: 'Profile',    icon: profileIcon() }
        ].map(function (item) {
            return '<a class="mobile-tab-nav__item" href="' + item.href + '">' +
                   '<span class="mobile-tab-nav__icon">' + item.icon + '</span>' +
                   '<span>' + item.label + '</span>' +
                   '</a>';
        }).join('');

        document.body.appendChild(nav);

        // Highlight active
        const path = window.location.pathname;
        nav.querySelectorAll('.mobile-tab-nav__item').forEach(function (a) {
            const href = a.getAttribute('href');
            if ((href === '/' && path === '/') || (href !== '/' && path.indexOf(href) === 0)) {
                a.classList.add('is-active');
            }
        });
    }

    // === SVG Icons (inline, no external deps) ===
    function homeIcon() {
        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12l9-9 9 9M5 10v10h14V10"/></svg>';
    }
    function petitionIcon() {
        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M9 13h6M9 17h4"/></svg>';
    }
    function feedIcon() {
        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 11a9 9 0 0 1 9 9M4 4a16 16 0 0 1 16 16"/><circle cx="5" cy="19" r="1.5" fill="currentColor"/></svg>';
    }
    function networkIcon() {
        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>';
    }
    function profileIcon() {
        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>';
    }

    // === Smooth scroll for anchor links ===
    function initSmoothScroll() {
        document.addEventListener('click', function (e) {
            const link = e.target.closest('a[href^="#"]');
            if (!link) return;
            const id = link.getAttribute('href');
            if (id.length <= 1) return;
            const target = document.querySelector(id);
            if (!target) return;
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    }

    // === Header scroll behavior (add shadow on scroll) ===
    function initHeaderScroll() {
        const header = document.querySelector('.site-header');
        if (!header) return;
        let ticking = false;
        window.addEventListener('scroll', function () {
            if (!ticking) {
                window.requestAnimationFrame(function () {
                    if (window.scrollY > 8) {
                        header.style.boxShadow = 'var(--shadow-sm)';
                    } else {
                        header.style.boxShadow = 'none';
                    }
                    ticking = false;
                });
                ticking = true;
            }
        }, { passive: true });
    }

    // === Init on DOM ready ===
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            initMobileTabNav();
            initSmoothScroll();
            initHeaderScroll();
        });
    } else {
        initMobileTabNav();
        initSmoothScroll();
        initHeaderScroll();
    }
})();
