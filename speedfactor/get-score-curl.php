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
function lhf_speedfactor_curl() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'lhf_sf_curl';

    $url = home_url();

    $raw_page_load_time = 0;
    $raw_page_ttfb      = 0;

    $cinfo = lhf_curl_api( $url, 'info' );

    $raw_page_load_time = $cinfo['total_time'];
    $raw_page_ttfb      = $cinfo['starttransfer_time'];

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
