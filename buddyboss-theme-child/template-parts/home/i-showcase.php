<?php
/**
 * Home: I-Showcase — mature 3D eye morph with neon glow.
 * Live looping demo: badge → eye slowly forms → badge returns + count.
 * Continuous subtle motion on eye pupil + iris glow pulse.
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
            <!-- 3D Eye Demo Stage -->
            <div class="sp-i-stage" aria-hidden="true">
                <svg width="180" height="180" viewBox="0 0 180 180" style="overflow:visible;">
                    <defs>
                        <!-- Neon saffron glow filter -->
                        <filter id="eyeNeonGlow" x="-40%" y="-40%" width="180%" height="180%">
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
                        <!-- Iris gradient -->
                        <radialGradient id="eyeIris" cx="50%" cy="50%" r="50%">
                            <stop offset="0%" stop-color="#FFD080"/>
                            <stop offset="40%" stop-color="#FF9933"/>
                            <stop offset="80%" stop-color="#CC5500"/>
                            <stop offset="100%" stop-color="#8B3A00"/>
                        </radialGradient>
                        <!-- Pupil depth -->
                        <radialGradient id="eyePupil" cx="45%" cy="45%" r="50%">
                            <stop offset="0%" stop-color="#1a0a00"/>
                            <stop offset="100%" stop-color="#000000"/>
                        </radialGradient>
                        <!-- Glass highlight -->
                        <radialGradient id="eyeHighlight" cx="35%" cy="30%" r="30%">
                            <stop offset="0%" stop-color="rgba(255,255,255,0.9)"/>
                            <stop offset="100%" stop-color="rgba(255,255,255,0)"/>
                        </radialGradient>
                    </defs>

                    <!-- Outer glow ring -->
                    <circle cx="90" cy="90" r="72" fill="none" stroke="rgba(255,153,51,0.15)" stroke-width="1"
                            class="sp-eye-3d__glow-ring"/>

                    <!-- Eye white (sclera) -->
                    <path class="sp-eye-3d__outline" d="M18 90 C18 50, 60 20, 90 20 C120 20, 162 50, 162 90 C162 130, 120 160, 90 160 C60 160, 18 130, 18 90 Z"
                          fill="#f5f0e8" stroke="rgba(139,58,0,0.3)" stroke-width="1.5"
                          filter="url(#eyeNeonGlow)"/>

                    <!-- Iris -->
                    <circle class="sp-eye-3d__iris" cx="90" cy="90" r="32" fill="url(#eyeIris)"/>

                    <!-- Iris detail rings -->
                    <circle cx="90" cy="90" r="28" fill="none" stroke="rgba(139,58,0,0.15)" stroke-width="0.5"/>
                    <circle cx="90" cy="90" r="22" fill="none" stroke="rgba(255,208,128,0.2)" stroke-width="0.5"/>

                    <!-- Pupil -->
                    <g class="sp-eye-3d__pupil">
                        <circle cx="90" cy="90" r="14" fill="url(#eyePupil)"/>
                        <!-- Pupil inner highlight -->
                        <circle cx="86" cy="85" r="4" fill="rgba(255,255,255,0.15)"/>
                    </g>

                    <!-- Main highlight (glass) -->
                    <circle cx="78" cy="75" r="8" fill="url(#eyeHighlight)"/>
                    <!-- Secondary highlight -->
                    <circle cx="100" cy="100" r="3" fill="rgba(255,255,255,0.35)"/>

                    <!-- Neon rim light -->
                    <path d="M18 90 C18 50, 60 20, 90 20 C120 20, 162 50, 162 90"
                          fill="none" stroke="rgba(255,153,51,0.3)" stroke-width="2"
                          stroke-linecap="round"/>
                </svg>

                <!-- +1 pop bubbles -->
                <span class="sp-i-pop sp-i-pop--1">+1</span>
                <span class="sp-i-pop sp-i-pop--2">+1</span>
                <span class="sp-i-pop sp-i-pop--3">+1</span>
            </div>
        </div>
    </div>

    <!-- Counter below -->
    <div class="d3-reveal" style="text-align:center; margin-top:clamp(2rem, 5vw, 3rem);">
        <div style="font-size:clamp(2rem, 5vw, 3.5rem); font-weight:800; color:var(--d3-text); letter-spacing:-0.02em;">
            <span data-count="12450">0</span><span style="color:var(--d3-neon-saffron); text-shadow:var(--d3-glow-sm);">+</span>
        </div>
        <p style="color:var(--d-text-muted); font-size:0.95rem; margin-top:0.5rem;"><?php esc_html_e( 'Every share and support adds another supporter', 'sampreshan-child' ); ?></p>
    </div>
</section>
