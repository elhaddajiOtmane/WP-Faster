<h2><?php _e( 'CMS Tweaks', 'WPFaster ' ); ?></h2>

<?php
if ( isset( $_POST['lhf_force_blog_cleanup'] ) ) {
    global $wpdb;

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    $table_name_posts    = $wpdb->prefix . 'posts';
    $table_name_options  = $wpdb->prefix . 'options';
    $table_name_comments = $wpdb->prefix . 'comments';

    $wpdb->query( "UPDATE $table_name_posts SET ping_status = 'closed'" );
    $wpdb->query( "UPDATE $table_name_posts SET comment_status = 'closed'" );

    $wpdb->query( "UPDATE $table_name_options SET default_ping_status = 'closed'" );
    $wpdb->query( "UPDATE $table_name_options SET default_comment_status = 'closed'" );

    $wpdb->query( "DELETE FROM $table_name_comments WHERE comment_type = 'trackback'" );

    update_option( 'default_pingback_flag', 0 );
    update_option( 'default_ping_status', 0 );
    update_option( 'default_comment_status', 0 );
    update_option( 'require_name_email', 1 );
    update_option( 'comment_registration', 1 );
    update_option( 'close_comments_for_old_posts', 1 );
    update_option( 'close_comments_days_old', 0 );
    update_option( 'show_comments_cookies_opt_in', 0 );
    update_option( 'thread_comments', 0 );
    update_option( 'page_comments', 0 );
    update_option( 'comments_notify', 0 );
    update_option( 'moderation_notify', 0 );
    update_option( 'comment_moderation', 0 );
    update_option( 'comment_previously_approved', 0 );
    update_option( 'show_avatars', 0 );
    update_option( 'ping_sites', '' );

    echo '<div class="updated notice is-dismissible"><p>Blogging/commenting features optimized!</p></div>';
}
?>

<p>These actions will optimize your WordPress settings based on predefined conditions for a <b>Business Website</b> vs <b>Blog</b>. Read more about <a href="https://otmane.tech/how-to-optimize-wordpress-native-settings-for-performance/" rel="external noopener" target="_blank">how to optimize WordPress’ native settings for maximum performance</a>.</p>

<table class="form-table">
    <tbody>
        <tr>
            <th scope="row"><label>Optimization</label></th>
            <td>
                <p>
                    <input type="submit" name="lhf_force_blog_cleanup" class="button button-secondary" value="Clean up and disable blogging/commenting features">
                    <br><small>These options are used for blogs and comments and are, usually, unused for business websites with no comments functionality. This is a one-off operation.</small>
                    <br><small style="color:#d93025">This option will disable comments, pingbacks and trackbacks and remove all trackbacks.</small>
                </p>

                <p><span class="dashicons dashicons-saved"></span> Make sure you set the <a href="<?php echo admin_url( 'options-permalink.php' ); ?>">Permalinks</a> structure to <b>Post name</b> (i.e. <code>/%postname%/</code>).</p>
                <p><span class="dashicons dashicons-saved"></span> Make sure you create and/or set a <a href="<?php echo admin_url( 'options-privacy.php' ); ?>">Privacy Policy</a> page.</p>
            </td>
        </tr>
    </tbody>
</table>
