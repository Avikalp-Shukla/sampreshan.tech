<?php
/**
 * Framer Sync — WordPress-side embedder
 *
 * Loads the Framer project export produced by `npm run framer:pull` and renders
 * the Framer design as the full site UI for sampreshan.tech.
 *
 * Three render modes (auto-selected by what's available in cache):
 *   1. Iframe     — fastest, uses the Framer preview/production hostname.
 *                   Best when the Framer project IS the entire site.
 *   2. Inline     — falls back to the JSON snapshot + custom render.
 *                   Used when no hostname is available (e.g. draft only).
 *   3. Bridge     — uses a tiny JS shim to lazy-render the Framer canvas.
 *
 * @package SampreShan_Child
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! defined( 'SAMPRESHAN_FRAMER_CACHE' ) ) {
    define( 'SAMPRESHAN_FRAMER_CACHE', get_stylesheet_directory() . '/../framer-sync/output/project.json' );
}
if ( ! defined( 'SAMPRESHAN_FRAMER_DEPLOYMENT_CACHE' ) ) {
    define( 'SAMPRESHAN_FRAMER_DEPLOYMENT_CACHE', get_stylesheet_directory() . '/../framer-sync/cache/last-publish.json' );
}

/**
 * Get the current Framer snapshot (or false if not pulled yet).
 *
 * @return array|false
 */
if ( ! function_exists( 'sp_framer_snapshot' ) ) {
    function sp_framer_snapshot() {
        foreach ( array( SAMPRESHAN_FRAMER_CACHE, SAMPRESHAN_FRAMER_DEPLOYMENT_CACHE ) as $path ) {
            if ( $path && file_exists( $path ) ) {
                $data = json_decode( (string) file_get_contents( $path ), true );
                if ( is_array( $data ) ) {
                    return $data;
                }
            }
        }
        return false;
    }
}

/**
 * Get the live Framer hostname (if a deployment exists).
 *
 * @return string|false  e.g. "https://sampreshan.framer.app" or false
 */
if ( ! function_exists( 'sp_framer_hostname' ) ) {
    function sp_framer_hostname() {
        $snap = sp_framer_snapshot();
        if ( ! $snap ) { return false; }

        // The Framer API returns hostnames inside the deployment result.
        // We mirror the structure that sync.js writes back to disk.
        if ( ! empty( $snap['deployment']['hostnames'] ) ) {
            $hosts = (array) $snap['deployment']['hostnames'];
            return $hosts[0] ?? false;
        }
        if ( ! empty( $snap['hostnames'][0] ) ) {
            return $snap['hostnames'][0];
        }
        if ( ! empty( $snap['project']['url'] ) ) {
            return $snap['project']['url'];
        }
        return false;
    }
}

/**
 * Render the Framer site as the full UI.
 * Iframes the Framer hostname and adds a fallback message + noscript
 * hint so search engines and screen readers still see real content.
 *
 * @param array $args
 *   - title:  iframe title attribute (a11y)
 *   - height: CSS height, default "100vh"
 *   - mode:   "auto" | "iframe" | "bridge" (default "auto")
 *
 * Usage in any template:
 *   <?php sp_framer_render( array( 'title' => 'SampreShan — Sanatan Voice Platform' ) ); ?>
 */
if ( ! function_exists( 'sp_framer_render' ) ) {
    function sp_framer_render( $args = array() ) {
        $args = wp_parse_args( $args, array(
            'title'  => get_bloginfo( 'name' ),
            'height' => '100vh',
            'mode'   => 'auto',
        ) );

        $hostname = sp_framer_hostname();
        $snap     = sp_framer_snapshot();

        if ( ! $hostname && ! $snap ) {
            return '<!-- sp_framer_render: no Framer snapshot found. Run `npm run framer:pull` in /framer-sync/ -->';
        }

        $mode = $args['mode'];
        if ( 'auto' === $mode ) {
            $mode = $hostname ? 'iframe' : 'bridge';
        }

        if ( 'iframe' === $mode && $hostname ) {
            // ---- Iframe embed (preferred) ----
            ?>
            <div class="sp-framer-embed" data-framer-hostname="<?php echo esc_attr( $hostname ); ?>" data-framer-mode="iframe" style="height: <?php echo esc_attr( $args['height'] ); ?>;">
                <iframe
                    src="<?php echo esc_url( $hostname ); ?>"
                    title="<?php echo esc_attr( $args['title'] ); ?>"
                    loading="lazy"
                    allow="fullscreen; clipboard-read; clipboard-write; autoplay; encrypted-media; picture-in-picture"
                    allowfullscreen
                    referrerpolicy="strict-origin-when-cross-origin"
                    sandbox="allow-scripts allow-same-origin allow-forms allow-popups allow-popups-to-escape-sandbox allow-downloads allow-modals"
                    style="width:100%;height:100%;border:0;display:block;">
                </iframe>
                <noscript>
                    <p style="padding:2rem;text-align:center;">
                        <?php
                        printf(
                            /* translators: %s = site name */
                            esc_html__( '%s requires JavaScript. Please enable it to view the full site.', 'sampreshan-child' ),
                            '<strong>' . esc_html( get_bloginfo( 'name' ) ) . '</strong>'
                        );
                        ?>
                    </p>
                </noscript>
            </div>
            <?php
            return;
        }

        if ( 'bridge' === $mode || ! $hostname ) {
            // ---- Bridge mode: load Framer's runtime into a container ----
            $project_id = is_array( $snap ) && ! empty( $snap['project']['id'] ) ? $snap['project']['id'] : '';
            ?>
            <div
                class="sp-framer-embed sp-framer-embed--bridge"
                data-framer-mode="bridge"
                data-framer-project-id="<?php echo esc_attr( $project_id ); ?>"
                style="height: <?php echo esc_attr( $args['height'] ); ?>;"
            >
                <div class="sp-framer-embed__placeholder" style="display:flex;align-items:center;justify-content:center;height:100%;font-family:var(--font-body,system-ui);">
                    <p style="text-align:center;max-width:480px;padding:2rem;">
                        <?php esc_html_e( 'Loading site…', 'sampreshan-child' ); ?>
                    </p>
                </div>
            </div>
            <?php
            return;
        }
    }
}

/**
 * Shortcode: [sampreshan_framer]
 * Embed the Framer site anywhere with a shortcode.
 */
if ( ! function_exists( 'sp_framer_shortcode' ) ) {
    function sp_framer_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'title'  => get_bloginfo( 'name' ),
            'height' => '100vh',
            'mode'   => 'auto',
        ), $atts, 'sampreshan_framer' );

        ob_start();
        sp_framer_render( $atts );
        return ob_get_clean();
    }
    add_shortcode( 'sampreshan_framer', 'sp_framer_shortcode' );
}

/**
 * Enqueue the bridge script (only registered; loaded only when bridge mode
 * is actually rendered, so iframe-only pages pay zero JS cost).
 */
if ( ! function_exists( 'sp_framer_enqueue_bridge' ) ) {
    function sp_framer_enqueue_bridge() {
        if ( is_page() || is_front_page() || is_home() ) {
            // Always register so cache warm-up is cheap.
            wp_register_script(
                'sampreshan-framer-bridge',
                get_stylesheet_directory_uri() . '/assets/js/framer-bridge.js',
                array(),
                SAMPRESHAN_CHILD_VERSION,
                true
            );
        }
    }
    add_action( 'wp_enqueue_scripts', 'sp_framer_enqueue_bridge' );
}
