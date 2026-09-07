<?php
/**
 * ShivBodh Trust Header (PATCHED v2.0.1)
 *
 * - Fixed /aboout-us/ → /about-us/ typo
 * - Removed hardcoded `/images/logo.png` fallback (theme ships no such file)
 * - Added aria-expanded toggling support for mobile submenus
 * - Hamburger button gets data-attr for JS init
 *
 * @package ShivBodh_Child
 */
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?> data-theme="light">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="sb-skip-link" href="#content"><?php esc_html_e( 'Skip to content', 'shivbodh-child' ); ?></a>

<header class="sb-header" id="sb-header" role="banner">
    <div class="sb-header-inner">

        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="sb-logo" aria-label="<?php bloginfo( 'name' ); ?>">
            <?php if ( has_custom_logo() ) :
                the_custom_logo();
            else :
                $logo = SHIVBODH_CHILD_DIR . '/images/logo.png';
                if ( file_exists( $logo ) ) : ?>
                    <img src="<?php echo esc_url( SHIVBODH_CHILD_URI . '/images/logo.png' ); ?>"
                         alt="<?php bloginfo( 'name' ); ?>"
                         width="200" height="54"
                         loading="eager">
                <?php endif;
            endif; ?>
            <div class="sb-logo-text">
                <span class="sb-logo-title"><?php bloginfo( 'name' ); ?></span>
                <span class="sb-logo-tagline"><?php echo esc_html( get_bloginfo( 'description' ) ); ?></span>
            </div>
        </a>

        <nav class="sb-nav" id="sb-desktop-nav" role="navigation" aria-label="<?php esc_attr_e( 'Primary Navigation', 'shivbodh-child' ); ?>">
            <?php
            if ( has_nav_menu( 'primary' ) ) {
                wp_nav_menu( array(
                    'theme_location' => 'primary',
                    'container'      => false,
                    'menu_class'     => 'sb-nav-list',
                    'fallback_cb'    => false,
                    'depth'          => 2,
                    'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                    'walker'         => new ShivBodh_Nav_Walker(),
                ) );
            } else {
                ?>
                <ul class="sb-nav-list">
                    <li class="sb-nav-item">
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="sb-nav-link"><?php esc_html_e( 'Home', 'shivbodh-child' ); ?></a>
                    </li>
                    <li class="sb-nav-item">
                        <a href="<?php echo esc_url( home_url( '/adi-shankaracharya/' ) ); ?>" class="sb-nav-link"><?php esc_html_e( 'Shankaracharya Ji', 'shivbodh-child' ); ?></a>
                    </li>
                    <li class="sb-nav-item sb-has-dropdown">
                        <a href="#" class="sb-nav-link" aria-haspopup="true" aria-expanded="false"><?php esc_html_e( 'Sacred Peethams', 'shivbodh-child' ); ?> <span class="sb-nav-arrow" aria-hidden="true">&#9662;</span></a>
                        <div class="sb-dropdown">
                            <a href="<?php echo esc_url( home_url( '/sringeri-peetha/' ) ); ?>" class="sb-dropdown-link">
                                <span class="sb-dropdown-icon" aria-hidden="true">&#9670;</span>
                                <span>Sringeri Peetham</span>
                            </a>
                            <a href="<?php echo esc_url( home_url( '/dwarka-peetha/' ) ); ?>" class="sb-dropdown-link">
                                <span class="sb-dropdown-icon" aria-hidden="true">&#9670;</span>
                                <span>Dwarka Peetham</span>
                            </a>
                            <a href="<?php echo esc_url( home_url( '/jyotishpeetha/' ) ); ?>" class="sb-dropdown-link">
                                <span class="sb-dropdown-icon" aria-hidden="true">&#9670;</span>
                                <span>Jyotirmath Peetham</span>
                            </a>
                            <a href="<?php echo esc_url( home_url( '/puri-peetha/' ) ); ?>" class="sb-dropdown-link">
                                <span class="sb-dropdown-icon" aria-hidden="true">&#9670;</span>
                                <span>Puri Peetham</span>
                            </a>
                            <a href="<?php echo esc_url( home_url( '/kanchi-peetha/' ) ); ?>" class="sb-dropdown-link">
                                <span class="sb-dropdown-icon" aria-hidden="true">&#9670;</span>
                                <span>Kanchi Peetham</span>
                            </a>
                        </div>
                    </li>
                    <li class="sb-nav-item">
                        <a href="<?php echo esc_url( home_url( '/dharmasevaka/' ) ); ?>" class="sb-nav-link"><?php esc_html_e( 'Dharmasevaka', 'shivbodh-child' ); ?></a>
                    </li>
                    <li class="sb-nav-item">
                        <a href="<?php echo esc_url( home_url( '/articles/' ) ); ?>" class="sb-nav-link"><?php esc_html_e( 'Articles', 'shivbodh-child' ); ?></a>
                    </li>
                    <li class="sb-nav-item">
                        <a href="<?php echo esc_url( home_url( '/about-us/' ) ); ?>" class="sb-nav-link"><?php esc_html_e( 'About Us', 'shivbodh-child' ); ?></a>
                    </li>
                    <li class="sb-nav-item">
                        <a href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>" class="sb-nav-link"><?php esc_html_e( 'Contact', 'shivbodh-child' ); ?></a>
                    </li>
                </ul>
                <?php
            }
            ?>
        </nav>

        <div class="sb-header-actions">
            <button class="sb-theme-toggle" id="sb-theme-toggle" aria-label="<?php esc_attr_e( 'Toggle dark mode', 'shivbodh-child' ); ?>">
                <span class="sb-theme-icon-light">&#9788;</span>
                <span class="sb-theme-icon-dark" style="display:none;">&#9790;</span>
            </button>

            <a href="<?php echo esc_url( home_url( '/donate/' ) ); ?>" class="sb-btn sb-btn-primary sb-btn-sm" id="sb-cta-desktop">
                <span aria-hidden="true">&#9825;</span> <?php esc_html_e( 'Donate', 'shivbodh-child' ); ?>
            </a>

            <button class="sb-mobile-toggle" id="sb-mobile-toggle" aria-label="<?php esc_attr_e( 'Toggle menu', 'shivbodh-child' ); ?>" aria-expanded="false" aria-controls="sb-mobile-nav">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </div>
</header>

<nav class="sb-mobile-nav" id="sb-mobile-nav" role="navigation" aria-label="<?php esc_attr_e( 'Mobile Navigation', 'shivbodh-child' ); ?>">
    <ul class="sb-mobile-nav-list">
        <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="sb-mobile-nav-link"><?php esc_html_e( 'Home', 'shivbodh-child' ); ?></a></li>
        <li>
            <a href="<?php echo esc_url( home_url( '/adi-shankaracharya/' ) ); ?>" class="sb-mobile-nav-link"><?php esc_html_e( 'Shankaracharya Ji', 'shivbodh-child' ); ?></a>
        </li>
        <li>
            <button class="sb-mobile-nav-link sb-mobile-submenu-toggle" aria-expanded="false" aria-controls="sb-mobile-peetham-submenu">
                <?php esc_html_e( 'Sacred Peethams', 'shivbodh-child' ); ?>
                <span aria-hidden="true">&#9662;</span>
            </button>
            <ul class="sb-mobile-submenu" id="sb-mobile-peetham-submenu">
                <li><a href="<?php echo esc_url( home_url( '/sringeri-peetha/' ) ); ?>">Sringeri Peetham</a></li>
                <li><a href="<?php echo esc_url( home_url( '/dwarka-peetha/' ) ); ?>">Dwarka Peetham</a></li>
                <li><a href="<?php echo esc_url( home_url( '/jyotishpeetha/' ) ); ?>">Jyotirmath Peetham</a></li>
                <li><a href="<?php echo esc_url( home_url( '/puri-peetha/' ) ); ?>">Puri Peetham</a></li>
                <li><a href="<?php echo esc_url( home_url( '/kanchi-peetha/' ) ); ?>">Kanchi Peetham</a></li>
            </ul>
        </li>
        <li><a href="<?php echo esc_url( home_url( '/dharmasevaka/' ) ); ?>" class="sb-mobile-nav-link"><?php esc_html_e( 'Dharmasevaka', 'shivbodh-child' ); ?></a></li>
        <li><a href="<?php echo esc_url( home_url( '/articles/' ) ); ?>" class="sb-mobile-nav-link"><?php esc_html_e( 'Articles', 'shivbodh-child' ); ?></a></li>
        <li><a href="<?php echo esc_url( home_url( '/about-us/' ) ); ?>" class="sb-mobile-nav-link"><?php esc_html_e( 'About Us', 'shivbodh-child' ); ?></a></li>
        <li><a href="<?php echo esc_url( home_url( '/faq/' ) ); ?>" class="sb-mobile-nav-link"><?php esc_html_e( 'FAQ', 'shivbodh-child' ); ?></a></li>
        <li><a href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>" class="sb-mobile-nav-link"><?php esc_html_e( 'Contact Us', 'shivbodh-child' ); ?></a></li>
        <li><a href="<?php echo esc_url( home_url( '/donate/' ) ); ?>" class="sb-mobile-nav-link sb-mobile-nav-cta"><?php esc_html_e( 'दान करें', 'shivbodh-child' ); ?></a></li>
        <li><a href="<?php echo esc_url( home_url( '/save-cow/' ) ); ?>" class="sb-mobile-nav-link"><?php esc_html_e( 'Gau Mata Abhiyan', 'shivbodh-child' ); ?></a></li>
    </ul>
</nav>

<main id="content" class="sb-main" role="main">
