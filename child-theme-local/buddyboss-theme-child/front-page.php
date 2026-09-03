<?php
/**
 * Front Page (Homepage)
 * SampreShan — Sanatan Voice Platform
 * Truth Social + Change.org fusion layout
 *
 * @package SampreShan_Child
 */

get_header();
?>

<main class="site-main" role="main">

    <!-- LEFT SIDEBAR (Navigation) -->
    <aside class="site-main__sidebar-left" aria-label="Sidebar navigation">
        <div class="card" style="padding: var(--space-5);">
            <h3 style="font-family: var(--font-display); font-size: var(--text-lg); margin-bottom: var(--space-3);">Quick Links</h3>
            <ul style="list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: var(--space-2);">
                <li><a class="btn btn--ghost btn--sm btn--block" href="<?php echo esc_url( home_url( '/start-a-petition/' ) ); ?>" style="justify-content: flex-start;">Start a Petition</a></li>
                <li><a class="btn btn--ghost btn--sm btn--block" href="<?php echo esc_url( home_url( '/activity-feeds/' ) ); ?>" style="justify-content: flex-start;">Activity Feed</a></li>
                <li><a class="btn btn--ghost btn--sm btn--block" href="<?php echo esc_url( home_url( '/members/' ) ); ?>" style="justify-content: flex-start;">Browse Members</a></li>
                <li><a class="btn btn--ghost btn--sm btn--block" href="<?php echo esc_url( home_url( '/groups/' ) ); ?>" style="justify-content: flex-start;">Groups</a></li>
                <li><a class="btn btn--ghost btn--sm btn--block" href="<?php echo esc_url( home_url( '/forums/' ) ); ?>" style="justify-content: flex-start;">Forums</a></li>
            </ul>
        </div>

        <div class="card" style="padding: var(--space-5); margin-top: var(--space-4);">
            <h3 style="font-family: var(--font-display); font-size: var(--text-lg); margin-bottom: var(--space-3);">Categories</h3>
            <ul style="list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: var(--space-2); font-size: var(--text-sm);">
                <li><a href="#" style="color: var(--text-muted);">Temple Preservation</a></li>
                <li><a href="#" style="color: var(--text-muted);">Cultural Heritage</a></li>
                <li><a href="#" style="color: var(--text-muted);">Religious Education</a></li>
                <li><a href="#" style="color: var(--text-muted);">Environmental Causes</a></li>
                <li><a href="#" style="color: var(--text-muted);">Community Welfare</a></li>
            </ul>
        </div>
    </aside>

    <!-- MAIN CONTENT (Feed) -->
    <div class="site-main__content">

        <!-- Hero / Mission (using existing real content) -->
        <section class="card" style="padding: var(--space-8); margin-bottom: var(--space-6); background: linear-gradient(135deg, var(--saffron-50), var(--bg-card));">
            <p class="display" style="font-size: var(--text-base); color: var(--saffron-800); margin-bottom: var(--space-3); text-transform: uppercase; letter-spacing: var(--tracking-widest); font-weight: var(--weight-semibold);">
                A Platform for Dharma-Driven Change
            </p>
            <h1 class="display" style="font-size: var(--text-5xl); margin-bottom: var(--space-4); color: var(--text);">
                <?php echo esc_html( get_bloginfo( 'name' ) ); ?>
            </h1>
            <p style="font-size: var(--text-lg); color: var(--text-muted); line-height: var(--leading-relaxed); margin-bottom: var(--space-5); max-width: 640px;">
                Sampreshan empowers individuals and organizations to raise awareness, gather support, and create positive change for causes related to Sanatana Dharma.
            </p>
            <p style="font-size: var(--text-base); color: var(--text-muted); line-height: var(--leading-relaxed); margin-bottom: var(--space-5); max-width: 640px;">
                Our mission is to connect communities, amplify important voices, and provide a trusted platform where meaningful petitions and campaigns can inspire collective action.
            </p>
            <div style="display: flex; gap: var(--space-3); flex-wrap: wrap;">
                <a class="btn btn--primary btn--lg" href="<?php echo esc_url( home_url( '/start-a-petition/' ) ); ?>">Start a Petition</a>
                <a class="btn btn--outline btn--lg" href="<?php echo esc_url( home_url( '/about/' ) ); ?>">Learn More</a>
            </div>
        </section>

        <!-- Section: Welcome Post (real content, ID:99) -->
        <?php
        $welcome_post = get_post( 99 );
        if ( $welcome_post ) :
        ?>
        <article class="post-card">
            <header class="post-card__header">
                <div class="post-card__avatar">SB</div>
                <div class="post-card__author">
                    <h3 class="post-card__author-name"><?php echo esc_html( $welcome_post->post_title ); ?></h3>
                    <p class="post-card__meta">
                        <?php echo esc_html( get_the_author_meta( 'display_name', $welcome_post->post_author ) ); ?>
                        &middot;
                        <?php echo esc_html( human_time_diff( strtotime( $welcome_post->post_date ), current_time( 'timestamp' ) ) ); ?> ago
                    </p>
                </div>
            </header>
            <div class="post-card__content">
                <?php echo wp_kses_post( wpautop( $welcome_post->post_content ) ); ?>
            </div>
            <div class="post-card__actions">
                <button class="post-card__action" type="button">Support</button>
                <button class="post-card__action" type="button">Share</button>
            </div>
        </article>
        <?php endif; ?>

        <!-- Section: Petitions (real BuddyBoss/BP active, no fake data) -->
        <section style="margin-top: var(--space-8);">
            <header style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--space-5);">
                <h2 class="display" style="font-size: var(--text-2xl);">Active Petitions</h2>
                <a class="btn btn--ghost btn--sm" href="<?php echo esc_url( home_url( '/start-a-petition/' ) ); ?>">View all</a>
            </header>

            <?php
            // Empty state if no petitions yet
            ?>
            <div class="empty-state">
                <h3 class="empty-state__title">No active petitions yet</h3>
                <p style="margin-bottom: var(--space-5);">Be the first to start a petition and rally support for a cause.</p>
                <a class="btn btn--primary" href="<?php echo esc_url( home_url( '/start-a-petition/' ) ); ?>">Start a Petition</a>
            </div>
        </section>

    </div>

    <!-- RIGHT SIDEBAR -->
    <aside class="site-main__sidebar-right" aria-label="Sidebar widgets">
        <div class="card" style="padding: var(--space-5);">
            <h3 style="font-family: var(--font-display); font-size: var(--text-lg); margin-bottom: var(--space-3);">About ShivBodh Trust</h3>
            <p style="color: var(--text-muted); font-size: var(--text-sm); line-height: var(--leading-relaxed); margin-bottom: var(--space-4);">
                Sampreshan is a digital initiative by ShivBodh Trust, dedicated to preserving, protecting, and promoting Sanatana Dharma through respectful community participation, petitions, and constructive dialogue.
            </p>
            <a class="btn btn--outline btn--sm btn--block" href="https://shivbodhtrust.org" target="_blank" rel="noopener">Visit shivbodhtrust.org</a>
        </div>

        <div class="card" style="padding: var(--space-5); margin-top: var(--space-4);">
            <h3 style="font-family: var(--font-display); font-size: var(--text-lg); margin-bottom: var(--space-3);">Community Guidelines</h3>
            <p style="color: var(--text-muted); font-size: var(--text-sm); line-height: var(--leading-relaxed); margin-bottom: var(--space-4);">
                Our platform is built on respect, dharmic values, and constructive dialogue. All members are expected to uphold these principles.
            </p>
            <a class="btn btn--ghost btn--sm btn--block" href="<?php echo esc_url( home_url( '/community-guidelines/' ) ); ?>">Read Guidelines</a>
        </div>
    </aside>

</main>

<?php
// Use custom footer if available, else parent footer
if ( file_exists( get_stylesheet_directory() . '/template-parts/footer/site-footer.php' ) ) {
    include get_stylesheet_directory() . '/template-parts/footer/site-footer.php';
} else {
    get_footer();
}
?>
