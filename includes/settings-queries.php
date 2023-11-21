<?php
global $wpdb;

$table_name = $wpdb->prefix . 'lhf_query_tracker';
$message    = '';

// Run any optional commands
if ( isset( $_POST['doClearOverage'] ) && (string) $_POST['doClearOverage'] === 'yes' ) {
    do_action( 'lt_clear_max' );

    $message = '<div id="message" class="updated fade"><p><strong>The records overage has been cleared</strong></p></div>';
}

if ( isset( $_POST['doClearAll'] ) && (string) $_POST['doClearAll'] === 'yes' ) {
    $query_result = $wpdb->query( 'TRUNCATE TABLE ' . $table_name );

    $message = '<div id="message" class="updated fade"><p><strong>All of the records have been cleared</strong></p></div>';
}

$lt_recent_requests = (int) get_option( 'lhf_queries_recent_requests' );

// Get the record count
$record_count = lt_get_record_count();

// Get most recent requests
$recent_results = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY longdatetime DESC LIMIT 0, $lt_recent_requests" );

// Get min, max, and average for qtime, qcount, and qmemory
$statistic = $wpdb->get_row( "SELECT max(qtime) as max_time, min(qtime) as min_time, avg(qtime) as avg_time, max(qcount) as max_queries, min(qcount) as min_queries, avg(qcount) as avg_queries, max(qmemory) as max_memory, min(qmemory) as min_memory, avg(qmemory) as avg_memory FROM $table_name" );

$lhf_queries_max_records = (int) get_option( 'lhf_queries_max_records' );
?>

<h2>Query Tracker</h2>

<p>Track the number of queries, processing time and memory for each hit to WordPress. This diagnostic is not recommended for production websites or for long periods of time.</p>

<form method="post" action="">
    <table class="form-table">
        <tbody>
            <tr>
                <th scope="row"><label>Query Settings</label></th>
                <td>
                    <p>
                        <input class="thin-ui-toggle" type="checkbox" name="lhf_queries_enable" value="1" <?php checked( 1, (int) get_option( 'lhf_queries_enable' ) ); ?>> <label>Enable query tracking</label>
                    </p>
                    <p>
                        <input type="number" name="lhf_queries_recent_requests" value="<?php echo get_option( 'lhf_queries_recent_requests' ); ?>"> <label>Recent requests to generate average (default is 50)</label>
                    </p>
                    <p>
                        <input type="number" name="lhf_queries_max_records" value="<?php echo get_option( 'lhf_queries_max_records' ); ?>"> <label>Maximum requests to keep</label>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>

    <hr>
    <p><input type="submit" name="info_queries_update" class="button button-primary" value="Save Changes"></p>
</form>

<hr>

<?php echo $message; ?>

<?php
if ( (int) $record_count === 0 ) {
    echo '<p>Not enough records yet, give it some time.</p>';

    return;
}
?>

<table class="widefat" cellpadding="1" cellspacing="0">
    <tbody>
        <tr>
            <td colspan="3">
                <h3>Time (Seconds to process)</h3>
            </td>
        </tr>
        <tr>
            <td>Min<br><code><?php echo number_format( $statistic->min_time, 3 ); ?></code></td>
            <td>Max<br><code><?php echo number_format( $statistic->max_time, 3 ); ?></code></td>
            <td>Average<br><code><?php echo number_format( $statistic->avg_time, 3 ); ?></code></td>
        </tr>

        <tr>
            <td colspan="3">
                <h3>Queries (Database requests)</h3>
            </td>
        </tr>
        <tr>
            <td>Min<br><code><?php echo number_format( $statistic->min_queries ); ?></code></td>
            <td>Max<br><code><?php echo number_format( $statistic->max_queries ); ?></code></td>
            <td>Average<br><code><?php echo number_format( $statistic->avg_queries ); ?></code></td>
        </tr>

        <tr>
            <td colspan="3">
                <h3>Memory</h3>
            </td>
        </tr>
        <tr>
            <td>Min<br><code><?php echo number_format( $statistic->min_memory, 3 ); ?>MB</code></td>
            <td>Max<br><code><?php echo number_format( $statistic->max_memory, 3 ); ?>MB</code></td>
            <td>Average<br><code><?php echo number_format( $statistic->avg_memory, 3 ); ?>MB</code></td>
        </tr>
    </tbody>
</table>

<h3>Recent Requests</h3>

<table class="widefat striped" cellpadding="1" cellspacing="0">
    <thead>
        <tr>
            <th>Date/Time</th>
            <th>Page</th>
            <th>Queries</th>
            <th>Memory</th>
            <th>Time</th>
        </tr>
    </thead>
    <tbody>

    <?php
    foreach ( $recent_results as $recent_result ) {
        echo '<tr>
            <td>' . $recent_result->longdatetime . '</td>
            <td>
                <a href="' . $recent_result->qpage . '">' . $recent_result->qpage . '</a>
                <br><small>' . $recent_result->useragent . '
            </td>
            <td><code>' . $recent_result->qcount . '</code></td>
            <td><code>' . $recent_result->qmemory . 'MB</code></td>
            <td><code>' . $recent_result->qtime . 's</code></td>
        </tr>';
    }
    ?>

    </tbody>
</table>

<?php if ( (int) $record_count > $lhf_queries_max_records ) { ?>
    <p style="color: red"><i>Records: <?php $record_count; ?></i></p>

    <form method="post">
        <input type="hidden" name="doClearOverage" value="yes">
        <p class="submit"><input type="submit" class="button button-secondary" value="Clear records overage"></p>
    </form>
<?php } else { ?>
    <p><i>Records: <?php echo $record_count; ?></i></p>
<?php } ?>

<form method="post">
    <input type="hidden" name="doClearAll" value="yes">
    <p class="submit"><input type="submit" class="button-secondary" value="Clear ALL records"></p>
</form>
