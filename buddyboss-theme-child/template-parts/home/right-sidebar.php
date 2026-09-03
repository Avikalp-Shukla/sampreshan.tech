<?php
/**
 * Home: Right Sidebar
 * About ShivBodh Trust card + Community Guidelines card.
 * No fundraising, no payments, no AI content.
 *
 * @package SampreShan_child
 */

$trust_url      = 'https://shivbodhtrust.org';
$guidelines_url = home_url( '/community-guidelines/' );
?>
<aside class="home-aside home-aside--right" aria-label="About and guidelines">
    <div class="card card--aside card--aside-accent">
        <h3 class="card__title card__title--aside"><?php echo esc_html__( 'About ShivBodh Trust', 'sampreshan-child' ); ?></h3>
        <p class="home-aside__text">
            <?php echo esc_html__( 'Sampreshan is a non-commercial digital initiative by ShivBodh Trust, dedicated to preserving, protecting, and promoting Sanatana Dharma through respectful community participation, petitions, and constructive dialogue.', 'sampreshan-child' ); ?>
        </p>
        <a class="btn btn--outline btn--sm btn--block" href="<?php echo esc_url( $trust_url ); ?>" target="_blank" rel="noopener noreferrer">
            <?php echo esc_html__( 'Visit shivbodhtrust.org', 'sampreshan-child' ); ?>
        </a>
    </div>

    <div class="card card--aside">
        <h3 class="card__title card__title--aside"><?php echo esc_html__( 'Community Guidelines', 'sampreshan-child' ); ?></h3>
        <p class="home-aside__text">
            <?php echo esc_html__( 'This platform is built on respect, dharmic values, and constructive dialogue. All members are expected to participate with civility, factual accuracy, and a spirit of service to the community.', 'sampreshan-child' ); ?>
        </p>
        <a class="btn btn--ghost btn--sm btn--block" href="<?php echo esc_url( $guidelines_url ); ?>">
            <?php echo esc_html__( 'Read Guidelines', 'sampreshan-child' ); ?>
        </a>
    </div>

    <div class="card card--aside card--aside-cta">
        <h3 class="card__title card__title--aside"><?php echo esc_html__( 'Raise Your Voice', 'sampreshan-child' ); ?></h3>
        <p class="home-aside__text">
            <?php echo esc_html__( 'Anyone can sign up, start a petition, and collect signatures. Bring local issues to the wider community and amplify your message.', 'sampreshan-child' ); ?>
        </p>
        <a class="btn btn--primary btn--sm btn--block" href="<?php echo esc_url( home_url( '/start-a-petition/' ) ); ?>">
            <?php echo esc_html__( 'Get Started', 'sampreshan-child' ); ?>
        </a>
    </div>
</aside>
