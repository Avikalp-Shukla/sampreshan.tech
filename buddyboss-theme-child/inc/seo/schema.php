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
    $org = array(
        '@type' => 'Organization',
        '@id'   => home_url( '/#organization' ),
        'name'  => get_bloginfo( 'name' ),
        'url'   => home_url( '/' ),
        'logo'  => $logo ? array( '@type' => 'ImageObject', 'url' => $logo ) : home_url( '/' ),
    );
    if ( ! is_singular( 'dharma_profile' ) && ! is_post_type_archive( 'dharma_profile' ) && ! is_tax( 'dharma_peeth' ) ) {
        $org['sameAs'] = array( 'https://shivbodhtrust.org' );
    }
    return $org;
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
    /* --- Dharma profile: Sampreshan is the only publisher/host identity. --- */
    elseif ( is_singular( 'dharma_profile' ) ) {
        $profile_id = get_queried_object_id();
        $profile    = get_post( $profile_id );
        if ( $profile ) {
            $peeth = (string) get_post_meta( $profile_id, '_sp_dharma_peeth', true );
            $graph = array(
                '@type'           => 'ProfilePage',
                'mainEntityOfPage'=> get_permalink( $profile_id ),
                'name'            => get_the_title( $profile_id ),
                'description'     => has_excerpt( $profile_id ) ? get_the_excerpt( $profile_id ) : wp_trim_words( wp_strip_all_tags( $profile->post_content ), 30, '...' ),
                'publisher'       => sp_seo_site_org(),
                'mainEntity'      => array(
                    '@type'       => 'Person',
                    '@id'         => get_permalink( $profile_id ) . '#profile',
                    'name'        => get_the_title( $profile_id ),
                    'description' => has_excerpt( $profile_id ) ? get_the_excerpt( $profile_id ) : '',
                    'url'         => get_permalink( $profile_id ),
                    'affiliation' => $peeth ? array( '@type' => 'Organization', 'name' => $peeth ) : null,
                ),
            );
            if ( empty( $graph['mainEntity']['affiliation'] ) ) {
                unset( $graph['mainEntity']['affiliation'] );
            }
        }
    }
    /* --- Dharma directory and Peeth listings. --- */
    elseif ( is_post_type_archive( 'dharma_profile' ) || is_tax( 'dharma_peeth' ) ) {
        $ids = get_posts( array(
            'post_type'      => 'dharma_profile',
            'post_status'    => 'publish',
            'posts_per_page' => 50,
            'fields'         => 'ids',
            'no_found_rows'  => true,
        ) );
        $graph = array(
            '@type'           => 'CollectionPage',
            'mainEntityOfPage'=> get_permalink(),
            'name'            => is_tax() ? single_term_title( '', false ) : __( 'Acharyas & Peeths', 'sampreshan-child' ),
            'publisher'       => sp_seo_site_org(),
            'mainEntity'      => array( '@type' => 'ItemList', 'itemListElement' => sp_seo_itemlist( $ids, 'ProfilePage' ) ),
        );
    }

    if ( ! $graph ) { return; }
    $graph['@context'] = 'https://schema.org';
    return $graph;
}

/** Add the child-theme graph to Rank Math instead of printing a second graph. */
function sp_seo_rank_math_graph( $data ) {
    $graph = sp_seo_print_schema();
    if ( ! $graph ) { return $data; }

    if ( is_array( $data ) && isset( $data['@graph'] ) && is_array( $data['@graph'] ) ) {
        $data['@graph'][] = $graph;
        return $data;
    }

    return $data;
}

if ( function_exists( 'rank_math' ) ) {
    add_filter( 'rank_math/json_ld', 'sp_seo_rank_math_graph', 20, 1 );
} else {
    add_action(
        'wp_head',
        static function () {
            $graph = sp_seo_print_schema();
            if ( $graph ) {
                echo '<script type="application/ld+json">' . wp_json_encode( $graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
            }
        },
        30
    );
}

/** Stable titles and descriptions for the public SampreShan information pages. */
function sp_seo_public_description( $description ) {
    if ( is_front_page() ) {
        return 'SampreShan is a Sanatana community platform for meaningful dialogue, cultural education, public causes and positive social participation.';
    }

    $descriptions = array(
        'about'                 => 'Learn about SampreShan, a community platform for Sanatana Dharma, dialogue, education and positive social participation.',
        'contact'               => 'Contact SampreShan for community questions, feedback, collaboration and platform support.',
        'contact-2'             => 'Contact SampreShan for community questions, feedback, collaboration and platform support.',
        'community-guidelines'  => 'Read the SampreShan community guidelines for respectful dialogue, participation and shared Sanatana values.',
        'petitions'             => 'Discover and support public petitions from the SampreShan community across social welfare, culture, education and dharmic causes.',
        'feed-2'                => 'Read the latest SampreShan community stories, updates and petitions from members across the sangha.',
        'community'             => 'Connect with SampreShan members and discover community conversations, groups and shared causes.',
        'start-a-petition'      => 'Start a petition on SampreShan and bring a meaningful Sanatana, cultural or community cause to people who can support it.',
        'privacy-policy'        => 'Read the SampreShan privacy policy and learn how account, community and platform data is handled.',
        'terms-conditions'     => 'Read the SampreShan terms and conditions for using the platform and participating in the community.',
        'disclaimer'            => 'Read the SampreShan disclaimer covering platform information, community content and user participation.',
    );

    $slug = is_page() ? (string) get_post_field( 'post_name', get_queried_object_id() ) : '';
    if ( isset( $descriptions[ $slug ] ) ) {
        return $descriptions[ $slug ];
    }

    if ( is_singular( 'post' ) ) {
        $content = wp_trim_words( wp_strip_all_tags( get_post_field( 'post_content', get_queried_object_id() ) ), 28, '...' );
        return $content ?: 'A community story shared on SampreShan, the Sanatana dialogue and participation platform.';
    }

    if ( is_singular( 'petition' ) ) {
        $content = wp_trim_words( wp_strip_all_tags( get_post_field( 'post_content', get_queried_object_id() ) ), 28, '...' );
        return $content ?: 'Support a meaningful community cause on SampreShan.';
    }

    if ( is_page() && ( '' === trim( (string) $description ) || false !== stripos( (string) $description, 'skip to main content' ) ) ) {
        return get_the_title() . ' on SampreShan: Sanatana community, dialogue and positive participation.';
    }

    return $description;
}
add_filter( 'rank_math/frontend/description', 'sp_seo_public_description', 30 );

function sp_seo_public_title( $title ) {
    if ( is_front_page() ) {
        return 'SampreShan | Sanatana dialogue, community and positive change';
    }
    if ( is_singular( 'post' ) ) {
        return get_the_title() . ' | SampreShan Community';
    }
    if ( is_singular( 'petition' ) ) {
        return get_the_title() . ' | SampreShan Petition';
    }
    if ( is_page() && get_the_title() ) {
        return get_the_title() . ' | SampreShan';
    }
    return $title;
}
add_filter( 'rank_math/frontend/title', 'sp_seo_public_title', 30 );

function sp_seo_private_page_robots( $robots ) {
    if ( is_page() && in_array( (string) get_post_field( 'post_name', get_queried_object_id() ), array( 'login', 'register', 'dashboard', 'settings', 'my-petitions', 'signed-petitions', 'account-security' ), true ) ) {
        return array( 'noindex', 'nofollow' );
    }
    return $robots;
}
add_filter( 'rank_math/frontend/robots', 'sp_seo_private_page_robots', 30 );

/** Keep the XML sitemap itself crawlable; individual public URLs remain governed by Rank Math. */
function sp_seo_sitemap_headers( $headers ) {
    if ( is_array( $headers ) ) {
        unset( $headers['X-Robots-Tag'], $headers['x-robots-tag'] );
    }
    return $headers;
}
add_filter( 'rank_math/sitemap/http_headers', 'sp_seo_sitemap_headers', 99 );

add_action( 'send_headers', static function () {
    $path = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
    if ( false !== strpos( $path, 'sitemap' ) && function_exists( 'header_remove' ) ) {
        header_remove( 'X-Robots-Tag' );
    }
}, 999 );

add_filter( 'robots_txt', static function ( $output, $public ) {
    if ( ! $public ) { return $output; }
    $sitemap = home_url( '/sitemap_index.xml' );
    if ( false === strpos( $output, $sitemap ) ) {
        $output = rtrim( $output ) . "\nSitemap: " . esc_url_raw( $sitemap ) . "\n";
    }
    return $output;
}, 20, 2 );

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
