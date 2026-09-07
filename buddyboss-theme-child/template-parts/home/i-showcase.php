<?php
/**
 * Home: I-Showcase — mature 3D eye morph with breathing I→eye loop.
 * Loop: I badge breathes small→big → morphs into eye → absorbed into
 * retina → eye fades → I badge returns. Continuous infinite play.
 *
 * @package SampreShan_Child
 */

$start_url = home_url( '/start-a-petition/' );
$feed_url  = function_exists( 'sp_feed_url' ) ? sp_feed_url() : home_url( '/feed-2/' );
?>
<section class="d3-section" aria-labelledby="sp-ishow-h" style="overflow:hidden;">
    <div class="d3-section__light" aria-hidden="true"></div>
    <div class="d3-section__inner" style="display:flex; align-items:center; gap:clamp(2rem, 5vw, 5rem); flex-wrap:wrap;">
        <div class="d3-reveal-left" style="flex:1 1 340px; max-width:520px;">
            <p class="d3-section__eyebrow"><?php esc_html_e( 'See it work — live', 'sampreshan-child' ); ?></p>
            <h2 class="d3-section__title" id="sp-ishow-h"><?php esc_html_e( 'One tap. One ', 'sampreshan-child' ); ?><span style="color:var(--d3-neon-saffron); text-shadow:0 0 20px rgba(255,153,51,0.5);"><?php esc_html_e( 'support.', 'sampreshan-child' ); ?></span></h2>
            <p class="d3-section__sub"><?php esc_html_e( 'After any login — email, OTP or social — tap the badge on any issue. Watch: it slowly becomes an eye, holds for two seconds, then returns as your counted support. Just like a Like — but sacred.', 'sampreshan-child' ); ?></p>
            <ol style="list-style:none; padding:0; margin:1.5rem 0 2rem; display:flex; flex-direction:column; gap:0.75rem;">
                <li style="display:flex; align-items:center; gap:0.75rem;">
                    <span style="width:28px; height:28px; border-radius:50%; background:linear-gradient(135deg, var(--d3-neon-saffron), var(--d3-neon-ember)); display:inline-flex; align-items:center; justify-content:center; font-size:0.8rem; font-weight:700; color:#fff; flex-shrink:0; box-shadow:var(--d3-glow-sm);">1</span>
                    <?php esc_html_e( 'Tap the badge on any issue', 'sampreshan-child' ); ?>
                </li>
                <li style="display:flex; align-items:center; gap:0.75rem;">
                    <span style="width:28px; height:28px; border-radius:50%; background:linear-gradient(135deg, var(--d3-neon-saffron), var(--d3-neon-ember)); display:inline-flex; align-items:center; justify-content:center; font-size:0.8rem; font-weight:700; color:#fff; flex-shrink:0; box-shadow:var(--d3-glow-sm);">2</span>
                    <?php esc_html_e( 'The eye forms — your support is seen', 'sampreshan-child' ); ?>
                </li>
                <li style="display:flex; align-items:center; gap:0.75rem;">
                    <span style="width:28px; height:28px; border-radius:50%; background:linear-gradient(135deg, var(--d3-neon-saffron), var(--d3-neon-ember)); display:inline-flex; align-items:center; justify-content:center; font-size:0.8rem; font-weight:700; color:#fff; flex-shrink:0; box-shadow:var(--d3-glow-sm);">3</span>
                    <?php esc_html_e( 'The badge returns with the growing count', 'sampreshan-child' ); ?>
                </li>
            </ol>
            <div style="display:flex; gap:1rem; flex-wrap:wrap;">
                <a class="d3-btn d3-btn--primary d3-btn--lg" href="<?php echo esc_url( $start_url ); ?>"><?php esc_html_e( 'Raise an Issue', 'sampreshan-child' ); ?></a>
                <a class="d3-btn d3-btn--ghost d3-btn--lg" href="<?php echo esc_url( $feed_url ); ?>"><?php esc_html_e( 'Try it on live issues', 'sampreshan-child' ); ?></a>
            </div>
        </div>

        <div class="d3-reveal-right" style="flex:1 1 280px; max-width:380px; display:flex; justify-content:center;">
            <!-- Breathing I→Eye Morph Loop Stage -->
            <div class="sp-i-stage" aria-hidden="true">
                <!-- I Badge (breathes small→big, then morphs into eye) -->
                <div class="sp-i-breathe" id="sp-i-breathe">
                    <svg width="180" height="180" viewBox="0 0 180 180" style="overflow:visible;">
                        <defs>
                            <filter id="ibNeon" x="-30%" y="-30%" width="160%" height="160%">
                                <feGaussianBlur in="SourceAlpha" stdDeviation="4" result="b"/>
                                <feFlood flood-color="#FF9933" flood-opacity="0.5" result="c"/>
                                <feComposite in="c" in2="b" operator="in" result="g"/>
                                <feMerge><feMergeNode in="g"/><feMergeNode in="SourceGraphic"/></feMerge>
                            </filter>
                            <linearGradient id="ibGold" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#FFE9C4"/>
                                <stop offset="35%" stop-color="#F5C542"/>
                                <stop offset="100%" stop-color="#B8860B"/>
                            </linearGradient>
                            <linearGradient id="ibSaffron" x1="0" y1="0" x2="1" y2="0">
                                <stop offset="0%" stop-color="#FFB877"/>
                                <stop offset="50%" stop-color="#FF9933"/>
                                <stop offset="100%" stop-color="#C2410C"/>
                            </linearGradient>
                        </defs>
                        <g filter="url(#ibNeon)">
                            <rect x="54" y="38" width="72" height="18" rx="9" fill="url(#ibGold)"/>
                            <rect x="78" y="56" width="24" height="60" rx="6" fill="url(#ibSaffron)"/>
                            <rect x="54" y="116" width="72" height="18" rx="9" fill="url(#ibGold)"/>
                            <rect x="60" y="42" width="60" height="4" rx="2" fill="#fff" opacity="0.6"/>
                            <rect x="84" y="60" width="5" height="52" rx="2.5" fill="#fff" opacity="0.45"/>
                            <rect x="60" y="120" width="60" height="4" rx="2" fill="#fff" opacity="0.6"/>
                        </g>
                        <!-- Neon letter glow -->
                        <rect x="78" y="56" width="24" height="60" rx="6" fill="none"
                              stroke="rgba(255,153,51,0.6)" stroke-width="2" class="sp-i-breathe__glow"/>
                    </svg>
                </div>

                <!-- Eye (appears after I morphs) -->
                <div class="sp-eye-morph" id="sp-eye-morph">
                    <svg width="180" height="180" viewBox="0 0 180 180" style="overflow:visible;">
                        <defs>
                            <filter id="eyeNeonGlow2" x="-40%" y="-40%" width="180%" height="180%">
                                <feGaussianBlur in="SourceAlpha" stdDeviation="5" result="blur1"/>
                                <feFlood flood-color="#FF9933" flood-opacity="0.5" result="color1"/>
                                <feComposite in="color1" in2="blur1" operator="in" result="glow1"/>
                                <feGaussianBlur in="SourceAlpha" stdDeviation="12" result="blur2"/>
                                <feFlood flood-color="#FF9933" flood-opacity="0.2" result="color2"/>
                                <feComposite in="color2" in2="blur2" operator="in" result="glow2"/>
                                <feMerge>
                                    <feMergeNode in="glow2"/>
                                    <feMergeNode in="glow1"/>
                                    <feMergeNode in="SourceGraphic"/>
                                </feMerge>
                            </filter>
                            <radialGradient id="eyeIris2" cx="50%" cy="50%" r="50%">
                                <stop offset="0%" stop-color="#FFD080"/>
                                <stop offset="40%" stop-color="#FF9933"/>
                                <stop offset="80%" stop-color="#CC5500"/>
                                <stop offset="100%" stop-color="#8B3A00"/>
                            </radialGradient>
                            <radialGradient id="eyePupil2" cx="45%" cy="45%" r="50%">
                                <stop offset="0%" stop-color="#1a0a00"/>
                                <stop offset="100%" stop-color="#000"/>
                            </radialGradient>
                            <radialGradient id="eyeHL2" cx="35%" cy="30%" r="30%">
                                <stop offset="0%" stop-color="rgba(255,255,255,0.9)"/>
                                <stop offset="100%" stop-color="rgba(255,255,255,0)"/>
                            </radialGradient>
                        </defs>

                        <!-- Outer glow ring -->
                        <circle cx="90" cy="90" r="72" fill="none" stroke="rgba(255,153,51,0.12)" stroke-width="1"
                                class="sp-eye-3d__glow-ring"/>

                        <!-- Eyebrow -->
                        <path d="M30 52 Q60 22, 90 20 Q120 22, 150 52"
                              fill="none" stroke="rgba(90,60,30,0.7)" stroke-width="4" stroke-linecap="round"
                              class="sp-eye-3d__brow"/>

                        <!-- Eye white (sclera) -->
                        <path class="sp-eye-3d__outline"
                              d="M18 90 C18 55, 55 25, 90 25 C125 25, 162 55, 162 90 C162 125, 125 155, 90 155 C55 155, 18 125, 18 90 Z"
                              fill="#f5f0e8" stroke="rgba(139,58,0,0.3)" stroke-width="1.5"
                              filter="url(#eyeNeonGlow2)"/>

                        <!-- Upper eyelid crease -->
                        <path d="M25 85 C25 52, 58 28, 90 28 C122 28, 155 52, 155 85"
                              fill="none" stroke="rgba(139,58,0,0.15)" stroke-width="1"/>

                        <!-- Iris -->
                        <circle class="sp-eye-3d__iris" cx="90" cy="90" r="32" fill="url(#eyeIris2)"/>
                        <circle cx="90" cy="90" r="28" fill="none" stroke="rgba(139,58,0,0.12)" stroke-width="0.5"/>
                        <circle cx="90" cy="90" r="22" fill="none" stroke="rgba(255,208,128,0.15)" stroke-width="0.5"/>

                        <!-- Pupil -->
                        <g class="sp-eye-3d__pupil">
                            <circle cx="90" cy="90" r="14" fill="url(#eyePupil2)"/>
                            <circle cx="86" cy="85" r="4" fill="rgba(255,255,255,0.12)"/>
                        </g>

                        <!-- Glass highlights -->
                        <circle cx="78" cy="75" r="8" fill="url(#eyeHL2)"/>
                        <circle cx="100" cy="100" r="3" fill="rgba(255,255,255,0.3)"/>

                        <!-- Neon rim -->
                        <path d="M18 90 C18 55, 55 25, 90 25 C125 25, 162 55, 162 90"
                              fill="none" stroke="rgba(255,153,51,0.25)" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>

                <!-- +1 pop bubbles -->
                <span class="sp-i-pop sp-i-pop--1">+1</span>
                <span class="sp-i-pop sp-i-pop--2">+1</span>
                <span class="sp-i-pop sp-i-pop--3">+1</span>
            </div>
        </div>
    </div>

    <!-- Breathing I→Eye morph loop JS -->
    <script>
    (function () {
        'use strict';
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

        var iBadge = document.getElementById('sp-i-breathe');
        var eyeEl  = document.getElementById('sp-eye-morph');
        if (!iBadge || !eyeEl) return;

        // Initial states
        iBadge.style.cssText = 'opacity:1; transform:scale(0.6); transition:none;';
        eyeEl.style.cssText  = 'opacity:0; transform:scale(0.3); transition:none;';

        var phase = 0; // 0=I breathing, 1=I→eye morph, 2=eye holds, 3=eye absorbed, 4=reset
        var timer = 0;
        var start = performance.now();

        // Phase durations (ms)
        var DURATIONS = [2400, 1200, 2000, 1000, 600];

        function easeOutBack(t) {
            var c1 = 1.70158;
            var c3 = c1 + 1;
            return 1 + c3 * Math.pow(t - 1, 3) + c1 * Math.pow(t - 1, 2);
        }
        function easeInBack(t) {
            var c1 = 1.70158;
            var c3 = c1 + 1;
            return c3 * t * t * t - c1 * t * t;
        }
        function easeOutQuad(t) { return 1 - (1 - t) * (1 - t); }
        function easeInQuad(t) { return t * t; }

        function tick(now) {
            var elapsed = now - start;
            var dur = DURATIONS[phase];
            var t = Math.min(elapsed / dur, 1);

            if (phase === 0) {
                // I badge breathing: scale 0.6 → 1.0 → 0.6 (breathing loop within phase)
                var breathe = Math.sin(t * Math.PI * 2) * 0.2 + 0.8;
                iBadge.style.opacity = '1';
                iBadge.style.transform = 'scale(' + (0.6 + breathe * 0.4) + ')';
                iBadge.style.filter = 'drop-shadow(0 0 ' + (8 + breathe * 12) + 'px rgba(255,153,51,' + (0.3 + breathe * 0.3) + '))';
            }
            else if (phase === 1) {
                // I → Eye morph: I shrinks + fades, eye appears + grows
                var ease = easeOutBack(t);
                iBadge.style.opacity = 1 - t;
                iBadge.style.transform = 'scale(' + (1 - t * 0.7) + ')';
                eyeEl.style.opacity = t;
                eyeEl.style.transform = 'scale(' + ease + ')';
            }
            else if (phase === 2) {
                // Eye holds — subtle pulse
                var pulse = 1 + Math.sin(t * Math.PI * 4) * 0.02;
                eyeEl.style.opacity = '1';
                eyeEl.style.transform = 'scale(' + pulse + ')';
            }
            else if (phase === 3) {
                // Eye absorbed into retina — shrinks into pupil center
                var ease = easeInQuad(t);
                eyeEl.style.opacity = 1 - t;
                eyeEl.style.transform = 'scale(' + (1 - ease * 0.8) + ')';
            }
            else if (phase === 4) {
                // Reset: fade out eye, prepare I return
                eyeEl.style.opacity = '0';
                eyeEl.style.transform = 'scale(0.3)';
                iBadge.style.opacity = '0';
            }

            if (t >= 1) {
                phase++;
                start = now;
                if (phase >= DURATIONS.length) {
                    phase = 0;
                    iBadge.style.opacity = '1';
                    iBadge.style.filter = '';
                }
            }

            requestAnimationFrame(tick);
        }

        requestAnimationFrame(tick);
    })();
    </script>
</section>
