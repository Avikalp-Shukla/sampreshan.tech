<?php
/**
 * Front Page: Framer-backed full-site UI
 *
 * If a Framer snapshot exists (output/project.json from framer-sync), render
 * the Framer design as the entire homepage. Otherwise fall back to the
 * hand-built template parts (hero, feed, petitions, etc.).
 *
 * @package SampreShan_Child
 */

$framer_snapshot = function_exists( 'sp_framer_snapshot' ) ? sp_framer_snapshot() : false;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <?php wp_head(); ?>
</head>
<body <?php body_class( 'sp-framer-front' ); ?>>
<?php wp_body_open(); ?>

<?php if ( $framer_snapshot ) : ?>
    <?php
    sp_framer_render( array(
        'title'  => get_bloginfo( 'name' ) . ' — ' . get_bloginfo( 'description' ),
        'height' => '100vh',
    ) );
    ?>
<?php else : ?>
    <?php
    // Fallback: use the existing hand-built front-page.
    $fallback = get_stylesheet_directory() . '/front-page.php';
    if ( file_exists( $fallback ) ) {
        include $fallback;
    } else {
        echo '<p style="padding:2rem;">' . esc_html__( 'No Framer snapshot and no front-page.php fallback found.', 'sampreshan-child' ) . '</p>';
    }
    ?>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
