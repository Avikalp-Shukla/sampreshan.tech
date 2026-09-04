<?php
/**
 * IconScout API Client
 * Search and fetch icons, illustrations, Lottie animations from IconScout v3 API.
 *
 * @package SampreShan_Child
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'Sp_IconScout_API' ) ) {

    class Sp_IconScout_API {

        private const BASE_URL = 'https://api.iconscout.com/v3';
        private const CACHE_TTL = 3600; // 1 hour

        private string $client_id;
        private string $api_key;
        private array $cache = [];

        public function __construct( ?string $client_id = null, ?string $api_key = null ) {
            $this->client_id = $client_id ?? $this->get_client_id_from_config();
            $this->api_key   = $api_key   ?? $this->get_api_key_from_config();
        }

        private function get_client_id_from_config(): string {
            // Priority: wp-config constant > env var > option
            if ( defined( 'ICONSCOUT_CLIENT_ID' ) ) {
                return ICONSCOUT_CLIENT_ID;
            }
            $env = getenv( 'ICONSCOUT_CLIENT_ID' );
            if ( $env ) {
                return $env;
            }
            return (string) get_option( 'iconscout_client_id', '' );
        }

        private function get_api_key_from_config(): string {
            if ( defined( 'ICONSCOUT_API_KEY' ) ) {
                return ICONSCOUT_API_KEY;
            }
            $env = getenv( 'ICONSCOUT_API_KEY' );
            if ( $env ) {
                return $env;
            }
            return (string) get_option( 'iconscout_api_key', '' );
        }

        public function is_configured(): bool {
            return '' !== $this->client_id && '' !== $this->api_key;
        }

        /**
         * Search icons/illustrations/lottie.
         *
         * @param string $query       Search term.
         * @param string $asset       icon | illustration | lottie | all.
         * @param int    $page        Page number (1-based).
         * @param int    $per_page    Results per page (max 100).
         * @param array  $filters     Optional: style, color, premium, etc.
         * @return array|WP_Error     API response or error.
         */
        public function search( string $query, string $asset = 'icon', int $page = 1, int $per_page = 30, array $filters = [] ): array|WP_Error {
            if ( ! $this->is_configured() ) {
                return new WP_Error( 'iconscout_not_configured', __( 'IconScout credentials not set.', 'sampreshan-child' ) );
            }

            $cache_key = md5( "search:{$query}:{$asset}:{$page}:{$per_page}:" . json_encode( $filters ) );
            if ( isset( $this->cache[ $cache_key ] ) ) {
                return $this->cache[ $cache_key ];
            }

            $params = array_merge( [
                'query'     => sanitize_text_field( $query ),
                'asset'     => in_array( $asset, [ 'icon', 'illustration', 'lottie', 'all' ], true ) ? $asset : 'icon',
                'page'      => max( 1, $page ),
                'per_page'  => min( 100, max( 1, $per_page ) ),
            ], $filters );

            $url = self::BASE_URL . '/search?' . http_build_query( $params );

            $response = wp_remote_get( $url, [
                'headers' => [
                    'Client-ID' => $this->client_id,
                    'Authorization' => 'Bearer ' . $this->api_key,
                    'Accept' => 'application/json',
                ],
                'timeout' => 15,
            ] );

            if ( is_wp_error( $response ) ) {
                return $response;
            }

            $code = wp_remote_retrieve_response_code( $response );
            $body = wp_remote_retrieve_body( $response );

            if ( 200 !== $code ) {
                $data = json_decode( $body, true );
                $msg  = $data['message'] ?? "IconScout API error (HTTP {$code})";
                return new WP_Error( 'iconscout_api_error', $msg );
            }

            $data = json_decode( $body, true );
            if ( ! is_array( $data ) ) {
                return new WP_Error( 'iconscout_invalid_response', __( 'Invalid API response.', 'sampreshan-child' ) );
            }

            $this->cache[ $cache_key ] = $data;
            return $data;
        }

        /**
         * Get single asset details by ID.
         */
        public function get_asset( string $asset_type, int $asset_id ): array|WP_Error {
            if ( ! $this->is_configured() ) {
                return new WP_Error( 'iconscout_not_configured', __( 'IconScout credentials not set.', 'sampreshan-child' ) );
            }

            $url = self::BASE_URL . "/{$asset_type}/{$asset_id}";
            $response = wp_remote_get( $url, [
                'headers' => [
                    'Client-ID' => $this->client_id,
                    'Authorization' => 'Bearer ' . $this->api_key,
                    'Accept' => 'application/json',
                ],
                'timeout' => 10,
            ] );

            if ( is_wp_error( $response ) ) { return $response; }
            $code = wp_remote_retrieve_response_code( $response );
            $body = wp_remote_retrieve_body( $response );
            if ( 200 !== $code ) { return new WP_Error( 'iconscout_api_error', "HTTP {$code}" ); }

            return json_decode( $body, true ) ?? new WP_Error( 'iconscout_invalid_response', __( 'Invalid response.', 'sampreshan-child' ) );
        }

        /**
         * Get download URL for asset (SVG/PNG/Lottie JSON).
         */
        public function get_download_url( string $asset_type, int $asset_id, string $format = 'svg', int $size = 512 ): string|WP_Error {
            $asset = $this->get_asset( $asset_type, $asset_id );
            if ( is_wp_error( $asset ) ) { return $asset; }

            $formats = $asset['data']['formats'] ?? [];
            foreach ( $formats as $fmt ) {
                if ( ( $fmt['format'] ?? '' ) === $format && ( $fmt['size'] ?? 512 ) === $size ) {
                    return $fmt['url'] ?? new WP_Error( 'iconscout_no_url', __( 'Download URL not available.', 'sampreshan-child' ) );
                }
            }
            // Fallback: first matching format
            foreach ( $formats as $fmt ) {
                if ( ( $fmt['format'] ?? '' ) === $format ) {
                    return $fmt['url'] ?? new WP_Error( 'iconscout_no_url', __( 'Download URL not available.', 'sampreshan-child' ) );
                }
            }
            return new WP_Error( 'iconscout_format_missing', __( 'Requested format not available.', 'sampreshan-child' ) );
        }

        /**
         * Clear internal cache.
         */
        public function clear_cache(): void {
            $this->cache = [];
        }
    }
}

/**
 * Helper: get singleton instance.
 */
function sp_iconscout_api(): Sp_IconScout_API {
    static $instance = null;
    if ( null === $instance ) {
        $instance = new Sp_IconScout_API();
    }
    return $instance;
}