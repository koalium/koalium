<?php

namespace cnb\cache;

use cnb\notices\CnbNotice;
use cnb\utils\CnbUtils;
use Exception;

class CacheHandler {

    /**
     * Returns all plugins that the Call Now Button is incompatible with.
     *
     * @return string[]
     */
    private function get_conflicting_cache_plugins() {
        return array(
            'autoptimize/autoptimize.php',
            'breeze/breeze.php',
            'cache-control/cache-control.php',
            'cache-enabler/cache-enabler.php',
            'comet-cache/comet-cache.php',
            'fast-velocity-minify/fvm.php',
            'hyper-cache/plugin.php',
            'litespeed-cache/litespeed-cache.php',
            'simple-cache/simple-cache.php',
            'w3-total-cache/w3-total-cache.php',
            'wp-fastest-cache/wpFastestCache.php',
            'wp-super-cache/wp-cache.php',
            'wp-rocket/wp-rocket.php',
        );
    }

    /**
     * Return all active Caching plugins.
     *
     * @return array
     */
    function get_active_caching_plugins() {
        $active_plugins  = array();
        $caching_plugins = $this->get_conflicting_cache_plugins();
        foreach ( $caching_plugins as $plugin ) {
            if ( is_plugin_active( $plugin ) ) {
                $active_plugins[] = $plugin;
            }
        }

        return $active_plugins;
    }

    /**
     * Exclude the cloud version from the WP Rocket cache
     *
     * Since this JS file  is managed externally and
     * modified via the API (not via WordPress).
     *
     * @return void
     */
    private function exclude_cloud_from_wprocket() {
        // Exclude our CDN hosted JS file(s)
        add_filter( 'rocket_minify_excluded_external_js',
            function ( $excluded_external_js ) {
                $excluded_external_js[] = 'user.callnowbutton.com';

                return $excluded_external_js;
            }
        );

        // This removes lazyloading from our Modern renderer,
        // which is incompatbile with our base64-ended background image
        // See https://github.com/wp-media/wp-rocket/blob/develop/inc/Dependencies/RocketLazyload/Image.php#L379
        add_filter( 'rocket_lazyload_excluded_attributes',
            function ( $excluded_attributes ) {
                $excluded_attributes[] = 'id="callnowbutton"';

                return $excluded_attributes;
            }
        );
    }

    /**
     * Clean the cache for WP Super Cache
     *
     * Ref https://github.com/Automattic/wp-super-cache/blob/master/wp-cache.php#L2430
     *
     * @return void
     */
    public function clear_wpsupercache() {
        try {
            if ( function_exists( 'wp_cache_clean_cache' ) ) {
                global $file_prefix;
                wp_cache_clean_cache( $file_prefix );
            }
        } catch ( Exception $e ) {
            // NOOP
        }
    }

    /**
     * Clean the cache for WP Rocket
     *
     * @return void
     */
    public function clear_wprocketcache() {
        $cnb_options = get_option( 'cnb' );
        $cnb_utils   = new CnbUtils();
        // This action doesn't work - since it requires some args and a nonce
        // do_action( 'admin_post_purge_cache' );

        // However, we can simply call the function that clears the cache after a save

        // rocket_clean_domain is called to remove all cached pages
        if ( function_exists( 'rocket_clean_domain' ) ) {
            rocket_clean_domain(); // Check if we need "all" as well?
        }

        // rocket_clean_minify & rocket_clean_cache_busting are only needed if the Premium version is active,
        // since the Modern version does not have any JS enabled.
        if ( ! $cnb_utils->isCloudActive( $cnb_options ) ) {
            return;
        }

        // rocket_clean_minify is called to ensure our cached (Cloud) JS file is cleared
        if ( function_exists( 'rocket_clean_minify' ) ) {
            rocket_clean_minify( array( 'js' ) );
        }

        if ( function_exists( 'rocket_clean_cache_busting' ) ) {
            rocket_clean_cache_busting( array( 'js' ) );
        }
    }

    /**
     * @param $plugin array expects the array with a single plugin found via get_plugins()
     *
     * @return CnbNotice
     */
    private function get_notice_after_save( $plugin ) {
        $cnb_utils = new CnbUtils();
        $name      = $plugin['Name'];

        $url = $cnb_utils->get_support_url( 'wordpress/empty-cache/', 'save-notification', 'learn-more' );

        $message = '<p><span class="dashicons dashicons-warning"></span> ';
        $message .= 'You are using a <strong><i>Caching Plugin</i></strong> (' . $name . '). ';
        $message .= "If you're not seeing your changes, please empty your cache.";
        $message .= ' (<a href="' . esc_url( $url ) . '">Learn more...</a>)</p>';

        return new CnbNotice( 'warning', $message, true );
    }

    public function add_warning_if_cache_plugin_active( $messages ) {
        $active_caching_plugins = $this->get_active_caching_plugins();
        if ( $active_caching_plugins ) {
            $plugins = get_plugins();
            foreach ( $active_caching_plugins as $caching_plugin_name ) {
                if ( ! is_array( $plugins ) || ! array_key_exists( $caching_plugin_name, $plugins ) ) {
                    continue;
                }
                $plugin     = $plugins[ $caching_plugin_name ];
                $messages[] = $this->get_notice_after_save( $plugin );
            }
        }

        return $messages;
    }

    public static function exclude() {
        $handler = new CacheHandler();
        $handler->exclude_cloud_from_wprocket();

        add_action( 'cnb_after_button_changed', array( $handler, 'clear_wprocketcache' ) );
        add_action( 'cnb_after_button_changed', array( $handler, 'clear_wpsupercache' ) );

        add_filter( 'cnb_after_save', array( $handler, 'add_warning_if_cache_plugin_active' ) );
    }
}
