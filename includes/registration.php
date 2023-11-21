<?php
function wpfaster _run_blacklist_email( $sanitized_user_login, $user_email, $errors ) {
    global $wpdb;

    // Create the table name
    $table_name = $wpdb->prefix . 'wpfaster _blacklist';

    // Get the domain name and clean it
    [$username, $domain] = explode( '@', $user_email );
    $domain              = esc_sql( strtolower( trim( $domain ) ) );

    // Check the blacklist
    $blacklist_domain = $wpdb->get_results( "SELECT * FROM $table_name WHERE domain LIKE '%$domain%'" );

    if ( ! empty( $blacklist_domain ) || substr_count( $sanitized_user_login, '.' ) > 5 ) {
        $errors->add( 'invalid_email', get_option( 'wpfaster _blacklist_email_message' ) );
    }

    if ( (int) get_option( 'wpfaster _use_external_blacklist' ) === 1 ) {
        // Check external blacklist and get the contents of the txt file
        $ch = curl_init();

        curl_setopt( $ch, CURLOPT_URL, 'https://raw.githubusercontent.com/wolffe/WPFaster -spam-emails/master/blacklist.txt' );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $blacklist_data = curl_exec( $ch );
        curl_close( $ch );

        // Split the contents into an array of domain names
        $blacklist_domains = explode( "\n", $blacklist_data );
        $blacklist_domains = array_map( 'trim', $blacklist_domains );
        $blacklist_domains = array_filter( $blacklist_domains );

        // Check if the domain matches any of the blacklisted domains
        foreach ( $blacklist_domains as $blacklist_domain ) {
            // Check if the blacklisted domain is a substring of the input domain
            if ( trim( $blacklist_domain ) === $domain || strstr( $domain, trim( $blacklist_domain ) ) !== false ) {
                $errors->add( 'invalid_email', get_option( 'wpfaster _blacklist_email_message' ) );
                break; // Exit the loop if a match is found
            }
        }
        // End external blacklist
    }

    if ( (int) get_option( 'wpfaster _use_isspammy' ) === 1 ) {
        $curl = curl_init();

        curl_setopt_array(
            $curl,
            [
                CURLOPT_URL            => 'https://isspammy.com/?check=' . $user_email,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING       => '',
                CURLOPT_MAXREDIRS      => 10,
                CURLOPT_TIMEOUT        => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST  => 'GET',
            ]
        );

        $response = curl_exec( $curl );

        curl_close( $curl );

        // Decode the JSON response
        $data = json_decode( $response, true );

        if ( $data && isset( $data['spammer_found'] ) ) {
            $spammer_found = (string) $data['spammer_found'];

            if ( $spammer_found === 'yes' ) {
                // Spammer found
                $errors->add( 'invalid_email', get_option( 'wpfaster _blacklist_email_message' ) );

                // Increment counter
                $count = get_option( 'wpfaster _isspammy_count', 0 );
                $count++;
                update_option( 'wpfaster _isspammy_count', $count );
            } elseif ( $spammer_found === 'no' ) {
                // No spammer found
            } else {
                // Unknown spammer status
            }
        } else {
            // Error parsing JSON response
        }
    }

    // Increment counter
    $count = get_option( 'wpfaster _spam_registration_count', 0 );
    $count++;
    update_option( 'wpfaster _spam_registration_count', $count );

    return $errors;
}

if ( get_option( 'wpfaster _check_registration_spam' ) === '1' ) {
    //add_filter( 'registration_errors', 'wpfaster _run_blacklist_email', 20, 3 );
    add_filter( 'register_post', 'wpfaster _run_blacklist_email', 20, 3 );
}

// @todo https://plugins.trac.wordpress.org/browser/ban-hammer/trunk/ban-hammer.php#L108
