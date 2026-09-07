/**
 * PREMIUM 3D SCROLL ENGINE — IntersectionObserver + CSS Perspective
 * Handles: d3-reveal animations, 3D scroll rotations, parallax depth,
 * animated counters, horizontal scroll carousel, hero 3D mouse-follow,
 * cinematic easing on all transitions.
 * @package SampreShan_Child
 */
(function () {
    'use strict';

    /* ── Reduced motion: show everything immediately ── */
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        document.querySelectorAll(
            '.d3-reveal, .d3-reveal-left, .d3-reveal-right, .d3-reveal-scale, .d3-stagger, ' +
            '.reveal, .reveal-left, .reveal-right, .reveal-scale, .reveal-stagger'
        ).forEach(function (el) {
            el.classList.add('is-visible');
        });
        return;
    }

    /* ── Enable animations (content is visible by default, JS enables motion) ── */
    document.documentElement.classList.add('d3-animated');

    /* ── Cinematic easing (shared) ── */
    function easeOutQuart(t) { return 1 - Math.pow(1 - t, 4); }
    function easeOutExpo(t) { return t === 1 ? 1 : 1 - Math.pow(2, -10 * t); }

    /* ════════════════════════════════════════════
       1. REVEAL ON SCROLL (d3-reveal family)
       ════════════════════════════════════════════ */
    var revealObserver = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                revealObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.10, rootMargin: '0px 0px -50px 0px' });

    document.querySelectorAll(
        '.d3-reveal, .d3-reveal-left, .d3-reveal-right, .d3-reveal-scale, .d3-stagger, ' +
        '.reveal, .reveal-left, .reveal-right, .reveal-scale, .reveal-stagger'
    ).forEach(function (el) {
        revealObserver.observe(el);
    });

    /* ════════════════════════════════════════════
       2. ANIMATED COUNTERS
       ════════════════════════════════════════════ */
    var counterObserver = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (!entry.isIntersecting) return;
            var el = entry.target;
            var target = parseInt(el.getAttribute('data-count'), 10);
            if (isNaN(target) || target <= 0) { counterObserver.unobserve(el); return; }

            var duration = 2200;
            var start = performance.now();

            function format(n) { return n.toLocaleString('en-IN'); }

            function step(now) {
                var progress = Math.min((now - start) / duration, 1);
                var eased = easeOutQuart(progress);
                el.textContent = format(Math.round(target * eased));
                if (progress < 1) {
                    requestAnimationFrame(step);
                } else {
                    el.textContent = format(target);
                }
            }

            el.textContent = '0';
            requestAnimationFrame(step);
            counterObserver.unobserve(el);
        });
    }, { threshold: 0.5 });

    document.querySelectorAll('[data-count]').forEach(function (el) {
        counterObserver.observe(el);
    });

    /* ════════════════════════════════════════════
       3. 3D SCROLL ROTATIONS (data-d3-rotate)
       ════════════════════════════════════════════ */
    var rotateEls = document.querySelectorAll('[data-d3-rotate]');
    if (rotateEls.length > 0) {
        var rotateTicking = false;
        function updateRotations() {
            var scrollY = window.pageYOffset;
            var vh = window.innerHeight;
            rotateEls.forEach(function (el) {
                var rect = el.getBoundingClientRect();
                var progress = 1 - (rect.top + rect.height) / (vh + rect.height);
                progress = Math.max(0, Math.min(1, progress));
                var rotateX = (parseFloat(el.getAttribute('data-d3-rotate-x')) || 0) * progress;
                var rotateY = (parseFloat(el.getAttribute('data-d3-rotate-y')) || 0) * progress;
                var rotateZ = (parseFloat(el.getAttribute('data-d3-rotate-z')) || 0) * progress;
                var translateZ = (parseFloat(el.getAttribute('data-d3-translate-z')) || 0) * progress;
                el.style.transform =
                    'perspective(1200px) ' +
                    'rotateX(' + rotateX + 'deg) ' +
                    'rotateY(' + rotateY + 'deg) ' +
                    'rotateZ(' + rotateZ + 'deg) ' +
                    'translateZ(' + translateZ + 'px)';
            });
            rotateTicking = false;
        }
        window.addEventListener('scroll', function () {
            if (!rotateTicking) { requestAnimationFrame(updateRotations); rotateTicking = true; }
        }, { passive: true });
        updateRotations();
    }

    /* ════════════════════════════════════════════
       4. PARALLAX DEPTH (data-parallax + data-parallax-z)
       ════════════════════════════════════════════ */
    var parallaxEls = document.querySelectorAll('[data-parallax], [data-parallax-z]');
    if (parallaxEls.length > 0) {
        var parallaxTicking = false;
        function updateParallax() {
            var scrollY = window.pageYOffset;
            var vh = window.innerHeight;
            parallaxEls.forEach(function (el) {
                var rect = el.getBoundingClientRect();
                var centerY = rect.top + rect.height / 2;
                var viewProgress = 1 - (rect.top + rect.height) / (vh + rect.height);
                viewProgress = Math.max(0, Math.min(1, viewProgress));

                var ySpeed = parseFloat(el.getAttribute('data-parallax')) || 0;
                var zSpeed = parseFloat(el.getAttribute('data-parallax-z')) || 0;
                var y = (vh / 2 - centerY) * ySpeed;
                var z = zSpeed * viewProgress * 100;

                var existingTransform = el.style.transform || '';
                // Only replace translate3d parts, preserve perspective/rotate from 3D scroll
                el.style.transform = existingTransform
                    .replace(/translate3d\([^)]+\)/g, '')
                    .trim() +
                    ' translate3d(0,' + y + 'px,' + z + 'px)';
            });
            parallaxTicking = false;
        }
        window.addEventListener('scroll', function () {
            if (!parallaxTicking) { requestAnimationFrame(updateParallax); parallaxTicking = true; }
        }, { passive: true });
        updateParallax();
    }

    /* ════════════════════════════════════════════
       5. HORIZONTAL SCROLL CAROUSEL (d3-hscroll)
       ════════════════════════════════════════════ */
    document.querySelectorAll('.d3-hscroll, .prem-hscroll').forEach(function (container) {
        var track = container.querySelector('.d3-hscroll__track, .prem-hscroll__track');
        var prevBtn = container.querySelector('.d3-hscroll__nav--prev, .prem-hscroll__nav--prev');
        var nextBtn = container.querySelector('.d3-hscroll__nav--next, .prem-hscroll__nav--next');
        if (!track) return;

        var scrollAmount = 340;

        if (prevBtn) {
            prevBtn.addEventListener('click', function () {
                track.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
            });
        }
        if (nextBtn) {
            nextBtn.addEventListener('click', function () {
                track.scrollBy({ left: scrollAmount, behavior: 'smooth' });
            });
        }

        // Keyboard navigation
        track.setAttribute('tabindex', '0');
        track.setAttribute('role', 'region');
        track.setAttribute('aria-label', 'Petitions carousel');
        track.addEventListener('keydown', function (e) {
            if (e.key === 'ArrowLeft') { track.scrollBy({ left: -scrollAmount, behavior: 'smooth' }); }
            if (e.key === 'ArrowRight') { track.scrollBy({ left: scrollAmount, behavior: 'smooth' }); }
        });
    });

    /* ════════════════════════════════════════════
       6. HERO 3D MOUSE-FOLLOW (d3-hero__object)
       ════════════════════════════════════════════ */
    var heroObject = document.querySelector('.d3-hero__object');
    var heroSection = document.querySelector('.d3-hero');
    if (heroObject && heroSection && window.innerWidth > 768) {
        heroSection.addEventListener('mousemove', function (e) {
            var rect = heroSection.getBoundingClientRect();
            var x = (e.clientX - rect.left) / rect.width - 0.5;
            var y = (e.clientY - rect.top) / rect.height - 0.5;
            heroObject.style.transform =
                'perspective(1000px) rotateY(' + (x * 12) + 'deg) rotateX(' + (-y * 12) + 'deg) translateZ(20px)';
        });
        heroSection.addEventListener('mouseleave', function () {
            heroObject.style.transform = 'perspective(1000px) rotateY(-4deg) rotateX(2deg) translateZ(0)';
        });
    }

    /* ════════════════════════════════════════════
       7. 3D CARD HOVER (d3-card, d3-petcard, d3-howcard)
       ════════════════════════════════════════════ */
    document.querySelectorAll('.d3-card, .d3-petcard, .d3-howcard').forEach(function (card) {
        if (window.innerWidth <= 768) return;
        card.addEventListener('mousemove', function (e) {
            var rect = card.getBoundingClientRect();
            var x = (e.clientX - rect.left) / rect.width - 0.5;
            var y = (e.clientY - rect.top) / rect.height - 0.5;
            card.style.transform =
                'perspective(800px) rotateY(' + (x * 6) + 'deg) rotateX(' + (-y * 6) + 'deg) translateZ(8px)';
            card.style.transition = 'transform 0.1s ease-out';
        });
        card.addEventListener('mouseleave', function () {
            card.style.transform = 'perspective(800px) rotateY(0) rotateX(0) translateZ(0)';
            card.style.transition = 'transform 0.6s cubic-bezier(0.23, 1, 0.32, 1)';
        });
    });

    /* ════════════════════════════════════════════
       8. SMOOTH ANCHOR SCROLLING
       ════════════════════════════════════════════ */
    document.querySelectorAll('a[href^="#"]').forEach(function (link) {
        link.addEventListener('click', function (e) {
            var target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    /* ════════════════════════════════════════════
       9. SCROLL INDICATOR FADE (d3-hero__scroll)
       ════════════════════════════════════════════ */
    var scrollIndicator = document.querySelector('.d3-hero__scroll');
    if (scrollIndicator) {
        var fadeTicking = false;
        window.addEventListener('scroll', function () {
            if (!fadeTicking) {
                requestAnimationFrame(function () {
                    var opacity = Math.max(0, 1 - window.pageYOffset / 300);
                    scrollIndicator.style.opacity = opacity;
                    scrollIndicator.style.pointerEvents = opacity < 0.1 ? 'none' : 'auto';
                    fadeTicking = false;
                });
                fadeTicking = true;
            }
        }, { passive: true });
    }

})();
