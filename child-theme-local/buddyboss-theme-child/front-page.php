<?php
/**
 * Front Page (Homepage)
 * SampreShan — Sanatan Voice Platform
 * Truth Social + Change.org fusion layout — Premium, Responsive, No AI Content
 *
 * @package SampreShan_Child
 */

get_header();
?>

<main class="site-main" role="main">

    <!-- LEFT SIDEBAR -->
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

    <!-- MAIN CONTENT -->
    <div class="site-main__content">

        <!-- Hero / Mission -->
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

        <!-- Featured Petition (Real existing page ID:94) -->
        <?php include get_stylesheet_directory() . '/template-parts/petition/card.php'; ?>

        <!-- Welcome Post (Real content, ID:99) -->
        <?php
        $welcome_post = get_post( 99 );
        if ( $welcome_post ) :
        ?>
        <?php include get_stylesheet_directory() . '/template-parts/feed/post-card.php'; ?>
        <?php endif; ?>

        <!-- Active Petitions Section -->
        <section style="margin-top: var(--space-8);">
            <header style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--space-5);">
                <h2 class="display" style="font-size: var(--text-2xl);">Active Petitions</h2>
                <a class="btn btn--ghost btn--sm" href="<?php echo esc_url( home_url( '/start-a-petition/' ) ); ?>">View all</a>
            </header>

            <?php
            // Check if there are any petitions (using BuddyBoss/BP data or pages)
            $petition_pages = get_posts( array(
                'post_type'      => 'page',
                'posts_per_page' => 3,
                'post_status'    => 'publish',
                'post__not_in'   => array( 17, 42, 55, 72, 78, 84, 89, 120, 122, 123, 124, 125, 127, 128, 134 ),
                'orderby'        => 'date',
                'order'          => 'DESC',
            ) );
            ?>

            <?php if ( ! empty( $petition_pages ) ) : ?>
                <?php foreach ( $petition_pages as $petition ) : ?>
                    <article class="petition-card" aria-label="Petition: <?php echo esc_attr( $petition->post_title ); ?>">
                        <div class="petition-card__image" aria-hidden="true">
                            <span aria-label="Petition" style="font-family: var(--font-display); font-size: var(--text-3xl); color: var(--saffron-800);">&#10003;</span>
                        </div>
                        <div class="petition-card__body">
                            <div class="petition-card__champion">
                                <span>Started by <?php echo esc_html( get_the_author_meta( 'display_name', $petition->post_author ) ); ?></span>
                                <span aria-hidden="true">&middot;</span>
                                <time datetime="<?php echo esc_attr( get_the_date( 'c', $petition ) ); ?>"><?php echo esc_html( get_the_date( 'F j, Y', $petition ) ); ?></time>
                            </div>
                            <h3 class="petition-card__title">
                                <a href="<?php echo esc_url( get_permalink( $petition->ID ) ); ?>"><?php echo esc_html( $petition->post_title ); ?></a>
                            </h3>
                            <p class="petition-card__desc">
                                <?php echo esc_html( wp_trim_words( strip_tags( $petition->post_content ), 25 ) ); ?>
                            </p>
                            <div class="petition-card__actions">
                                <a class="btn btn--primary btn--sm" href="<?php echo esc_url( get_permalink( $petition->ID ) ); ?>">Sign Petition</a>
                                <a class="btn btn--outline btn--sm" href="<?php echo esc_url( get_permalink( $petition->ID ) ); ?>">Read More</a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="empty-state">
                    <h3 class="empty-state__title">No active petitions yet</h3>
                    <p style="margin-bottom: var(--space-5);">Be the first to start a petition and rally support for a cause.</p>
                    <a class="btn btn--primary" href="<?php echo esc_url( home_url( '/start-a-petition/' ) ); ?>">Start a Petition</a>
                </div>
            <?php endif; ?>
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
if ( file_exists( get_stylesheet_directory() . '/template-parts/footer/site-footer.php' ) ) {
    include get_stylesheet_directory() . '/template-parts/footer/site-footer.php';
} else {
    get_footer();
}
?>
