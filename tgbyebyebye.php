<?php
/*
Plugin Name: Bye Bye Bye Lines
Plugin URI:  http://www.nsync.com/
Description: Display a byline at the end of a post, making it a Bye bye bye line.
Version:     1.0
Author:      'N Sync
Author URI:  http://www.nsync.com/
License:     GPLv2 or later
*/

/**
 * Set up the metabox.
 *
 * @param  string    $post_type    The post type.
 * @param  object    $post         The current post object.
 * @return void
 */
function nync_call_meta_box( $post_type, $post ) {
    add_meta_box(
        'byebyebye_line',
        __( 'Bye Bye Bye Line', 'byebyebye_lines' ),
        'nync_display_meta_box',
        'post',
        'side',
        'high'
    );
}

add_action( 'add_meta_boxes', 'nync_call_meta_box', 10, 2 );

/**
 * Display the HTML for the metabox.
 *
 * @param  object    $post    The current post object
 * @param  array     $args    Additional arguments for the metabox.
 * @return void
 */
function nync_display_meta_box( $post, $args ) {
    wp_nonce_field( 'bye-line-save', 'nsync_bye_line_noncename' );
    $nsync_display_val = nsync_get_bye_line_meta( $post->ID );
?>
    <p>
        <label for="byeline">
            <?php _e( 'Bye Bye Bye Line', 'byebyebye_lines' ); ?>:&nbsp;
        </label>
        <input type="text" class="widefat" name="byeline" value="<?php echo $nsync_display_val; ?>" />
        <em>
            <?php _e( 'HTML is not allowed', 'byebyebye_lines' ); ?>
        </em>
    </p>
<?php
}

function nsync_get_bye_line_meta( $post_id ){
    $get_bye_line_val = get_post_meta( $post_id, 'byebyebye-line', true );
    if ( ! empty( $get_bye_line_val ) ){
        $byebyemetavalue = get_post_meta( $post_id, 'byebyebye-line', true );
        return esc_html( $byebyemetavalue );
    }
}

/**
 * Save the metabox.
 *
 * @param  int       $post_id    The ID for the current post.
 * @param  object    $post       The current post object.
 */
function nync_save_meta_box( $post_id, $post ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
        return;
    }

    if ( ! isset( $_POST['byeline'] ) ) {
        return;
    }

    if ( ! wp_verify_nonce( $_POST['nsync_bye_line_noncename'], 'bye-line-save' ) ){
        return;
    }

    $byeline = $_POST['byeline'];
    update_post_meta( $post_id, 'byebyebye-line', $byeline );
}

add_action( 'save_post', 'nync_save_meta_box', 10, 2 );

/**
 * Append the Bye Bye Bye Line to the content.
 *
 * @param  string    $content    The original content.
 * @return string                The altered content.
 */
function nync_print_byebyebye_line( $content ) {
    $byebyebye_line = get_post_meta( get_the_ID(), 'byebyebye-line', true );
    return $content . esc_html( $byebyebye_line );
}

add_filter( 'the_content', 'nync_print_byebyebye_line' );