<?php
/**
 * Home: I-Showcase — how one tap becomes an I (full-width).
 * Live looping demo: I badge → eye slowly forms → I returns + count.
 *
 * @package SampreShan_Child
 */

$start_url = home_url( '/start-a-petition/' );
$feed_url  = function_exists( 'sp_feed_url' ) ? sp_feed_url() : home_url( '/feed-2/' );
?>
<section class="sp-ishow sp-fullbleed" aria-labelledby="sp-ishow-h">
    <div class="sp-ishow__inner">
        <div class="sp-ishow__copy">
            <p class="sp-ishow__eyebrow"><?php esc_html_e( 'See it work — live', 'sampreshan-child' ); ?></p>
            <h2 class="sp-ishow__title" id="sp-ishow-h"><?php esc_html_e( 'One tap. One ', 'sampreshan-child' ); ?><span class="sp-i-accent"><?php esc_html_e( 'support.', 'sampreshan-child' ); ?></span></h2>
            <p class="sp-ishow__sub"><?php esc_html_e( 'After any login — email, OTP or social — tap the badge on any issue. Watch: it slowly becomes an eye, holds for two seconds, then returns as your counted support. Just like a Like — but sacred.', 'sampreshan-child' ); ?></p>
            <ol class="sp-ishow__steps">
                <li><span class="sp-ishow__stepnum" aria-hidden="true">1</span><?php esc_html_e( 'Tap the badge on any issue', 'sampreshan-child' ); ?></li>
                <li><span class="sp-ishow__stepnum" aria-hidden="true">2</span><?php esc_html_e( 'The eye forms — your support is seen', 'sampreshan-child' ); ?></li>
                <li><span class="sp-ishow__stepnum" aria-hidden="true">3</span><?php esc_html_e( 'The badge returns with the growing count', 'sampreshan-child' ); ?></li>
            </ol>
            <div class="sp-ishow__actions">
                <a class="btn btn--primary btn--lg" href="<?php echo esc_url( $start_url ); ?>"><?php esc_html_e( 'Raise an Issue', 'sampreshan-child' ); ?></a>
                <a class="btn btn--ghost btn--lg" href="<?php echo esc_url( $feed_url ); ?>"><?php esc_html_e( 'Try it on live issues', 'sampreshan-child' ); ?></a>
            </div>
        </div>
        <div class="sp-i-demo" role="img" aria-label="<?php esc_attr_e( 'Animation: badge turning into an eye and back', 'sampreshan-child' ); ?>">
            <span class="sp-i-demo__tag"><span class="sp-i-demo__live" aria-hidden="true"></span><?php esc_html_e( 'Live demo', 'sampreshan-child' ); ?></span>
            <div class="sp-i-demo__stage" aria-hidden="true">
                <svg class="sp-i-demo__svg" viewBox="0 0 120 120">
                    <defs>
                        <linearGradient id="ildBar" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#FFE9C4"/><stop offset="45%" stop-color="#F5C542"/><stop offset="100%" stop-color="#B8860B"/>
                        </linearGradient>
                        <linearGradient id="ildStem" x1="0" y1="0" x2="1" y2="0">
                            <stop offset="0%" stop-color="#FFB877"/><stop offset="50%" stop-color="#FF9933"/><stop offset="100%" stop-color="#C2410C"/>
                        </linearGradient>
                    </defs>
                    <g class="sp-i-demo__igroup">
                        <g transform="translate(12,12) scale(1.5)">
                            <rect x="14" y="8" width="36" height="9" rx="4.5" fill="url(#ildBar)"/>
                            <rect x="26" y="17" width="12" height="30" rx="3" fill="url(#ildStem)"/>
                            <rect x="14" y="47" width="36" height="9" rx="4.5" fill="url(#ildBar)"/>
                            <rect x="17" y="10" width="30" height="2" rx="1" fill="#FFFFFF" opacity="0.65"/>
                            <rect x="28" y="19" width="2.6" height="26" rx="1.3" fill="#FFFFFF" opacity="0.5"/>
                            <rect x="17" y="49" width="30" height="2" rx="1" fill="#FFFFFF" opacity="0.65"/>
                        </g>
                    </g>
                    <g class="sp-i-demo__eyegroup" fill="none" stroke="#FFE9C4" stroke-width="3" stroke-linecap="round">
                        <path class="sp-i-demo__eyedraw" pathLength="100" d="M10 60 C30 28, 90 28, 110 60 C90 92, 30 92, 10 60 Z"/>
                        <circle class="sp-i-demo__pupil" cx="60" cy="60" r="12" fill="#FF9933" stroke="none"/>
                        <circle class="sp-i-demo__pupil" cx="60" cy="60" r="5" fill="#231309" stroke="none"/>
                    </g>
                </svg>
                <span class="sp-i-pop sp-i-pop--1">+1</span>
                <span class="sp-i-pop sp-i-pop--2">+1</span>
                <span class="sp-i-pop sp-i-pop--3">+1</span>
            </div>
            <div class="sp-i-demo__count">12,450<small>+</small></div>
            <p class="sp-i-demo__hint"><?php esc_html_e( 'Every share and support adds another supporter', 'sampreshan-child' ); ?></p>
        </div>
    </div>
</section>
