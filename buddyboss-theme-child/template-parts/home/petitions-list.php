<?php
/**
 * Home: Active Petitions Grid
 * Real existing published pages only. Excludes system pages by ID.
 * No AI content. No fake data.
 *
 * @package SampreShan_Child
 */

$exclude_ids = array( 17, 42, 55, 72, 78, 84, 89, 120, 122, 123, 124, 125, 127, 128, 134 );

$petitions = new WP_Query( array(
    'post_type'      => 'page',
    'post_status'    => 'publish',
    'posts_per_page' => 6,
    'post__not_in'   => $exclude_ids,
    'orderby'        => 'date',
    'order'          => 'DESC',
) );

$all_url = home_url( '/start-a-petition/' );
?>
<section class="home-section" aria-labelledby="home-petitions-heading">
    <header class="home-section__head">
        <div>
            <p class="home-section__eyebrow"><?php echo esc_html__( 'Active Causes', 'sampreshan-child' ); ?></p>
            <h2 id="home-petitions-heading" class="home-section__title"><?php echo esc_html__( 'Petitions & Campaigns', 'sampreshan-child' ); ?></h2>
        </div>
        <a class="btn btn--ghost btn--sm" href="<?php echo esc_url( $all_url ); ?>">
            <?php echo esc_html__( 'View all', 'sampreshan-child' ); ?> &rarr;
        </a>
    </header>

    <?php if ( $petitions->have_posts() ) : ?>
        <div class="home-petitions__grid">
            <?php while ( $petitions->have_posts() ) : $petitions->the_post();
                $pid           = get_the_ID();
                $purl          = get_permalink();
                $ptitle        = get_the_title();
                $pexcerpt      = wp_trim_words( strip_tags( get_the_content() ), 22, '…' );
                $pauthor       = get_the_author();
                $pdate         = get_the_date( 'M j, Y' );
                $psig_current  = (int) get_post_meta( $pid, 'sampreshan_signatures', true );
                $psig_goal     = (int) get_post_meta( $pid, 'sampreshan_goal', true );
                if ( $psig_goal <= 0 ) {
                    $psig_goal = 25000;
                }
                $psig_pct = $psig_goal > 0 ? min( 100, ( $psig_current / $psig_goal ) * 100 ) : 0;
            ?>
                <article class="petition-card" aria-label="<?php echo esc_attr( $ptitle ); ?>">
                    <div class="petition-card__image" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="36" height="36" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            <polyline points="14 2 14 8 20 8" />
                            <line x1="9" y1="13" x2="15" y2="13" />
                            <line x1="9" y1="17" x2="15" y2="17" />
                        </svg>
                    </div>
                    <div class="petition-card__body">
                        <div class="petition-card__champion">
                            <span><?php echo esc_html( $pauthor ); ?></span>
                            <span aria-hidden="true">&middot;</span>
                            <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( $pdate ); ?></time>
                        </div>
                        <h3 class="petition-card__title">
                            <a href="<?php echo esc_url( $purl ); ?>"><?php echo esc_html( $ptitle ); ?></a>
                        </h3>
                        <p class="petition-card__desc"><?php echo esc_html( $pexcerpt ); ?></p>
                        <div class="petition-card__progress" aria-label="Signature progress">
                            <div class="petition-card__progress-label">
                                <span class="petition-card__progress-count">
                                    <?php echo esc_html( number_format_i18n( $psig_current ) ); ?>
                                    <small><?php echo esc_html__( 'signed', 'sampreshan-child' ); ?></small>
                                </span>
                                <span><?php echo esc_html__( 'Goal: ', 'sampreshan-child' ); ?><?php echo esc_html( number_format_i18n( $psig_goal ) ); ?></span>
                            </div>
                            <div class="petition-card__progress-bar" role="progressbar" aria-valuenow="<?php echo esc_attr( $psig_current ); ?>" aria-valuemin="0" aria-valuemax="<?php echo esc_attr( $psig_goal ); ?>">
                                <div class="petition-card__progress-fill" style="width: <?php echo esc_attr( $psig_pct ); ?>%;"></div>
                            </div>
                        </div>
                        <div class="petition-card__actions">
                            <a class="btn btn--primary btn--sm" href="<?php echo esc_url( $purl ); ?>"><?php echo esc_html__( 'Sign', 'sampreshan-child' ); ?></a>
                            <a class="btn btn--outline btn--sm" href="<?php echo esc_url( $purl ); ?>"><?php echo esc_html__( 'Read', 'sampreshan-child' ); ?></a>
                        </div>
                    </div>
                </article>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
    <?php else : ?>
        <div class="empty-state">
            <h3 class="empty-state__title"><?php echo esc_html__( 'No active petitions yet', 'sampreshan-child' ); ?></h3>
            <p><?php echo esc_html__( 'Be the first to start a petition and rally support for a cause that matters to the community.', 'sampreshan-child' ); ?></p>
            <a class="btn btn--primary" href="<?php echo esc_url( $all_url ); ?>"><?php echo esc_html__( 'Start a Petition', 'sampreshan-child' ); ?></a>
        </div>
    <?php endif; ?>
</section>
