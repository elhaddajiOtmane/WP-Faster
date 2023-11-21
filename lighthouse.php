<?php
/*
Plugin Name: WP Faster
Plugin URI: https://github.com/elhaddajiOtmane/WP-Faster
Description: WP Faster is a performance tuning plugin, removing lots of default WordPress behaviour, such as filters, actions, injected code, native code and third-party actions.
Version: 3.9.9
Author: Elhaddaji Otmane
Author URI: https://otmane.tech/

WP Faster
Copyright (C) 2023-2023 Elhaddaji Otmane (elhaddajiotmane@gmail.com)

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

define( 'wpfaster _VERSION', '3.9.9' );
define( 'wpfaster _PLUGIN_URL', WP_PLUGIN_URL . '/' . dirname( plugin_basename( __FILE__ ) ) );
define( 'wpfaster _PLUGIN_PATH', WP_PLUGIN_DIR . '/' . dirname( plugin_basename( __FILE__ ) ) );
define( 'wpfaster _PLUGIN_FILE_PATH', WP_PLUGIN_DIR . '/' . plugin_basename( __FILE__ ) );

define( 'wpfaster _CHECK_PHP_MIN_VERSION', '7.4' );
define( 'wpfaster _CHECK_PHP_REC_VERSION', '8.2' );
define( 'wpfaster _CHECK_WP_MIN_VERSION', '6.3' );
define( 'wpfaster _CHECK_WP_REC_VERSION', '6.4.1' );

define( 'wpfaster _CHECK_CP_REC_VERSION', '1.7.1' );

define( 'CORE_UPGRADE_SKIP_NEW_BUNDLED', true );

if ( ! defined( 'CURL_HTTP_VERSION_2_0' ) ) {
    define( 'CURL_HTTP_VERSION_2_0', 3 );
}

include wpfaster _PLUGIN_PATH . '/includes/updater.php';

if ( (int) get_option( 'wpfaster _brute_force' ) === 1 ) {
    include 'includes/lh-security.php';
}

include_once wpfaster _PLUGIN_PATH . '/includes/functions.php';
include_once wpfaster _PLUGIN_PATH . '/includes/functions-queries.php';
include_once wpfaster _PLUGIN_PATH . '/includes/minify2.php';
include_once wpfaster _PLUGIN_PATH . '/includes/registration.php';

if ( is_admin() ) {
    include_once wpfaster _PLUGIN_PATH . '/includes/settings.php';
}

include_once wpfaster _PLUGIN_PATH . '/includes/metrics.php';

// SpeedFactor
include_once wpfaster _PLUGIN_PATH . '/speedfactor/get-score-curl.php';
include_once wpfaster _PLUGIN_PATH . '/speedfactor/get-score-curl-beacon.php';
include_once wpfaster _PLUGIN_PATH . '/speedfactor/get-site-assets.php';



register_activation_hook( __FILE__, 'wpfaster _install' );



/**
 * Hook into wp_enqueue_scripts and enqueue/dequeue or register/deregister as required
 *
 * @return void
 */
function wpfaster _enqueue_scripts() {
    // WP Faster Instant Loading
    if ( (int) get_option( 'wpfaster _prefetch' ) === 1 ) {
        wp_enqueue_script( 'lhf-prefetch', plugins_url( '/assets/prerender.min.js', __FILE__ ), [], wpfaster _VERSION, true );
    } elseif ( (int) get_option( 'wpfaster _prefetch' ) === 2 ) {
        wp_enqueue_script( 'lhf-prefetch', plugins_url( '/assets/prefetch.min.js', __FILE__ ), [], wpfaster _VERSION, true );
    }

    if ( (int) get_option( 'wpfaster _prefetch' ) > 0 ) {
        wp_localize_script(
            'lhf-prefetch',
            'lhf_ajax_var',
            [
                'prefetch_throttle' => (int) get_option( 'wpfaster _prefetch_throttle' ),
            ]
        );
    }

    // Dequeue mediaelement.js script
    if ( (int) get_option( 'wpfaster _mediaelement' ) === 1 ) {
        wpfaster _disable_wp_media_elements_js();
    }

    // Dequeue comment-reply.js script
    if ( (int) get_option( 'wpfaster _comment_reply' ) === 1 ) {
        wp_dequeue_script( 'comment-reply' );
    }
}

add_action( 'wp_enqueue_scripts', 'wpfaster _enqueue_scripts', PHP_INT_MAX );



// Dequeue jetpack.css stylesheet
if ( (int) get_option( 'wpfaster _no_jetpack_css' ) === 1 ) {
    add_filter( 'jetpack_sharing_counts', '__return_false', 99 );
    add_filter( 'jetpack_implode_frontend_css', '__return_false', 99 );
}

// Dequeue classic-themes.min.css style
if ( (int) get_option( 'wpfaster _no_classic_css' ) === 1 ) {
    add_action(
        'wp_enqueue_scripts',
        function() {
            wp_dequeue_style( 'classic-theme-styles' );
        },
        20
    );
}



/**
 * Hook into plugins_loaded and filter as required
 *
 * @return void
 */
function wpfaster _plugins_loaded() {
    // Plugin initialization and textdomain setup
    load_plugin_textdomain( 'WPFaster ', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

add_action( 'plugins_loaded', 'wpfaster _plugins_loaded' );



function wpfaster _on_init() {
    lhf_declutter_head();
    lhf_disable_emojis();
    lhf_disable_embeds_init();
}

add_action( 'init', 'wpfaster _on_init', 3 );



add_action( 'admin_menu', 'wpfaster _add_option_page' );
add_action( 'admin_enqueue_scripts', 'lhf_load_admin_style' );
add_action( 'after_setup_theme', 'wpfaster _setup' );
add_action( 'pre_ping', 'lhf_no_self_ping' );

if ( (int) get_option( 'lhfm_responsive' ) === 1 ) {
    add_filter( 'the_content', 'wp_make_content_images_responsive' );

    // Re-enable responsive images or srcset if the theme disabled it
    add_action( 'after_setup_theme', 'lighhouse_reenable_srcset', 11 );

} elseif ( (int) get_option( 'lhfm_responsive' ) === 2 ) {
    add_filter( 'wp_calculate_image_srcset', 'wpfaster _disable_srcset' );
    add_filter( 'max_srcset_image_width', 'wpfaster _remove_max_srcset_image_width' );
}


function lighhouse_reenable_srcset() {
    remove_filter( 'wp_calculate_image_srcset', '__return_false' );
}
function wpfaster _disable_srcset( $sources ) {
    return false;
}
function wpfaster _remove_max_srcset_image_width( $max_width ) {
    return false;
}


if ( (int) get_option( 'wpfaster _zen' ) === 1 ) {
    add_action( 'wp_before_admin_bar_render', 'lhf_admin_bar' );
    add_action( 'wp_dashboard_setup', 'lhf_dashboard_widgets' );

    lhf_capital_p_bangit();
    lhf_taxonomies();
}

if ( (int) get_option( 'wpfaster _no_lazy_loading' ) === 1 ) {
    add_filter( 'wp_lazy_loading_enabled', '__return_false' );
}

function wpfaster _add_option_page() {
    add_options_page( 'WPFaster ', 'WPFaster ', 'manage_options', 'WPFaster ', 'wpfaster _options_page' );

    if ( (int) get_option( 'wpfaster _remove_custom_fields_metabox' ) === 1 ) {
        /**
         * Remove Custom Fields metabox from post editor because it uses a very slow meta_key sort query.
         * On sites with large wp_postmeta tables, it is very slow.
         */
        foreach ( get_post_types( '', 'names' ) as $post_type ) {
            remove_meta_box( 'postcustom', $post_type, 'normal' );
        }
    }
}

function lhf_load_admin_style() {
    wp_enqueue_style( 'WPFaster ', wpfaster _PLUGIN_URL . '/assets/WPFaster .css', false, wpfaster _VERSION );
    wp_enqueue_style( 'thin-ui', wpfaster _PLUGIN_URL . '/assets/thin-ui.css', false, '2.1.1' );
    wp_enqueue_style( 'WPFaster -speedfactor', wpfaster _PLUGIN_URL . '/assets/WPFaster -speedfactor.css', false, wpfaster _VERSION );

    // SpeedFactor
    wp_enqueue_script( 'sparkline', plugins_url( '/assets/js/sparkline.min.js', __FILE__ ), [], '1.0.0', true );

    wp_register_script( 'lhf-chartjs', plugins_url( '/assets/js/chart.umd.min.js', __FILE__ ), [], '4.3.2', true );

    wp_register_script( 'sf-charts', plugins_url( '/assets/js/charts.js', __FILE__ ), [ 'lhf-chartjs', 'sparkline' ], wpfaster _VERSION, true );

    // Synchro
    wp_register_style( 'lhf-segment', plugins_url( '/assets/js/segment/segment.css', __FILE__ ), [], '1.0.0' );
    wp_register_script( 'lhf-segment', plugins_url( '/assets/js/segment/segment.js', __FILE__ ), [], '1.0.0', true );
    //

    wp_enqueue_script( 'sf-init', plugins_url( '/assets/js/init.js', __FILE__ ), [], wpfaster _VERSION, true );

    wp_localize_script(
        'sf-init',
        'sfAjaxVar',
        [
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
        ]
    );
}

function wpfaster _setup() {
    if ( (int) get_option( 'wpfaster _version_parameter' ) === 1 ) {
        add_filter( 'script_loader_src', 'lhf_remove_script_version', 15, 1 );
        add_filter( 'style_loader_src', 'lhf_remove_script_version', 15, 1 );
    }

    if ( (int) get_option( 'wpfaster _head_cleanup' ) === 1 ) {
        remove_action( 'wp_head', 'rsd_link' );
        remove_action( 'wp_head', 'wlwmanifest_link' );
        remove_action( 'wp_head', 'wp_generator' );
        remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );
        remove_action( 'template_redirect', 'wp_shortlink_header', 11, 0 );
    }

    if ( (int) get_option( 'wpfaster _rss_links' ) === 1 ) {
        remove_action( 'wp_head', 'feed_links_extra', 3 );
        remove_action( 'wp_head', 'feed_links', 2 );
    }

    if ( (int) get_option( 'wpfaster _author_archive' ) === 1 ) {
        add_action( 'template_redirect', 'lhf_disable_author_archive' );
    }
    if ( (int) get_option( 'wpfaster _canonical' ) === 1 ) {
        remove_filter( 'template_redirect', 'redirect_canonical' );
    }

    if ( (int) get_option( 'wpfaster _comment_html' ) === 1 ) {
        add_filter( 'pre_comment_content', 'esc_html' );
    }

    //
    if ( (int) get_option( 'wpfaster _theme_support_formats' ) === 1 ) {
        remove_theme_support( 'post-formats' );
    }
    if ( (int) get_option( 'wpfaster _theme_support_block_widgets' ) === 1 ) {
        remove_theme_support( 'widgets-block-editor' );
    }
    if ( (int) get_option( 'wpfaster _theme_support_responsive_embeds' ) === 1 ) {
        remove_theme_support( 'responsive-embeds' );
    }
    if ( (int) get_option( 'wpfaster _theme_support_editor_styles' ) === 1 ) {
        remove_theme_support( 'editor-styles' );
    }
    if ( (int) get_option( 'wpfaster _theme_support_block_styles' ) === 1 ) {
        remove_theme_support( 'wp-block-styles' );
    }
    if ( (int) get_option( 'wpfaster _theme_support_block_templates' ) === 1 ) {
        remove_theme_support( 'block-templates' );
    }
    if ( (int) get_option( 'wpfaster _theme_support_core_block_patterns' ) === 1 ) {
        remove_theme_support( 'core-block-patterns' );
    }
    if ( (int) get_option( 'wpfaster _theme_support_woocommerce' ) === 1 ) {
        remove_theme_support( 'woocommerce' );
    }
}

function lhf_declutter_head() {
    if ( (int) get_option( 'wpfaster _comment_cookies' ) === 1 ) {
        remove_action( 'set_comment_cookies', 'wp_set_comment_cookies' );
    }

    if ( (int) get_option( 'wpfaster _prepend_attachment' ) === 1 ) {
        remove_filter( 'the_content', 'prepend_attachment' );
    }
}

if ( (int) get_option( 'wpfaster _xmlrpc' ) === 1 ) {
    add_filter( 'xmlrpc_methods', 'lhf_remove_xmlrpc_methods' );
    add_filter( 'xmlrpc_enabled', '__return_false' );
    add_filter( 'pre_option_default_pingback_flag', '__return_zero' );

    if ( isset( $_GET['doing_wp_cron'] ) ) {
        remove_action( 'do_pings', 'do_all_pings' );
        wp_clear_scheduled_hook( 'do_pings' );
    }

    // Force removal of physical pingback tag
    add_filter( 'bloginfo_url', 'lhf_remove_pingback_url', 10, 2 );

    // Hide xmlrpc.php in HTTP response headers // default is on
    add_filter( 'wp_headers', 'lhf_remove_x_pingback' );
}

if ( (int) get_option( 'wpfaster _normalize_scheme' ) === 1 ) {
    add_filter( 'script_loader_src', 'lhf_src_scheme' );
    add_filter( 'style_loader_src', 'lhf_src_scheme' );
}



// SpeedFactor // Beta
function lhf_db_install() {
    global $wpdb;

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    $table_name = $wpdb->prefix . 'lhf_sf_curl';

    $sql = "CREATE TABLE $table_name (
        audit_id int(11) NOT NULL AUTO_INCREMENT,
        audit_total_time float NOT NULL,
        audit_namelookup_time float NOT NULL,
        audit_connect_time float NOT NULL,
        audit_pretransfer_time float NOT NULL,
        audit_redirect_time float NOT NULL,
        audit_starttransfer_time float NOT NULL,
        audit_timestamp timestamp NOT NULL,
        PRIMARY KEY (audit_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

    dbDelta( $sql );
    maybe_convert_table_to_utf8mb4( $table_name );

    //
    $table_name = $wpdb->prefix . 'lhf_sf_curl_beacon';

    $sql = "CREATE TABLE $table_name (
        audit_id int(11) NOT NULL AUTO_INCREMENT,
        audit_total_time float NOT NULL,
        audit_namelookup_time float NOT NULL,
        audit_connect_time float NOT NULL,
        audit_pretransfer_time float NOT NULL,
        audit_redirect_time float NOT NULL,
        audit_starttransfer_time float NOT NULL,
        audit_timestamp timestamp NOT NULL,
        PRIMARY KEY (audit_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

    dbDelta( $sql );
    maybe_convert_table_to_utf8mb4( $table_name );

    //
    $table_name = $wpdb->prefix . 'lhf_sf_curl_payload';

    $sql = "CREATE TABLE $table_name (
        audit_id int(11) NOT NULL AUTO_INCREMENT,
        audit_site_assets_img int(11) NOT NULL,
        audit_site_assets_css int(11) NOT NULL,
        audit_site_assets_js int(11) NOT NULL,
        audit_site_requests int(11) NOT NULL,
        audit_timestamp timestamp NOT NULL,
        PRIMARY KEY (audit_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

    dbDelta( $sql );
    maybe_convert_table_to_utf8mb4( $table_name );

    //
    $table_name = $wpdb->prefix . 'lhf_sf_cwv';

    // Drop the CWV table from the database
    $wpdb->query( "DROP TABLE IF EXISTS $table_name" );

    // Query tracker
    $table_name = $wpdb->prefix . 'lhf_query_tracker';

    if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) !== $table_name ) {
        $sql = "CREATE TABLE $table_name (
    		id mediumint(16) NOT NULL AUTO_INCREMENT,
    		longdatetime datetime NOT NULL,
    		qcount mediumint(16) NOT NULL,
    		qmemory float NOT NULL,
    		qtime float NOT NULL,
    		qpage varchar(255) NOT NULL,
    		useragent varchar(255) NOT NULL,
    		UNIQUE KEY id (id)
		) ENGINE=InnoDB;";

        dbDelta( $sql );
        maybe_convert_table_to_utf8mb4( $table_name );
    }

    // Insert default query tracking options
    add_option( 'lhf_queries_recent_requests', 50 );
    add_option( 'lhf_queries_max_records', 256 );

    // Security
    $table_name = $wpdb->prefix . 'wpfaster _blacklist';

    $results = $wpdb->query( "CREATE TABLE IF NOT EXISTS $table_name (id INT(11) NOT NULL AUTO_INCREMENT, domain VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id), KEY domain (domain));" );
}



function lhf_db_upgrade() {
    global $wpdb;

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    $table_name = $wpdb->prefix . 'lhf_sf_cwv';

    // Drop the CWV table from the database
    $wpdb->query( "DROP TABLE IF EXISTS $table_name" );

    update_option( 'lhf_db_version', '2.0.3' );
}

if ( version_compare( get_option( 'lhf_db_version' ), '2.0.3', '<' ) ) {
    add_action( 'admin_init', 'lhf_db_upgrade' );
}



register_activation_hook( __FILE__, 'lhf_db_install' );
register_deactivation_hook( __FILE__, 'lhf_on_deactivation' );
register_uninstall_hook( __FILE__, 'lhf_on_uninstall' );


function lhf_cron_speedfactor() {
    if ( (string) get_option( 'lhf_speedfactor_schedule' ) !== '' ) {
        lhf_speedfactor_curl();
        lhf_speedfactor_curl_beacon();
        lhf_speedfactor_curl_payload();

        $body = '<h3>WP Faster SpeedFactor: Task(s) Finished</h3>
        <p>You are receiving this audit notification because you opted in by adding your email address to your WP Faster plugin settings -&raquo; <b>SpeedFactor</b> -&raquo; <b>SpeedFactor Notifications</b>.</p>';

        if ( (string) get_option( 'lhf_speedfactor_audit_email' ) !== '' ) {
            wp_mail( get_option( 'lhf_speedfactor_audit_email' ), 'WP Faster SpeedFactor: Task(s) Finished', $body, [ 'Content-Type: text/html; charset=UTF-8' ] );
        }
    }
}
add_action( 'lhf_run_cron_speedfactor', 'lhf_cron_speedfactor' );

function lhf_on_deactivation() {
    wp_clear_scheduled_hook( 'lhf_run_cron_speedfactor' );
}
function lhf_on_uninstall() {
    wp_clear_scheduled_hook( 'lhf_run_cron_speedfactor' );
}

if ( (string) get_option( 'lhf_speedfactor_schedule' ) !== '' ) {
    if ( ! wp_next_scheduled( 'lhf_run_cron_speedfactor' ) ) {
        wp_schedule_event( time(), get_option( 'lhf_speedfactor_schedule' ), 'lhf_run_cron_speedfactor' );
    }
}



function lhf_resource_hints_prefetch( $hints, $relation_type ) {
    $resource_hints = array_map( 'trim', explode( PHP_EOL, get_option( 'lhf_resource_hints_prefetch' ) ) );
    $resource_hints = array_unique( array_filter( $resource_hints ) );

    foreach ( $resource_hints as $resource_hint ) {
        if ( 'dns-prefetch' === $relation_type ) {
            $hints[] = $resource_hint;
        }
    }

    return $hints;
}

function lhf_resource_hints_preconnect( $hints, $relation_type ) {
    $resource_hints = array_map( 'trim', explode( PHP_EOL, get_option( 'lhf_resource_hints_preconnect' ) ) );
    $resource_hints = array_unique( array_filter( $resource_hints ) );

    foreach ( $resource_hints as $resource_hint ) {
        if ( 'preconnect' === $relation_type ) {
            $hints[] = $resource_hint;
        }
    }

    return $hints;
}

add_filter( 'wp_resource_hints', 'lhf_resource_hints_prefetch', 10, 2 );
add_filter( 'wp_resource_hints', 'lhf_resource_hints_preconnect', 10, 2 );
