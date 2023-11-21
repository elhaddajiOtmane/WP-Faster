<?php
function lt_store_timer_data() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'lhf_query_tracker';

    $lt_data = [
        'longdatetime' => date( 'Y-m-d H:i:s', time() ),
        'qcount'       => get_num_queries(),
        'qtime'        => timer_stop( 0, 3 ),
        'qpage'        => 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
        'qmemory'      => round( memory_get_peak_usage() / 1024 / 1024, 3 ),
        'useragent'    => $_SERVER['HTTP_USER_AGENT'],
    ];

    $wpdb->insert( $table_name, $lt_data );
}

function lt_clear_max_run() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'lhf_query_tracker';

    // Get the record count 
    $record_count = lt_get_record_count();

    $lhf_queries_max_records = (int) get_option( 'lhf_queries_max_records' );

    if ( (int) $record_count > (int) $lhf_queries_max_records ) {
        // Delete the overage
        $record_overage = $record_count - $lhf_queries_max_records;
        $query          = "DELETE FROM $table_name ORDER BY ID ASC LIMIT $record_overage";
        $query_result   = $wpdb->query( $query );
    }
}

function lt_get_record_count() {
    global $wpdb;

    $table_name         = $wpdb->prefix . 'lhf_query_tracker';
    $record_count_query = $wpdb->get_row( "SELECT count(*) AS record_count FROM $table_name" );

    return $record_count_query->record_count;
}

// Bind to wp_footer() function to track latency and store results
if ( (int) get_option( 'lhf_queries_enable' ) === 1 ) {
    add_action( 'wp_footer', 'lt_store_timer_data' );
}

// Bind scheduled event to a function
add_action( 'lt_clear_max', 'lt_clear_max_run' );
