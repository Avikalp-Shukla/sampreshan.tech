<?php
/**
 * Custom site footer — Premium Edition
 * Newsletter signup, animated links, social icons, gradient accents
 *
 * @package SampreShan_Child
 */

$site_name    = get_bloginfo( 'name' );
$year         = date( 'Y' );
$is_logged_in = is_user_logged_in();
$logo_url     = function_exists( 'sp_logo_url' ) ? sp_logo_url() : content_url( 'uploads/2026/06/sampreshan-logo-svg.svg' );
?>
<?php
/* Close the BuddyBoss content wrappers opened in the parent header.php
 * (#content > .container > .bb-grid). This partial bypasses the parent
 * footer.php, so without these closes every page using it ships broken DOM.
 */
?>
</div><!-- .bb-grid -->
</div><!-- .container -->
</div><!-- #content -->
<footer class="site-footer" role="contentinfo">
    <div class="site-footer__inner">
        <div class="site-footer__grid">
            <div class="site-footer__brand">
                <a class="site-footer__brand-row" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" aria-label="<?php echo esc_attr( $site_name ); ?>">
                    <img class="site-footer__logo" src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $site_name ); ?>" width="36" height="36" loading="lazy" />
                    <span class="site-footer__title"><?php echo esc_html( $site_name ); ?></span>
                </a>
                <p class="site-footer__tagline">
                    A digital platform by ShivBodh Trust for the Sanatana Dharma community to connect, raise awareness, and gather support for meaningful causes.
                </p>
                <div class="site-footer__social">
                    <a class="site-footer__social-link" href="#" aria-label="Twitter" target="_blank" rel="noopener">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    </a>
                    <a class="site-footer__social-link" href="#" aria-label="Facebook" target="_blank" rel="noopener">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    <a class="site-footer__social-link" href="#" aria-label="YouTube" target="_blank" rel="noopener">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                    </a>
                    <a class="site-footer__social-link" href="https://shivbodhtrust.org" aria-label="ShivBodh Trust" target="_blank" rel="noopener">
                        <?php sp_icon_e( 'dharma', 'sp-icon--sm', '' ); ?>
                    </a>
                </div>
            </div>

            <div>
                <h4 class="site-footer__col-title">Platform</h4>
                <ul class="site-footer__list">
                    <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>">About</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/petitions/' ) ); ?>">Petitions</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/start-a-petition/' ) ); ?>">Start a Petition</a></li>
                    <li><a href="<?php echo esc_url( function_exists( 'sp_feed_url' ) ? sp_feed_url() : home_url( '/feed-2/' ) ); ?>">Feed</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/community/' ) ); ?>">Community</a></li>
                    <?php if ( $is_logged_in ) : ?>
                        <li><a href="<?php echo esc_url( home_url( '/dashboard/' ) ); ?>">Dashboard</a></li>
                        <li><a href="<?php echo esc_url( home_url( '/my-petitions/' ) ); ?>">My Petitions</a></li>
                        <li><a href="<?php echo esc_url( home_url( '/settings/' ) ); ?>">Settings</a></li>
                    <?php endif; ?>
                    <li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">Contact</a></li>
                </ul>
            </div>

            <div>
                <h4 class="site-footer__col-title">Community</h4>
                <ul class="site-footer__list">
                    <li><a href="<?php echo esc_url( home_url( '/community-guidelines/' ) ); ?>">Guidelines</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>">Privacy</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/terms-conditions/' ) ); ?>">Terms</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/disclaimer/' ) ); ?>">Disclaimer</a></li>
                </ul>
            </div>

            <div>
                <h4 class="site-footer__col-title">ShivBodh Trust</h4>
                <ul class="site-footer__list">
                    <li><a href="https://shivbodhtrust.org" target="_blank" rel="noopener">shivbodhtrust.org</a></li>
                    <li><a href="https://shivbodhtrust.org/about/" target="_blank" rel="noopener">About the Trust</a></li>
                    <li><a href="https://shivbodhtrust.org/contact/" target="_blank" rel="noopener">Contact</a></li>
                </ul>
                <div class="site-footer__newsletter">
                    <p style="font-size: var(--text-sm); color: rgba(255,255,255,0.6); margin-bottom: var(--space-2);">Stay updated with community news</p>
                    <form class="site-footer__newsletter-form" onsubmit="return false;">
                        <input class="site-footer__newsletter-input" type="email" placeholder="Your email" aria-label="Email for newsletter" />
                        <button class="site-footer__newsletter-btn" type="submit">Subscribe</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="site-footer__bottom">
            <p>&copy; <?php echo esc_html( $year ); ?> <?php echo esc_html( $site_name ); ?>. All rights reserved.</p>
            <p class="site-footer__credit">
                A non-commercial initiative by <a href="https://shivbodhtrust.org" target="_blank" rel="noopener">ShivBodh Trust</a>
                &middot; <span>3D icons by <a href="https://icons8.com" target="_blank" rel="noopener nofollow">Icons8</a></span>
            </p>
        </div>
    </div>
</footer>

<!-- Scroll to top -->
<button class="scroll-top" id="scroll-top" aria-label="Scroll to top">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg>
</button>

</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
