<?php
/**
 * Petitions: Admin meta boxes + custom columns
 *
 * Adds the meta box on the petition edit screen with:
 *   - signature goal (number)
 *   - current signature count (read-only, auto-computed)
 *   - champion (user picker)
 *   - petition status (active / closed / featured)
 *   - deadline (date)
 * Also adds custom columns to the petitions list table.
 *
 * @package SampreShan_Child
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! function_exists( 'sp_petition_meta_box' ) ) {
    function sp_petition_meta_box() {
        add_meta_box(
            'sp_petition_details',
            __( 'Petition Details', 'sampreshan-child' ),
            'sp_petition_meta_box_render',
            'petition',
            'side',
            'high'
        );
    }
    add_action( 'add_meta_boxes', 'sp_petition_meta_box' );

    function sp_petition_meta_box_render( $post ) {
        wp_nonce_field( 'sp_save_petition', 'sp_petition_nonce' );

        $goal      = (int) get_post_meta( $post->ID, 'sampreshan_goal', true );
        $champion  = (int) get_post_meta( $post->ID, 'sampreshan_champion', true );
        $status    = get_post_meta( $post->ID, 'sampreshan_status', true ) ?: 'active';
        $deadline  = get_post_meta( $post->ID, 'sampreshan_deadline', true );
        $featured  = (int) get_post_meta( $post->ID, 'sampreshan_featured', true );

        // Live count from the signatures table
        $count = function_exists( 'sp_petition_signature_count' )
            ? (int) sp_petition_signature_count( $post->ID )
            : (int) get_post_meta( $post->ID, 'sampreshan_signatures', true );
        ?>
        <p>
            <label for="sp_petition_goal"><strong><?php esc_html_e( 'Signature goal', 'sampreshan-child' ); ?></strong></label><br>
            <input type="number" min="1" id="sp_petition_goal" name="sampreshan_goal" value="<?php echo esc_attr( $goal ?: 25000 ); ?>" style="width:100%" />
        </p>

        <p>
            <label><strong><?php esc_html_e( 'Current signatures', 'sampreshan-child' ); ?></strong></label><br>
            <input type="text" value="<?php echo esc_attr( number_format_i18n( $count ) ); ?>" readonly style="width:100%;background:#f6f6f6" />
            <small style="color:#666"><?php esc_html_e( 'Auto-computed from the signatures table.', 'sampreshan-child' ); ?></small>
        </p>

        <p>
            <label for="sp_petition_champion"><strong><?php esc_html_e( 'Champion (organizer)', 'sampreshan-child' ); ?></strong></label><br>
            <?php
            wp_dropdown_users( array(
                'name'             => 'sampreshan_champion',
                'id'               => 'sp_petition_champion',
                'selected'         => $champion ?: $post->post_author,
                'include_selected' => true,
                'show_option_none' => __( '— None —', 'sampreshan-child' ),
                'who'              => '',
            ) );
            ?>
        </p>

        <p>
            <label for="sp_petition_status"><strong><?php esc_html_e( 'Status', 'sampreshan-child' ); ?></strong></label><br>
            <select id="sp_petition_status" name="sampreshan_status" style="width:100%">
                <option value="active"   <?php selected( $status, 'active' ); ?>><?php esc_html_e( 'Active — accepting signatures', 'sampreshan-child' ); ?></option>
                <option value="victory"  <?php selected( $status, 'victory' ); ?>><?php esc_html_e( 'Victory — won, read-only', 'sampreshan-child' ); ?></option>
                <option value="closed"   <?php selected( $status, 'closed' ); ?>><?php esc_html_e( 'Closed — read-only', 'sampreshan-child' ); ?></option>
                <option value="draft"    <?php selected( $status, 'draft' ); ?>><?php esc_html_e( 'Draft — hidden', 'sampreshan-child' ); ?></option>
            </select>
        </p>

        <p>
            <label for="sp_petition_deadline"><strong><?php esc_html_e( 'Deadline', 'sampreshan-child' ); ?></strong></label><br>
            <input type="date" id="sp_petition_deadline" name="sampreshan_deadline" value="<?php echo esc_attr( $deadline ); ?>" style="width:100%" />
        </p>

        <p>
            <label>
                <input type="checkbox" name="sampreshan_featured" value="1" <?php checked( $featured, 1 ); ?> />
                <?php esc_html_e( 'Mark as Featured', 'sampreshan-child' ); ?>
            </label>
        </p>
        <?php
    }

    /**
     * Persist the meta box fields on save.
     */
    function sp_save_petition_meta( $post_id ) {
        if ( ! isset( $_POST['sp_petition_nonce'] ) ) { return; }
        if ( ! wp_verify_nonce( $_POST['sp_petition_nonce'], 'sp_save_petition' ) ) { return; }
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
        if ( ! current_user_can( 'edit_petition', $post_id ) ) { return; }

        $fields = array(
            'sampreshan_goal'      => 'absint',
            'sampreshan_champion'  => 'absint',
            'sampreshan_status'    => 'sanitize_text_field',
            'sampreshan_deadline'  => 'sanitize_text_field',
        );
        foreach ( $fields as $key => $cb ) {
            if ( isset( $_POST[ $key ] ) ) {
                update_post_meta( $post_id, $key, $cb( wp_unslash( $_POST[ $key ] ) ) );
            }
        }
        update_post_meta( $post_id, 'sampreshan_featured', isset( $_POST['sampreshan_featured'] ) ? 1 : 0 );
    }
    add_action( 'save_post_petition', 'sp_save_petition_meta' );
}

if ( ! function_exists( 'sp_petition_admin_columns' ) ) {
    /**
     * Custom columns on the petitions list table.
     */
    function sp_petition_admin_columns( $columns ) {
        $new = array();
        foreach ( $columns as $key => $label ) {
            $new[ $key ] = $label;
            if ( 'title' === $key ) {
                $new['sp_signatures'] = __( 'Signatures', 'sampreshan-child' );
                $new['sp_goal']       = __( 'Goal', 'sampreshan-child' );
                $new['sp_status']     = __( 'Status', 'sampreshan-child' );
                $new['sp_reports']    = __( 'Reports', 'sampreshan-child' );
            }
        }
        return $new;
    }
    add_filter( 'manage_petition_posts_columns', 'sp_petition_admin_columns' );

    function sp_petition_admin_column_render( $column, $post_id ) {
        switch ( $column ) {
            case 'sp_signatures':
                $count = function_exists( 'sp_petition_signature_count' ) ? sp_petition_signature_count( $post_id ) : (int) get_post_meta( $post_id, 'sampreshan_signatures', true );
                echo esc_html( number_format_i18n( $count ) );
                break;
            case 'sp_goal':
                $goal = (int) get_post_meta( $post_id, 'sampreshan_goal', true );
                echo esc_html( number_format_i18n( $goal ?: 25000 ) );
                break;
            case 'sp_status':
                $status = get_post_meta( $post_id, 'sampreshan_status', true ) ?: 'active';
                $colors = array(
                    'active'  => '#2D7A4D',
                    'victory' => '#B45309',
                    'closed'  => '#5C5C5C',
                    'draft'   => '#D4A017',
                );
                $c = $colors[ $status ] ?? '#5C5C5C';
                printf( '<span style="color:%s;font-weight:600">%s</span>', esc_attr( $c ), esc_html( ucfirst( $status ) ) );
                break;
            case 'sp_reports':
                $n = (int) get_post_meta( $post_id, 'sp_report_count', true );
                if ( $n > 0 ) {
                    printf( '<strong style="color:#B91C1C">%s</strong>', esc_html( number_format_i18n( $n ) ) );
                } else {
                    echo '—';
                }
                break;
        }
    }
    add_action( 'manage_petition_posts_custom_column', 'sp_petition_admin_column_render', 10, 2 );

    function sp_petition_admin_sortable_columns( $columns ) {
        $columns['sp_signatures'] = 'sp_signatures';
        return $columns;
    }
    add_filter( 'manage_edit-petition_sortable_columns', 'sp_petition_admin_sortable_columns' );
}
