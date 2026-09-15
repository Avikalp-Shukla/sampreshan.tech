<?php
/**
 * Clean and describe the migrated Acharya and Peetham pages.
 *
 * @package SampreShan_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function sp_migrated_page_slugs() {
    return array(
        'adi-shankaracharya',
        'sringeri-peetha',
        'dwarka-peetha',
        'puri-peetha',
        'jyotishpeetha',
        'kanchi-peetha',
        'swami-vidhushekhara-bharati',
        'swami-nishchalananda-saraswati',
        'swami-sadanand-saraswati',
        'swami-avimukta',
        'sri-satya-chandrashekarendra-saraswathi-shankaracharya',
    );
}

function sp_is_migrated_acharya_page() {
    return is_page() && in_array( get_post_field( 'post_name', get_queried_object_id() ), sp_migrated_page_slugs(), true );
}

/** Remove nested document wrappers from the imported Elementor HTML widget. */
function sp_clean_migrated_document_widget( $content ) {
    if ( false === stripos( $content, '<!doctype html' ) && false === stripos( $content, '<html' ) ) {
        return $content;
    }

    $styles = '';
    if ( preg_match( '/<head\b[^>]*>(.*?)<\/head>/is', $content, $head_match ) ) {
        preg_match_all( '/<style\b[^>]*>.*?<\/style>/is', $head_match[1], $style_matches );
        $styles = implode( "\n", $style_matches[0] );
    }

    if ( preg_match( '/<body\b[^>]*>(.*?)<\/body>/is', $content, $body_match ) ) {
        $content = $body_match[1];
    } else {
        $content = preg_replace( '/<!doctype html[^>]*>|<\/?html\b[^>]*>|<head\b[^>]*>.*?<\/head>/is', '', $content );
    }

    $content = preg_replace( '/<title\b[^>]*>.*?<\/title>|<meta\b[^>]*>|<link\b[^>]*>/is', '', $content );
    return $styles . $content;
}
add_filter( 'elementor/widget/render_content', 'sp_clean_migrated_document_widget', 999 );

/** Clean nested full-document HTML after Elementor has assembled the response. */
function sp_clean_migrated_page_response( $html ) {
    if ( substr_count( strtolower( $html ), '<!doctype html' ) < 2 ) {
        return $html;
    }

    $first = stripos( $html, '<!doctype html' );
    $offset = $first + 15;
    while ( false !== ( $start = stripos( $html, '<!doctype html', $offset ) ) ) {
        $end = stripos( $html, '</html>', $start );
        if ( false === $end ) {
            break;
        }

        $fragment = substr( $html, $start, $end + 7 - $start );
        $fragment = sp_clean_migrated_document_widget( $fragment );
        $html = substr_replace( $html, $fragment, $start, $end + 7 - $start );
        $offset = $start + strlen( $fragment );
    }

    return $html;
}

add_action(
    'template_redirect',
    static function (): void {
        if ( sp_is_migrated_acharya_page() ) {
            ob_start( 'sp_clean_migrated_page_response' );
        }
    },
    0
);

function sp_migrated_page_description( $description ) {
    if ( ! sp_is_migrated_acharya_page() ) {
        return $description;
    }

    $excerpt = wp_trim_words( wp_strip_all_tags( get_post_field( 'post_content', get_queried_object_id() ) ), 28, '...' );
    return $excerpt ? $excerpt : $description;
}
add_filter( 'rank_math/frontend/description', 'sp_migrated_page_description', 20 );

function sp_migrated_pages_schema() {
    if ( ! sp_is_migrated_acharya_page() ) {
        return;
    }

    $slug = get_post_field( 'post_name', get_queried_object_id() );
    $url = get_permalink();
    $title = get_the_title();
    $description = sp_migrated_page_description( '' );
    $profile_slugs = array( 'swami-vidhushekhara-bharati', 'swami-nishchalananda-saraswati', 'swami-sadanand-saraswati', 'swami-avimukta', 'sri-satya-chandrashekarendra-saraswathi-shankaracharya' );
    $type = in_array( $slug, $profile_slugs, true ) ? 'Person' : 'Place';

    $entity = array(
        '@type'        => $type,
        '@id'          => $url . '#entity',
        'name'         => $title,
        'url'          => $url,
        'description'  => $description,
        'isPartOf'     => array( '@id' => home_url( '/#website' ) ),
    );

    if ( 'Person' === $type ) {
        $entity['jobTitle'] = 'Shankaracharya';
        $entity['knowsAbout'] = array( 'Sanatana Dharma', 'Advaita Vedanta', 'Dharmic education' );
    } else {
        $entity['additionalType'] = 'https://schema.org/Place';
    }

    $schema = array(
        '@context' => 'https://schema.org',
        '@graph'  => array(
            array(
                '@type'      => 'WebPage',
                '@id'        => $url . '#webpage',
                'url'        => $url,
                'name'       => $title,
                'description' => $description,
                'mainEntity' => array( '@id' => $url . '#entity' ),
            ),
            $entity,
        ),
    );

    echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_head', 'sp_migrated_pages_schema', 35 );
