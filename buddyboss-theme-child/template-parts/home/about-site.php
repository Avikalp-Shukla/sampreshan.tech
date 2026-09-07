<?php
/**
 * Home: What is Sampreshan + How it works — Premium 3D Dark Edition
 *
 * @package SampreShan_Child
 */

$start_url = home_url( '/start-a-petition/' );
$about_url = home_url( '/about/' );
$guide_url = home_url( '/community-guidelines/' );
?>
<section class="d3-section" aria-labelledby="sp-what-h">
    <div class="d3-section__light" aria-hidden="true"></div>
    <div class="d3-section__inner">
        <div class="d3-reveal" style="text-align:center; max-width:640px; margin:0 auto clamp(2rem, 5vw, 4rem);">
            <p class="d3-section__eyebrow" style="justify-content:center;"><?php esc_html_e( 'What is Sampreshan?', 'sampreshan-child' ); ?></p>
            <h2 class="d3-section__title" id="sp-what-h"><?php esc_html_e( 'A simple place to raise what matters', 'sampreshan-child' ); ?></h2>
            <p class="d3-section__sub" style="margin-left:auto;margin-right:auto;"><?php esc_html_e( 'Hindus and Sanatan followers across the world — connect, post and discuss here. Temple upkeep, heritage, education, welfare: if it affects the Dharmic community, raise it as an Issue Sampreshan.', 'sampreshan-child' ); ?></p>
        </div>

        <div class="d3-grid-3 d3-stagger">
            <div class="d3-card">
                <div class="d3-card__icon"><?php sp_icon_auto( 'pen', 'sp-icon--md', '' ); ?></div>
                <h3 class="d3-card__title"><?php esc_html_e( 'Raise', 'sampreshan-child' ); ?></h3>
                <p class="d3-card__text"><?php esc_html_e( 'Write your issue in simple words, add a photo, set a signature goal. It takes two minutes.', 'sampreshan-child' ); ?></p>
            </div>
            <div class="d3-card">
                <div class="d3-card__icon"><?php sp_icon_auto( 'network', 'sp-icon--md', '' ); ?></div>
                <h3 class="d3-card__title"><?php esc_html_e( 'Gather', 'sampreshan-child' ); ?></h3>
                <p class="d3-card__text"><?php esc_html_e( 'Share with friends and community. Every signature and comment adds weight to your cause.', 'sampreshan-child' ); ?></p>
            </div>
            <div class="d3-card">
                <div class="d3-card__icon"><?php sp_icon_auto( 'shankh', 'sp-icon--md', '' ); ?></div>
                <h3 class="d3-card__title"><?php esc_html_e( 'Impact', 'sampreshan-child' ); ?></h3>
                <p class="d3-card__text"><?php esc_html_e( 'Take the collective voice to authorities, temples and media — with proof of public support.', 'sampreshan-child' ); ?></p>
            </div>
        </div>

        <div class="d3-reveal" style="text-align:center; margin-top:clamp(2rem, 4vw, 3rem);">
            <a class="d3-btn d3-btn--primary d3-btn--lg" href="<?php echo esc_url( $start_url ); ?>"><?php esc_html_e( 'Start a Petition', 'sampreshan-child' ); ?></a>
            <a class="d3-btn d3-btn--ghost d3-btn--lg" href="<?php echo esc_url( $about_url ); ?>" style="margin-left:0.75rem;"><?php esc_html_e( 'Read our mission', 'sampreshan-child' ); ?></a>
        </div>
    </div>
</section>
