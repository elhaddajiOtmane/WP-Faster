<?php
/**
 * Main WP Faster functions
 *
 * @since 2.2.0
 */

/**
 * Plugin installation.
 *
 * Add options and remove unused/old ones.
 */
function wpfaster _install() {
    add_option( 'wpfaster _zen', 0 );

    add_option( 'wpfaster _version_parameter', 0 );
    add_option( 'wpfaster _emoji', 0 );
    add_option( 'wpfaster _canonical', 0 );
    add_option( 'wpfaster _author_archive', 0 );

    add_option( 'wpfaster _normalize_scheme', 0 );

    add_option( 'wpfaster _head_cleanup', 0 );
    add_option( 'wpfaster _rss_links', 0 );

    add_option( 'wpfaster _xmlrpc', 0 );
    add_option( 'wpfaster _comment_cookies', 0 );
    add_option( 'wpfaster _embed', 0 );

    add_option( 'wpfaster _http_headers', 0 );

    add_option( 'wpfaster _comment_html', 0 );
    add_option( 'wpfaster _comment_reply', 0 );

    add_option( 'lhf_speedfactor_results', 30 );

    lhf_tidy();
}

/**
 * Delete folder and contents
 *
 */
function lhf_delete_tree( $dir ) {
    $files = glob( $dir . '*', GLOB_MARK );

    foreach ( $files as $file ) {
        if ( substr( $file, -1 ) === '/' ) {
            lhf_delete_tree( $file );
        } else {
            unlink( $file );
        }
    }

    rmdir( $dir );
}

function lhf_tidy() {
    // Delete old options
    delete_option( 'wpfaster _smilies' );
    delete_option( 'wpfaster _canonical_sf' );
    delete_option( 'wpfaster _rsd_links' );
    delete_option( 'wpfaster _wlw_links' );
    delete_option( 'wpfaster _shortlink' );
    delete_option( 'wpfaster _generator' );
    delete_option( 'wpfaster _xmlrpc_safe' );
    delete_option( 'wpfaster _hsts_simple' );
    delete_option( 'wpfaster _nofollow_author' );
    delete_option( 'wpfaster _backup' );
    delete_option( 'wpfaster _backup' );
    delete_option( 'wpfaster _remove_pings' );

    delete_option( 'wpfaster _clean_style_tag' );
    delete_option( 'wpfaster _clean_script_tag' );
    delete_option( 'wpfaster _clean_css_attr' );
    delete_option( 'wpfaster _opensans_frontend' );
    delete_option( 'wpfaster _attribution' );
    delete_option( 'wpfaster _gravatar_alt' );

    delete_option( 'wpfaster _adjacent' );
    delete_option( 'wpfaster _genericons_frontend' );
    delete_option( 'wpfaster _content_conversion' );
    delete_option( 'wpfaster _fancybox' );

    delete_option( 'wpfaster _bad_queries' );
    delete_option( 'wpfaster _jqueryui' );
    delete_option( 'wpfaster _nofollow_comment' );

    delete_option( 'wpfaster _script_html5shiv' );
    delete_option( 'wpfaster _style_masonry' );
    delete_option( 'wpfaster _script_modernizr' );
    delete_option( 'wpfaster _script_jquery' );
    delete_option( 'wpfaster _style_normalize' );
    delete_option( 'wpfaster _style_pure' );
    delete_option( 'wpfaster _style_dashicons' );
    delete_option( 'wpfaster _style_entypo' );
    delete_option( 'wpfaster _style_fa' );

    delete_option( 'wpfaster _gravatar_cache' );
    delete_option( 'wpgc_dir' );
    delete_option( 'wpgc_url' );
    delete_option( 'wpgc_exp' );
    delete_option( 'wpgc_cc' );
    delete_option( 'wpgc_dir' );
    delete_option( 'wpfaster _compress_scripts' );
    delete_option( 'wpfaster _transients' );
    delete_option( 'wpfaster _devicepx' );

    delete_option( 'wpfaster _disable_author_archives' );
    delete_option( 'wpfaster _http_headers' );

    delete_option( 'wpfaster _recent_comments_css' );
    delete_option( 'wpfaster _remove_gallery_style' );
    delete_option( 'wpfaster _remove_srcset' );

    delete_option( 'wpfaster _clean_attributes' );
    delete_option( 'wpfaster _scripts_to_footer' );

    delete_option( 'wpfaster _jquery_migrate' );
    delete_option( 'wpfaster _taxonomy_archive' );

    delete_option( 'lhf_error_reporting' );
    delete_option( 'lhf_error_log' );
    delete_option( 'lhf_error_log_size' );
    delete_option( 'lhf_error_monitoring' );
    delete_option( 'lhf_error_monitoring_dashboard' );
    delete_option( 'wpfaster _compress_html' );

    delete_option( 'wpfaster _widget_pages' );
    delete_option( 'wpfaster _widget_calendar' );
    delete_option( 'wpfaster _widget_archives' );
    delete_option( 'wpfaster _widget_links' );
    delete_option( 'wpfaster _widget_meta' );
    delete_option( 'wpfaster _widget_search' );
    delete_option( 'wpfaster _widget_text' );
    delete_option( 'wpfaster _widget_categories' );
    delete_option( 'wpfaster _widget_posts' );
    delete_option( 'wpfaster _widget_comments' );
    delete_option( 'wpfaster _widget_rss' );
    delete_option( 'wpfaster _widget_tag' );

    delete_option( 'wpfaster _widget_html' );
    delete_option( 'wpfaster _widget_media' );
    delete_option( 'wpfaster _widget_tag' );

    delete_option( 'wpfaster _disable_rest_api' );
    delete_option( 'wpfaster _hsts' );

    delete_option( 'wpfaster _dashicons_frontend' );

    delete_option( 'wpfaster _js_lazy_loading' );
    delete_option( 'wpfaster _theme_support_srcset' );

    delete_option( 'wpfaster _no_webp' );

    delete_option( 'wpfaster _plugin_notifications' );
    delete_option( 'wpfaster _theme_notifications' );
    delete_option( 'wpfaster _core_autoupdates' );
    delete_option( 'wpfaster _plugin_autoupdates' );

    delete_option( 'wpfaster _blacklist_log' );
    delete_option( 'lhf_speedfactor_wpfaster _api' );

    if ( (int) get_option( 'lhf_queries_enable' ) !== 1 ) {
        wp_clear_scheduled_hook( 'lt_clear_max' );
    }

    if ( file_exists( WP_CONTENT_DIR . '/WPFaster -cache' ) ) {
        lhf_delete_tree( WP_CONTENT_DIR . '/WPFaster -cache' );
    }

    if ( file_exists( WP_CONTENT_DIR . '/uploads/WPFaster ' ) ) {
        lhf_delete_tree( WP_CONTENT_DIR . '/uploads/WPFaster ' );
    }

    if ( file_exists( WP_CONTENT_DIR . '/WPFaster -blacklist.log' ) ) {
        unlink( WP_CONTENT_DIR . '/uploads/WPFaster ' );
    }

    global $wpdb;

    $table_name = $wpdb->prefix . 'lhf_sf_cwv';

    // Drop the CWV table from the database
    $wpdb->query( "DROP TABLE IF EXISTS $table_name" );
}

function lhf_apply_preset( $mode = 'safe' ) {
    if ( $mode === 'none' ) {
        update_option( 'wpfaster _zen', 0 );
        update_option( 'wpfaster _prefetch', 0 );
        update_option( 'wpfaster _prefetch_throttle', 0 );
        update_option( 'wpfaster _version_parameter', 0 );
        update_option( 'wpfaster _emoji', 0 );
        update_option( 'wpfaster _canonical', 0 );
        update_option( 'wpfaster _author_archive', 0 );
        update_option( 'wpfaster _prepend_attachment', 0 );
        update_option( 'wpfaster _normalize_scheme', 0 );
        update_option( 'wpfaster _head_cleanup', 0 );
        update_option( 'wpfaster _rss_links', 0 );
        update_option( 'wpfaster _comment_cookies', 0 );
        update_option( 'wpfaster _embed', 0 );
        update_option( 'wpfaster _mediaelement', 0 );
        update_option( 'wpfaster _heartbeat', 0 );

        update_option( 'lhf_minify_html_active', 0 );
        update_option( 'lhf_minify_javascript', 0 );
        update_option( 'lhf_minify_html_comments', 0 );
        update_option( 'lhf_minify_html_xhtml', 0 );
        update_option( 'lhf_minify_html_utf8', 0 );

        update_option( 'wpfaster _xmlrpc', 0 );
        update_option( 'wpfaster _brute_force', 0 );

        update_option( 'wpfaster _no_big_images', 0 );
        update_option( 'wpfaster _no_intermediate_images', 0 );
        update_option( 'lhfm_lazy_loading', 0 );
        update_option( 'lhfm_responsive', 0 );
        update_option( 'lhfm_compression_level', 82 );

        update_option( 'wpfaster _comment_html', 0 );
        update_option( 'wpfaster _comment_reply', 0 );
        update_option( 'wpfaster _no_lazy_loading', 0 );
        update_option( 'wpfaster _no_jetpack_css', 0 );
        update_option( 'wpfaster _no_classic_css', 0 );
    } elseif ( $mode === 'safe' ) {
        update_option( 'wpfaster _zen', 0 );
        update_option( 'wpfaster _prefetch', 0 );
        update_option( 'wpfaster _prefetch_throttle', 150 );
        update_option( 'wpfaster _version_parameter', 0 );
        update_option( 'wpfaster _emoji', 1 );
        update_option( 'wpfaster _canonical', 1 );
        update_option( 'wpfaster _author_archive', 1 );
        update_option( 'wpfaster _prepend_attachment', 0 );
        update_option( 'wpfaster _normalize_scheme', 0 );
        update_option( 'wpfaster _head_cleanup', 1 );
        update_option( 'wpfaster _rss_links', 1 );
        update_option( 'wpfaster _comment_cookies', 0 );
        update_option( 'wpfaster _embed', 0 );
        update_option( 'wpfaster _mediaelement', 0 );
        update_option( 'wpfaster _heartbeat', 1 );

        update_option( 'lhf_minify_html_active', 1 );
        update_option( 'lhf_minify_javascript', 0 );
        update_option( 'lhf_minify_html_comments', 0 );
        update_option( 'lhf_minify_html_xhtml', 1 );
        update_option( 'lhf_minify_html_utf8', 0 );

        update_option( 'wpfaster _xmlrpc', 0 );
        update_option( 'wpfaster _brute_force', 0 );

        update_option( 'wpfaster _no_big_images', 0 );
        update_option( 'wpfaster _no_intermediate_images', 0 );
        update_option( 'lhfm_lazy_loading', 1 );
        update_option( 'lhfm_responsive', 1 );
        update_option( 'lhfm_compression_level', 82 );

        update_option( 'wpfaster _comment_html', 0 );
        update_option( 'wpfaster _comment_reply', 0 );
        update_option( 'wpfaster _no_lazy_loading', 0 );
        update_option( 'wpfaster _no_jetpack_css', 0 );
        update_option( 'wpfaster _no_classic_css', 1 );
    } elseif ( $mode === 'advanced' ) {
        update_option( 'wpfaster _zen', 0 );
        update_option( 'wpfaster _prefetch', 2 );
        update_option( 'wpfaster _prefetch_throttle', 150 );
        update_option( 'wpfaster _version_parameter', 0 );
        update_option( 'wpfaster _emoji', 1 );
        update_option( 'wpfaster _canonical', 1 );
        update_option( 'wpfaster _author_archive', 1 );
        update_option( 'wpfaster _prepend_attachment', 0 );
        update_option( 'wpfaster _normalize_scheme', 0 );
        update_option( 'wpfaster _head_cleanup', 1 );
        update_option( 'wpfaster _rss_links', 1 );
        update_option( 'wpfaster _comment_cookies', 0 );
        update_option( 'wpfaster _embed', 1 );
        update_option( 'wpfaster _mediaelement', 1 );
        update_option( 'wpfaster _heartbeat', 1 );

        update_option( 'lhf_minify_html_active', 1 );
        update_option( 'lhf_minify_javascript', 1 );
        update_option( 'lhf_minify_html_comments', 1 );
        update_option( 'lhf_minify_html_xhtml', 1 );
        update_option( 'lhf_minify_html_utf8', 1 );

        update_option( 'wpfaster _xmlrpc', 1 );
        update_option( 'wpfaster _brute_force', 0 );

        update_option( 'wpfaster _no_big_images', 1 );
        update_option( 'wpfaster _no_intermediate_images', 1 );
        update_option( 'lhfm_lazy_loading', 1 );
        update_option( 'lhfm_responsive', 1 );
        update_option( 'lhfm_compression_level', 82 );

        update_option( 'wpfaster _comment_html', 0 );
        update_option( 'wpfaster _comment_reply', 0 );
        update_option( 'wpfaster _no_lazy_loading', 0 );
        update_option( 'wpfaster _no_jetpack_css', 0 );
        update_option( 'wpfaster _no_classic_css', 1 );
    }
}

/**
 * Move scripts to footer.
 *
 * Remove all enqueued scripts and styles and enqueue them
 * in the theme's footer. It only works with properly
 * enqueued functions.
 */
function lhf_scripts_to_footer() {
    remove_action( 'wp_head', 'wp_print_scripts' );
    remove_action( 'wp_head', 'wp_print_head_scripts', 9 );
    remove_action( 'wp_head', 'wp_enqueue_scripts', 1 );

    add_action( 'wp_footer', 'wp_print_scripts', 5 );
    add_action( 'wp_footer', 'wp_enqueue_scripts', 5 );
    add_action( 'wp_footer', 'wp_print_head_scripts', 5 );
}

/**
 * Remove version parameter.
 *
 * Remove version parameter ('ver', 'v', 'sv') from scripts and styles.
 *
 * @return string
 */
function lhf_remove_script_version( $src ) {
    return esc_url( remove_query_arg( [ 'ver', 'v', 'sv' ], $src ) );
}

/**
 * Remove version parameter.
 *
 * Remove version parameter ('ver', 'v', 'sv') from scripts and styles.
 */
function lhf_disable_emojis() {
    if ( (int) get_option( 'wpfaster _emoji' ) === 1 ) {
        remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
        remove_action( 'wp_print_styles', 'print_emoji_styles' );
        remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
        remove_action( 'admin_print_styles', 'print_emoji_styles' );
        remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
        remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
        remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

        remove_action( 'init', 'smilies_init', 5 );

        remove_filter( 'comment_text', 'make_clickable', 9 );
        remove_filter( 'the_content', 'convert_bbcode' );
        remove_filter( 'the_content', 'convert_gmcode' );
        remove_filter( 'the_content', 'convert_smilies' );
        remove_filter( 'the_content', 'convert_chars' );

        //add_filter( 'option_use_smilies', '__return_false' );

        if ( ! is_admin() ) {
            add_filter( 'emoji_svg_url', '__return_false' );
        }
    }
}

function lhf_disable_author_archive() {
    // If we are on author archive
    if ( is_author() ) {
        global $wp_query;

        $wp_query->set_404();
    } else {
        redirect_canonical();
    }
}

function lhf_get_message() {
    global $wpdb, $wp_version;

    $all_pass = true;

    $php_min_version_check = version_compare( wpfaster _CHECK_PHP_MIN_VERSION, PHP_VERSION, '<=' );
    $php_rec_version_check = version_compare( wpfaster _CHECK_PHP_REC_VERSION, PHP_VERSION, '<=' );

    $wp_min_version_check = version_compare( wpfaster _CHECK_WP_MIN_VERSION, $wp_version, '<=' );
    $wp_rec_version_check = version_compare( wpfaster _CHECK_WP_REC_VERSION, $wp_version, '<=' );

    // Memory usage
    $memory = [];

    $memory['limit'] = (int) ini_get( 'memory_limit' );
    $memory['usage'] = function_exists( 'memory_get_peak_usage' ) ? round( memory_get_peak_usage( true ) / 1024 / 1024, 2 ) : 0;

    // Images
    $check  = '<img src="' . wpfaster _PLUGIN_URL . '/assets/icons/check.svg" alt="" width="16" height="16">';
    $x      = '<img src="' . wpfaster _PLUGIN_URL . '/assets/icons/x.svg" alt="" width="16" height="16">';
    $preset = '<img src="' . wpfaster _PLUGIN_URL . '/assets/icons/preset.svg" alt="" width="16" height="16">';
    //

    if ( ! empty( $memory['usage'] ) && ! empty( $memory['limit'] ) ) {
        $memory['percent'] = round( $memory['usage'] / $memory['limit'] * 100, 0 );
        $memory['color']   = 'font-weight:normal;';

        if ( $memory['percent'] > 75 ) {
            $memory['color'] = 'font-weight:bold;color:#E66F00';
        }

        if ( $memory['percent'] > 90 ) {
            $memory['color'] = 'font-weight:bold;color:red';
        }
    }

    $server_ip_address = ( ! empty( $_SERVER['SERVER_ADDR'] ) ? $_SERVER['SERVER_ADDR'] : '' );

    if ( $server_ip_address === '' ) {
        $server_ip_address = ( ! empty( $_SERVER['LOCAL_ADDR'] ) ? $_SERVER['LOCAL_ADDR'] : '' );
    }
    //

    $message = '<section class="lhf-grid lhf-grid-4 sf-WPFaster -results">
        <div class="lhf-grid-item">
            <span class="lhf-sf-metric-name">Server Platform</span>
            <p>
                <span class="lhf-sf-metric-value">' . sanitize_text_field( $_SERVER['SERVER_SOFTWARE'] ) . '</span>
                <small>' . ( PHP_INT_SIZE * 8 ) . 'bit</small>
                <br>IP <code>' . $server_ip_address . '</code> (' . gethostname() . ')
                <br>' . OPENSSL_VERSION_TEXT . '
            </p>
            <p>';

            if ( $_SERVER['HTTP_ACCEPT_ENCODING'] === 'gzip' || function_exists( 'ob_gzhandler' ) || ini_get( 'zlib.output_compression' ) ) {
                $message .= '<code class="lhfr">' . $check . ' gzip</code>';
            } else {
                $message .= '<code class="lhfw">' . $x . ' gzip</code>';
            }

            $is_protocol = wp_get_server_protocol();

            if ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ) {
                $message .= '<code class="lhfr">' . $check . ' HTTPS</code><code class="lhfr">' . $check . ' ' . $is_protocol . '</code></p>';
            } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' || ! empty( $_SERVER['HTTP_X_FORWARDED_SSL'] ) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on' ) {
                $message .= '<code class="lhfw">' . $x . ' HTTP</code>';
            }

            $message .= '</p>
        </div>
        <div class="lhf-grid-item">
            <span class="lhf-sf-metric-name">Server Stack</span>
            <p>
                <b>PHP</b> <span class="lhf-sf-metric-value"><code>' . PHP_VERSION . '</code></span> with <b>cURL</b> <code>' . curl_version()['version'] . '</code>
                <br><b>MySQL</b> <span class="lhf-sf-metric-value"><code>' . $wpdb->db_version() . '</code></span> (' . $wpdb->db_server_info() . ')
            </p>
        </div>
        <div class="lhf-grid-item">
            <span class="lhf-sf-metric-name">Memory</span>
            <p>
                WordPress/server memory and peak usage
                <br><span class="lhf-sf-metric-value"><code>' . WP_MEMORY_LIMIT . '</code></span>
                <br>Peak usage: ' . $memory['usage'] . '/' . $memory['limit'] . 'MB (<span style="' . $memory['color'] . '">' . $memory['percent'] . '%</span>)
            </p>
        </div>
        <div class="lhf-grid-item">
            <span class="lhf-sf-metric-name">Optimization</span>
            <p>
                WP Faster optimization preset
                <br><span class="lhf-sf-metric-value"><code class="lhfn">' . $preset . ' ' . ( (string) get_option( 'wpfaster _preset' ) !== '' ? get_option( 'wpfaster _preset' ) : 'custom' ) . '</code></span>
            </p>
        </div>
        <div class="lhf-grid-item">
            <span class="lhf-sf-metric-name">WP Faster Beacon</span>
            <p>
                Track your real <abbr title="Time to First Byte">TTFB</abbr>
                <br>';

                if ( ! file_exists( trailingslashit( ABSPATH ) . 'beacon.html' ) ) {
                    $message .= '<br>' . $x . ' Beacon file does not exist
                    <br><a href="' . admin_url( 'admin.php?page=WPFaster &tab=lhf_speedfactor' ) . '&action=create-config" class="button button-secondary">Attempt Beacon file creation</a>';
                } else {
                    $message .= '<br>' . $check . ' Beacon file found';
                }

            $message .= '</p>
        </div>
        <div class="lhf-grid-item">
            <span class="lhf-sf-metric-name">SpeedFactor Tracking</span>
            <p>
                WordPress performance, assets and connection speed
                <br>';

                if ( (string) get_option( 'lhf_speedfactor_schedule' ) !== '' ) {
                    $message .= '<br>' . $check . ' SpeedFactor is active';
                } else {
                    $message .= '<br>' . $x . ' SpeedFactor is not active
                    <br><a href="' . admin_url( 'admin.php?page=WPFaster &tab=lhf_speedfactor' ) . '" class="button button-secondary">Enable SpeedFactor</a>';
                }

            $message .= '</p>
        </div>
        <div class="lhf-grid-item">
            <span class="lhf-sf-metric-name">Registration Spam</span>
            <p>
                <span class="lhf-sf-metric-value">' . number_format( get_option( 'wpfaster _spam_registration_count', 0 ) ) . '</span> 
                (' . number_format( get_option( 'wpfaster _isspammy_count', 0 ) ) . ')
            </p>
            <p>
                Total spam registrations blocked on your site.
                <br><small style="color:var(--color-grey)">This module protects your site from direct or injected spam registration.</small>
            </p>
        </div>
        <div class="lhf-grid-item">
            <span class="lhf-sf-metric-name">Brute Force Protection</span>
            <p>
                <span class="lhf-sf-metric-value">' . number_format( get_option( 'wpfaster _failed_login_count', 0 ) ) . '</span>
            </p>
            <p>
                Total malicious attacks blocked on your site.
                <br><small style="color:var(--color-grey)">This module protects your site from traditional and distributed brute force login attacks.</small>
            </p>
        </div>
    </section>';

    $success = '';
    $warning = '';
    $error   = '';

    if ( function_exists( 'classicpress_version' ) ) {
        // ClassicPress check
        global $cp_version;

        if ( version_compare( $cp_version, wpfaster _CHECK_CP_REC_VERSION, '<' ) ) {
            $error   .= '<p><b>' . __( 'Warning:', 'WPFaster ' ) . '</b> WP Faster recommends ClassicPress ' . wpfaster _CHECK_CP_REC_VERSION . ' or higher.</p>';
            $all_pass = false;
        }
    } else {
        // WordPress check
        if ( ! $wp_min_version_check ) {
            $error   .= '<p><b>' . __( 'Warning:', 'WPFaster ' ) . '</b> WP Faster requires WordPress ' . wpfaster _CHECK_WP_MIN_VERSION . ' or higher.</p>';
            $all_pass = false;
        }
        if ( ! $wp_rec_version_check ) {
            $error   .= '<p><b>' . __( 'Warning:', 'WPFaster ' ) . '</b> WP Faster recommends WordPress ' . wpfaster _CHECK_WP_REC_VERSION . ' or higher.</p>';
            $all_pass = false;
        }
    }

    if ( ! $php_min_version_check ) {
        $error   .= '<p><b>' . __( 'Warning:', 'WPFaster ' ) . '</b> WordPress 4.3+ requires PHP version ' . wpfaster _CHECK_PHP_MIN_VERSION . ' or higher.</p>';
        $all_pass = false;
    }
    if ( version_compare( $wpdb->db_version(), '5.5.3', '<' ) ) {
        $warning .= '<p><b>' . __( 'Error:', 'WPFaster ' ) . '</b> WordPress <code>utf8mb4</code> support requires MySQL version 5.5.3 or higher.</p>';
        $all_pass = false;
    }

    if ( ! $php_rec_version_check ) {
        $warning .= '<p><strong>' . __( 'Warning:', 'WPFaster ' ) . '</strong> For performance and security reasons, we recommend running PHP version ' . wpfaster _CHECK_PHP_REC_VERSION . ' or higher.</p>';
        $all_pass = false;
    }

    if ( $error ) {
        $message .= '<div class="lhf-notice lhf-notice-error">' . $error . '</div>';
    }
    if ( $warning ) {
        $message .= '<div class="lhf-notice lhf-notice-warning">' . $warning . '</div>';
    }
    if ( $success ) {
        $message .= '<div class="lhf-notice lhf-notice-success">' . $success . '</div>';
    }

    return $message;
}



function lhf_src_scheme( $url ) {
    if ( is_admin() ) {
        return $url;
    }

    return str_replace( [ 'http:', 'https:' ], '', $url );
}



function lhf_remove_xmlrpc_methods( $methods ) {
    unset( $methods['pingback.ping'] );
    unset( $methods['pingback.extensions.getPingbacks'] );

    unset( $methods['system.multicall'] );
    unset( $methods['system.listMethods'] );
    unset( $methods['system.getCapabilities'] );

    unset( $methods['wp.getUsersBlogs'] );

    return $methods;
}
function lhf_remove_x_pingback( $headers ) {
    unset( $headers['X-Pingback'] );

    return $headers;
}
function lhf_remove_pingback_url( $output, $show ) {
    if ( (string) $show === 'pingback_url' ) {
        $output = '';
    }

    return $output;
}



// No self pings
function lhf_no_self_ping( $links ) {
    $home = get_option( 'home' );
    foreach ( $links as $l => $link ) {
        if ( 0 === strpos( $link, $home ) ) {
            unset( $links[ $l ] );
        }
    }
}



/**
 * Disable embeds on init.
 *
 * - Removes the needed query vars.
 * - Disables oEmbed discovery.
 * - Completely removes the related JavaScript.
 *
 */
function lhf_disable_embeds_init() {
    if ( (int) get_option( 'wpfaster _embed' ) === 1 ) {
        global $wp;

        $wp->public_query_vars = array_diff(
            $wp->public_query_vars,
            [
                'embed',
            ]
        );

        add_filter( 'embed_oembed_discover', '__return_false' );
        remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );
        remove_action( 'rest_api_init', 'wp_oembed_register_route' );
        remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
        remove_action( 'wp_head', 'wp_oembed_add_host_js' );

        remove_filter( 'pre_oembed_result', 'wp_filter_pre_oembed_result', 10 );

        if ( ! is_admin() ) {
            wp_deregister_script( 'wp-embed' );
        }
    }
}

function lhf_capital_p_bangit() {
    remove_filter( 'the_title', 'capital_P_dangit', 11 );
    remove_filter( 'the_content', 'capital_P_dangit', 11 );
    remove_filter( 'comment_text', 'capital_P_dangit', 31 );
}
function lhf_taxonomies() {
    global $wp_taxonomies;

    unset( $wp_taxonomies['link_category'] );
    unset( $wp_taxonomies['post_format'] );
}
function lhf_admin_bar() {
    global $wp_admin_bar;

    if ( ! is_admin_bar_showing() ) {
        return;
    }

    $wp_admin_bar->remove_menu( 'wp-logo' );
    $wp_admin_bar->remove_menu( 'comments' );
    $wp_admin_bar->remove_menu( 'my-account' );
    $wp_admin_bar->remove_menu( 'appearance' );
    $wp_admin_bar->remove_menu( 'new-content' );
    $wp_admin_bar->remove_menu( 'my-account-with-avatar' );
}
function lhf_dashboard_widgets() {
    remove_meta_box( 'dashboard_browser_nag', 'dashboard', 'normal' );
    remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
    remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
    remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );

    remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
    remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
    remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
    remove_meta_box( 'dashboard_secondary', 'dashboard', 'side' );
}
function lhf_note() {
    $screen = get_current_screen();

    if ( $screen->id === 'settings_page_wpfaster ' ) {
        print '<div id="message" class="updated notice is-dismissible"><p>' . __( 'WP Faster Zen mode is active.', 'WPFaster ' ) . '</p></div>';
    }
}



function wpfaster _disable_wp_media_elements_js() {
    wp_deregister_script( 'wp-mediaelement' );
    wp_deregister_style( 'wp-mediaelement' );
}



function wpfaster _custom_footer() {
    if ( (int) get_option( 'wpfaster _thank_you' ) === 1 ) {
        echo '<p class="has-text-align-center has-small-font-size has-powered-by-WPFaster"><a href="https://otmane.tech/wordpress-plugins/WPFaster/" rel="external follow noopener">Pagespeed Optimization</a> by <a href="https://otmane.tech/wordpress-plugins/WPFaster /" rel="external follow noopener">WPFaster</a>.</p>';
    }
}

add_action( 'wp_footer', 'wpfaster _custom_footer' );



if ( (int) get_option( 'wpfaster _no_app_passwords' ) === 1 ) {
    add_filter( 'wp_is_application_passwords_available', '__return_false' );
}



if ( (int) get_option( 'wpfaster _no_big_images' ) === 1 ) {
    add_filter( 'big_image_size_threshold', '__return_false' );
}


function wpfaster _remove_default_image_sizes( $sizes ) {
    unset( $sizes['medium_large'] );
    unset( $sizes['1536x1536'] );
    unset( $sizes['2048x2048'] );

    return $sizes;
}

function wpfaster _filter_image_sizes() {
    foreach ( get_intermediate_image_sizes() as $size ) {
        if ( in_array( $size, [ '1536x1536', '2048x2048', 'medium_large' ] ) ) {
            remove_image_size( $size );
        }
    }
}

if ( (int) get_option( 'wpfaster _no_intermediate_images' ) === 1 ) {
    add_action( 'init', 'wpfaster _filter_image_sizes' );
    add_filter( 'intermediate_image_sizes_advanced', 'wpfaster _remove_default_image_sizes' );
}



/**
 * Add 'loading="lazy" to all images.
 *
 * @param string $content The content to check.
 * @return string
 */
function lhfm_lazy_load_image( $content ) {
    return (string) preg_replace(
        '/<img /',
        '<img loading="lazy" ',
        $content
    );
}

/**
 * Add 'loading="lazy" to all Iframes.
 *
 * @param string $content The content to check.
 * @return string
 */
function lhfm_lazy_load_iframe( $content ) {
    return (string) preg_replace(
        '/<iframe /',
        '<iframe loading="lazy" ',
        $content
    );
}

if ( (int) get_option( 'lhfm_lazy_loading' ) ) {
    add_filter( 'the_content', 'lhfm_lazy_load_image', 100 );
    add_filter( 'the_content', 'lhfm_lazy_load_iframe', 100 );
    add_filter( 'get_avatar', 'lhfm_lazy_load_image', 100 );
}



if ( (int) get_option( 'lhfm_compression_level' ) > 0 ) {
    add_filter(
        'jpeg_quality',
        function ( $arg ) {
            return (int) get_option( 'lhfm_compression_level' );
        }
    );
}



// Slow down the default heartbeat
if ( (int) get_option( 'wpfaster _heartbeat' ) === 1 ) {
    add_filter(
        'heartbeat_settings',
        function ( $settings ) {
            // 60 seconds
            $settings['interval'] = 60;

            return $settings;
        }
    );
}



if ( (int) get_option( 'wpfaster _disable_rest' ) === 1 ) {
    add_filter(
        'rest_authentication_errors',
        function( $result ) {
            // If a previous authentication check was applied, pass that result along without modification.
            if ( true === $result || is_wp_error( $result ) ) {
                return $result;
            }

            // No authentication has been performed yet.
            // Return an error if user is not logged in.
            if ( ! is_user_logged_in() ) {
                return new WP_Error(
                    'rest_not_logged_in',
                    __( 'You are not currently logged in.' ),
                    [ 'status' => 401 ]
                );
            }

            // Our custom authentication check should have no effect on logged-in requests
            return $result;
        }
    );
}

if ( (int) get_option( 'wpfaster _disable_user_enumeration' ) === 1 ) {
    if ( ! is_admin() ) {
        if ( preg_match( '/author=([0-9]*)/i', $_SERVER['QUERY_STRING'] ) ) {
            die();
        }

        add_filter( 'redirect_canonical', 'lhf_check_enum', 10, 2 );
    }

    function lhf_check_enum( $redirect, $request ) {
        if ( preg_match( '/\?author=([0-9]*)(\/*)/i', $request ) ) {
            die();
        } else {
            return $redirect;
        }
    }
}




function lhf_admin_notice() {
    global $pagenow;

    if ( 'options-general.php' === $pagenow && ( isset( $_GET['page'] ) && 'WPFaster ' === $_GET['page'] ) ) {
        /*
        ?>
        <div class="notice notice-error is-dismissible">
            <p><?php _e( '<b>Notice</b>: WP Faster is removing the Core Web Vitals and the CrUX features.', 'WPFaster ' ); ?></p>
            <p><?php _e( '<a href="https://otmane.tech/WPFaster -is-removing-the-core-web-vitals-and-crux-features/" rel="external noopener" target="_blank">Read the announcement here.</a>', 'WPFaster ' ); ?></p>
        </div>
        <?php
        /**/
    }
}

add_action( 'admin_notices', 'lhf_admin_notice' );
