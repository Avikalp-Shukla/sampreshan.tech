<?php
/**
 * Home: Community Pulse — social fusion rail
 * Left: start-petition CTA · Center: live signature activity · Right: trending.
 *
 * @package SampreShan_Child
 */

$start_url     = home_url( '/start-a-petition/' );
$petitions_url = home_url( '/petitions/' );
$feed_url      = function_exists( 'sp_feed_url' ) ? sp_feed_url() : home_url( '/feed-2/' );

// Trending: most signatures.
$trending = new WP_Query( array(
    'post_type'      => 'petition',
    'post_status'    => 'publish',
    'posts_per_page' => 5,
    'meta_key'       => 'sampreshan_signatures',
    'orderby'        => 'meta_value_num',
    'order'          => 'DESC',
) );

// Live activity: latest signatures.
$live = array();
if ( function_exists( 'sp_petitions_table' ) ) {
    global $wpdb;
    $table = sp_petitions_table();
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery
    $exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) );
    if ( $exists === $table ) {
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery
        $live = $wpdb->get_results(
            "SELECT s.user_id, s.petition_id, s.created_at, s.is_anonymous, p.post_title
               FROM $table s
               JOIN {$wpdb->posts} p ON p.ID = s.petition_id
              WHERE p.post_status = 'publish'
              ORDER BY s.created_at DESC LIMIT 5"
        );
    }
}
?>
<section class="home-section" aria-labelledby="home-pulse-heading">
    <div class="sp-fu-sechead">
        <div>
            <p class="sp-fu-eyebrow"><?php echo esc_html__( 'Community Pulse', 'sampreshan-child' ); ?></p>
            <h2 id="home-pulse-heading" class="sp-fu-sectitle"><?php sp_icon_auto( 'feed', 'sp-icon--md sp-icon--saffron', '' ); ?> <?php echo esc_html__( 'Live from the Sangha', 'sampreshan-child' ); ?></h2>
        </div>
        <a class="sp-fu-link" href="<?php echo esc_url( $feed_url ); ?>">
            <?php echo esc_html__( 'Open full feed', 'sampreshan-child' ); ?> &rarr;
        </a>
    </div>

    <div class="sp-fu-social">
        <!-- Left: CTA -->
        <aside class="sp-fu-side sp-fu-side--nav" aria-label="<?php esc_attr_e( 'Take action', 'sampreshan-child' ); ?>">
            <h3><?php sp_icon_auto( 'plus', 'sp-icon--sm sp-icon--saffron', '' ); ?> <?php esc_html_e( 'Take action', 'sampreshan-child' ); ?></h3>
            <p style="margin:0 0 0.8rem;font-size:0.86rem;line-height:1.6;color:var(--fu-ink-soft,#6B4A2F);">
                <?php esc_html_e( 'Your voice matters. Start a cause in two minutes, or back one that moves you.', 'sampreshan-child' ); ?>
            </p>
            <a class="sp-fu-btn sp-fu-btn--primary sp-fu-btn--sm" href="<?php echo esc_url( $start_url ); ?>" style="width:100%;justify-content:center;margin-bottom:0.5rem;">
                <?php sp_icon_auto( 'plus', 'sp-icon--xs', '' ); ?>
                <?php esc_html_e( 'Start a Petition', 'sampreshan-child' ); ?>
            </a>
            <a class="sp-fu-sidelink" href="<?php echo esc_url( $petitions_url ); ?>">
                <?php sp_icon_auto( 'petition', 'sp-icon--sm', '' ); ?>
                <?php esc_html_e( 'Browse petitions', 'sampreshan-child' ); ?>
            </a>
            <a class="sp-fu-sidelink" href="<?php echo esc_url( $feed_url ); ?>">
                <?php sp_icon_auto( 'feed', 'sp-icon--sm', '' ); ?>
                <?php esc_html_e( 'Community feed', 'sampreshan-child' ); ?>
            </a>
        </aside>

        <!-- Center: live activity -->
        <div aria-label="<?php esc_attr_e( 'Recent signatures', 'sampreshan-child' ); ?>">
            <?php if ( ! empty( $live ) ) : ?>
                <?php foreach ( $live as $sig ) :
                    $is_anon = ! empty( $sig->is_anonymous );
                    $who     = $is_anon ? __( 'Someone', 'sampreshan-child' ) : get_the_author_meta( 'display_name', (int) $sig->user_id );
                    $ago     = human_time_diff( strtotime( $sig->created_at ), current_time( 'timestamp' ) );
                    $face    = ( ! $is_anon && (int) $sig->user_id > 0 ) ? get_avatar_url( (int) $sig->user_id, array( 'size' => 80 ) ) : '';
                ?>
                    <article class="sp-fu-post">
                        <div class="sp-fu-post__head">
                            <span class="sp-fu-post__avatar" aria-hidden="true">
                                <?php if ( $face ) : ?>
                                    <img src="<?php echo esc_url( $face ); ?>" alt="" width="40" height="40" loading="lazy" />
                                <?php else : ?>
                                    <span style="font-family:serif;">ॐ</span>
                                <?php endif; ?>
                            </span>
                            <div class="sp-fu-post__who">
                                <strong><?php echo esc_html( $who ); ?></strong>
                                <small><?php echo esc_html( sprintf( __( 'signed %s ago', 'sampreshan-child' ), $ago ) ); ?></small>
                            </div>
                        </div>
                        <h3 class="sp-fu-post__title">
                            <a href="<?php echo esc_url( get_permalink( (int) $sig->petition_id ) ); ?>"><?php echo esc_html( $sig->post_title ); ?></a>
                        </h3>
                    </article>
                <?php endforeach; ?>
            <?php else : ?>
                <article class="sp-fu-post">
                    <p class="sp-fu-post__meta" style="margin:0;">
                        <?php esc_html_e( 'Signatures will appear here live. Be the first to sign a cause today.', 'sampreshan-child' ); ?>
                    </p>
                    <p style="margin:0.6rem 0 0;">
                        <a class="sp-fu-btn sp-fu-btn--primary sp-fu-btn--sm" href="<?php echo esc_url( $petitions_url ); ?>"><?php esc_html_e( 'Browse petitions', 'sampreshan-child' ); ?></a>
                    </p>
                </article>
            <?php endif; ?>
        </div>

        <!-- Right: trending -->
        <aside class="sp-fu-side" aria-label="<?php esc_attr_e( 'Trending petitions', 'sampreshan-child' ); ?>">
            <h3><?php sp_icon_auto( 'trending', 'sp-icon--sm', '' ); ?> <?php esc_html_e( 'Trending now', 'sampreshan-child' ); ?></h3>
            <?php if ( $trending->have_posts() ) : ?>
                <?php $rank = 1; ?>
                <?php while ( $trending->have_posts() ) : $trending->the_post();
                    $tid = get_the_ID();
                    $tc  = (int) get_post_meta( $tid, 'sampreshan_signatures', true );
                ?>
                    <div class="sp-fu-trend">
                        <span class="sp-fu-trend__rank" aria-hidden="true"><?php echo esc_html( $rank ); ?></span>
                        <div>
                            <p class="sp-fu-trend__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></p>
                            <p class="sp-fu-trend__meta"><?php echo esc_html( sprintf( _n( '%s signature', '%s signatures', $tc, 'sampreshan-child' ), number_format_i18n( $tc ) ) ); ?></p>
                        </div>
                    </div>
                    <?php $rank++; ?>
                <?php endwhile; wp_reset_postdata(); ?>
            <?php else : ?>
                <p style="margin:0;font-size:0.86rem;color:var(--fu-ink-soft,#6B4A2F);"><?php esc_html_e( 'Trending causes will appear here once petitions gather signatures.', 'sampreshan-child' ); ?></p>
            <?php endif; ?>
        </aside>
    </div>
</section>
