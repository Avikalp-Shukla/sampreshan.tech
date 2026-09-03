<?php
/**
 * Custom site footer
 * Premium, with Shivbodh Trust attribution
 *
 * @package SampreShan_Child
 */

$site_name = get_bloginfo( 'name' );
$year      = date( 'Y' );
?>
<footer class="site-footer" role="contentinfo">
    <div class="site-footer__inner">
        <div class="site-footer__grid">
            <div class="site-footer__brand">
                <h3 class="site-footer__title"><?php echo esc_html( $site_name ); ?></h3>
                <p class="site-footer__tagline">
                    A digital platform by ShivBodh Trust for the Sanatana Dharma community to connect, raise awareness, and gather support for meaningful causes.
                </p>
            </div>

            <div>
                <h4 class="site-footer__col-title">Platform</h4>
                <ul class="site-footer__list">
                    <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/start-a-petition/' ) ); ?>">Petitions</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/activity-feeds/' ) ); ?>">Activity</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/members/' ) ); ?>">Members</a></li>
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
            </div>
        </div>

        <div class="site-footer__bottom">
            <p>&copy; <?php echo esc_html( $year ); ?> <?php echo esc_html( $site_name ); ?>. All rights reserved.</p>
            <p class="site-footer__credit">
                A non-commercial initiative by <a href="https://shivbodhtrust.org" target="_blank" rel="noopener">ShivBodh Trust</a>
            </p>
        </div>
    </div>
</footer>
