<?php
/**
 * Dharma Acharya and Peeth directory, Anusaran, and update delivery.
 *
 * @package SampreShan_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

const SP_DHARMA_DIRECTORY_VERSION = '1.1.0';

function sp_dharma_profile_post_type() {
    return 'dharma_profile';
}

function sp_dharma_update_post_type() {
    return 'dharma_update';
}

function sp_dharma_register_content_types() {
    register_taxonomy(
        'dharma_peeth',
        array( sp_dharma_profile_post_type() ),
        array(
            'labels'            => array(
                'name'          => __( 'Peeths', 'sampreshan-child' ),
                'singular_name' => __( 'Peeth', 'sampreshan-child' ),
            ),
            'public'            => true,
            'show_admin_column' => true,
            'show_in_rest'      => true,
            'rewrite'           => array( 'slug' => 'peeth' ),
        )
    );

    register_post_type(
        sp_dharma_profile_post_type(),
        array(
            'labels' => array(
                'name'               => __( 'Dharma Profiles', 'sampreshan-child' ),
                'singular_name'      => __( 'Dharma Profile', 'sampreshan-child' ),
                'add_new_item'       => __( 'Add Dharma Profile', 'sampreshan-child' ),
                'edit_item'          => __( 'Edit Dharma Profile', 'sampreshan-child' ),
                'search_items'       => __( 'Search Dharma Profiles', 'sampreshan-child' ),
                'not_found'          => __( 'No Dharma Profiles found.', 'sampreshan-child' ),
                'all_items'          => __( 'Dharma Profiles', 'sampreshan-child' ),
            ),
            'public'       => true,
            'show_in_rest' => true,
            'has_archive'  => 'dharma-acharya',
            'rewrite'      => array( 'slug' => 'dharmacharya', 'with_front' => false ),
            'menu_icon'    => 'dashicons-groups',
            'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions' ),
            'map_meta_cap' => true,
        )
    );

    register_post_type(
        sp_dharma_update_post_type(),
        array(
            'labels' => array(
                'name'          => __( 'Dharma Updates', 'sampreshan-child' ),
                'singular_name' => __( 'Dharma Update', 'sampreshan-child' ),
                'add_new_item'  => __( 'Add Dharma Update', 'sampreshan-child' ),
                'edit_item'     => __( 'Edit Dharma Update', 'sampreshan-child' ),
            ),
            'public'       => true,
            'show_in_rest' => true,
            'has_archive'  => false,
            'rewrite'      => array( 'slug' => 'dharma-update', 'with_front' => false ),
            'menu_icon'    => 'dashicons-megaphone',
            'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'author', 'revisions' ),
            'map_meta_cap' => true,
        )
    );
}
add_action( 'init', 'sp_dharma_register_content_types', 5 );

function sp_dharma_legacy_profile_content( $slug, $fallback_content ) {
    $legacy = get_page_by_path( $slug, OBJECT, 'page' );
    if ( ! $legacy || '' === trim( wp_strip_all_tags( $legacy->post_content ) ) ) {
        return $fallback_content;
    }

    $content = $legacy->post_content;
    if ( function_exists( 'sp_clean_migrated_document_widget' ) ) {
        $content = sp_clean_migrated_document_widget( $content );
    }
    $content = preg_replace( '#https?://(?:www\.)?shivbodhtrust\.org/?#i', home_url( '/' ), $content );
    $content = str_ireplace( 'ShivBodh Trust', get_bloginfo( 'name' ), $content );
    $content = wp_kses_post( $content );

    return '' === trim( wp_strip_all_tags( $content ) ) ? $fallback_content : $content;
}

function sp_dharma_profile_image_url( $profile_id, $size = 'large' ) {
    static $cache = array();

    $profile_id = (int) $profile_id;
    $cache_key  = $profile_id . '|' . $size;
    if ( isset( $cache[ $cache_key ] ) ) {
        return $cache[ $cache_key ];
    }

    $thumbnail_id = get_post_thumbnail_id( $profile_id );
    if ( $thumbnail_id ) {
        $url = wp_get_attachment_image_url( $thumbnail_id, $size );
        if ( $url ) {
            return $cache[ $cache_key ] = $url;
        }
    }

    $filenames = (array) get_post_meta( $profile_id, '_sp_dharma_image_filenames', true );
    foreach ( $filenames as $filename ) {
        $filename = sanitize_file_name( $filename );
        if ( '' === $filename ) {
            continue;
        }
        $attachments = get_posts(
            array(
                'post_type'      => 'attachment',
                'post_status'    => 'inherit',
                'posts_per_page' => 1,
                'meta_query'     => array(
                    array(
                        'key'     => '_wp_attached_file',
                        'value'   => '/' . $filename,
                        'compare' => 'LIKE',
                    ),
                ),
                'fields'         => 'ids',
                'no_found_rows'  => true,
            )
        );
        if ( ! empty( $attachments ) ) {
            $url = wp_get_attachment_image_url( (int) $attachments[0], $size );
            if ( $url ) {
                return $cache[ $cache_key ] = $url;
            }
        }
    }

    return $cache[ $cache_key ] = '';
}

/**
 * Seed the four Peeth profiles from the legacy ShivBodh source. These are
 * regular WordPress records after creation and may be expanded by editors.
 */
function sp_dharma_seed_profiles() {
    $profiles = array(
        array(
            'slug'       => 'purvamnaya-govardhan-matha-puri',
            'title'      => 'Purvamnaya Govardhan Matha (Puri, Odisha)',
            'peeth'      => 'Govardhan Math, Puri',
            'peeth_slug' => 'govardhan-math-puri',
            'kind'       => 'peeth',
            'direction'  => 'East',
            'veda'       => 'Rig Veda',
            'mahavakya'  => 'Prajnanam Brahma',
            'legacy_slug'=> 'puri-peetha',
            'images'     => array( 'puri.jpg', 'puripeethama.png', 'puri-shivbodh.png', 'govardhana-peetha.png' ),
            'summary'    => 'Public information profile for Purvamnaya Govardhan Matha, Puri, Odisha, including verified updates and reference information.',
        ),
        array(
            'slug'       => 'paschimamnaya-dwarka-sharada-peetham',
            'title'      => 'Paschimamnaya Dwarka Sharada Peetham (Dwarka, Gujarat)',
            'peeth'      => 'Dwarka Sharada Peetham',
            'peeth_slug' => 'dwarka-sharada-peetham',
            'kind'       => 'peeth',
            'direction'  => 'West',
            'veda'       => 'Sama Veda',
            'mahavakya'  => 'Tat Tvam Asi',
            'legacy_slug'=> 'dwarka-peetha',
            'images'     => array( 'dwarka.peethams.png', 'dwarka.peethams.jpg', 'dwarka-shivbodh.png' ),
            'summary'    => 'Public information profile for Paschimamnaya Dwarka Sharada Peetham, Dwarka, Gujarat, including verified updates and reference information.',
        ),
        array(
            'slug'       => 'uttaraminaya-jyotirmath-peeth',
            'title'      => 'Uttaraminaya Jyotirmath Peeth (Joshimath, Uttarakhand)',
            'peeth'      => 'Jyotishpeeth',
            'peeth_slug' => 'jyotishpeeth',
            'kind'       => 'peeth',
            'direction'  => 'North',
            'veda'       => 'Atharva Veda',
            'mahavakya'  => 'Ayam Atma Brahma',
            'legacy_slug'=> 'jyotishpeetha',
            'images'     => array( 'jyotirmath.jpg', 'jyotirpeetham.png', 'jyotirmath-shivbodha.png' ),
            'summary'    => 'Public information profile for Uttaraminaya Jyotirmath Peeth, Joshimath, Uttarakhand, including verified updates and reference information.',
        ),
        array(
            'slug'       => 'dakshinamnaya-sri-sharada-peetham',
            'title'      => 'Dakshinamnaya Sri Sharada Peetham (Sringeri, Karnataka)',
            'peeth'      => 'Sringeri Sharada Peetham',
            'peeth_slug' => 'sringeri-sharada-peetham',
            'kind'       => 'peeth',
            'direction'  => 'South',
            'veda'       => 'Yajur Veda',
            'mahavakya'  => 'Aham Brahmasmi',
            'legacy_slug'=> 'sringeri-peetha',
            'images'     => array( 'sringeri.jpg', 'sringeri.peethams.png', 'sringeri-shivbodh.png', 'sringeri-peetha.png' ),
            'summary'    => 'Public information profile for Dakshinamnaya Sri Sharada Peetham, Sringeri, Karnataka, including verified updates and reference information.',
        ),
        array(
            'slug'       => 'kanchi-kamakoti-peetham',
            'title'      => 'Kanchi Kamakoti Peetham',
            'peeth'      => 'Kanchi Kamakoti Peetham',
            'peeth_slug' => 'kanchi-kamakoti-peetham',
            'kind'       => 'peeth',
            'direction'  => 'South-East',
            'veda'       => 'All Vedas',
            'mahavakya'  => 'Sarvam Khalvidam Brahma',
            'legacy_slug'=> 'kanchi-peetha',
            'images'     => array( 'kanchi-peetha.png', 'kanchi-shivbodh.png', 'kanchiaacharya.jpg' ),
            'summary'    => 'Public information profile for Kanchi Kamakoti Peetham, including verified updates and reference information.',
        ),
        array(
            'slug'      => 'swami-vidhushekhara-bharati',
            'title'     => 'Jagadguru Shankaracharya Sri Sri Vidhushekhara Bharati Sannidhanam',
            'peeth'     => 'Sringeri Sharada Peetham',
            'peeth_slug'=> 'sringeri-sharada-peetham',
            'kind'      => 'acharya',
            'direction' => 'South',
            'veda'      => 'Yajur Veda',
            'mahavakya' => 'Aham Brahmasmi',
            'images'    => array( 'sringerividhushekharaji.png', 'sringeri-aacharya.png', 'sringeri-shankaracharya-.png' ),
            'summary'   => 'Learn about the Sringeri Sharada Peetham tradition, its Vedic association, and verified public updates connected with this profile.',
        ),
        array(
            'slug'      => 'swami-sadanand-saraswati',
            'title'     => 'Jagadguru Shankaracharya Swami Sadanand Saraswat Ji',
            'peeth'     => 'Dwarka Sharada Peetham',
            'peeth_slug'=> 'dwarka-sharada-peetham',
            'kind'      => 'acharya',
            'direction' => 'West',
            'veda'      => 'Sama Veda',
            'mahavakya' => 'Tat Tvam Asi',
            'images'    => array( 'dwarka-aacharya.png', 'dwarka-shankaracharya.png', 'dwarka.peethams.png' ),
            'summary'   => 'Learn about the Dwarka Sharada Peetham tradition, its Vedic association, and verified public updates connected with this profile.',
        ),
        array(
            'slug'      => 'swami-avimukta',
            'title'     => 'Jagadguru Shankaracharya Swami Shri Avimukteshwaranand Saraswati Ji Maharaj',
            'peeth'     => 'Jyotishpeeth',
            'peeth_slug'=> 'jyotishpeeth',
            'kind'      => 'acharya',
            'direction' => 'North',
            'veda'      => 'Atharva Veda',
            'mahavakya' => 'Ayam Atma Brahma',
            'images'    => array( 'jyotirmathaswamiavimktaji.png', 'jyotirmatha-avimukta-sswami-peetha.png', 'govardhana-aacharya.png' ),
            'summary'   => 'Learn about the Jyotishpeeth tradition, its Vedic association, and verified public updates connected with this profile.',
        ),
        array(
            'slug'      => 'swami-nishchalananda-saraswati',
            'title'     => 'Jagadguru Shankaracharya Swami Shri Nischalananda Saraswati Ji Maharaj',
            'peeth'     => 'Govardhan Math, Puri',
            'peeth_slug'=> 'govardhan-math-puri',
            'kind'      => 'acharya',
            'direction' => 'East',
            'veda'      => 'Rig Veda',
            'mahavakya' => 'Prajnanam Brahma',
            'images'    => array( 'puri-peetha-shankaracharya-.png', 'govardhana-aacharya.png', 'puri.jpg' ),
            'summary'   => 'Learn about the Govardhan Math, Puri tradition, its Vedic association, and verified public updates connected with this profile.',
        ),
        array(
            'slug'      => 'sri-satya-chandrashekarendra-saraswathi-shankaracharya',
            'title'     => 'Sri Satya Chandrashekarendra Saraswathi Shankaracharya',
            'peeth'     => 'Kanchi Kamakoti Peetham',
            'peeth_slug'=> 'kanchi-kamakoti-peetham',
            'kind'      => 'acharya',
            'direction' => 'South-East',
            'veda'      => 'All Vedas',
            'mahavakya' => 'Sarvam Khalvidam Brahma',
            'images'    => array( 'kanchi-Sri-Satya-Chandrashekarendra-Saraswathi-Shankaracharya-swami-peetha.jpg', 'kanchi-aacharya.png', 'kanchiaacharya.jpg' ),
            'summary'   => 'Learn about the Kanchi Kamakoti Peetham tradition, its Vedic association, and verified public updates connected with this profile.',
        ),
    );

    foreach ( $profiles as $profile ) {
        $existing = get_page_by_path( $profile['slug'], OBJECT, sp_dharma_profile_post_type() );
        if ( ! $existing ) {
            $profile_id = wp_insert_post(
                array(
                    'post_type'    => sp_dharma_profile_post_type(),
                    'post_status'  => 'publish',
                    'post_name'    => $profile['slug'],
                    'post_title'   => $profile['title'],
                    'post_excerpt' => $profile['summary'],
                    'post_content' => sp_dharma_legacy_profile_content( isset( $profile['legacy_slug'] ) ? $profile['legacy_slug'] : $profile['slug'], sprintf(
                        '<p>%s</p><h2>%s</h2><p>%s</p><dl class="sp-dharma-facts"><div><dt>%s</dt><dd>%s</dd></div><div><dt>%s</dt><dd>%s</dd></div><div><dt>%s</dt><dd>%s</dd></div></dl>',
                        esc_html( $profile['summary'] ),
                        esc_html( $profile['peeth'] ),
                        esc_html__( 'This profile is a public information space on Sampreshan. Programme, activity, and news updates are published only after verification by the Sampreshan editorial team.', 'sampreshan-child' ),
                        esc_html__( 'Direction', 'sampreshan-child' ),
                        esc_html( $profile['direction'] ),
                        esc_html__( 'Veda', 'sampreshan-child' ),
                        esc_html( $profile['veda'] ),
                        esc_html__( 'Mahavakya', 'sampreshan-child' ),
                        esc_html( $profile['mahavakya'] )
                    ) ),
                ),
                true
            );
            if ( is_wp_error( $profile_id ) ) {
                continue;
            }
        } else {
            $profile_id = (int) $existing->ID;
            wp_update_post( array( 'ID' => $profile_id, 'post_title' => $profile['title'], 'post_excerpt' => $profile['summary'] ) );
        }

        wp_set_object_terms( $profile_id, $profile['peeth'], 'dharma_peeth', false );
        update_post_meta( $profile_id, '_sp_dharma_peeth', $profile['peeth'] );
        update_post_meta( $profile_id, '_sp_dharma_direction', $profile['direction'] );
        update_post_meta( $profile_id, '_sp_dharma_veda', $profile['veda'] );
        update_post_meta( $profile_id, '_sp_dharma_mahavakya', $profile['mahavakya'] );
        update_post_meta( $profile_id, '_sp_dharma_kind', $profile['kind'] );
        update_post_meta( $profile_id, '_sp_dharma_image_filenames', $profile['images'] );
    }

    update_option( 'sp_dharma_directory_version', SP_DHARMA_DIRECTORY_VERSION, false );
    flush_rewrite_rules( false );
}

function sp_dharma_maybe_seed_profiles() {
    if ( SP_DHARMA_DIRECTORY_VERSION !== get_option( 'sp_dharma_directory_version' ) ) {
        sp_dharma_seed_profiles();
    }
}
add_action( 'init', 'sp_dharma_maybe_seed_profiles', 20 );

function sp_dharma_directory_url() {
    return get_post_type_archive_link( sp_dharma_profile_post_type() );
}

function sp_dharma_followed_ids( $user_id = 0 ) {
    $user_id = $user_id ? (int) $user_id : get_current_user_id();
    if ( $user_id <= 0 ) {
        return array();
    }
    return array_values( array_unique( array_filter( array_map( 'absint', (array) get_user_meta( $user_id, '_sp_dharma_following', true ) ) ) ) );
}

function sp_dharma_is_following( $profile_id, $user_id = 0 ) {
    return in_array( (int) $profile_id, sp_dharma_followed_ids( $user_id ), true );
}

function sp_dharma_follow_count( $profile_id ) {
    return max( 0, (int) get_post_meta( (int) $profile_id, '_sp_dharma_follow_count', true ) );
}

function sp_dharma_follow_button( $profile_id, $classes = '' ) {
    $profile_id = (int) $profile_id;
    if ( $profile_id <= 0 ) {
        return;
    }

    if ( ! is_user_logged_in() ) {
        printf(
            '<a class="sp-dharma-follow %1$s" href="%2$s">%3$s</a>',
            esc_attr( $classes ),
            esc_url( wp_login_url( get_permalink( $profile_id ) ) ),
            esc_html__( 'Anusaran karein', 'sampreshan-child' )
        );
        return;
    }

    $following = sp_dharma_is_following( $profile_id );
    ?>
    <form class="sp-dharma-follow-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <input type="hidden" name="action" value="sp_toggle_dharma_follow" />
        <input type="hidden" name="profile_id" value="<?php echo esc_attr( $profile_id ); ?>" />
        <input type="hidden" name="redirect_to" value="<?php echo esc_url( add_query_arg( 'anusaran', $following ? 'removed' : 'added', get_permalink( $profile_id ) ) ); ?>" />
        <?php wp_nonce_field( 'sp_toggle_dharma_follow_' . $profile_id ); ?>
        <button class="sp-dharma-follow <?php echo esc_attr( $classes . ( $following ? ' is-following' : '' ) ); ?>" type="submit">
            <?php echo esc_html( $following ? __( 'Anusaran mein hai', 'sampreshan-child' ) : __( 'Anusaran karein', 'sampreshan-child' ) ); ?>
        </button>
    </form>
    <?php
}

function sp_dharma_toggle_follow() {
    if ( ! is_user_logged_in() ) {
        wp_safe_redirect( wp_login_url() );
        exit;
    }

    $profile_id = isset( $_POST['profile_id'] ) ? absint( $_POST['profile_id'] ) : 0;
    if ( $profile_id <= 0 || sp_dharma_profile_post_type() !== get_post_type( $profile_id ) ) {
        wp_die( esc_html__( 'The requested profile could not be found.', 'sampreshan-child' ), 404 );
    }
    check_admin_referer( 'sp_toggle_dharma_follow_' . $profile_id );

    $user_id   = get_current_user_id();
    $following = sp_dharma_followed_ids( $user_id );
    $key       = array_search( $profile_id, $following, true );
    if ( false === $key ) {
        $following[] = $profile_id;
        update_post_meta( $profile_id, '_sp_dharma_follow_count', sp_dharma_follow_count( $profile_id ) + 1 );
    } else {
        unset( $following[ $key ] );
        update_post_meta( $profile_id, '_sp_dharma_follow_count', max( 0, sp_dharma_follow_count( $profile_id ) - 1 ) );
    }
    update_user_meta( $user_id, '_sp_dharma_following', array_values( $following ) );

    $redirect = isset( $_POST['redirect_to'] ) ? wp_unslash( $_POST['redirect_to'] ) : get_permalink( $profile_id );
    wp_safe_redirect( wp_validate_redirect( $redirect, get_permalink( $profile_id ) ) );
    exit;
}
add_action( 'admin_post_sp_toggle_dharma_follow', 'sp_dharma_toggle_follow' );

function sp_dharma_update_profile_id( $update_id ) {
    return absint( get_post_meta( (int) $update_id, '_sp_dharma_profile_id', true ) );
}

function sp_dharma_updates_for_profiles( $profile_ids, $limit = 8 ) {
    $profile_ids = array_values( array_filter( array_map( 'absint', (array) $profile_ids ) ) );
    if ( empty( $profile_ids ) ) {
        return new WP_Query( array( 'post__in' => array( 0 ) ) );
    }
    return new WP_Query(
        array(
            'post_type'      => sp_dharma_update_post_type(),
            'post_status'    => 'publish',
            'posts_per_page' => absint( $limit ),
            'meta_query'     => array(
                array(
                    'key'     => '_sp_dharma_profile_id',
                    'value'   => $profile_ids,
                    'compare' => 'IN',
                ),
            ),
        )
    );
}

function sp_dharma_add_update_metabox() {
    add_meta_box(
        'sp-dharma-update-profile',
        __( 'Associated Acharya or Peeth profile', 'sampreshan-child' ),
        'sp_dharma_render_update_metabox',
        sp_dharma_update_post_type(),
        'side',
        'high'
    );
}
add_action( 'add_meta_boxes', 'sp_dharma_add_update_metabox' );

function sp_dharma_render_update_metabox( $post ) {
    wp_nonce_field( 'sp_dharma_update_profile', 'sp_dharma_update_profile_nonce' );
    $selected = sp_dharma_update_profile_id( $post->ID );
    $profiles = get_posts(
        array(
            'post_type'      => sp_dharma_profile_post_type(),
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'title',
            'order'          => 'ASC',
        )
    );
    ?>
    <p><label for="sp_dharma_profile_id"><?php esc_html_e( 'Publish this update to followers of:', 'sampreshan-child' ); ?></label></p>
    <select class="widefat" id="sp_dharma_profile_id" name="sp_dharma_profile_id">
        <option value=""><?php esc_html_e( 'Select a profile', 'sampreshan-child' ); ?></option>
        <?php foreach ( $profiles as $profile ) : ?>
            <option value="<?php echo esc_attr( $profile->ID ); ?>" <?php selected( $selected, $profile->ID ); ?>><?php echo esc_html( $profile->post_title ); ?></option>
        <?php endforeach; ?>
    </select>
    <p class="description"><?php esc_html_e( 'Only verified programme, activity, and news information should be published here.', 'sampreshan-child' ); ?></p>
    <?php
}

function sp_dharma_save_update_profile( $post_id, $post ) {
    if ( sp_dharma_update_post_type() !== $post->post_type || wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
        return;
    }
    if ( ! isset( $_POST['sp_dharma_update_profile_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['sp_dharma_update_profile_nonce'] ) ), 'sp_dharma_update_profile' ) ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    $profile_id = isset( $_POST['sp_dharma_profile_id'] ) ? absint( $_POST['sp_dharma_profile_id'] ) : 0;
    if ( $profile_id > 0 && sp_dharma_profile_post_type() === get_post_type( $profile_id ) ) {
        update_post_meta( $post_id, '_sp_dharma_profile_id', $profile_id );
    } else {
        delete_post_meta( $post_id, '_sp_dharma_profile_id' );
    }
}
add_action( 'save_post_' . 'dharma_update', 'sp_dharma_save_update_profile', 10, 2 );

function sp_dharma_notify_followers_on_publish( $new_status, $old_status, $post ) {
    if ( sp_dharma_update_post_type() !== $post->post_type || 'publish' !== $new_status || 'publish' === $old_status || get_post_meta( $post->ID, '_sp_dharma_update_notified', true ) ) {
        return;
    }
    $profile_id = sp_dharma_update_profile_id( $post->ID );
    if ( $profile_id <= 0 ) {
        return;
    }

    $followers = get_users(
        array(
            'meta_key'     => '_sp_dharma_following',
            'meta_value'   => '"' . $profile_id . '"',
            'meta_compare' => 'LIKE',
            'fields'       => 'ID',
        )
    );
    foreach ( $followers as $user_id ) {
        sp_notify_user( (int) $user_id, 'dharma_update', (int) $post->ID, $profile_id );
    }
    update_post_meta( $post->ID, '_sp_dharma_update_notified', current_time( 'mysql' ) );
}
add_action( 'transition_post_status', 'sp_dharma_notify_followers_on_publish', 10, 3 );

function sp_dharma_format_notification( $action, $item_id, $secondary_item_id, $total_items, $format, $component_action_name = '', $component_name = '' ) {
    if ( 'sampreshan' !== $component_name || 'dharma_update' !== $component_action_name ) {
        return $action;
    }
    $update  = get_post( (int) $item_id );
    $profile = get_post( (int) $secondary_item_id );
    if ( ! $update || sp_dharma_update_post_type() !== $update->post_type || ! $profile || sp_dharma_profile_post_type() !== $profile->post_type ) {
        return $action;
    }
    $text = sprintf( __( 'New update from %1$s: %2$s', 'sampreshan-child' ), $profile->post_title, $update->post_title );
    if ( 'string' === $format ) {
        return '<a href="' . esc_url( get_permalink( $update ) ) . '">' . esc_html( $text ) . '</a>';
    }
    return array( 'text' => $text, 'link' => get_permalink( $update ) );
}
add_filter( 'bp_notifications_get_notifications_for_user', 'sp_dharma_format_notification', 20, 7 );

function sp_dharma_legacy_redirects() {
    if ( ! is_page() ) {
        return;
    }
    $slug = get_post_field( 'post_name', get_queried_object_id() );
    $profile_map = array(
        'swami-vidhushekhara-bharati' => 'swami-vidhushekhara-bharati',
        'swami-sadanand-saraswati'    => 'swami-sadanand-saraswati',
        'swami-avimukta'              => 'swami-avimukta',
        'swami-nishchalananda-saraswati' => 'swami-nishchalananda-saraswati',
    );
    if ( isset( $profile_map[ $slug ] ) ) {
        $profile = get_page_by_path( $profile_map[ $slug ], OBJECT, sp_dharma_profile_post_type() );
        if ( $profile ) {
            wp_safe_redirect( get_permalink( $profile ), 301 );
            exit;
        }
    }

    $peeth_map = array(
        'sringeri-peetha' => 'sringeri-sharada-peetham',
        'dwarka-peetha'   => 'dwarka-sharada-peetham',
        'jyotishpeetha'   => 'jyotishpeeth',
        'puri-peetha'     => 'govardhan-math-puri',
    );
    if ( isset( $peeth_map[ $slug ] ) ) {
        $term = get_term_by( 'slug', $peeth_map[ $slug ], 'dharma_peeth' );
        if ( $term && ! is_wp_error( $term ) ) {
            wp_safe_redirect( get_term_link( $term ), 301 );
            exit;
        }
    }
}
add_action( 'template_redirect', 'sp_dharma_legacy_redirects', 1 );

function sp_dharma_dashboard_section() {
    if ( ! is_user_logged_in() ) {
        return;
    }
    $followed = sp_dharma_followed_ids();
    $profiles = empty( $followed ) ? array() : get_posts(
        array(
            'post_type'      => sp_dharma_profile_post_type(),
            'post__in'       => $followed,
            'posts_per_page' => -1,
            'orderby'        => 'post__in',
        )
    );
    $updates = sp_dharma_updates_for_profiles( $followed, 5 );
    ?>
    <section class="sp-dharma-dashboard sp-dash-card sp-dash-card--pad" aria-labelledby="sp-dharma-dashboard-title">
        <div class="sp-dharma-section-head">
            <div>
                <p class="sp-dash-eyebrow"><?php esc_html_e( 'Anusaran', 'sampreshan-child' ); ?></p>
                <h2 id="sp-dharma-dashboard-title" class="sp-dash-card__title"><?php esc_html_e( 'Your Dharma updates', 'sampreshan-child' ); ?></h2>
            </div>
            <a class="sp-text-link" href="<?php echo esc_url( sp_dharma_directory_url() ); ?>"><?php esc_html_e( 'Discover Acharyas & Peeths', 'sampreshan-child' ); ?> &rarr;</a>
        </div>
        <?php if ( ! empty( $profiles ) ) : ?>
            <div class="sp-dharma-following-list">
                <?php foreach ( $profiles as $profile ) : ?>
                    <a href="<?php echo esc_url( get_permalink( $profile ) ); ?>"><?php echo esc_html( $profile->post_title ); ?></a>
                <?php endforeach; ?>
            </div>
            <?php if ( $updates->have_posts() ) : ?>
                <div class="sp-dharma-update-list">
                    <?php while ( $updates->have_posts() ) : $updates->the_post(); ?>
                        <?php $profile = get_post( sp_dharma_update_profile_id( get_the_ID() ) ); ?>
                        <article>
                            <p><?php echo esc_html( $profile ? $profile->post_title : __( 'Dharma update', 'sampreshan-child' ) ); ?> · <?php echo esc_html( get_the_date() ); ?></p>
                            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                        </article>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
            <?php else : ?>
                <p class="sp-dharma-muted"><?php esc_html_e( 'Verified updates from your followed profiles will appear here.', 'sampreshan-child' ); ?></p>
            <?php endif; ?>
        <?php else : ?>
            <p class="sp-dharma-muted"><?php esc_html_e( 'Choose an Acharya or Peeth to receive verified programme, activity, and news updates in this dashboard.', 'sampreshan-child' ); ?></p>
        <?php endif; ?>
    </section>
    <?php
}
