/**
 * Navigation & Interactions — Premium Edition
 * Header scroll, search overlay, scroll-to-top, animated counters,
 * mobile tab nav, scroll reveal
 * @package SampreShan_Child
 */
(function () {
    'use strict';

    /* === HEADER SCROLL EFFECT === */
    var header = document.getElementById('site-header');
    if (header) {
        var lastScroll = 0;
        var ticking = false;
        window.addEventListener('scroll', function () {
            lastScroll = window.scrollY;
            if (!ticking) {
                window.requestAnimationFrame(function () {
                    if (lastScroll > 10) {
                        header.classList.add('is-scrolled');
                    } else {
                        header.classList.remove('is-scrolled');
                    }
                    ticking = false;
                });
                ticking = true;
            }
        }, { passive: true });
    }

    /* === SEARCH OVERLAY === */
    var searchBtn = document.getElementById('header-search-btn');
    var searchOverlay = document.getElementById('search-overlay');
    var searchClose = document.getElementById('search-overlay-close');
    var searchInput = searchOverlay ? searchOverlay.querySelector('.search-overlay__input') : null;

    if (searchBtn && searchOverlay) {
        searchBtn.addEventListener('click', function () {
            searchOverlay.classList.add('is-active');
            if (searchInput) {
                setTimeout(function () { searchInput.focus(); }, 100);
            }
        });

        if (searchClose) {
            searchClose.addEventListener('click', function () {
                searchOverlay.classList.remove('is-active');
            });
        }

        searchOverlay.addEventListener('click', function (e) {
            if (e.target === searchOverlay) {
                searchOverlay.classList.remove('is-active');
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && searchOverlay.classList.contains('is-active')) {
                searchOverlay.classList.remove('is-active');
            }
            // Cmd/Ctrl + K to open search
            if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                e.preventDefault();
                searchOverlay.classList.toggle('is-active');
                if (searchOverlay.classList.contains('is-active') && searchInput) {
                    setTimeout(function () { searchInput.focus(); }, 100);
                }
            }
        });
    }

    /* === SCROLL TO TOP === */
    var scrollTopBtn = document.getElementById('scroll-top');
    if (scrollTopBtn) {
        var scrollTopTicking = false;
        window.addEventListener('scroll', function () {
            if (!scrollTopTicking) {
                window.requestAnimationFrame(function () {
                    if (window.scrollY > 400) {
                        scrollTopBtn.classList.add('is-visible');
                    } else {
                        scrollTopBtn.classList.remove('is-visible');
                    }
                    scrollTopTicking = false;
                });
                scrollTopTicking = true;
            }
        }, { passive: true });

        scrollTopBtn.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    /* === ANIMATED COUNTERS (IntersectionObserver) === */
    var counters = document.querySelectorAll('[data-count]');
    if (counters.length && 'IntersectionObserver' in window) {
        var counterObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    var el = entry.target;
                    var target = parseInt(el.getAttribute('data-count'), 10);
                    if (isNaN(target) || target <= 0) return;

                    var duration = 1200;
                    var start = 0;
                    var startTime = null;

                    function easeOutCubic(t) {
                        return 1 - Math.pow(1 - t, 3);
                    }

                    function animate(timestamp) {
                        if (!startTime) startTime = timestamp;
                        var progress = Math.min((timestamp - startTime) / duration, 1);
                        var eased = easeOutCubic(progress);
                        var current = Math.floor(eased * target);
                        el.textContent = current.toLocaleString();
                        if (progress < 1) {
                            requestAnimationFrame(animate);
                        } else {
                            el.textContent = target.toLocaleString();
                        }
                    }

                    requestAnimationFrame(animate);
                    counterObserver.unobserve(el);
                }
            });
        }, { threshold: 0.3 });

        counters.forEach(function (counter) {
            counterObserver.observe(counter);
        });
    }

    /* === SCROLL REVEAL (IntersectionObserver) === */
    var reveals = document.querySelectorAll('.reveal');
    if (reveals.length && 'IntersectionObserver' in window) {
        var revealObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    revealObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

        reveals.forEach(function (el) {
            revealObserver.observe(el);
        });
    }

    /* === MOBILE BOTTOM TAB NAV === */
    var mobileNav = document.querySelector('.mobile-tab-nav');
    if (mobileNav) {
        var currentPath = window.location.pathname;
        var tabs = mobileNav.querySelectorAll('.mobile-tab-nav__item');
        tabs.forEach(function (tab) {
            var href = tab.getAttribute('href');
            if (href) {
                var tabPath = new URL(href, window.location.origin).pathname;
                if (currentPath === tabPath || (currentPath === '/' && tabPath === '/')) {
                    tab.classList.add('is-active');
                }
            }
        });
    }

    /* === SMOOTH ANCHOR SCROLL === */
    document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
        anchor.addEventListener('click', function (e) {
            var targetId = this.getAttribute('href');
            if (targetId === '#') return;
            var target = document.querySelector(targetId);
            if (target) {
                e.preventDefault();
                var headerHeight = header ? header.offsetHeight : 0;
                var targetPosition = target.getBoundingClientRect().top + window.scrollY - headerHeight - 16;
                window.scrollTo({ top: targetPosition, behavior: 'smooth' });
            }
        });
    });

    /* === MOBILE HAMBURGER MENU (if present) === */
    var menuToggle = document.querySelector('[data-menu-toggle]');
    var mobileMenu = document.querySelector('[data-mobile-menu]');
    if (menuToggle && mobileMenu) {
        menuToggle.addEventListener('click', function () {
            mobileMenu.classList.toggle('is-open');
            menuToggle.classList.toggle('is-active');
            document.body.classList.toggle('menu-open');
        });
    }

    /* === FOCUS TRAP (for overlays) === */
    function trapFocus(element) {
        var focusableEls = element.querySelectorAll('a[href], button:not([disabled]), input:not([disabled]), textarea:not([disabled]), select:not([disabled]), [tabindex]:not([tabindex="-1"])');
        if (focusableEls.length === 0) return;
        var firstEl = focusableEls[0];
        var lastEl = focusableEls[focusableEls.length - 1];

        element.addEventListener('keydown', function (e) {
            if (e.key !== 'Tab') return;
            if (e.shiftKey) {
                if (document.activeElement === firstEl) {
                    lastEl.focus();
                    e.preventDefault();
                }
            } else {
                if (document.activeElement === lastEl) {
                    firstEl.focus();
                    e.preventDefault();
                }
            }
        });
    }

    if (searchOverlay) {
        trapFocus(searchOverlay);
    }

    /* === SMOOTH PAGE LOAD === */
    document.body.classList.add('is-loaded');

    /* === ICONSCOUT SEARCH WIDGET === */
    (function () {
        var input = document.getElementById('sp-icon-scout-input');
        var btn   = document.getElementById('sp-icon-scout-btn');
        var sel   = document.getElementById('sp-icon-scout-asset');
        var res   = document.getElementById('sp-icon-scout-results');
        var load  = document.getElementById('sp-icon-scout-loading');
        var err   = document.getElementById('sp-icon-scout-error');
        if (!input || !btn) return;

        function doSearch(page) {
            var q = input.value.trim();
            if (!q) return;
            if (res) res.innerHTML = '';
            if (load) load.hidden = false;
            if (err) err.hidden = true;
            var asset = sel ? sel.value : 'icon';
            var xhr = new XMLHttpRequest();
            xhr.open('POST', SampreshanAuth.ajaxUrl);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function () {
                if (load) load.hidden = true;
                if (xhr.status !== 200) {
                    if (err) { err.textContent = 'Search failed. Try again.'; err.hidden = false; }
                    return;
                }
                try {
                    var d = JSON.parse(xhr.responseText);
                    if (d.success && d.data.items) {
                        var html = '';
                        d.data.items.forEach(function (item) {
                            var thumb = item.thumb ? '<img src="' + item.thumb + '" alt="' + item.title + '" width="48" height="48" />' : '';
                            html += '<a class="sp-icon-scout-item" href="' + item.thumb + '" download title="' + item.title + '">' + thumb + '<span>' + item.title + '</span></a>';
                        });
                        if (html) {
                            res.innerHTML = html;
                            if (d.data.has_more) {
                                html += '<a class="sp-icon-scout-item" href="#" data-page="' + (page + 1) + '"><span>More →</span></a>';
                            }
                        } else {
                            res.innerHTML = '<p style="color:var(--muted);grid-column:1/-1;padding:1rem;text-align:center;">No results found.</p>';
                        }
                    }
                } catch (e) { /* ignore */ }
            };
            xhr.onerror = function () { if (load) load.hidden = true; };
            xhr.send('action=sp_iconscout_search&nonce=' + SampreshanAuth.phoneNonce + '&query=' + encodeURIComponent(q) + '&asset=' + encodeURIComponent(asset) + '&page=' + (page || 1));
        }

        btn.addEventListener('click', function (e) { e.preventDefault(); doSearch(1); });
        input.addEventListener('keydown', function (e) { if (e.key === 'Enter') { e.preventDefault(); doSearch(1); } });
        document.addEventListener('click', function (e) {
            var more = e.target.closest('[data-page]');
            if (more) { e.preventDefault(); doSearch(parseInt(more.dataset.page, 10) || 1); }
        });
    })();
})();
