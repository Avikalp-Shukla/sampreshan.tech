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
        // A page renders 20-50 icons; read each file once per request.
        static $svg_cache = array();

        $name  = sanitize_key( $name );
        $class = esc_attr( $class );
        $path  = get_stylesheet_directory() . '/assets/icons/3d/' . $name . '.svg';

        if ( ! isset( $svg_cache[ $name ] ) ) {
            if ( ! file_exists( $path ) ) {
                return '<!-- sp_icon: ' . esc_html( $name ) . ' not found -->';
            }

            $svg = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions
            if ( false === $svg ) {
                return '<!-- sp_icon: ' . esc_html( $name ) . ' unreadable -->';
            }

            // Strip XML declaration if present (WordPress renders inline fine without it).
            $svg_cache[ $name ] = preg_replace( '/<\?xml[^?]*\?>/', '', $svg );
        }

        $svg = $svg_cache[ $name ];

        /*
         * Gradient and filter IDs are already namespaced per icon file
         * (e.g. `omBody`, `homeGlow`), so the same icon can be inlined many
         * times on one page without ID collisions — no per-instance suffix
         * needed, which keeps this call allocation-free.
         */
        if ( '' !== $label ) {
            $aria = ' role="img" aria-label="' . esc_attr( $label ) . '"';
        } else {
            // Decorative by default: keep it out of the accessibility tree
            // instead of shipping an empty aria-label.
            $aria = ' aria-hidden="true" focusable="false"';
        }

        /*
         * Replace — rather than append to — the root <svg> attributes.
         * The source files already ship `role`/`aria-label`, so injecting a
         * second copy produced duplicate attributes: invalid markup that
         * browsers silently resolved in favour of the file's own label,
         * meaning the caller's label was ignored. Strip ours-in-waiting
         * first, then set them once.
         */
        $svg = preg_replace_callback(
            '/<svg([^>]*)>/',
            function ( $m ) use ( $name, $aria ) {
                $attrs = preg_replace(
                    '/\s(?:role|aria-label|aria-hidden|aria-labelledby|focusable|class|data-icon)="[^"]*"/',
                    '',
                    $m[1]
                );
                return '<svg' . $attrs . ' class="sp-icon__svg"' . $aria
                    . ' data-icon="' . esc_attr( $name ) . '">';
            },
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
 * SampreShan artwork: URL of an original SVG in assets/art/.
 *
 * Centralising the path keeps templates short and means the art directory
 * can move without touching 20 files.
 *
 * @param string $file Filename inside assets/art/ (e.g. 'mandala.svg').
 * @return string Absolute URL, or '' when the file does not exist.
 */
if ( ! function_exists( 'sp_art_url' ) ) {
    function sp_art_url( $file ) {
        $file = basename( (string) $file );
        $path = get_stylesheet_directory() . '/assets/art/' . $file;
        if ( ! file_exists( $path ) ) {
            return '';
        }
        return get_stylesheet_directory_uri() . '/assets/art/' . $file;
    }
}

/**
 * Auto icon — renders the original Sampreshan SVG art.
 *
 * This used to prefer imported Icons8 PNG stock art and fall back to our
 * custom SVGs. All Icons8 stock art has been removed in favour of the
 * original Sampreshan SVG set, so this now renders the SVG directly.
 * The name is kept so the 200+ existing call sites stay untouched.
 *
 * @param string $name  Icon name (file in assets/icons/3d/).
 * @param string $class Wrapper class list.
 * @param string $label Optional aria-label override.
 */
if ( ! function_exists( 'sp_icon_auto' ) ) {
    function sp_icon_auto( $name, $class = 'sp-icon sp-icon--md', $label = '' ) {
        sp_icon_e( $name, $class, $label );
    }
}

/**
 * Cause icon (echo variant).
 */
if ( ! function_exists( 'sp_cause_icon_e' ) ) {
    function sp_cause_icon_e( $slug_or_name, $class = 'sp-icon sp-icon--md', $label = '' ) {
        sp_icon_e( sp_cause_icon( $slug_or_name ), $class, $label );
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

/**
 * Messenger pigeon — 3D SVG dove with flapping wings.
 *
 * Sampreshan's "kabootar post": the bird carries the community's voice.
 * Pure inline SVG (zero HTTP requests); flight + wing-flap motion comes
 * from CSS classes in homepage.css (.sp-pigeon, .sp-pigeon__wing...).
 *
 * @param string $suffix Unique per instance on a page (gradient IDs).
 * @param bool   $mail   Hang a saffron letter from the beak.
 */
if ( ! function_exists( 'sp_pigeon' ) ) {
    function sp_pigeon( $suffix = 'a', $mail = true ) {
        $s = preg_replace( '/[^a-z0-9]/', '', strtolower( (string) $suffix ) );
        if ( '' === $s ) { $s = 'a'; }
        ob_start();
        ?>
        <svg class="sp-pigeon__svg" viewBox="0 0 220 150" aria-hidden="true" focusable="false">
            <defs>
                <linearGradient id="pg-body-<?php echo esc_attr( $s ); ?>" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0" stop-color="#FFFFFF" />
                    <stop offset="0.55" stop-color="#E9EDF3" />
                    <stop offset="1" stop-color="#C6CEDA" />
                </linearGradient>
                <linearGradient id="pg-wing-<?php echo esc_attr( $s ); ?>" x1="0" y1="0" x2="1" y2="1">
                    <stop offset="0" stop-color="#FFFFFF" />
                    <stop offset="1" stop-color="#BCC6D4" />
                </linearGradient>
                <linearGradient id="pg-wingback-<?php echo esc_attr( $s ); ?>" x1="0" y1="0" x2="1" y2="1">
                    <stop offset="0" stop-color="#D3D9E3" />
                    <stop offset="1" stop-color="#A3AEC0" />
                </linearGradient>
                <linearGradient id="pg-env-<?php echo esc_attr( $s ); ?>" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0" stop-color="#FFC46B" />
                    <stop offset="1" stop-color="#E0851A" />
                </linearGradient>
            </defs>
            <!-- tail feathers -->
            <g fill="url(#pg-wingback-<?php echo esc_attr( $s ); ?>)" stroke="#8E99A8" stroke-width="1" stroke-opacity="0.55" stroke-linejoin="round">
                <path d="M66,84 L20,64 L34,86 Z" />
                <path d="M66,90 L14,88 L34,96 Z" />
                <path d="M66,96 L24,114 L40,100 Z" />
            </g>
            <!-- far wing (darker, opposite phase) -->
            <g class="sp-pigeon__wing sp-pigeon__wing--back">
                <path d="M96,74 C90,60 80,44 62,36 C60,35 58,36 59,38 C72,56 82,68 90,78 C92,81 97,79 96,74 Z" fill="url(#pg-wingback-<?php echo esc_attr( $s ); ?>)" stroke="#8E99A8" stroke-width="1" stroke-opacity="0.5" />
                <path d="M90,64 C82,56 74,49 66,44" fill="none" stroke="#8E99A8" stroke-width="1.2" stroke-opacity="0.55" stroke-linecap="round" />
            </g>
            <!-- body -->
            <path d="M30,100 C55,88 70,78 92,74 C100,72 108,66 116,60 C124,52 136,50 144,56 C150,60 150,68 144,72 C138,78 140,84 136,90 C120,104 80,110 52,106 C40,104 32,102 30,100 Z" fill="url(#pg-body-<?php echo esc_attr( $s ); ?>)" />
            <ellipse cx="100" cy="94" rx="34" ry="10" fill="#FFFFFF" opacity="0.65" />
            <path d="M60,82 C85,70 115,68 138,74" fill="none" stroke="#AEB8C6" stroke-width="4" stroke-opacity="0.45" stroke-linecap="round" />
            <!-- head details -->
            <circle cx="140" cy="62" r="3" fill="#26303B" />
            <circle cx="141" cy="61" r="1" fill="#FFFFFF" />
            <path d="M152,62 L168,66 L151,71 Z" fill="#E0851A" />
            <circle cx="152" cy="63" r="2.5" fill="#F5F7FA" />
            <!-- near wing (big, leading) -->
            <g class="sp-pigeon__wing sp-pigeon__wing--front">
                <path d="M104,72 C96,52 80,30 52,18 C50,17 48,18 49,20 C66,44 82,62 94,78 C97,82 103,78 104,72 Z" fill="url(#pg-wing-<?php echo esc_attr( $s ); ?>)" stroke="#8E99A8" stroke-width="1" stroke-opacity="0.5" />
                <path d="M92,60 C80,48 68,38 56,32" fill="none" stroke="#9AA5B5" stroke-width="1.5" stroke-opacity="0.6" stroke-linecap="round" />
                <path d="M98,66 C88,56 78,48 68,42" fill="none" stroke="#9AA5B5" stroke-width="1.2" stroke-opacity="0.5" stroke-linecap="round" />
            </g>
            <?php if ( $mail ) : ?>
            <!-- letter on a string -->
            <g class="sp-pigeon__mail">
                <path d="M160,70 C166,82 172,92 178,100" fill="none" stroke="#8A6D3B" stroke-width="1.5" />
                <rect x="160" y="100" width="36" height="25" rx="4" fill="url(#pg-env-<?php echo esc_attr( $s ); ?>)" stroke="#B45309" stroke-width="1" />
                <path d="M160,104 L178,116 L196,104" fill="none" stroke="#B45309" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                <circle cx="178" cy="113" r="3" fill="#9A3412" />
                <circle cx="177" cy="112" r="1" fill="#FFD9A8" />
            </g>
            <?php endif; ?>
        </svg>
        <?php
        echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
}
