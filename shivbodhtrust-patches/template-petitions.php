<?php
/**
 * Template Name: Petitions
 * Description: Dharmic petitions archive + sign form
 *
 * @package ShivBodh_Child
 */

get_header();
?>

<section class="sb-page-header">
    <div class="sb-container">
        <div class="sb-page-header-content">
            <div class="sb-breadcrumb">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'shivbodh-child' ); ?></a>
                <span>/</span>
                <span><?php esc_html_e( 'Petitions', 'shivbodh-child' ); ?></span>
            </div>
            <h1><?php esc_html_e( 'धार्मिक याचिकाएँ', 'shivbodh-child' ); ?></h1>
            <p><?php esc_html_e( 'Sanatan Dharma की रक्षा के लिए सामूहिक आवाज़ उठाएँ।', 'shivbodh-child' ); ?></p>
        </div>
    </div>
</section>

<section class="sb-section">
    <div class="sb-container">

        <?php
        $petitions = new WP_Query( array(
            'post_type'      => 'sb_petition',
            'posts_per_page' => 12,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
        ) );

        if ( $petitions->have_posts() ) : ?>
            <div class="sb-petitions-grid">
                <?php while ( $petitions->have_posts() ) : $petitions->the_post();
                    $pid       = get_the_ID();
                    $target    = (int) get_post_meta( $pid, 'target_signatures', true );
                    $current   = (int) get_post_meta( $pid, 'current_signatures', true );
                    $progress  = $target > 0 ? min( 100, round( ( $current / $target ) * 100 ) ) : 0;
                    $peethams  = get_the_term_list( $pid, 'petition_peetham', '', ', ', '' );
                ?>
                <article class="sb-petition-card sb-fade-in">
                    <div class="sb-petition-card-inner">
                        <div class="sb-petition-meta">
                            <span class="sb-petition-date"><?php echo esc_html( get_the_date() ); ?></span>
                            <?php if ( $peethams ) : ?>
                                <span class="sb-petition-peetham"><?php echo wp_kses_post( $peethams ); ?></span>
                            <?php endif; ?>
                        </div>
                        <h3 class="sb-petition-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>
                        <p class="sb-petition-excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 30 ) ); ?></p>

                        <div class="sb-petition-progress">
                            <div class="sb-petition-progress-bar">
                                <div class="sb-petition-progress-fill" style="width: <?php echo esc_attr( $progress ); ?>%"></div>
                            </div>
                            <div class="sb-petition-progress-text">
                                <strong><?php echo esc_html( number_format_i18n( $current ) ); ?></strong> / <?php echo esc_html( number_format_i18n( $target ) ); ?> हस्ताक्षर
                            </div>
                        </div>

                        <a href="<?php the_permalink(); ?>" class="sb-btn sb-btn-primary sb-btn-block">
                            <?php esc_html_e( 'पढ़ें और हस्ताक्षर करें', 'shivbodh-child' ); ?>
                        </a>
                    </div>
                </article>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        <?php else : ?>
            <div class="sb-empty-state">
                <h2><?php esc_html_e( 'अभी कोई सक्रिय याचिका नहीं है', 'shivbodh-child' ); ?></h2>
                <p><?php esc_html_e( 'जल्द ही नई याचिकाएँ यहाँ प्रकाशित की जाएंगी।', 'shivbodh-child' ); ?></p>
            </div>
        <?php endif; ?>

    </div>
</section>

<?php get_footer(); ?>
