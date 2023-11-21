<?php
/**
 * Get cURL audit scores (CRON job)
 *
 * This audit stores the following metrics:
 *
 * Page Load Time
 * Page TTFB
 * DNS Lookup Time
 * Connect Time
 * PreTransfer Time
 * Redirect Time
 * StartTransfer Time
 *
 */
function lhf_speedfactor_curl_beacon() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'lhf_sf_curl_beacon';

    $url = trailingslashit( home_url() ) . 'beacon.html';

    $raw_page_load_time = 0;

    $cinfo = lhf_curl_api( $url, 'info' );

    $raw_page_load_time = $cinfo['total_time'];

    // Get bar chart values
    $value_lookup        = number_format( $cinfo['namelookup_time'], 8 );
    $value_connect       = number_format( $cinfo['connect_time'], 8 );
    $value_pretransfer   = number_format( $cinfo['pretransfer_time'], 8 );
    $value_redirect      = number_format( $cinfo['redirect_time'], 8 );
    $value_starttransfer = number_format( $cinfo['starttransfer_time'], 8 );

    /**
     * Save score to database
     */
    $wpdb->query(
        $wpdb->prepare(
            "INSERT INTO $table_name (
                audit_total_time,
                audit_namelookup_time,
                audit_connect_time,
                audit_pretransfer_time,
                audit_redirect_time,
                audit_starttransfer_time,
                audit_timestamp
            ) VALUES (
                %f,
                %f,
                %f,
                %f,
                %f,
                %f,
                %s
            )",
            [
                $raw_page_load_time,
                $value_lookup,
                $value_connect,
                $value_pretransfer,
                $value_redirect,
                $value_starttransfer,
                gmdate( 'Y-m-d H:i:s' ),
            ]
        )
    );
}
