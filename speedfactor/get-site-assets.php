<?php
/**
 * Get site assets payload (CRON job)
 *
 */
function get_asset_size( $asset_array, $asset_type ) {
    $asset_array = array_filter( $asset_array );
    $asset_type  = 'lhf_site_assets_' . $asset_type;
    $total_kb    = 0;
    $asset_list  = [];

    foreach ( $asset_array as $key => $value ) {
        $curl = curl_init( $value );

        curl_setopt( $curl, CURLOPT_REFERER, home_url() );
        curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $curl, CURLOPT_HEADER, true );
        curl_setopt( $curl, CURLOPT_NOBODY, false );
        curl_exec( $curl );

        $file_size    = curl_getinfo( $curl, CURLINFO_SIZE_DOWNLOAD );
        $file_size_kb = round( $file_size / 1024 );
        $total_kb    += $file_size_kb;

        $asset_list[] = [
            $value,
            $file_size_kb,
        ];
    }

    update_option( $asset_type, $asset_list );

    return $asset_list;
}


function lhf_speedfactor_curl_payload() {
    global $wpdb;

    $url = home_url();

    // cURL
    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
    curl_setopt( $ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0 );
    curl_setopt( $ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2 );
    curl_setopt( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:31.0) Gecko/20100101 Firefox/31.0' );
    $res = curl_exec( $ch );
    curl_close( $ch );

    $dom = new DomDocument();
    libxml_use_internal_errors( true );

    if ( ! $res ) {
        wp_die();
    }

    $dom->loadHTML( $res );
    libxml_clear_errors();

    /**
     * Get IMG payload
     */
    $images     = [];
    $image_tags = $dom->getElementsByTagName( 'img' );

    foreach ( $image_tags as $tag ) {
        $images[] = urldecode( sf_fix_uri_schema( $tag->getAttribute( 'src' ) ) );
    }
    $assets_img = get_asset_size( $images, 'img' );

    /**
     * Get JS payload
     */
    $scripts     = [];
    $script_tags = $dom->getElementsByTagName( 'script' );

    foreach ( $script_tags as $script ) {
        if ( $script->getAttribute( 'src' ) && $script->getAttribute( 'src' ) !== '' ) {
            $scripts[] = sf_fix_uri_schema( $script->getAttribute( 'src' ) );
        }
    }

    $assets_js = get_asset_size( $scripts, 'js' );

    /**
     * Get CSS payload
     */
    $links     = [];
    $link_tags = $dom->getElementsByTagName( 'link' );

    foreach ( $link_tags as $link ) {
        if ( $link->getAttribute( 'rel' ) === 'stylesheet' && $link->getAttribute( 'href' ) !== '' ) {
            $links[] = sf_fix_uri_schema( $link->getAttribute( 'href' ) );
        }
    }

    $assets_css = get_asset_size( $links, 'css' );

    $audit_site_requests   = (int) count( $assets_img ) + (int) count( $assets_css ) + (int) count( $assets_js );
    $audit_site_assets_img = (int) get_total_asset_size( $assets_img );
    $audit_site_assets_css = (int) get_total_asset_size( $assets_css );
    $audit_site_assets_js  = (int) get_total_asset_size( $assets_js );

    $table_name = $wpdb->prefix . 'lhf_sf_curl_payload';

    // Insert into database
    $wpdb->query(
        $wpdb->prepare(
            "INSERT INTO $table_name (
                audit_site_assets_img,
                audit_site_assets_css,
                audit_site_assets_js,
                audit_site_requests,
                audit_timestamp
            ) VALUES (
                %d,
                %d,
                %d,
                %d,
                %s
            )",
            [
                $audit_site_assets_img,
                $audit_site_assets_css,
                $audit_site_assets_js,
                $audit_site_requests,
                gmdate( 'Y-m-d H:i:s' ),
            ]
        )
    );
}
