<form method="post" action="">
    <h2><?php _e( 'Media & Storage', 'WPFaster ' ); ?></h2>

    <table class="form-table">
        <tbody>
            <tr>
                <th scope="row"><label>Storage Settings</label></th>
                <td>
                    <p>
                        <input class="thin-ui-toggle" type="checkbox" name="wpfaster _no_big_images" value="1" <?php checked( 1, (int) get_option( 'wpfaster _no_big_images' ) ); ?>> <label>Disable big image functionality</label> <span class="lhfr">recommended</span>
                        <br><small><a href="https://make.wordpress.org/core/2019/10/09/introducing-handling-of-big-images-in-wordpress-5-3/" target="_blank" rel="external noopener">Learn more</a></small>
                    </p>
                    <p>
                        <input class="thin-ui-toggle" type="checkbox" name="wpfaster _no_intermediate_images" value="1" <?php checked( 1, (int) get_option( 'wpfaster _no_intermediate_images' ) ); ?>> <label>Remove additional image sizes (<code>medium_large</code>, <code>1536x1536</code> and <code>2048x2048</code>)</label>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>

    <h2><?php _e( 'Image Behaviour', 'WPFaster ' ); ?></h2>

    <table class="form-table">
        <tbody>
            <tr>
                <th scope="row"><label>Native Lazy Loading</label></th>
                <td>
                    <p>
                        <input class="thin-ui-toggle" type="checkbox" name="lhfm_lazy_loading" value="1" <?php checked( 1, (int) get_option( 'lhfm_lazy_loading' ) ); ?>> <label>Force native lazy loading for all content images and <code>&lt;iframe&gt;</code> elements</label>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label>Responsive Images</label></th>
                <td>
                    <p>
                        <select id="lhfm_responsive" name="lhfm_responsive">
                            <option value="0" <?php selected( 0, (int) get_option( 'lhfm_responsive' ) ); ?>>Leave unchanged (inherit value from theme)</option>
                            <option value="1" <?php selected( 1, (int) get_option( 'lhfm_responsive' ) ); ?>>Force responsive image srcset functionality</option>
                            <option value="2" <?php selected( 2, (int) get_option( 'lhfm_responsive' ) ); ?>>Remove responsive image srcset theme support</option>
                        </select>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>

    <h2>Compression Settings</h2>

    <p>The following settings will only apply to uploaded JPEG images and images converted to JPEG format.</p>

    <table class="form-table">
        <tr>
            <th scope="row">JPEG Compression Level</th>
            <td>
                <?php
                $lhfm_compression_level = (int) get_option( 'lhfm_compression_level' );
                $lhfm_compression_level = ( $lhfm_compression_level > 0 ) ? $lhfm_compression_level : 82;
                ?>
                <select id="lhfm_compression_level" name="lhfm_compression_level">
                    <?php for ( $i = 60; $i <= 100; $i++ ) { ?>
                        <option value="<?php echo $i; ?>" <?php selected( $i, $lhfm_compression_level ); ?>><?php echo $i; ?></option>
                    <?php } ?>
                </select>
                <br><small>WordPress default is 82.</small>
            </td>
        </tr>
    </table>

    <table class="form-table">
        <tbody>
            <tr>
                <th scope="row"><label>Registered Image Sizes</label></th>
                <td>
                    <p>
                        <?php
                        $registered_image_sizes = wp_get_registered_image_subsizes();

                        foreach ( $registered_image_sizes as $key => $image_size ) {
                            echo "<div><code>{$key}</code> ({$image_size['width']} &times; {$image_size['height']})</div>";
                        }
                        ?>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>

    <hr>
    <p><input type="submit" name="info_storage_update" class="button button-primary" value="Save Changes"></p>
</form>
