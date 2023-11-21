<form method="post" action="">
    <h2><?php _e( 'Speed Settings', 'WPFaster ' ); ?></h2>

    <h3>Prefetching</h3>
    <p>Because <code>dns-prefetch</code> resolves only the domain name but doesn't preconnect to the remote server or preload the resource, it requires little bandwidth. However, it can significantly improve DNS latency — the total request-response time between the DNS server and the user's browser.</p>
    <p>You'll only need to use dns-prefetch when the resource is hosted on a different domain, since you don't need to resolve your own domain name. <code>dns-prefetch</code> is typically recommended when prefetching domain names for: web fonts, such as Google Fonts or custom CDN fonts, analytics scripts, scripts coming from third-party resources, social media widgets or any widget that loads third-party content via the <code>&lt;script&gt;</code> tag, resources hosted on a CDN.</p>

    <h3>Preconnecting</h3>
    <p>For the most part, you can use the <code>preconnect</code> resource hint for the same things as <code>dns-prefetch</code>. You should choose it only <b>if you are sure</b> the user will request the script, font, stylesheet, or other resource from the third-party server.</p>
    <p>Since <code>preconnect</code> exchanges more data, it also needs more bandwidth. So you have to be more careful with it to avoid slowing down the page and wasting your user's bandwidth with redundant data.</p>

    <p>Be careful to not add too many resource hints as they could quite easily negatively impact performance, especially on mobile.</p>

    <p>Check the <a href="#" rel="external noopener" target="_blank">Using Resource Hints to Optimize WordPress Performance</a> guide.</p>

    <table class="form-table">
        <tbody>
            <tr>
                <th scope="row"><label for="lhf_resource_hints_prefetch">Custom Resource Hints (DNS Prefetch)</label></th>
                <td>
                    <p>All URLs added below will be added to your document's <code>&lt;head&gt;</code> section as <code>&lt;link rel="dns-prefetch" href="https://example.com"&gt;</code>.</p>
                    <p>
                        <textarea class="large-text" rows="6" name="lhf_resource_hints_prefetch"><?php echo get_option( 'lhf_resource_hints_prefetch' ); ?></textarea>
                        <br><small>Add one or more URLs (one URL per line).</small>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="lhf_resource_hints_preconnect">Custom Resource Hints (Preconnect)</label></th>
                <td>
                    <p>All URLs added below will be added to your document's <code>&lt;head&gt;</code> section as <code>&lt;link rel="preconnect" href="https://example.com"&gt;</code>.</p>
                    <p>
                        <textarea class="large-text" rows="6" name="lhf_resource_hints_preconnect"><?php echo get_option( 'lhf_resource_hints_preconnect' ); ?></textarea>
                        <br><small>Add one or more URLs (one URL per line).</small>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>

    <hr>
    <p><input type="submit" name="info_speed_update" class="button button-primary" value="Save Changes"></p>
</form>
