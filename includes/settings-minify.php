<?php
if ( isset( $_POST['lhf_minify_html_submit_hidden'] ) && (int) $_POST['lhf_minify_html_submit_hidden'] === 1 ) {
    update_option( 'lhf_minify_html_active', (int) sanitize_text_field( $_POST['lhf_minify_html_active'] ) );
    update_option( 'lhf_minify_javascript', (int) sanitize_text_field( $_POST['lhf_minify_javascript'] ) );
    update_option( 'lhf_minify_html_comments', (int) sanitize_text_field( $_POST['lhf_minify_html_comments'] ) );
    update_option( 'lhf_minify_html_xhtml', (int) sanitize_text_field( $_POST['lhf_minify_html_xhtml'] ) );
    update_option( 'lhf_minify_html_utf8', (int) sanitize_text_field( $_POST['minify_html_utf8'] ) );

    echo '<div class="updated notice is-dismissible"><p>' . __( 'Options updated successfully!', 'WPFaster ' ) . '</p></div>';
}
?>

<form name="form1" method="post" action="">
    <h2><?php _e( 'Minify Settings', 'WPFaster ' ); ?></h2>

    <input type="hidden" name="lhf_minify_html_submit_hidden" value="1">

    <table class="form-table">
        <tbody>
            <tr>
                <th><label><?php _e( 'Minify', 'WPFaster ' ); ?></label></th>
                <td>
                    <p>
                        <input class="thin-ui-toggle" type="checkbox" name="lhf_minify_html_active" value="1" <?php checked( 1, (int) get_option( 'lhf_minify_html_active' ) ); ?>> <label><?php _e( 'Minify HTML', 'WPFaster ' ); ?></label>
                        <br><small><?php _e( 'Enable or disable HTML minification', 'WPFaster ' ); ?></small>
                    </p>
                    <p>
                        <input class="thin-ui-toggle" type="checkbox" name="lhf_minify_javascript" value="1" <?php checked( 1, (int) get_option( 'lhf_minify_javascript' ) ); ?>> <label><?php _e( 'Minify inline JavaScript', 'WPFaster ' ); ?></label>
                        <br><small><?php _e( 'Default is "Yes"', 'WPFaster ' ); ?></small>
                    </p>
                </td>
            </tr>
            <tr>
                <th><label><?php _e( 'Code Cleanup', 'WPFaster ' ); ?></label></th>
                <td>
                    <p>
                        <input class="thin-ui-toggle" type="checkbox" name="lhf_minify_html_comments" value="1" <?php checked( 1, (int) get_option( 'lhf_minify_html_comments' ) ); ?>> <label><?php _e( 'Remove HTML, JavaScript and CSS comments', 'WPFaster ' ); ?></label>
                        <br><small><?php _e( 'Default is "Yes"', 'WPFaster ' ); ?></small>
                    </p>
                    <p>
                        <input class="thin-ui-toggle" type="checkbox" name="lhf_minify_html_xhtml" value="1" <?php checked( 1, (int) get_option( 'lhf_minify_html_xhtml' ) ); ?>> <span class="lhfn">use if needed</span> <label><?php _e( 'Remove (X)HTML closing tags from HTML5 void elements', 'WPFaster ' ); ?></label>
                        <br><small><?php _e( 'Default is "Yes"', 'WPFaster ' ); ?></small>
                    </p>
                    <p>
                        <input class="thin-ui-toggle" type="checkbox" name="lhf_minify_html_utf8" value="1" <?php checked( 1, (int) get_option( 'lhf_minify_html_utf8' ) ); ?>> <span class="lhfn">use if needed</span> <label><?php _e( 'Support multi-byte UTF-8 encoding (if you see odd characters)', 'WPFaster ' ); ?></label>
                        <br><small><?php _e( 'Default is "No"', 'WPFaster ' ); ?></small>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>

    <p class="submit">
        <input type="submit" name="submit" class="button button-primary" value="<?php esc_attr_e( 'Save Changes', 'WPFaster ' ); ?>">
    </p>
</form>
