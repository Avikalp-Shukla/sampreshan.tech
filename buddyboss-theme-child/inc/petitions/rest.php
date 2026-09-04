<?php
/**
 * Petitions: REST API
 *
 * Namespace:  sampreshan/v1
 *
 * GET    /petitions                       list (paginated, filterable)
 * GET    /petitions/{id}                  single
 * POST   /petitions                       create (auth: edit_petitions)
 * PUT    /petitions/{id}                  update (auth: edit_petition)
 * DELETE /petitions/{id}                  delete (auth: delete_petition)
 * GET    /petitions/{id}/signatures       list signatures
 * POST   /petitions/{id}/sign             sign (auth: sign_petitions)
 * DELETE /petitions/{id}/sign             un-sign (auth: sign_petitions)
 *
 * @package SampreShan_Child
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! function_exists( 'sp_register_petition_rest_routes' ) ) {
    function sp_register_petition_rest_routes() {
        register_rest_route( 'sampreshan/v1', '/petitions', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => 'sp_rest_petitions_index',
                'permission_callback' => '__return_true',
                'args' => array(
                    'per_page' => array( 'default' => 12, 'sanitize_callback' => 'absint' ),
                    'page'     => array( 'default' => 1,  'sanitize_callback' => 'absint' ),
                    'category' => array( 'default' => '',  'sanitize_callback' => 'sanitize_text_field' ),
                    'status'   => array( 'default' => 'active', 'sanitize_callback' => 'sanitize_text_field' ),
                    'search'   => array( 'default' => '',  'sanitize_callback' => 'sanitize_text_field' ),
                ),
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => 'sp_rest_petitions_create',
                'permission_callback' => 'sp_rest_petition_create_perm',
            ),
        ) );

        register_rest_route( 'sampreshan/v1', '/petitions/(?P<id>\d+)', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => 'sp_rest_petitions_show',
                'permission_callback' => '__return_true',
            ),
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => 'sp_rest_petitions_update',
                'permission_callback' => 'sp_rest_petition_edit_perm',
            ),
            array(
                'methods'             => WP_REST_Server::DELETABLE,
                'callback'            => 'sp_rest_petitions_delete',
                'permission_callback' => 'sp_rest_petition_edit_perm',
            ),
        ) );

        register_rest_route( 'sampreshan/v1', '/petitions/(?P<id>\d+)/signatures', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => 'sp_rest_petition_signatures',
            'permission_callback' => '__return_true',
            'args' => array(
                'per_page' => array( 'default' => 20, 'sanitize_callback' => 'absint' ),
                'page'     => array( 'default' => 1,  'sanitize_callback' => 'absint' ),
            ),
        ) );

        register_rest_route( 'sampreshan/v1', '/petitions/(?P<id>\d+)/sign', array(
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => 'sp_rest_petition_sign',
                'permission_callback' => 'sp_rest_petition_sign_perm',
            ),
            array(
                'methods'             => WP_REST_Server::DELETABLE,
                'callback'            => 'sp_rest_petition_unsign',
                'permission_callback' => 'sp_rest_petition_sign_perm',
            ),
        ) );
    }
    add_action( 'rest_api_init', 'sp_register_petition_rest_routes' );
}

function sp_rest_petition_create_perm() {
    return is_user_logged_in() && current_user_can( 'edit_petitions' );
}
function sp_rest_petition_edit_perm( $request ) {
    $id = (int) $request['id'];
    return is_user_logged_in() && current_user_can( 'edit_petition', $id );
}
function sp_rest_petition_sign_perm() {
    return is_user_logged_in() && current_user_can( 'sign_petitions' );
}

/* ============================================================
   Handlers
   ============================================================ */

function sp_rest_petitions_index( WP_REST_Request $request ) {
    $args = array(
        'post_type'      => 'petition',
        'post_status'    => 'publish',
        'posts_per_page' => (int) $request->get_param( 'per_page' ),
        'paged'          => (int) $request->get_param( 'page' ),
        's'              => (string) $request->get_param( 'search' ),
    );
    $status = (string) $request->get_param( 'status' );
    $meta_query = array();
    if ( $status && 'all' !== $status ) {
        $meta_query[] = array( 'key' => 'sampreshan_status', 'value' => $status );
    }
    if ( $meta_query ) {
        $args['meta_query'] = $meta_query;
    }
    $cat = (string) $request->get_param( 'category' );
    if ( $cat ) {
        $args['tax_query'] = array( array(
            'taxonomy' => 'cause_category',
            'field'    => 'slug',
            'terms'    => explode( ',', $cat ),
        ) );
    }
    $q = new WP_Query( $args );
    $items = array_map( 'sp_rest_serialize_petition', $q->posts );
    return new WP_REST_Response( array(
        'items'      => $items,
        'total'      => (int) $q->found_posts,
        'pages'      => (int) $q->max_num_pages,
        'page'       => (int) $request->get_param( 'page' ),
        'per_page'   => (int) $request->get_param( 'per_page' ),
    ), 200 );
}

function sp_rest_petitions_show( WP_REST_Request $request ) {
    $post = get_post( (int) $request['id'] );
    if ( ! $post || 'petition' !== $post->post_type ) {
        return new WP_Error( 'not_found', 'Petition not found', array( 'status' => 404 ) );
    }
    return new WP_REST_Response( sp_rest_serialize_petition( $post, true ), 200 );
}

function sp_rest_petitions_create( WP_REST_Request $request ) {
    $body = (array) $request->get_json_params();
    $title = sanitize_text_field( $body['title'] ?? '' );
    if ( ! $title ) {
        return new WP_Error( 'missing_title', 'Title is required', array( 'status' => 400 ) );
    }
    $post_id = wp_insert_post( array(
        'post_type'    => 'petition',
        'post_status'  => 'publish',
        'post_title'   => $title,
        'post_content' => wp_kses_post( $body['content'] ?? '' ),
        'post_excerpt' => sanitize_textarea_field( $body['excerpt'] ?? '' ),
        'post_author'  => get_current_user_id(),
    ), true );
    if ( is_wp_error( $post_id ) ) { return $post_id; }

    if ( ! empty( $body['goal'] ) ) {
        update_post_meta( $post_id, 'sampreshan_goal', absint( $body['goal'] ) );
    }
    if ( ! empty( $body['category'] ) ) {
        wp_set_object_terms( $post_id, (array) $body['category'], 'cause_category' );
    }
    return new WP_REST_Response( sp_rest_serialize_petition( get_post( $post_id ), true ), 201 );
}

function sp_rest_petitions_update( WP_REST_Request $request ) {
    $id = (int) $request['id'];
    $body = (array) $request->get_json_params();
    $update = array( 'ID' => $id );
    if ( isset( $body['title'] ) )   { $update['post_title']   = sanitize_text_field( $body['title'] ); }
    if ( isset( $body['content'] ) ) { $update['post_content'] = wp_kses_post( $body['content'] ); }
    if ( isset( $body['excerpt'] ) )  { $update['post_excerpt'] = sanitize_textarea_field( $body['excerpt'] ); }
    if ( isset( $body['status'] ) )  { $update['post_status']  = sanitize_text_field( $body['status'] ); }
    $r = wp_update_post( $update, true );
    if ( is_wp_error( $r ) ) { return $r; }
    if ( isset( $body['goal'] ) )    { update_post_meta( $id, 'sampreshan_goal', absint( $body['goal'] ) ); }
    if ( isset( $body['category'] ) ) { wp_set_object_terms( $id, (array) $body['category'], 'cause_category' ); }
    return new WP_REST_Response( sp_rest_serialize_petition( get_post( $id ), true ), 200 );
}

function sp_rest_petitions_delete( WP_REST_Request $request ) {
    $id = (int) $request['id'];
    $r = wp_delete_post( $id, true );
    if ( ! $r ) {
        return new WP_Error( 'delete_failed', 'Could not delete petition', array( 'status' => 500 ) );
    }
    return new WP_REST_Response( array( 'deleted' => true, 'id' => $id ), 200 );
}

function sp_rest_petition_signatures( WP_REST_Request $request ) {
    $rows = sp_petition_get_signatures( array(
        'petition_id' => (int) $request['id'],
        'per_page'    => (int) $request->get_param( 'per_page' ),
        'page'        => (int) $request->get_param( 'page' ),
    ) );
    $items = array();
    foreach ( $rows as $r ) {
        $items[] = array(
            'id'         => (int) $r->id,
            'user_id'    => (int) $r->user_id,
            'name'       => (int) $r->is_anonymous ? __( 'Anonymous supporter', 'sampreshan-child' ) : $r->display_name,
            'comment'    => $r->comment,
            'created_at' => $r->created_at,
        );
    }
    $count = sp_petition_signature_count( (int) $request['id'] );
    return new WP_REST_Response( array(
        'items'    => $items,
        'total'    => $count,
        'page'     => (int) $request->get_param( 'page' ),
        'per_page' => (int) $request->get_param( 'per_page' ),
    ), 200 );
}

function sp_rest_petition_sign( WP_REST_Request $request ) {
    $id = sp_petition_add_signature( array(
        'petition_id'  => (int) $request['id'],
        'user_id'      => get_current_user_id(),
        'comment'      => sanitize_textarea_field( (string) $request->get_param( 'comment' ) ),
        'is_anonymous' => (int) $request->get_param( 'is_anonymous' ),
    ) );
    if ( ! $id ) {
        return new WP_Error( 'already_signed', 'You have already signed this petition', array( 'status' => 409 ) );
    }
    return new WP_REST_Response( array(
        'id'    => (int) $id,
        'count' => sp_petition_signature_count( (int) $request['id'] ),
    ), 201 );
}

function sp_rest_petition_unsign( WP_REST_Request $request ) {
    $ok = sp_petition_remove_signature( (int) $request['id'], get_current_user_id() );
    if ( ! $ok ) {
        return new WP_Error( 'not_signed', 'You have not signed this petition', array( 'status' => 404 ) );
    }
    return new WP_REST_Response( array(
        'count' => sp_petition_signature_count( (int) $request['id'] ),
    ), 200 );
}

/**
 * Serialize a petition post for the REST response.
 */
function sp_rest_serialize_petition( $post, $detailed = false ) {
    $pid = is_object( $post ) ? $post->ID : (int) $post;
    $post = get_post( $pid );
    if ( ! $post ) { return null; }
    $count = function_exists( 'sp_petition_signature_count' ) ? sp_petition_signature_count( $pid ) : 0;
    $goal  = (int) get_post_meta( $pid, 'sampreshan_goal', true );
    $data = array(
        'id'             => (int) $pid,
        'title'          => get_the_title( $pid ),
        'excerpt'        => get_the_excerpt( $pid ),
        'permalink'      => get_permalink( $pid ),
        'author'         => (int) $post->post_author,
        'author_name'    => get_the_author_meta( 'display_name', $post->post_author ),
        'featured_image' => get_the_post_thumbnail_url( $pid, 'medium_large' ),
        'signatures'     => $count,
        'goal'           => $goal,
        'percent'        => $goal > 0 ? min( 100, round( ( $count / $goal ) * 100, 1 ) ) : 0,
        'status'         => get_post_meta( $pid, 'sampreshan_status', true ) ?: 'active',
        'deadline'       => get_post_meta( $pid, 'sampreshan_deadline', true ),
        'categories'     => wp_list_pluck( wp_get_post_terms( $pid, 'cause_category' ), 'name' ),
        'created_at'     => mysql2date( 'c', $post->post_date_gmt ),
    );
    if ( $detailed ) {
        $data['content']      = apply_filters( 'the_content', $post->post_content );
        $data['signed_by_me'] = is_user_logged_in() ? sp_petition_user_has_signed( $pid ) : false;
    }
    return $data;
}
