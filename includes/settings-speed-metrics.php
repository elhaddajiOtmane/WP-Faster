<?php
global $wpdb;

wp_enqueue_script( 'lhf-chartjs' );
wp_enqueue_script( 'sf-charts' );

wp_enqueue_style( 'lhf-segment' );
wp_enqueue_script( 'lhf-segment' );

define( 'SF_HELP_ICON', '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" width="16px" height="16px"><defs></defs><path fill="#666666" fill-rule="evenodd" d="M6.9 11v2h2.3v-2zM8 16A8 8 0 118 0a8 8 0 010 16zM5.6 3.5C4.6 4 4 5 4 6.2h2.3c0-.3.1-.7.4-1 .2-.4.7-.6 1.3-.6.6 0 1 .2 1.2.5.3.2.4.5.4.9 0 .3-.1.5-.3.8l-.5.4-.6.4a3 3 0 00-1 1L6.9 10h2.2v-.8l.7-.7.5-.3c.6-.4 1-.7 1.2-1 .3-.4.5-.8.5-1.4 0-1-.4-1.7-1.2-2.2C10 3.2 9 3 7.8 3c-.8 0-1.6.2-2.2.5zm0 0"></path></svg>' );

$table_curl_name        = $wpdb->prefix . 'lhf_sf_curl';
$table_curl_beacon_name = $wpdb->prefix . 'lhf_sf_curl_beacon';
$table_name_payload     = $wpdb->prefix . 'lhf_sf_curl_payload';
$days                   = (int) get_option( 'lhf_speedfactor_results' );



/**
 * Get array of dates and array of scores
 */
$result = $wpdb->get_results( "SELECT * FROM $table_curl_name ORDER BY audit_timestamp DESC LIMIT $days" );
$result = array_reverse( $result );

$timing_dns      = [];
$timing_tcp      = [];
$timing_request  = [];
$timing_redirect = [];
$timing_ttfb     = [];
$timing_dom      = 0;

foreach ( $result as $page ) {
    $timing_dns[]      = $page->audit_namelookup_time;
    $timing_tcp[]      = $page->audit_connect_time;
    $timing_request[]  = $page->audit_pretransfer_time;
    $timing_redirect[] = $page->audit_redirect_time;
    $timing_ttfb[]     = $page->audit_starttransfer_time;
}

$timing_dns      = end( $timing_dns );
$timing_tcp      = end( $timing_tcp );
$timing_request  = end( $timing_request );
$timing_redirect = end( $timing_redirect );
$timing_ttfb     = end( $timing_ttfb );

$timing_value_dns      = $timing_dns;
$timing_value_tcp      = $timing_tcp;
$timing_value_request  = $timing_request;
$timing_value_redirect = $timing_redirect;
$timing_value_ttfb     = $timing_ttfb;
$timing_value_ttfb_raw = $timing_ttfb;
$timing_value_ttfb     = round( $timing_value_ttfb * 1000 );

// Get total time
$timing_dom      += $timing_dns + $timing_tcp + $timing_request + $timing_redirect + $timing_ttfb;
$timing_value_dom = $timing_dom * 1000;

// Check if each timing value is less than 1 second
$timing_dns      = number_format( ( (float) $timing_dns * 1000 ), 2 ) . 'ms';
$timing_tcp      = number_format( ( (float) $timing_tcp * 1000 ), 2 ) . 'ms';
$timing_request  = number_format( ( (float) $timing_request * 1000 ), 2 ) . 'ms';
$timing_redirect = number_format( ( (float) $timing_redirect * 1000 ), 2 ) . 'ms';
$timing_ttfb     = number_format( ( (float) $timing_ttfb * 1000 ), 2 ) . 'ms';
$timing_dom      = number_format( ( (float) $timing_dom * 1000 ), 2 ) . 'ms';



/**
 * Speed Factors
 *
 */
?>

<h2 style="font-size: 1.8em;">
    Speed Metrics <label class="thin-ui-popover">
        <button name="popover"><?php echo SF_HELP_ICON; ?></button>
        <div class="thin-ui-popover-body">
            <p><b>When you measure your site, WPFaster 's SpeedFactor module uses a bespoke method.</b> A custom-tailored <b>Simulated User Monitoring</b> audit, testing for raw data related to pageload timing, such as connection, redirection, Time to First Byte and more.</p>
            <p>SpeedFactor will then save all this data and show its progress in a daily chart. This is useful for event monitoring and for experimenting with various site features, content management systems, cache systems, database queries and resource requests.</p>
        </div>
    </label>
</h2>

<p>Speed testing, evolution and breakdown using raw data related to pageload timing, such as connection, redirection, Time to First Byte, and more.</p>

<h3>Time to First Byte? <a class="lhf-helplink" href="https://otmane.tech/faq/wordpress-WPFaster -what-is-time-to-first-byte/" rel="external" target="_blank"><span class="dashicons dashicons-info"></span></a></h3>

<section class="lhf-grid lhf-grid-2 sf-WPFaster -results">
    <div class="lhf-grid-item">
        <div class="lhf-sf-metric-name">TTFB (FRT)</div>
        First Response Time <label class="thin-ui-popover">
            <button name="popover"><?php echo SF_HELP_ICON; ?></button>
            <div class="thin-ui-popover-body">
                <p>The total time, in seconds, that the full pageload operation lasted, including connection and document return. This does not include any JavaScript, third-party resources or images. Also known as <b>Raw Pageload Time</b>, <b>Time to First Byte (<abbr title="Time to First Byte">TTFB</abbr>)</b> or <b>StartTransfer Time</b>.</p>
                <p>The <abbr title="Time to First Byte">TTFB</abbr> is the total amount of time spent to receive the first byte of the response once it has been requested.</p>
                <p>This metric is one of the key indicators of web performance. Google recommends this value to be between 250ms and 500ms.</p>
            </div>
        </label>
        <br>
        <b class="lhf-sf-metric-value lhf-sf-metric-value--ttfb" data-value="<?php echo $timing_value_ttfb; ?>"><?php echo get_metric_frt(); ?></b>
        <br><small>Time to First Byte</small>

        <p>
            <a href="#timing-evolution" class="button button-secondary">Breakdown</a>
            <a href="#ttfb-evolution" class="button button-secondary">Evolution</a>
        </p>
    </div>
    <div class="lhf-grid-item">
        <div class="lhf-sf-metric-name">DLT (SRT)</div>
        Document Loaded Time <label class="thin-ui-popover">
            <button name="popover"><?php echo SF_HELP_ICON; ?></button>
            <div class="thin-ui-popover-body">
                <p>The <code>DOMContentLoaded</code> event fires when the initial HTML document has been completely loaded and parsed, without waiting for stylesheets, images, and subframes to finish loading.</p>
                <p>This is the time the document has loaded completely, without taking into account dynamic or delayed JavaScript.</p>
                <p>This metric measures the server response time for the main document. Server Response Time (SRT) is the amount of time between when a web client makes a request (e.g., clicking on a link or entering a URL into the address bar) and the server responds to that request.</p>
            </div>
        </label>
        <br>
        <b class="lhf-sf-metric-value lhf-sf-metric-value--dlt" data-value="<?php echo $timing_value_dom; ?>"><?php echo $timing_dom; ?></b>
        <br><small>DOMLoaded Time or Server Response Time</small>
    </div>
</section>

<h3>Average Page Load Time</h3>

<section class="lhf-grid lhf-grid-4 sf-WPFaster -results">
    <div class="lhf-grid-item">
        <span class="lhf-sf-metric-name">DNS</span>
        <br>DNS/IP lookup time <label class="thin-ui-popover">
            <button name="popover"><?php echo SF_HELP_ICON; ?></button>
            <div class="thin-ui-popover-body">
                <p>DNS lookup is a request to another server requesting the IP address for your domain name.</p>
                <p>Minimize external requests (or consolidate them into one CDN). Additionally, use <code>dns-prefetch</code> and preconnect to required resources to minimize handshake time.</p>
                <p><a href="https://otmane.tech/how-to-improve-dns-lookup-time/">Learn more</a></p>
            </div>
        </label>
        <br><span class="lhf-sf-metric-value lhf-sf-metric-value--dns" data-value="<?php echo $timing_value_dns; ?>"><?php echo $timing_dns; ?></span>
    </div>
    <div class="lhf-grid-item">
        <span class="lhf-sf-metric-name">Connect</span>
        <br>Connection, redirection, pre-transfer <label class="thin-ui-popover">
            <button name="popover"><?php echo SF_HELP_ICON; ?></button>
            <div class="thin-ui-popover-body">
                <p>This is the time to connection, redirection and pre-transfer operations.</p>
            </div>
        </label>
        <br><span class="lhf-sf-metric-value lhf-sf-metric-value--tcp" data-value="<?php echo $timing_value_tcp; ?>"><?php echo $timing_tcp; ?></span>
    </div>
    <div class="lhf-grid-item">
        <span class="lhf-sf-metric-name">Request</span>
        <br>Time until file transfer starts <label class="thin-ui-popover">
            <button name="popover"><?php echo SF_HELP_ICON; ?></button>
            <div class="thin-ui-popover-body">
                <p>This is the time the browser requests the content from your server, and WordPress starts generating the response. It is, basically, the time it took from the start until the file transfer is <b>just about to begin</b>. This includes all pre-transfer commands and negotiations that are specific to the particular protocol(s) involved. Everything from this point on is WordPress. There is nothing to optimize here.</p>
            </div>
        </label>
        <br><span class="lhf-sf-metric-value lhf-sf-metric-value--request" data-value="<?php echo $timing_value_request; ?>"><?php echo $timing_request; ?></span>
    </div>
    <div class="lhf-grid-item">
        <span class="lhf-sf-metric-name">Redirect</span>
        <br>Domain redirects <label class="thin-ui-popover">
            <button name="popover"><?php echo SF_HELP_ICON; ?></button>
            <div class="thin-ui-popover-body">
                <p>The time, in seconds, it took for all redirection steps include name lookup, connect, pretransfer and transfer before the final transaction was started. The redirect time shows the complete execution time for multiple redirections.</p>
                <p>This metric value should be 0.</p>
            </div>
        </label>
        <br><span class="lhf-sf-metric-value lhf-sf-metric-value--redirect" data-value="<?php echo $timing_value_redirect; ?>"><?php echo $timing_redirect; ?></span>
    </div>
</section>

<h3 id="timing-evolution">
    Timing Breakdown <label class="thin-ui-popover">
        <button name="popover"><?php echo SF_HELP_ICON; ?></button>
        <div class="thin-ui-popover-body">
            <p><b>Lookup Time</b>: The time, in seconds, it took from the start until the name resolving was completed.</p>
            <p><b>Connect Time</b>: The time, in seconds, it took from the start until the TCP connect to the remote host (or proxy) was completed.</p>
            <p><b>PreTransfer Time</b>: The time, in seconds, it took from the start until the file transfer was just about to begin. This includes all pre-transfer commands and negotiations that are specific to the particular protocol(s) involved.</p>
            <p><b>Redirect Time</b>: The time, in seconds, it took for all redirection steps include name lookup, connect, pretransfer and transfer before the final transaction was started. The redirect time shows the complete execution time for multiple redirections.</p>
            <p><b>StartTransfer Time</b>: The time, in seconds, it took from the start until the first byte was just about to be transferred. This includes pre-transfer and also the time the server needed to calculate the result.</p>
        </div>
    </label>
</h3>

<div id="chart-connection"></div>

<script>
document.addEventListener('DOMContentLoaded', (event) => {
    buildSegmentBar(document.getElementById('chart-connection'), {
        data: [
            { title: 'Lookup', value: <?php echo $timing_value_dns; ?> },
            { title: 'Connect', value: <?php echo $timing_value_tcp; ?> },
            { title: 'Pretransfer', value: <?php echo $timing_value_request; ?> },
            { title: 'Redirect', value: <?php echo $timing_value_redirect; ?> },
            { title: 'StartTransfer', value: <?php echo $timing_value_ttfb_raw; ?> },
        ]
    });
});
</script>

<h3 id="ttfb-evolution">
    Time to First Byte Evolution
    <small>Last <?php echo $days; ?> Days</small>
</h3>

<section class="thin-ui-grid" style="margin: 24px 0;">
    <div class="thin-ui-col col--inner">
        <?php
        echo '<div class="aws-container">
            <canvas id="ttfb-bar-values" height="300"
                data-labels="' . implode( '|', array_reverse( sf_get_ttfb( 'date', $days ) ) ) . '"
                data-values="' . implode( '|', array_reverse( sf_get_ttfb( 'score', $days ) ) ) . '"
                data-values-beacon="' . implode( '|', array_reverse( sf_get_beacon_ttfb( 'score', $days ) ) ) . '"
            ></canvas>
        </div>';
        ?>
    </div>
</section>



<?php
$raw_timing_ttfb        = $wpdb->get_var( "SELECT audit_starttransfer_time FROM $table_curl_name ORDER BY audit_timestamp DESC LIMIT 1" );
$raw_beacon_timing_ttfb = $wpdb->get_var( "SELECT audit_starttransfer_time FROM $table_curl_beacon_name ORDER BY audit_timestamp DESC LIMIT 1" );


// Beacon Difference
$beacon_diff = $raw_timing_ttfb - $raw_beacon_timing_ttfb;

if ( $beacon_diff * 1000 < 1000 ) {
    $beacon_diff = number_format( ( (float) $beacon_diff * 1000 ), 2 ) . 'ms';
} else {
    $beacon_diff = number_format( (float) $beacon_diff, 6 ) . 's';
}

if ( (int) $raw_beacon_timing_ttfb > 0 ) {
    $beacon_diff_percent = $raw_timing_ttfb / $raw_beacon_timing_ttfb;
    $beacon_percent      = number_format( $beacon_diff_percent * 100, 0 ) . '%';
} else {
    $beacon_percent = 'n/a';
}
?>
<section class="thin-ui-grid" style="margin: 24px 0;">
    <div class="thin-ui-col col--inner">
        <h4>What does the Beacon measure?</h4>
        <p>The Beacon measures the raw loading time of your server, excluding the server software (e.g. PHP, .NET), the database connection (e.g. MySQL, MariaDB) and the <abbr title="Content Management System">CMS</abbr> (e.g. WordPress, Drupal, Craft).</p>

        <h4>What does the difference between TTFB and Beacon TTFB represent?</h4>
        <p>The difference is the load time added by the CMS, your server software and your database.</p>

        <p>Your CMS adds <b><?php echo $beacon_diff; ?></b> to your website's loading time (<?php echo $beacon_percent; ?> difference).</p> 
    </div>
    <div class="thin-ui-col col--inner">
        <h4>Installation</h4>

        <?php
        if ( file_exists( trailingslashit( ABSPATH ) . 'beacon.html' ) ) {
            echo '<h4 style="color: #18b663;">Your <b>Beacon</b> is installed!</h4>';
        } else {
            echo '<h4 style="color: #e53935;">Your <b>Beacon</b> is not installed!</h4>';
        }
        ?>

        <p>You can install and verify the <b>Beacon</b> by uploading a special HTML file to your site. Removing this <b>Beacon</b> file from your site will cause you to lose statistics for the site.</p>

        <p>In your site's root, typically <code><?php echo home_url(); ?></code>, create an empty file called <code>beacon.html</code>. This file needs to be publicly accessible at <code><?php echo trailingslashit( home_url() ); ?>beacon.html</code>.</p>

        <h4>When will I start to see <b>Beacon</b> data?</h4>
        <p><b>Beacon</b> data is collected for a site from the time that you first add the <b>Beacon</b> file.</p>
    </div>
</section>

<?php
/**
 * Get array of dates and array of scores
 */
$result = $wpdb->get_results( "SELECT audit_timestamp, audit_site_assets_img, audit_site_assets_css, audit_site_assets_js, audit_site_requests FROM $table_name_payload ORDER BY audit_timestamp DESC LIMIT $days" );
$result = array_reverse( $result );

$payload_date_array       = [];
$payload_date_array_piped = [];

$score_assets_img_array = [];
$score_assets_css_array = [];
$score_assets_js_array  = [];
$score_requests_array   = [];

foreach ( $result as $page ) {
    $payload_date_array[]       = gmdate( 'M d, Y', strtotime( $page->audit_timestamp ) );
    $payload_date_array_piped[] = gmdate( 'M d, Y', strtotime( $page->audit_timestamp ) );

    $score_assets_img_array[] = $page->audit_site_assets_img;
    $score_assets_css_array[] = $page->audit_site_assets_css;
    $score_assets_js_array[]  = $page->audit_site_assets_js;
    $score_requests_array[]   = $page->audit_site_requests;
}

$payload_date_array_piped = implode( '|', $payload_date_array_piped );
//



$site_assets_img = get_option( 'lhf_site_assets_img' );
$site_assets_css = get_option( 'lhf_site_assets_css' );
$site_assets_js  = get_option( 'lhf_site_assets_js' );

$out = '';

if ( $site_assets_img || $site_assets_css || $site_assets_js ) {
    $site_assets_img_size = number_format( get_total_asset_size( $site_assets_img ) ) . 'KB';
    $site_assets_css_size = number_format( get_total_asset_size( $site_assets_css ) ) . 'KB';
    $site_assets_js_size  = number_format( get_total_asset_size( $site_assets_js ) ) . 'KB';

    $site_assets_total_size = number_format( ( get_total_asset_size( $site_assets_img ) + get_total_asset_size( $site_assets_css ) + get_total_asset_size( $site_assets_js ) ) ) . 'KB';

    $out .= '<h3>
        Site Assets (Payload) <label class="thin-ui-popover">
            <button name="popover">' . SF_HELP_ICON . '</button>
            <div class="thin-ui-popover-body">
                <p><strong>Brotli</strong>: Speed up page load times for your visitor\'s HTTPS traffic by applying Brotli compression.</p>
                <p>Cloudflare applies Brotli compression to help speed up page load times for your visitors. Cloudflare will select Brotli compression as the preferred content encoding method if multiple compression methods are supported by the client.</p>
            </div>
        </label>
    </h3>

    <div class="thin-ui-grid thin-ui-grid-push">
        <div class="thin-ui-col col--inner">
            <b class="sf-value--numeric sf-font-size-36 ' . get_color_grade( count( $site_assets_img ), 5, 20 ) . '">' . count( $site_assets_img ) . '</b>
            <br>Images <code>' . $site_assets_img_size . '</code>
            <br><small class="sf-faded">Lower is better</small>
        </div>
        <div class="thin-ui-col col--inner">
            <b class="sf-value--numeric sf-font-size-36 ' . get_color_grade( count( $site_assets_css ), 3, 8 ) . '">' . count( $site_assets_css ) . '</b>
            <br>Stylesheets <code>' . $site_assets_css_size . '</code>
            <br><small class="sf-faded">Lower is better</small>
        </div>
        <div class="thin-ui-col col--inner">
            <b class="sf-value--numeric sf-font-size-36 ' . get_color_grade( count( $site_assets_js ), 5, 10 ) . '">' . count( $site_assets_js ) . '</b>
            <br>Scripts <code>' . $site_assets_js_size . '</code>
            <br><small class="sf-faded">Lower is better</small>
        </div>
        <div class="thin-ui-col col--inner">
            <b class="sf-value--numeric sf-font-size-36">' . ( count( $site_assets_img ) + count( $site_assets_css ) + count( $site_assets_js ) ) . '</b>
            <br>Requests <code>' . $site_assets_total_size . '</code>
            <br><small class="sf-faded">Lower is better</small>
        </div>
        <div class="thin-ui-col col--inner">';
            $checkmark = '<svg aria-hidden="true" focusable="false" data-prefix="fal" data-icon="check" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="svg-inline--fa fa-check fa-w-14 fa-fw"><path fill="currentColor" d="M413.505 91.951L133.49 371.966l-98.995-98.995c-4.686-4.686-12.284-4.686-16.971 0L6.211 284.284c-4.686 4.686-4.686 12.284 0 16.971l118.794 118.794c4.686 4.686 12.284 4.686 16.971 0l299.813-299.813c4.686-4.686 4.686-12.284 0-16.971l-11.314-11.314c-4.686-4.686-12.284-4.686-16.97 0z" class=""></path></svg>';

            $out .= '<h3>Requests Evolution</h3>';

            $out .= get_sparkline( 'sparkline-payload', explode( '|', $payload_date_array_piped ), $score_requests_array );
        $out .= '</div>
    </div>';


    $domain = str_ireplace( 'www.', '', parse_url( home_url(), PHP_URL_HOST ) );

    $img_string = '';
    $imgs       = get_option( 'lhf_site_assets_img' );

    foreach ( $imgs as $img ) {
        $img_path = str_replace( 'http://', '<i>http://</i>', $img[0] );

        $img_string .= '<a href="' . $img[0] . '">' . $img_path . '</a> (<code>' . $img[1] . 'KB</code>)<br>';
    }

    $internal_css_size = 0;
    $external_css_size = 0;
    $css_string        = '';
    $csss              = get_option( 'lhf_site_assets_css' );

    foreach ( $csss as $css ) {
        $css[0] = sf_fix_uri_schema( $css[0] );

        $css_path = str_replace( 'http://', '<i>http://</i>', $css[0] );
        $css_path = str_replace( '.min.css', '<b>.min.css</b>', $css_path );

        // Extract third-party URL
        $parsed_domain = str_ireplace( 'www.', '', parse_url( $css[0], PHP_URL_HOST ) );

        if ( (string) $domain !== (string) $parsed_domain ) {
            $external_css_size += $css[1];
        } else {
            $internal_css_size += $css[1];
        }

        $css_string .= '<a href="' . $css[0] . '">' . $css_path . '</a> (<code>' . $css[1] . 'KB</code>)<br>';
    }

    $internal_js_size = 0;
    $external_js_size = 0;
    $js_string        = '';
    $jss              = get_option( 'lhf_site_assets_js' );

    foreach ( $jss as $js ) {
        $js_path = str_replace( 'http://', '<i>http://</i>', $js[0] );
        $js_path = str_replace( '.min.js', '<b>.min.js</b>', $js_path );

        // Extract third-party URL
        $parsed_domain = str_ireplace( 'www.', '', parse_url( $js[0], PHP_URL_HOST ) );

        if ( (string) $domain !== (string) $parsed_domain ) {
            $external_js_size += $js[1];
        } else {
            $internal_js_size += $js[1];
        }

        $js_string .= '<a href="' . $js[0] . '">' . $js_path . '</a> (<code>' . $js[1] . 'KB</code>)<br>';
    }

    $out .= '<div class="thin-ui-grid thin-ui-grid-push">
        <div class="thin-ui-col col--inner">
            <div class="thin-ui-tabstrip">
                <input type="radio" name="tabs" id="tabone" checked="checked">
                <label for="tabone">Asset Size (images)</label>
                <div class="thin-ui-tab sf-shadow-dreamy">
                    <div class="sf-url-size-list">
                        ' . $img_string . '
                    </div>
                </div>

                <input type="radio" name="tabs" id="tabtwo">
                <label for="tabtwo">Asset Size (CSS)</label>
                <div class="thin-ui-tab sf-shadow-dreamy">
                    <p>
                        Third-party (libraries/frameworks) stylesheets: <code>' . $external_css_size . 'KB</code> <small>(out of <code>' . ( $internal_css_size + $external_css_size ) . 'KB</code>)</small>
                        <br><code>' . get_percentage( ( $internal_css_size + $external_css_size ), $external_css_size ) . '%</code> of your CSS is third-party.
                    </p>

                    <div class="frappe-chart--group">
                        <div class="frappe-chart--bar">
                            <div class="frappe-chart--bar-line" style="width: ' . ( 100 - get_percentage( ( $internal_css_size + $external_css_size ), $external_css_size ) ) . '%;">' . ( 100 - get_percentage( ( $internal_css_size + $external_css_size ), $external_css_size ) ) . '%</div>
                            <div class="frappe-chart--bar-line-end" style="width: ' . get_percentage( ( $internal_css_size + $external_css_size ), $external_css_size ) . '%;">' . get_percentage( ( $internal_css_size + $external_css_size ), $external_css_size ) . '%</div>
                        </div>
                        <div class="frappe-chart--legend"><em></em>Local/Static CSS: ' . $internal_css_size . 'KB <em></em>External/Third-Party CSS: ' . $external_css_size . 'KB</div>
                    </div>

                    <div class="sf-url-size-list">
                        ' . $css_string . '
                    </div>
                </div>

                <input type="radio" name="tabs" id="tabthree">
                <label for="tabthree">Asset Size (JS)</label>
                <div class="thin-ui-tab sf-shadow-dreamy">
                    <p>
                        Third-party (tracking/marketing/analytics) scripts: <code>' . $external_js_size . 'KB</code> <small>(out of <code>' . ( $internal_js_size + $external_js_size ) . 'KB</code>)</small>
                        <br><code>' . get_percentage( ( $internal_js_size + $external_js_size ), $external_js_size ) . '%</code> of your JavaScript is third-party.
                    </p>

                    <div class="frappe-chart--group">
                        <div class="frappe-chart--bar">
                            <div class="frappe-chart--bar-line" style="width: ' . ( 100 - get_percentage( ( $internal_js_size + $external_js_size ), $external_js_size ) ) . '%;">' . ( 100 - get_percentage( ( $internal_js_size + $external_js_size ), $external_js_size ) ) . '%</div>
                            <div class="frappe-chart--bar-line-end" style="width: ' . get_percentage( ( $internal_js_size + $external_js_size ), $external_js_size ) . '%;">' . get_percentage( ( $internal_js_size + $external_js_size ), $external_js_size ) . '%</div>
                        </div>
                        <div class="frappe-chart--legend"><em></em>Local/Static JS: ' . $internal_js_size . 'KB <em></em>External/Third-Party JS: ' . $external_js_size . 'KB</div>
                    </div>

                    <div class="sf-url-size-list">
                        ' . $js_string . '
                    </div>
                </div>
            </div>
        </div>
    </div>';
}



$out .= '<div class="thin-ui-grid thin-ui-grid-push">
    <div class="thin-ui-col col--inner">
        <h3>Tips</h3>
        <ul>
            <li>Place your JS scripts just before the closing <code>&lt;/body&gt;</code> tag.</li>
            <li>Minify your scripts and serve them compressed (Brotli or Gzip).<br><small>A minifier removes the comments and unnecessary whitespace from a program. Depending on how the program is written, this can reduce the size by about half. An obfuscator also minifies, but it will also make modifications to the program, changing the names of variables, functions, and members, making the program much harder to understand, and further reducing its size in the bargain.</small></li>
            <li>Defer scripts you do not need to run at startup.</li>
            <li>Optionally, concatenate your scripts (i.e. merge/join them all inside one script file). This reduces the load on your server and saves extra HTTP requests.</li>
            <li>Minimize the use of JavaScript files (rethink, refactor, reorganize).</li>
            <li>Minimize the use of stylesheets (combine, reorganize).</li>
        </ul>
    </div>
</div>';

$out .= '<div class="thin-ui-grid thin-ui-grid-push">
    <div class="thin-ui-col col--inner">
        <h3 class="sf-header--secondary">
            Asset Size Evolution (KB)
            <small>Last ' . $days . ' Days</small>
        </h3>';

        $out .= '<div class="aws-container">
            <canvas id="chartjs-payload" height="300"
                data-labels="' . $payload_date_array_piped . '"
                data-assets-img="' . implode( ',', $score_assets_img_array ) . '"
                data-assets-css="' . implode( ',', $score_assets_css_array ) . '"
                data-assets-js="' . implode( ',', $score_assets_js_array ) . '"
            ></canvas>
        </div>';

    $out .= '</div>
</div>';

$out .= '<div class="thin-ui-grid thin-ui-grid-push">
    <div class="thin-ui-col col--inner">
        <h3 class="sf-header--secondary">
            Requests Evolution
            <small>Last ' . $days . ' Days</small>
        </h3>';

        $out .= '<div class="aws-container">
            <canvas id="chartjs-payload-requests" height="300"
                data-labels="' . $payload_date_array_piped . '"
                data-requests="' . implode( ',', $score_requests_array ) . '"
            ></canvas>
        </div>';

    $out .= '</div>
</div>';

echo $out;
