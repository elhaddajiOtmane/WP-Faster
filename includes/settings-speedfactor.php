<form method="post" action="">
    <h2><?php _e( 'SpeedFactor Settings', 'WPFaster ' ); ?></h2>

    <p><b>Set a frequency below to enable SpeedFactor audits.</b></p>
    <p>💡 Note that not all reports and audits will be available instantly, especially if your WP Faster installation is new. Some of them will be available in up to 24 hours. Make sure to check your stats every day to identify potential pagespeed issues or performance bottlenecks.</p>

    <table class="form-table">
        <tbody>
            <tr>
                <th scope="row"><label>SpeedFactor Beacon</label></th>
                <td>
                    <?php
                    if ( isset( $_GET['action'] ) && sanitize_title( $_GET['action'] ) === 'create-config' ) {
                        $beacon = trailingslashit( ABSPATH ) . 'beacon.html';

                        fopen( $beacon, 'w' );
                    }

                    if ( ! file_exists( trailingslashit( ABSPATH ) . 'beacon.html' ) ) {
                        ?>
                        <p>❌ Beacon file does not exist!</p>
                        <p>
                            <a href="<?php echo admin_url( 'admin.php?page=WPFaster &tab=lhf_speedfactor' ); ?>&action=create-config" class="button button-secondary">Attempt Beacon file creation</a>
                        </p>
                    <?php } else { ?>
                        <p>✔️ Beacon file successfully created.</p>
                    <?php } ?>
                </td>
            </tr>
            <tr>
                <th scope="row"><label>SpeedFactor Frequency</label></th>
                <td>
                    <select name="lhf_speedfactor_schedule">
                        <option value="hourly" <?php selected( (string) get_option( 'lhf_speedfactor_schedule' ), 'hourly' ); ?>>hourly</option>
                        <option value="twicedaily" <?php selected( (string) get_option( 'lhf_speedfactor_schedule' ), 'twicedaily' ); ?>>twicedaily</option>
                        <option value="daily" <?php selected( (string) get_option( 'lhf_speedfactor_schedule' ), 'daily' ); ?>>daily (default)</option>
                        <option value="weekly" <?php selected( (string) get_option( 'lhf_speedfactor_schedule' ), 'weekly' ); ?>>weekly</option>
                    </select>
                    <br><small>This is the frequency of running the SpeedFactor audit. For active development work, an hourly interval is recommended.</small>
                </td>
            </tr>
            <tr>
                <th scope="row"><label>SpeedFactor Results</label></th>
                <td>
                    <input type="number" min="1" name="lhf_speedfactor_results" value="<?php echo get_option( 'lhf_speedfactor_results' ); ?>">
                    <br><small>This is the number of results (days or hours) to show in the evolution charts.</small>
                </td>
            </tr>

            <tr>
                <th scope="row"><label>SpeedFactor Audit Settings</label></th>
                <td>
                    <p>
                        <select name="lhf_speedfactor_audit_http">
                            <option value="http2" <?php selected( (string) get_option( 'lhf_speedfactor_audit_http' ), 'http2' ); ?>>Use HTTP 2.0 (default, recommended)</option>
                            <option value="http1" <?php selected( (string) get_option( 'lhf_speedfactor_audit_http' ), 'http1' ); ?>>Use HTTP 1.1</option>
                        </select>
                        <br><small>SpeedFactor will fall back to <code>HTTP 1.1</code> if <code>HTTP 2</code> can't be negotiated with the server.</small>
                    </p>
                    <p>
                        <select name="lhf_speedfactor_audit_tls">
                            <option value="tls12" <?php selected( (string) get_option( 'lhf_speedfactor_audit_tls' ), 'tls12' ); ?>>Use TLS 1.2 (default)</option>
                            <option value="tls13" <?php selected( (string) get_option( 'lhf_speedfactor_audit_tls' ), 'tls13' ); ?>>Use TLS 1.3</option>
                        </select>
                        <br><small>SpeedFactor will use <code>TLS 1.3</code> or later if your cURL version is equal to or higher than <code>7.52.0</code> and OpenSSL was built with <code>TLS 1.3</code> support.</small>
                        <br><small>Your cURL version is <code><?php echo curl_version()['version']; ?></code>.</small>
                    </p>
                    <p>
                        <select name="lhf_speedfactor_audit_ipv4">
                            <option value="ipv6" <?php selected( (string) get_option( 'lhf_speedfactor_audit_ipv4' ), 'ipv6' ); ?>>Use IPv6 (default, recommended)</option>
                            <option value="ipv4" <?php selected( (string) get_option( 'lhf_speedfactor_audit_ipv4' ), 'ipv4' ); ?>>Use IPv4</option>
                        </select>
                        <br><small>In some rare cases, the audit will not work with IPv6. Switch to IPv4 to debug.</small>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label>SpeedFactor Notifications</label></th>
                <td>
                    <p>
                        <input type="email" class="regular-text" name="lhf_speedfactor_audit_email" value="<?php echo get_option( 'lhf_speedfactor_audit_email' ); ?>" placeholder="<?php _e( 'Your email address...', 'WPFaster ' ); ?>">
                        <br><small><?php _e( 'Receive an email whenever an audit runs.', 'WPFaster ' ); ?></small>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>

    <hr>
    <p><input type="submit" name="info_speedfactor_update" class="button button-primary" value="Save Changes"></p>
</form>
