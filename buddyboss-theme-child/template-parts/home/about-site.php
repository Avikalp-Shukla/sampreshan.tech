<?php
/**
 * Home: What is Sampreshan + How it works — Clean Edition
 *
 * @package SampreShan_Child
 */

$start_url = home_url( '/start-a-petition/' );
$about_url = home_url( '/about/' );
$guide_url = home_url( '/community-guidelines/' );
?>
<section class="sp-section" aria-labelledby="sp-what-h">
    <div class="sp-section__head">
        <p class="sp-section__eyebrow"><?php esc_html_e( 'What is Sampreshan?', 'sampreshan-child' ); ?></p>
        <h2 class="sp-section__title" id="sp-what-h"><?php sp_icon_auto( 'dharma', 'sp-icon--md sp-icon--saffron', '' ); ?> <?php esc_html_e( 'A simple place to raise what matters', 'sampreshan-child' ); ?></h2>
        <p class="sp-section__sub"><?php esc_html_e( 'Temple upkeep, cultural heritage, religious education, community welfare — if it affects the Dharmic community, it belongs here.', 'sampreshan-child' ); ?></p>
    </div>
    <div class="sp-steps">
        <div class="sp-step-card">
            <span class="sp-step-card__num" aria-hidden="true">1</span>
            <span class="sp-step-card__icon" aria-hidden="true"><?php sp_icon_e( 'pen', 'sp-icon--md', '' ); ?></span>
            <h3><?php esc_html_e( 'Raise', 'sampreshan-child' ); ?></h3>
            <p><?php esc_html_e( 'Write your issue in simple words, add a photo, set a signature goal. It takes two minutes.', 'sampreshan-child' ); ?></p>
        </div>
        <div class="sp-step-card">
            <span class="sp-step-card__num" aria-hidden="true">2</span>
            <span class="sp-step-card__icon" aria-hidden="true"><?php sp_icon_e( 'network', 'sp-icon--md', '' ); ?></span>
            <h3><?php esc_html_e( 'Gather', 'sampreshan-child' ); ?></h3>
            <p><?php esc_html_e( 'Share with friends and community. Every signature and comment adds weight to your cause.', 'sampreshan-child' ); ?></p>
        </div>
        <div class="sp-step-card">
            <span class="sp-step-card__num" aria-hidden="true">3</span>
            <span class="sp-step-card__icon" aria-hidden="true"><?php sp_icon_e( 'megaphone', 'sp-icon--md', '' ); ?></span>
            <h3><?php esc_html_e( 'Impact', 'sampreshan-child' ); ?></h3>
            <p><?php esc_html_e( 'Take the collective voice to authorities, temples and media — with proof of public support.', 'sampreshan-child' ); ?></p>
        </div>
    </div>
    <div class="sp-section__actions">
        <a class="btn btn--primary" href="<?php echo esc_url( $start_url ); ?>"><?php esc_html_e( 'Start a Petition', 'sampreshan-child' ); ?></a>
        <a class="btn btn--ghost" href="<?php echo esc_url( $about_url ); ?>"><?php esc_html_e( 'Read our mission', 'sampreshan-child' ); ?></a>
        <a class="sp-text-link" href="<?php echo esc_url( $guide_url ); ?>"><?php esc_html_e( 'Community guidelines →', 'sampreshan-child' ); ?></a>
    </div>
</section>
