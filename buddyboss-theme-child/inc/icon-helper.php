<?php
/**
 * SampreShan Icon Helper
 *
 * Inlines an SVG icon file directly into the page, so each icon
 * needs zero extra HTTP requests and inherits color from `currentColor`.
 *
 * Usage in any template:
 *   <?php echo sp_icon( 'home', 'sp-icon--md sp-icon--saffron' ); ?>
 *   <?php echo sp_icon( 'dharma', 'sp-icon--xl sp-icon--gold sp-icon--float' ); ?>
 *
 * The first arg is the icon name (filename without .svg).
 * The second arg is the wrapper class list (sizes, colors, animations).
 * Third optional arg is an aria-label override.
 *
 * @param string $name   Icon name (file in assets/icons/3d/).
 * @param string $class  Wrapper class list. Defaults to 'sp-icon sp-icon--md'.
 * @param string $label  Optional aria-label override.
 * @return string        Inline SVG markup, escaped-safe.
 */
if ( ! function_exists( 'sp_icon' ) ) {
    function sp_icon( $name, $class = 'sp-icon sp-icon--md', $label = '' ) {
        $name  = sanitize_key( $name );
        $class = esc_attr( $class );
        $path  = get_stylesheet_directory() . '/assets/icons/3d/' . $name . '.svg';

        if ( ! file_exists( $path ) ) {
            return '<!-- sp_icon: ' . esc_html( $name ) . ' not found -->';
        }

        $svg = file_get_contents( $path );

        // Strip XML declaration if present (WordPress renders inline fine without it).
        $svg = preg_replace( '/<\?xml[^?]*\?>/', '', $svg );

        // Inject the wrapper class and a stable id (for accessibility).
        $id   = 'sp-icon-' . $name . '-' . wp_generate_password( 4, false, false );
        $aria = $label !== '' ? esc_attr( $label ) : '';
        $svg  = preg_replace(
            '/<svg([^>]*)>/',
            '<svg$1 class="sp-icon__svg" role="img" aria-label="' . $aria . '" data-icon="' . esc_attr( $name ) . '">',
            $svg,
            1
        );

        return '<span class="sp-icon ' . $class . '" data-icon-wrapper="' . esc_attr( $name ) . '">' . $svg . '</span>';
    }
}

/**
 * SampreShan Icon (echo variant)
 */
if ( ! function_exists( 'sp_icon_e' ) ) {
    function sp_icon_e( $name, $class = 'sp-icon sp-icon--md', $label = '' ) {
        echo sp_icon( $name, $class, $label );
    }
}

/**
 * SampreShan Favicon: returns the SVG favicon URL.
 */
if ( ! function_exists( 'sp_favicon_url' ) ) {
    function sp_favicon_url() {
        return get_stylesheet_directory_uri() . '/assets/brand/favicon.svg';
    }
}

/**
 * Icons8 3D Fluency imports (PNG, transparent background).
 *
 * Our-name => Icons8 slug. Files live in assets/icons/icons8/
 * as <slug>-48.png / <slug>-96.png / <slug>-192.png
 * (mobile / tablet / desktop-retina).
 *
 * Brand icons (om, lotus, diya, dharma, petition, network, video,
 * flag, image, warning, logout, share, users) intentionally stay
 * on our custom SVGs.
 *
 * Free-plan license: PNG use requires attribution (see footer).
 */
if ( ! function_exists( 'sp_icons8_map' ) ) {
    function sp_icons8_map() {
        return array(
            'home' => 'home', 'search' => 'search', 'bell' => 'bell',
            'heart' => 'like', 'comment' => 'chat', 'repost' => 'refresh',
            'reply' => 'undo', 'chart' => 'statistics', 'dots' => 'more',
            'send' => 'paper-plane', 'share' => 'forward',
            'bookmark' => 'bookmark', 'plus' => 'plus', 'pen' => 'edit',
            'settings' => 'gear', 'eye' => 'eye', 'download' => 'download',
            'upload' => 'upload', 'lock' => 'lock', 'mail' => 'mail',
            'info' => 'info', 'check' => 'checkmark', 'close' => 'cancel',
            'star' => 'star', 'warning' => 'high-priority',
            'calendar' => 'calendar', 'location' => 'location',
            'image' => 'picture', 'menu' => 'menu', 'logout' => 'door',
            'account' => 'user-male', 'profile' => 'user-male',
            'network' => 'hub', 'group' => 'conference', 'feed' => 'news',
            'verified' => 'approval', 'document' => 'document',
            'megaphone' => 'megaphone', 'target' => 'goal',
            'trending' => 'fire', 'hand' => 'handshake', 'globe' => 'globe',
            'link' => 'link', 'shield' => 'shield', 'spark' => 'sparkles',
            'temple' => 'temple',
        );
    }
}

/**
 * Responsive Icons8 <img> with srcset/sizes.
 * Desktop gets 192w, tablet 96w, mobile 48w (browser picks by DPR).
 *
 * @param string $name  Our icon name (must exist in sp_icons8_map()).
 * @param string $class Wrapper class list.
 * @param string $label Aria-label override.
 * @return string Markup (empty string when files are missing).
 */
if ( ! function_exists( 'sp_icon_img' ) ) {
    function sp_icon_img( $name, $class = 'sp-icon sp-icon--md', $label = '' ) {
        $name  = sanitize_key( $name );
        $map   = sp_icons8_map();
        if ( ! isset( $map[ $name ] ) ) { return ''; }
        $slug = $map[ $name ];
        $dir  = get_stylesheet_directory() . '/assets/icons/icons8/';
        $uri  = get_stylesheet_directory_uri() . '/assets/icons/icons8/';
        foreach ( array( 48, 96, 192 ) as $s ) {
            if ( ! file_exists( $dir . $slug . '-' . $s . '.png' ) ) { return ''; }
        }
        $srcset = esc_url( $uri . $slug . '-48.png' ) . ' 48w, '
                . esc_url( $uri . $slug . '-96.png' ) . ' 96w, '
                . esc_url( $uri . $slug . '-192.png' ) . ' 192w';
        return '<span class="sp-icon sp-icon--img ' . esc_attr( $class ) . '" data-icon-img="' . esc_attr( $name ) . '">'
            . '<img src="' . esc_url( $uri . $slug . '-96.png' ) . '" srcset="' . $srcset . '"'
            . ' sizes="(max-width: 767px) 32px, 64px"'
            . ' alt="' . esc_attr( $label ) . '" loading="lazy" decoding="async" />'
            . '</span>';
    }
}

if ( ! function_exists( 'sp_icon_img_e' ) ) {
    function sp_icon_img_e( $name, $class = 'sp-icon sp-icon--md', $label = '' ) {
        echo sp_icon_img( $name, $class, $label ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
}

/**
 * Auto icon: Icons8 PNG when imported, else our custom SVG.
 * Same signature as sp_icon_e() — drop-in upgrade path.
 */
if ( ! function_exists( 'sp_icon_auto' ) ) {
    function sp_icon_auto( $name, $class = 'sp-icon sp-icon--md', $label = '' ) {
        $img = sp_icon_img( $name, $class, $label );
        if ( '' !== $img ) {
            echo $img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            return;
        }
        sp_icon_e( $name, $class, $label );
    }
}

/**
 * SampreShan Logo URL — single source of truth.
 *
 * Priority:
 *   1. WordPress Customizer `custom_logo` (if the admin ever sets it).
 *   2. The original uploaded Sampreshan logo (Media Library, June 2026).
 *   3. Bundled brand fallback (favicon.svg).
 *
 * The "original" logo the site has always used lives at:
 *   /wp-content/uploads/2026/06/sampreshan-logo-svg.svg
 * Keep that path as the canonical default so header / footer / login /
 * dashboard / homepage all render the SAME mark.
 *
 * @return string Escaped-safe URL (not escaped — escape at output).
 */
if ( ! function_exists( 'sp_logo_url' ) ) {
    function sp_logo_url() {
        // 1. Customizer logo wins if set.
        $custom_id = function_exists( 'get_theme_mod' ) ? (int) get_theme_mod( 'custom_logo' ) : 0;
        if ( $custom_id > 0 ) {
            $src = wp_get_attachment_image_src( $custom_id, 'full' );
            if ( $src && ! empty( $src[0] ) ) {
                return $src[0];
            }
        }

        // 2. Original uploaded logo — the one already set on the live site.
        return content_url( 'uploads/2026/06/sampreshan-logo-svg.svg' );
    }
}

/**
 * Echo an <img> tag for the Sampreshan logo.
 *
 * @param string $class Extra CSS class(es).
 * @param int    $size  Width/height in px.
 */
if ( ! function_exists( 'sp_logo_e' ) ) {
    function sp_logo_e( $class = 'site-header__logo', $size = 40 ) {
        $url  = sp_logo_url();
        $name = function_exists( 'get_bloginfo' ) ? get_bloginfo( 'name' ) : 'SampreShan';
        printf(
            '<img class="%s" src="%s" alt="%s" width="%d" height="%d" fetchpriority="high" />',
            esc_attr( $class ),
            esc_url( $url ),
            esc_attr( $name ),
            (int) $size,
            (int) $size
        );
    }
}
