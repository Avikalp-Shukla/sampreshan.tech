<?php
/**
 * Home: Active Petitions — Premium 3D Dark Horizontal Scroll
 * Dark glass cards, scroll-snap, nav arrows, scroll-triggered reveals.
 *
 * @package SampreShan_Child
 */

$petitions = new WP_Query( array(
    'post_type'      => 'petition',
    'post_status'    => 'publish',
    'posts_per_page' => 8,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'meta_query'     => array(
        'relation' => 'OR',
        array( 'key' => 'sampreshan_status', 'value' => 'active' ),
        array( 'key' => 'sampreshan_status', 'compare' => 'NOT EXISTS' ),
    ),
) );

$all_url   = home_url( '/petitions/' );
$start_url = home_url( '/start-a-petition/' );
?>
<section class="d3-section" aria-labelledby="home-petitions-heading">
    <div class="d3-section__light" aria-hidden="true"></div>
    <div class="d3-section__inner">
        <div class="d3-reveal" style="display:flex; justify-content:space-between; align-items:flex-end; gap:1rem; margin-bottom:clamp(2rem, 4vw, 3rem); flex-wrap:wrap;">
            <div>
                <p class="d3-section__eyebrow"><?php echo esc_html__( 'Active Causes', 'sampreshan-child' ); ?></p>
                <h2 id="home-petitions-heading" class="d3-section__title" style="margin-bottom:0;"><?php echo esc_html__( 'Petitions & Campaigns', 'sampreshan-child' ); ?></h2>
            </div>
            <a class="d3-btn d3-btn--ghost" href="<?php echo esc_url( $all_url ); ?>">
                <?php echo esc_html__( 'View all', 'sampreshan-child' ); ?> &rarr;
            </a>
        </div>

        <?php if ( $petitions->have_posts() ) : ?>
            <div class="d3-hscroll d3-reveal">
                <div class="d3-hscroll__track">
                    <?php while ( $petitions->have_posts() ) : $petitions->the_post();
                        $pid       = get_the_ID();
                        $purl      = get_permalink( $pid );
                        $ptitle    = get_the_title();
                        $pexcerpt  = wp_trim_words( strip_tags( get_the_excerpt() ), 18, '...' );
                        $psig      = function_exists( 'sp_petition_signature_count' ) ? (int) sp_petition_signature_count( $pid ) : (int) get_post_meta( $pid, 'sampreshan_signatures', true );
                        $pterms    = get_the_terms( $pid, 'cause_category' );
                        $pcause    = ( $pterms && ! is_wp_error( $pterms ) && isset( $pterms[0] ) ) ? $pterms[0] : null;
                        $pcause_nm = $pcause ? $pcause->name : '';
                        $pcause_sl = $pcause ? $pcause->slug : '';
                        $picon     = function_exists( 'sp_cause_icon' ) ? sp_cause_icon( $pcause_sl ) : 'petition';
                        $gradients = array(
                            'educational' => 'linear-gradient(135deg, #1a1505 0%, #2d2010 50%, #0c0e14 100%)',
                            'heritage'    => 'linear-gradient(135deg, #1a0f05 0%, #2d1810 50%, #0c0e14 100%)',
                            'environment' => 'linear-gradient(135deg, #051a0f 0%, #102d1a 50%, #0c0e14 100%)',
                            'social'      => 'linear-gradient(135deg, #050f1a 0%, #101a2d 50%, #0c0e14 100%)',
                            'temple'      => 'linear-gradient(135deg, #12051a 0%, #1d102d 50%, #0c0e14 100%)',
                            'welfare'     => 'linear-gradient(135deg, #1a0510 0%, #2d101a 50%, #0c0e14 100%)',
                        );
                        $pgrad = isset( $gradients[ $pcause_sl ] ) ? $gradients[ $pcause_sl ] : 'linear-gradient(135deg, #1a0f05 0%, #2d1810 50%, #0c0e14 100%)';
                    ?>
                        <div class="d3-hscroll__item">
                            <article class="d3-petcard" data-petition-card data-petition-card-id="<?php echo esc_attr( $pid ); ?>">
                                <div class="d3-petcard__header">
                                    <div class="d3-petcard__header-bg" style="background:<?php echo esc_attr( $pgrad ); ?>;"></div>
                                    <div class="d3-petcard__header-icon">
                                        <?php sp_icon_e( $picon, 'sp-icon--xl', '' ); ?>
                                    </div>
                                    <?php if ( $pcause_nm ) : ?>
                                        <span class="d3-petcard__cause">
                                            <?php sp_icon_auto( $picon, 'sp-icon--xs', '' ); ?>
                                            <?php echo esc_html( $pcause_nm ); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="d3-petcard__body">
                                    <h3 class="d3-petcard__title">
                                        <a href="<?php echo esc_url( $purl ); ?>"><?php echo esc_html( $ptitle ); ?></a>
                                    </h3>
                                    <p class="d3-petcard__desc"><?php echo esc_html( $pexcerpt ); ?></p>
                                    <div class="d3-petcard__meta">
                                        <span class="d3-petcard__count">
                                            <span data-count="<?php echo esc_attr( $psig ); ?>"><?php echo esc_html( number_format_i18n( $psig ) ); ?></span>
                                            <small><?php echo esc_html__( 'I', 'sampreshan-child' ); ?></small>
                                        </span>
                                        <a class="d3-petcard__link" href="<?php echo esc_url( $purl ); ?>">
                                            <?php echo esc_html__( 'Support', 'sampreshan-child' ); ?> &rarr;
                                        </a>
                                    </div>
                                </div>
                            </article>
                        </div>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
                <button class="d3-hscroll__nav d3-hscroll__nav--prev" aria-label="Previous">&larr;</button>
                <button class="d3-hscroll__nav d3-hscroll__nav--next" aria-label="Next">&rarr;</button>
            </div>
        <?php else : ?>
            <div class="d3-reveal" style="text-align:center; padding:clamp(3rem, 8vw, 6rem) clamp(1rem, 3vw, 2rem);">
                <div style="font-size:3rem; margin-bottom:1rem;">
                    <?php sp_icon_auto( 'petition', 'sp-icon--2xl sp-icon--saffron', '' ); ?>
                </div>
                <h3 style="font-size:clamp(1.1rem, 2vw, 1.3rem); font-weight:700; margin:0 0 0.5rem; color:var(--d-text);"><?php echo esc_html__( 'No active petitions yet', 'sampreshan-child' ); ?></h3>
                <p style="color:var(--d-text-muted); margin:0 0 1.5rem;"><?php echo esc_html__( 'Be the first to start a petition and rally support for a cause that matters to the community.', 'sampreshan-child' ); ?></p>
                <a class="d3-btn d3-btn--primary" href="<?php echo esc_url( $start_url ); ?>">
                    <?php sp_icon_auto( 'plus', 'sp-icon--xs', '' ); ?>
                    <?php echo esc_html__( 'Start a Petition', 'sampreshan-child' ); ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>
