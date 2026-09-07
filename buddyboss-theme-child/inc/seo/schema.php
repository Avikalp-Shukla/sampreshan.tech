<?php
/**
 * Sampreshan SEO — structured data (JSON-LD) + social fallbacks.
 *
 * Rank Math already covers titles, meta, canonical, OG basics and the
 * homepage graph, so this file ONLY adds what Rank Math cannot know:
 * petition interaction counts (Is), collection ItemLists for listing
 * pages, member/profile Person graphs, and a logo fallback for the
 * OG image on cover-less petitions. Nothing here duplicates Rank Math.
 *
 * @package SampreShan_Child
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

function sp_seo_site_org() {
    $logo = function_exists( 'sp_logo_url' ) ? sp_logo_url() : get_site_icon_url( 512 );
    return array(
        '@type' => 'Organization',
        '@id'   => home_url( '/#organization' ),
        'name'  => get_bloginfo( 'name' ),
        'url'   => home_url( '/' ),
        'logo'  => $logo ? array( '@type' => 'ImageObject', 'url' => $logo ) : home_url( '/' ),
        'sameAs'=> array( 'https://shivbodhtrust.org' ),
    );
}

function sp_seo_petition_ids( $args = array() ) {
    $q = new WP_Query( array_merge( array(
        'post_type'      => 'petition',
        'post_status'    => 'publish',
        'posts_per_page' => 12,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'fields'         => 'ids',
        'no_found_rows'  => true,
    ), $args ) );
    $ids = $q->posts ? array_map( 'intval', (array) $q->posts ) : array();
    wp_reset_postdata();
    return $ids;
}

function sp_seo_itemlist( $ids, $kind = 'Article' ) {
    $items = array();
    $pos   = 1;
    foreach ( (array) $ids as $id ) {
        $id = (int) $id;
        if ( $id <= 0 ) { continue; }
        $items[] = array(
            '@type'    => 'ListItem',
            'position' => $pos++,
            'url'      => get_permalink( $id ),
            'name'     => get_the_title( $id ),
            'item'     => array( '@type' => $kind, 'url' => get_permalink( $id ), 'name' => get_the_title( $id ) ),
        );
    }
    return $items;
}

/**
 * Print page-specific JSON-LD (single graph per page).
 */
function sp_seo_print_schema() {
    if ( is_admin() || ! function_exists( 'get_queried_object_id' ) ) { return; }
    $graph = null;

    /* --- Single petition: Article + I interaction counter --- */
    if ( is_singular( 'petition' ) ) {
        $pid = get_queried_object_id();
        $post = get_post( $pid );
        if ( $post && 'petition' === $post->post_type ) {
            $count = function_exists( 'sp_petition_signature_count' ) ? (int) sp_petition_signature_count( $pid ) : (int) get_post_meta( $pid, 'sampreshan_signatures', true );
            $goal  = (int) get_post_meta( $pid, 'sampreshan_goal', true );
            $cover = get_the_post_thumbnail_url( $pid, 'large' );
            $logo  = function_exists( 'sp_logo_url' ) ? sp_logo_url() : '';
            $author_id = (int) $post->post_author;
            $article = array(
                '@type'            => 'Article',
                'mainEntityOfPage' => get_permalink( $pid ),
                'headline'         => get_the_title( $pid ),
                'description'      => has_excerpt( $pid ) ? get_the_excerpt( $pid ) : wp_trim_words( wp_strip_all_tags( $post->post_content ), 30, '...' ),
                'image'            => $cover ? $cover : $logo,
                'datePublished'    => get_post_time( 'c', true, $pid ),
                'dateModified'     => get_post_modified_time( 'c', true, $pid ),
                'author'           => array(
                    '@type' => 'Person',
                    'name'  => $author_id > 0 ? get_the_author_meta( 'display_name', $author_id ) : get_bloginfo( 'name' ),
                ),
                'publisher'        => sp_seo_site_org(),
                'interactionStatistic' => array(
                    '@type'              => 'InteractionCounter',
                    'interactionType'    => 'https://schema.org/LikeAction',
                    'userInteractionCount' => $count,
                ),
            );
            if ( $goal > 0 ) { $article['interactionStatistic']['target'] = $goal; }
            $graph = $article;
        }
    }
    /* --- All-petitions listing --- */
    elseif ( is_page_template( 'template-petitions.php' ) ) {
        $ids = sp_seo_petition_ids();
        if ( $ids ) {
            $graph = array(
                '@type'           => 'CollectionPage',
                'mainEntityOfPage'=> get_permalink(),
                'name'            => get_the_title(),
                'description'     => has_excerpt() ? get_the_excerpt() : '',
                'mainEntity'      => array( '@type' => 'ItemList', 'itemListElement' => sp_seo_itemlist( $ids ) ),
            );
        }
    }
    /* --- Cause category page --- */
    elseif ( is_page_template( 'template-category.php' ) ) {
        $slug = get_post_field( 'post_name', get_queried_object_id() );
        $term = str_replace( 'category-', '', (string) $slug );
        $ids  = $term ? sp_seo_petition_ids( array(
            'tax_query' => array( array( 'taxonomy' => 'cause_category', 'field' => 'slug', 'terms' => $term ) ),
        ) ) : array();
        $graph = array(
            '@type'           => 'CollectionPage',
            'mainEntityOfPage'=> get_permalink(),
            'name'            => get_the_title(),
            'about'           => get_the_title(),
            'mainEntity'      => array( '@type' => 'ItemList', 'itemListElement' => sp_seo_itemlist( $ids ) ),
        );
    }
    /* --- Community members grid --- */
    elseif ( is_page_template( 'template-members.php' ) ) {
        $users = get_users( array( 'number' => 12, 'orderby' => 'registered', 'order' => 'DESC', 'fields' => array( 'ID', 'display_name' ) ) );
        $items = array();
        $pos   = 1;
        foreach ( (array) $users as $u ) {
            $items[] = array(
                '@type'    => 'ListItem',
                'position' => $pos++,
                'url'      => get_author_posts_url( (int) $u->ID ),
                'name'     => $u->display_name,
            );
        }
        if ( $items ) {
            $graph = array(
                '@type'           => 'CollectionPage',
                'mainEntityOfPage'=> get_permalink(),
                'name'            => get_the_title(),
                'mainEntity'      => array( '@type' => 'ItemList', 'itemListElement' => $items ),
            );
        }
    }
    /* --- Own profile page --- */
    elseif ( is_page_template( 'template-profile.php' ) && is_user_logged_in() ) {
        $uid = get_current_user_id();
        $graph = array(
            '@type'           => 'ProfilePage',
            'mainEntityOfPage'=> get_permalink(),
            'mainEntity'      => array(
                '@type' => 'Person',
                'name'  => wp_get_current_user()->display_name,
                'url'   => get_author_posts_url( $uid ),
            ),
        );
    }

    if ( ! $graph ) { return; }
    $graph['@context'] = 'https://schema.org';
    echo '<script type="application/ld+json">' . wp_json_encode( $graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_head', 'sp_seo_print_schema', 30 );

/**
 * OG image fallback: logo when a petition has no cover
 * (no-ops harmlessly if Rank Math is absent or hooks differ).
 */
function sp_seo_og_image_fallback( $image ) {
    if ( ! empty( $image ) || ! is_singular( 'petition' ) ) { return $image; }
    if ( function_exists( 'sp_logo_url' ) ) {
        $logo = sp_logo_url();
        if ( $logo ) { return $logo; }
    }
    return $image;
}
add_filter( 'rank_math/opengraph/facebook/image', 'sp_seo_og_image_fallback', 20, 1 );
add_filter( 'rank_math/opengraph/twitter/image', 'sp_seo_og_image_fallback', 20, 1 );
