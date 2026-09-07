<?php
/**
 * Home: Flag Cinema — Premium 3D Waving Sanatan Dhwaja
 * JS sine-wave animation, glass transparency, neon saffron glow.
 * "वसुधैव कुटुम्बकम्" in handwriting style flowing with the flag wave.
 * Pure JS + SVG, no external deps.
 *
 * @package SampreShan_Child
 */
?>
<section class="d3-section sp-flagcinema-3d" aria-label="<?php esc_attr_e( 'Sanatan Dharma flag with Sampreshan', 'sampreshan-child' ); ?>" style="overflow:hidden;">
    <div class="d3-section__light" aria-hidden="true"></div>

    <!-- Neon glow behind flag -->
    <div class="sp-flag-3d__glow" aria-hidden="true"></div>

    <div class="sp-flag-3d" role="img" aria-label="<?php esc_attr_e( 'Waving saffron flag of Sanatan Dharma', 'sampreshan-child' ); ?>">
        <!-- Pole -->
        <div class="sp-flag-3d__pole" aria-hidden="true"></div>

        <!-- Flag canvas — JS draws sine-wave paths here -->
        <div class="sp-flag-3d__canvas">
            <svg id="sp-flag-svg" viewBox="0 0 420 280" preserveAspectRatio="xMidYMid meet">
                <defs>
                    <!-- Saffron gradient (front face) -->
                    <linearGradient id="flagGradFront" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#FFB877"/>
                        <stop offset="35%" stop-color="#FF9933"/>
                        <stop offset="70%" stop-color="#E87A1A"/>
                        <stop offset="100%" stop-color="#CC5500"/>
                    </linearGradient>
                    <!-- Darker saffron (back face / shadow folds) -->
                    <linearGradient id="flagGradBack" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#CC6600"/>
                        <stop offset="100%" stop-color="#8B3A00"/>
                    </linearGradient>
                    <!-- Glass highlight -->
                    <linearGradient id="flagGlass" x1="0" y1="0" x2="1" y2="1">
                        <stop offset="0%" stop-color="rgba(255,255,255,0.18)"/>
                        <stop offset="50%" stop-color="rgba(255,255,255,0.03)"/>
                        <stop offset="100%" stop-color="rgba(255,255,255,0.12)"/>
                    </linearGradient>
                    <!-- Neon glow filter -->
                    <filter id="flagNeonGlow" x="-20%" y="-20%" width="140%" height="140%">
                        <feGaussianBlur in="SourceAlpha" stdDeviation="6" result="blur"/>
                        <feFlood flood-color="#FF9933" flood-opacity="0.35" result="color"/>
                        <feComposite in="color" in2="blur" operator="in" result="glow"/>
                        <feMerge>
                            <feMergeNode in="glow"/>
                            <feMergeNode in="SourceGraphic"/>
                        </feMerge>
                    </filter>
                </defs>

                <!-- Back cloth (shadow layer) -->
                <path id="flagBack" d="" fill="url(#flagGradBack)" opacity="0.6"/>
                <!-- Front cloth -->
                <path id="flagFront" d="" fill="url(#flagGradFront)" filter="url(#flagNeonGlow)"/>
                <!-- Glass highlight overlay -->
                <path id="flagGlassPath" d="" fill="url(#flagGlass)"/>

                <!-- "वसुधैव कुटुम्बकम्" handwriting — moves with flag wave -->
                <text id="flagText1" text-anchor="middle" dominant-baseline="central"
                      font-size="26" font-family="'Kalam','Caveat','Noto Sans Devanagari',cursive"
                      font-style="italic" font-weight="700"
                      fill="rgba(255,248,236,0.92)" style="paint-order:stroke; letter-spacing:0.04em;"
                      stroke="rgba(139,58,0,0.35)" stroke-width="0.8">वसुधैव</text>
                <text id="flagText2" text-anchor="middle" dominant-baseline="central"
                      font-size="26" font-family="'Kalam','Caveat','Noto Sans Devanagari',cursive"
                      font-style="italic" font-weight="700"
                      fill="rgba(255,248,236,0.92)" style="paint-order:stroke; letter-spacing:0.04em;"
                      stroke="rgba(139,58,0,0.35)" stroke-width="0.8">कुटुम्बकम्</text>
            </svg>
        </div>

        <!-- Glass overlay -->
        <div class="sp-flag-3d__glass" aria-hidden="true"></div>
    </div>

    <!-- Word cycle -->
    <div class="sp-flagcinema-3d__words" aria-hidden="true">
        <span class="w-hi">संप्रेषण</span>
        <span class="w-en">SAMPRESHAN</span>
    </div>

    <p class="d3-section__sub" style="text-align:center; margin-top:clamp(1rem, 3vw, 2rem); color:var(--d-text-muted); max-width:480px; margin-left:auto; margin-right:auto;">
        <?php esc_html_e( 'One flag. One voice. Sanatan Dharma.', 'sampreshan-child' ); ?>
    </p>

    <!-- JS: sine-wave flag animation + handwriting flow -->
    <script>
    (function () {
        'use strict';
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

        var front  = document.getElementById('flagFront');
        var back   = document.getElementById('flagBack');
        var glass  = document.getElementById('flagGlassPath');
        var text1  = document.getElementById('flagText1');
        var text2  = document.getElementById('flagText2');
        if (!front || !back) return;

        var W = 420, H = 280;
        var startX = 14, endX = W - 10;
        var topY = 28, botY = topY + 110;
        var segments = 40;
        var segW = (endX - startX) / segments;
        var time = 0;

        function waveY(x, t, amp, freq) {
            return Math.sin((x / W) * freq * Math.PI * 2 + t) * amp
                 + Math.sin((x / W) * freq * 1.7 * Math.PI * 2 + t * 0.7) * amp * 0.4;
        }

        function buildPath(t, yBase, amp, freq) {
            var pts = [];
            for (var i = 0; i <= segments; i++) {
                var x = startX + i * segW;
                var yOff = waveY(x, t, amp, freq);
                pts.push([x, yBase + yOff]);
            }
            return pts;
        }

        function ptsToD(topPts, botPts) {
            var d = 'M' + topPts[0][0] + ',' + topPts[0][1];
            for (var i = 1; i < topPts.length; i++) {
                d += ' L' + topPts[i][0] + ',' + topPts[i][1];
            }
            for (var i = botPts.length - 1; i >= 0; i--) {
                d += ' L' + botPts[i][0] + ',' + botPts[i][1];
            }
            d += ' Z';
            return d;
        }

        function animate() {
            time += 0.025;

            var topFront = buildPath(time, topY, 8, 1.8);
            var botFront = buildPath(time + 0.3, botY, 10, 1.8);
            var topBack  = buildPath(time - 0.15, topY + 3, 7, 1.6);
            var botBack  = buildPath(time + 0.15, botY + 3, 9, 1.6);

            var dFront = ptsToD(topFront, botFront);
            var dBack  = ptsToD(topBack, botBack);

            front.setAttribute('d', dFront);
            back.setAttribute('d', dBack);
            glass.setAttribute('d', dFront);

            // Move "वसुधैव" with flag center (top half)
            var midX1 = startX + (endX - startX) * 0.48;
            var midYOff1 = waveY(midX1, time + 0.15, 8, 1.8);
            text1.setAttribute('x', midX1);
            text1.setAttribute('y', topY + 35 + midYOff1);
            // Subtle rotation follows the wave slope
            var dx1 = waveY(midX1 + 2, time + 0.15, 8, 1.8) - midYOff1;
            text1.setAttribute('transform', 'rotate(' + (dx1 * 1.2) + ',' + midX1 + ',' + (topY + 35 + midYOff1) + ')');

            // Move "कुटुम्बकम्" with flag lower half
            var midX2 = startX + (endX - startX) * 0.48;
            var midYOff2 = waveY(midX2, time + 0.45, 10, 1.8);
            text2.setAttribute('x', midX2);
            text2.setAttribute('y', topY + 72 + midYOff2);
            var dx2 = waveY(midX2 + 2, time + 0.45, 10, 1.8) - midYOff2;
            text2.setAttribute('transform', 'rotate(' + (dx2 * 1.2) + ',' + midX2 + ',' + (topY + 72 + midYOff2) + ')');

            requestAnimationFrame(animate);
        }

        animate();
    })();
    </script>
</section>
