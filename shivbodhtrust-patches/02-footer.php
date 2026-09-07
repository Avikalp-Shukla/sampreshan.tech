<?php
/**
 * ShivBodh Trust Footer (PATCHED v2.0.1)
 *
 * - Fixed /aboout-us/ → /about-us/ typo
 * - Removed broken emoji social icons; using proper SVG glyphs
 * - Walker class removed (now lives in inc/class-nav-walker.php)
 * - Newsletter form posts to FluentCRM (if active) via shortcode fallback
 *
 * @package ShivBodh_Child
 */
?>

<footer class="sb-footer" role="contentinfo">
    <div class="sb-container">
        <div class="sb-footer-grid">

            <div class="sb-footer-brand">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="sb-footer-logo">
                    <?php if ( has_custom_logo() ) : ?>
                        <?php the_custom_logo(); ?>
                    <?php else : ?>
                        <img src="<?php echo esc_url( SHIVBODH_CHILD_URI . '/images/logo-white.png' ); ?>"
                             alt="<?php bloginfo( 'name' ); ?>" width="200" height="54" loading="lazy"
                             onerror="this.style.display='none'">
                    <?php endif; ?>
                    <span class="sb-footer-logo-text"><?php bloginfo( 'name' ); ?></span>
                </a>
                <p class="sb-footer-desc">
                    <?php esc_html_e( 'A digital bridge connecting the modern Sanatani seeker with the primordial wisdom of the four Amnaya Peethams established by Jagadguru Adi Shankaracharya.', 'shivbodh-child' ); ?>
                </p>
                <div class="sb-footer-social">
                    <a href="https://www.facebook.com/shivbodhtrust" class="sb-footer-social-link" target="_blank" rel="noopener" aria-label="Facebook">
                        <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path fill="currentColor" d="M22 12a10 10 0 1 0-11.6 9.9v-7H7.9V12h2.5V9.8c0-2.5 1.5-3.9 3.8-3.9 1.1 0 2.2.2 2.2.2v2.5h-1.3c-1.3 0-1.7.8-1.7 1.6V12h2.8l-.4 2.9h-2.3v7A10 10 0 0 0 22 12z"/></svg>
                    </a>
                    <a href="https://twitter.com/shivbodhtrust" class="sb-footer-social-link" target="_blank" rel="noopener" aria-label="X (Twitter)">
                        <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path fill="currentColor" d="M18.244 2H21l-6.52 7.45L22 22h-6.84l-4.78-6.27L4.8 22H2.04l6.97-7.96L2 2h7l4.32 5.7L18.24 2zm-2.4 18h1.7L7.27 4H5.45l10.4 16z"/></svg>
                    </a>
                    <a href="https://www.youtube.com/@shivbodhtrust" class="sb-footer-social-link" target="_blank" rel="noopener" aria-label="YouTube">
                        <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path fill="currentColor" d="M23.5 6.2a3 3 0 0 0-2.1-2.1C19.5 3.5 12 3.5 12 3.5s-7.5 0-9.4.6A3 3 0 0 0 .5 6.2C0 8.1 0 12 0 12s0 3.9.5 5.8a3 3 0 0 0 2.1 2.1c1.9.6 9.4.6 9.4.6s7.5 0 9.4-.6a3 3 0 0 0 2.1-2.1c.5-1.9.5-5.8.5-5.8s0-3.9-.5-5.8zM9.6 15.6V8.4l6.2 3.6-6.2 3.6z"/></svg>
                    </a>
                    <a href="https://www.instagram.com/shivbodhtrust" class="sb-footer-social-link" target="_blank" rel="noopener" aria-label="Instagram">
                        <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path fill="currentColor" d="M12 2.2c3.2 0 3.6 0 4.8.1 1.2.1 1.8.3 2.2.4.6.2 1 .5 1.4 1 .5.4.8.8 1 1.4.2.4.4 1 .4 2.2.1 1.2.1 1.6.1 4.8s0 3.6-.1 4.8c-.1 1.2-.3 1.8-.4 2.2-.2.6-.5 1-1 1.4-.4.5-.8.8-1.4 1-.4.2-1 .4-2.2.4-1.2.1-1.6.1-4.8.1s-3.6 0-4.8-.1c-1.2-.1-1.8-.3-2.2-.4-.6-.2-1-.5-1.4-1-.5-.4-.8-.8-1-1.4-.2-.4-.4-1-.4-2.2-.1-1.2-.1-1.6-.1-4.8s0-3.6.1-4.8c.1-1.2.3-1.8.4-2.2.2-.6.5-1 1-1.4.4-.5.8-.8 1.4-1 .4-.2 1-.4 2.2-.4 1.2-.1 1.6-.1 4.8-.1zm0 1.8c-3.1 0-3.5 0-4.7.1-1 0-1.6.2-2 .3-.5.2-.8.4-1.2.8-.4.4-.6.7-.8 1.2-.1.4-.3 1-.3 2-.1 1.2-.1 1.6-.1 4.7s0 3.5.1 4.7c0 1 .2 1.6.3 2 .2.5.4.8.8 1.2.4.4.7.6 1.2.8.4.1 1 .3 2 .3 1.2.1 1.6.1 4.7.1s3.5 0 4.7-.1c1 0 1.6-.2 2-.3.5-.2.8-.4 1.2-.8.4-.4.6-.7.8-1.2.1-.4.3-1 .3-2 .1-1.2.1-1.6.1-4.7s0-3.5-.1-4.7c0-1-.2-1.6-.3-2-.2-.5-.4-.8-.8-1.2-.4-.4-.7-.6-1.2-.8-.4-.1-1-.3-2-.3-1.2-.1-1.6-.1-4.7-.1zm0 3.1a4.9 4.9 0 1 1 0 9.8 4.9 4.9 0 0 1 0-9.8zm0 8.1a3.2 3.2 0 1 0 0-6.4 3.2 3.2 0 0 0 0 6.4zm6.3-8.4a1.1 1.1 0 1 1-2.2 0 1.1 1.1 0 0 1 2.2 0z"/></svg>
                    </a>
                </div>
            </div>

            <div>
                <h4 class="sb-footer-heading"><?php esc_html_e( 'Sacred Peethams', 'shivbodh-child' ); ?></h4>
                <ul class="sb-footer-links">
                    <li><a href="<?php echo esc_url( home_url( '/sringeri-peetha/' ) ); ?>"><?php esc_html_e( 'Sringeri Peetham', 'shivbodh-child' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/dwarka-peetha/' ) ); ?>"><?php esc_html_e( 'Dwarka Peetham', 'shivbodh-child' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/jyotishpeetha/' ) ); ?>"><?php esc_html_e( 'Jyotirmath Peetham', 'shivbodh-child' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/puri-peetha/' ) ); ?>"><?php esc_html_e( 'Puri Peetham', 'shivbodh-child' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/kanchi-peetha/' ) ); ?>"><?php esc_html_e( 'Kanchi Peetham', 'shivbodh-child' ); ?></a></li>
                </ul>
            </div>

            <div>
                <h4 class="sb-footer-heading"><?php esc_html_e( 'Quick Links', 'shivbodh-child' ); ?></h4>
                <ul class="sb-footer-links">
                    <li><a href="<?php echo esc_url( home_url( '/adi-shankaracharya/' ) ); ?>"><?php esc_html_e( 'Adi Shankaracharya', 'shivbodh-child' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/dharmasevaka/' ) ); ?>"><?php esc_html_e( 'Dharmasevaka', 'shivbodh-child' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/articles/' ) ); ?>"><?php esc_html_e( 'Articles', 'shivbodh-child' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/shankaracharyas-news/' ) ); ?>"><?php esc_html_e( 'Shankaracharyas News', 'shivbodh-child' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/save-cow/' ) ); ?>"><?php esc_html_e( 'Save Cow Campaign', 'shivbodh-child' ); ?></a></li>
                </ul>
            </div>

            <div>
                <h4 class="sb-footer-heading"><?php esc_html_e( 'Support', 'shivbodh-child' ); ?></h4>
                <ul class="sb-footer-links">
                    <li><a href="<?php echo esc_url( home_url( '/about-us/' ) ); ?>"><?php esc_html_e( 'About Us', 'shivbodh-child' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>"><?php esc_html_e( 'Contact Us', 'shivbodh-child' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/faq/' ) ); ?>"><?php esc_html_e( 'FAQ', 'shivbodh-child' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>"><?php esc_html_e( 'Privacy Policy', 'shivbodh-child' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/terms-of-service/' ) ); ?>"><?php esc_html_e( 'Terms of Service', 'shivbodh-child' ); ?></a></li>
                </ul>
            </div>

            <div class="sb-footer-newsletter">
                <h4 class="sb-footer-heading"><?php esc_html_e( 'Stay Connected', 'shivbodh-child' ); ?></h4>
                <p><?php esc_html_e( 'Subscribe for dharmic updates, events, and news from the four Peethams.', 'shivbodh-child' ); ?></p>
                <?php
                if ( shortcode_exists( 'fluentform' ) ) {
                    echo do_shortcode( '[fluentform id="1"]' );
                } else {
                ?>
                <form class="sb-newsletter-form" action="#" method="post">
                    <label for="sb-newsletter-email" class="screen-reader-text"><?php esc_attr_e( 'Email', 'shivbodh-child' ); ?></label>
                    <input id="sb-newsletter-email" type="email" name="email" class="sb-newsletter-input" placeholder="<?php esc_attr_e( 'Enter your email', 'shivbodh-child' ); ?>" required>
                    <button type="submit" class="sb-newsletter-btn" aria-label="<?php esc_attr_e( 'Subscribe', 'shivbodh-child' ); ?>">
                        <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path fill="currentColor" d="M2 21l21-9L2 3v7l15 2-15 2v7z"/></svg>
                    </button>
                </form>
                <?php } ?>
            </div>
        </div>

        <div class="sb-footer-sanskrit">
            ॥ सत्यमेव जयते नानृतम् ॥
        </div>

        <div class="sb-footer-bottom">
            <p>&copy; <?php echo esc_html( date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'All rights reserved.', 'shivbodh-child' ); ?></p>
            <div class="sb-footer-bottom-links">
                <a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>"><?php esc_html_e( 'Privacy', 'shivbodh-child' ); ?></a>
                <a href="<?php echo esc_url( home_url( '/terms-of-service/' ) ); ?>"><?php esc_html_e( 'Terms', 'shivbodh-child' ); ?></a>
                <a href="<?php echo esc_url( home_url( '/user-data-protocol/' ) ); ?>"><?php esc_html_e( 'Data Protocol', 'shivbodh-child' ); ?></a>
            </div>
        </div>
    </div>
</footer>

<button class="sb-scroll-top" id="sb-scroll-top" aria-label="<?php esc_attr_e( 'Scroll to top', 'shivbodh-child' ); ?>">
    <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path fill="currentColor" d="M12 4l-8 8h5v8h6v-8h5l-8-8z"/></svg>
</button>

<?php wp_footer(); ?>
</body>
</html>
