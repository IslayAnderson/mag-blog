<?php
/**
 * Hands the instapress-social plugin its Google Analytics Measurement Protocol
 * API secret, so the secret isn't baked into the plugin's public code.
 *
 * Settings → instapress-social GA: enter the secret.
 * GET /wp-json/instapress/v1/ga-secret → { "measurement_id": "...", "api_secret": "..." }
 *
 * Installs of the plugin fetch this when they send their setup event. It's
 * public by nature (any install has to be able to read it); changing it here
 * takes effect for every install within their cache time, no plugin release needed.
 */

defined('ABSPATH') || exit;

const MAG_BLOG_IAR_GA_OPTION = 'mag_blog_iar_ga';

function mag_blog_iar_ga_settings()
{
    return wp_parse_args(get_option(MAG_BLOG_IAR_GA_OPTION, array()), array(
        'measurement_id' => 'G-XYP40D7EYL',
        'api_secret'     => '',
    ));
}

add_action('admin_menu', function () {
    add_options_page('instapress-social GA', 'instapress-social GA', 'manage_options', 'mag-blog-iar-ga', 'mag_blog_iar_ga_page');
});

add_action('admin_init', function () {
    register_setting('mag_blog_iar_ga', MAG_BLOG_IAR_GA_OPTION, array(
        'type'              => 'array',
        'sanitize_callback' => function ($value) {
            $value = (array) $value;
            return array(
                'measurement_id' => preg_replace('/[^A-Z0-9-]/', '', strtoupper((string) ($value['measurement_id'] ?? ''))),
                'api_secret'     => preg_replace('/[^A-Za-z0-9_-]/', '', (string) ($value['api_secret'] ?? '')),
            );
        },
    ));
});

function mag_blog_iar_ga_page()
{
    $s   = mag_blog_iar_ga_settings();
    $url = rest_url('instapress/v1/ga-secret');
    ?>
    <div class="wrap">
        <h1>instapress-social GA</h1>
        <p>The Google Analytics details instapress-social installs use to send their <code>plugin_setup</code> event (Measurement Protocol).
            They read them from <a href="<?php echo esc_url($url); ?>"><code><?php echo esc_html($url); ?></code></a>.</p>
        <form method="post" action="options.php">
            <?php settings_fields('mag_blog_iar_ga'); ?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="iar-ga-mid">Measurement ID</label></th>
                    <td><input id="iar-ga-mid" class="regular-text" name="<?php echo esc_attr(MAG_BLOG_IAR_GA_OPTION); ?>[measurement_id]" value="<?php echo esc_attr($s['measurement_id']); ?>" placeholder="G-XXXXXXXXXX"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="iar-ga-secret">API secret</label></th>
                    <td>
                        <input id="iar-ga-secret" class="regular-text" name="<?php echo esc_attr(MAG_BLOG_IAR_GA_OPTION); ?>[api_secret]" value="<?php echo esc_attr($s['api_secret']); ?>" autocomplete="off" spellcheck="false">
                        <p class="description">Google Analytics → Admin → Data streams → your stream → Measurement Protocol API secrets.
                            Leave empty to stop installs sending events. Installs cache it for up to 12 hours.</p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

add_action('rest_api_init', function () {
    register_rest_route('instapress/v1', '/ga-secret', array(
        'methods'             => 'GET',
        'permission_callback' => '__return_true',
        'callback'            => function () {
            $s = mag_blog_iar_ga_settings();
            if ('' === $s['api_secret'] || '' === $s['measurement_id']) {
                return new WP_Error('iar_ga_not_set', 'Not configured.', array('status' => 404));
            }
            $res = rest_ensure_response(array(
                'measurement_id' => $s['measurement_id'],
                'api_secret'     => $s['api_secret'],
            ));
            $res->header('Cache-Control', 'public, max-age=300');
            return $res;
        },
    ));
});
