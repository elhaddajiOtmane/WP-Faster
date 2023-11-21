<?php
function lhf_curl_api( $url, $return ) {
    $lhf_speedfactor_audit_http = (string) get_option( 'lhf_speedfactor_audit_http' );
    $lhf_speedfactor_audit_tls  = (string) get_option( 'lhf_speedfactor_audit_tls' );
    $lhf_speedfactor_audit_ipv4 = (string) get_option( 'lhf_speedfactor_audit_ipv4' );

    $ch = curl_init();

    curl_setopt( $ch, CURLOPT_URL, $url );

    curl_setopt( $ch, CURLOPT_TIMEOUT, 60 );
    curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );

    curl_setopt( $ch, CURLOPT_HEADER, true );
    curl_setopt( $ch, CURLOPT_NOBODY, true );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

    curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
    curl_setopt( $ch, CURLOPT_AUTOREFERER, true );

    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, true );
    curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 2 );
    curl_setopt( $ch, CURLOPT_CERTINFO, true );
    curl_setopt( $ch, CURLOPT_VERBOSE, 0 );

    // br (Brotli) support was first added to cURL in 7.57.0, released on November 29 2017
    curl_setopt( $ch, CURLOPT_ENCODING, 'br,gzip,deflate' );
    curl_setopt( $ch, CURLOPT_USERAGENT, 'WPFaster : SpeedFactor' );

    // Attempt HTTP 2 requests.
    // libcurl will fall back to HTTP 1.1 if HTTP 2 can't be negotiated with the server.
    if ( $lhf_speedfactor_audit_http === 'http1' ) {
        curl_setopt( $ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1 );
    } elseif ( $lhf_speedfactor_audit_http === 'http2' ) {
        curl_setopt( $ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0 );
    }

    // TLS v1.3 or later (Added in 7.52.0)
    if ( $lhf_speedfactor_audit_tls === 'tls12' ) {
        curl_setopt( $ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2 );
    } elseif ( $lhf_speedfactor_audit_tls === 'tls13' ) {
        curl_setopt( $ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_3 );
    }

    if ( $lhf_speedfactor_audit_ipv4 === 'ipv4' ) {
        curl_setopt( $ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
    }

    $response = curl_exec( $ch );

    if ( curl_error( $ch ) ) {
        $err = curl_error( $ch );
    } else {
        if ( (string) $return === 'info' ) {
            $response = curl_getinfo( $ch );
        } elseif ( (string) $return === 'response' ) {
            $response = $response;
        }
    }

    curl_close( $ch );

    return $response;
}



/**
 * Get timing metrics
 */
function get_timing_metric( $siteId, $metric, $days ) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'lhf_sf_curl';


    /**
     * Get array of dates and array of scores
     */
    $result = $wpdb->get_results( "SELECT audit_namelookup_time, audit_connect_time, audit_pretransfer_time, audit_redirect_time, audit_starttransfer_time FROM $table_name ORDER BY audit_timestamp DESC LIMIT $days" );
    $result = array_reverse( $result );

    $timingDNS      = [];
    $timingTCP      = [];
    $timingRequest  = [];
    $timingRedirect = [];
    $timingTTFB     = [];

    $timingDOM = 0;

    foreach ( $result as $page ) {
        $timingDNS[]      = $page->audit_namelookup_time;
        $timingTCP[]      = $page->audit_connect_time;
        $timingRequest[]  = $page->audit_pretransfer_time;
        $timingRedirect[] = $page->audit_redirect_time;
        $timingTTFB[]     = $page->audit_starttransfer_time;
    }

    // Get total time
    $timingDOM += end( $timingDNS ) + end( $timingTCP ) + end( $timingRequest ) + end( $timingRedirect ) + end( $timingTTFB );

    // Build timing string for ChartJS
    $timingValuesArray = [
        end( $timingDNS ),
        end( $timingTCP ),
        end( $timingRequest ),
        end( $timingRedirect) ,
        end( $timingTTFB ),
    ];

    $timingValues = implode( '|', $timingValuesArray );

    // Build each timing value string
    $timingDNS      = (float) end($timingDNS);
    $timingTCP      = (float) end($timingTCP);
    $timingRequest  = (float) end($timingRequest);
    $timingRedirect = (float) end($timingRedirect);
    $timingTTFB     = (float) end($timingTTFB);

    // Check if each timing value is less than 1 second
    if ($timingDNS * 1000 < 1000) {
        $timingDNS = number_format(((float) $timingDNS * 1000), 2) . 'ms';
    } else {
        $timingDNS = number_format((float) $timingDNS, 6) . 's';
    }

    if ($timingTCP * 1000 < 1000) {
        $timingTCP = number_format(((float) $timingTCP * 1000), 2) . 'ms';
    } else {
        $timingTCP = number_format((float) $timingTCP, 6) . 's';
    }

    if ($timingRequest * 1000 < 1000) {
        $timingRequest = number_format(((float) $timingRequest * 1000), 2) . 'ms';
    } else {
        $timingRequest = number_format((float) $timingRequest, 6) . 's';
    }

    if ($timingRedirect * 1000 < 1000) {
        $timingRedirect = number_format(((float) $timingRedirect * 1000), 2) . 'ms';
    } else {
        $timingRedirect = number_format((float) $timingRedirect, 6) . 's';
    }

    if ($timingTTFB * 1000 < 1000) {
        $timingTTFB = number_format(((float) $timingTTFB * 1000), 2) . 'ms';
    } else {
        $timingTTFB = number_format((float) $timingTTFB, 6) . 's';
    }

    if ($timingDOM * 1000 < 1000) {
        $timingDOM = number_format(((float) $timingDOM * 1000), 2) . 'ms';
    }

    switch ((string) $metric) {
        case ('timingDOM') :
            return $timingDOM;
            break;
        case ('timingTTFB') :
            return $timingTTFB;
            break;
    }

    return false;
}



/**
 * Metric Cards
 *
 */

// First Response Time
function get_metric_frt() {
    global $wpdb;

    $table_name = $wpdb->prefix . "lhf_sf_curl";

    $value = $wpdb->get_var("SELECT audit_starttransfer_time FROM $table_name ORDER BY audit_timestamp DESC LIMIT 1");

    $timingTTFB = (float) $value;

    $timingTTFB = number_format(((float) $timingTTFB * 1000), 2) . 'ms';

    return $timingTTFB;
}

// Document Loaded (DOMLoaded) Time
function get_metric_dlt($siteId) {
    global $wpdb;

    $table_name = $wpdb->prefix . "lhf_sf_curl";

    $siteId = (int) $siteId;

    $result = $wpdb->get_results("SELECT audit_namelookup_time, audit_connect_time, audit_pretransfer_time, audit_redirect_time, audit_starttransfer_time FROM $table_name ORDER BY audit_timestamp DESC LIMIT 1");

    foreach ($result as $page) {
        $timingDNS = (float) $page->audit_namelookup_time;
        $timingTCP = (float) $page->audit_connect_time;
        $timingRequest = (float) $page->audit_pretransfer_time;
        $timingRedirect = (float) $page->audit_redirect_time;
        $timingTTFB = (float) $page->audit_starttransfer_time;
    }

    // Get total time
    $timingDOM = $timingDNS + $timingTCP + $timingRequest + $timingRedirect + $timingTTFB;

    $timingDOM = number_format(((float) $timingDOM * 1000), 2) . 'ms';

    return $timingDOM;
}




function sf_get_ttfb($column, $days) {
	global $wpdb;

    $table_name = $wpdb->prefix . "lhf_sf_curl";

    $score = $date = [];

	$result = $wpdb->get_results("SELECT audit_starttransfer_time, audit_timestamp FROM $table_name ORDER BY audit_timestamp DESC LIMIT $days");
    foreach ($result as $value) {
        $score[] = $value->audit_starttransfer_time;
        $date[] = date('M d, Y', strtotime($value->audit_timestamp));
    }

    return ((string) $column === 'score') ? $score : $date;
}

function sf_get_beacon_ttfb($column, $days) {
	global $wpdb;

    $table_name = $wpdb->prefix . "lhf_sf_curl_beacon";

    $score = $date = [];

	$result = $wpdb->get_results("SELECT audit_starttransfer_time, audit_timestamp FROM $table_name ORDER BY audit_timestamp DESC LIMIT $days");
    foreach ($result as $value) {
        $score[] = $value->audit_starttransfer_time;
        $date[] = date('M d, Y', strtotime($value->audit_timestamp));
    }

    return ((string) $column === 'score') ? $score : $date;
}



function sf_fix_uri_schema( $uri ) {
    if ( parse_url( $uri, PHP_URL_SCHEME ) === null ) {
        return 'https://' . ltrim( $uri, '/' );
    } else {
        return $uri;
    }
}

function get_total_asset_size( $asset_array ) {
    return array_reduce(
        $asset_array,
        function ( $total, $asset ) {
            return $total + (int) $asset[1];
        },
        0
    );
}

function get_color_grade( $count, $min, $max ) {
    $color = '';

    if ( $count <= $min ) {
        $color = 'sf-color--green';
    } elseif ( $count > $min && $count <= $max ) {
        $color = 'sf-color--yellow';
    } else {
        $color = 'sf-color--red';
    }

    return $color;
}

/**
 * Generate a Sparkline chart container (SVG)

 * @param  string $element_id  ID of SVG element
 * @param  array  $data_labels Array of labels
 * @param  array  $data_values Array of values
 *
 * @return string              HTML Element
 */
function get_sparkline( $element_id, $data_labels, $data_values ) {
    $last_date  = end( $data_labels );
    $last_score = end( $data_values );

    $labels = implode( '|', $data_labels );
    $values = implode( ',', $data_values );

    $out = '<svg class="sparkline--svg" id="' . $element_id . '" width="200" height="40" stroke-width="3" stroke="#29275F" fill="url(#sparkline--svg-gradient) rgba(5, 203, 99, 0.2)" data-labels="' . $labels . '" data-values="' . $values . '"></svg>
    <div class="sparkline--tooltip black-50" data-default="' . $last_date . ': ' . $last_score . '">' . $last_date . ': ' . $last_score . '</div>';

    return $out;
}

function get_percentage( $total, $number ) {
    if ( $total > 0 ) {
        return round( $number / ( $total / 100 ), 2 );
    } else {
        return 0;
    }
}

function lhf_ms_to_s( $value, $precision = 2 ) {
    $value = (int) $value / 1000;
    $value = round( $value, $precision );

    return $value;
}
