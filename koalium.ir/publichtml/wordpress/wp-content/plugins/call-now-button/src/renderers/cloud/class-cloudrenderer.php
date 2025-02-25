<?php

namespace cnb\renderer;

use cnb\admin\api\CnbAppRemote;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

class CloudRenderer extends Renderer {

    /**
     * Get the configured user/domain ID for the client JS file.
     *
     * @return string|int returns 0 if no ID is found, otherise the configured ID.
     */
    function getId() {
        $cnb_options = get_option( 'cnb' );
        return isset( $cnb_options['cloud_use_id'] ) ? $cnb_options['cloud_use_id'] : 0;
    }

    /**
     * Create the URL for the user based on the user ID. Adds a cache busting query parameter
     * if the transient base is configured.
     *
     * @param $id {string} user/domain ID
     *
     * @return string URL
     */
    function getUrl($id) {
        $url = CnbAppRemote::cnb_get_user_base() . '/' . $id . '.js';
        $qs = CnbAppRemote::cnb__get_transient_base();
        if ($qs) {
            $url = add_query_arg( array(
                'dbver' => $qs,
            ), $url );
        }
        return $url;
    }

    /**
     * Prints a comment indicating cloud usage and which ID is used
     *
     * @param $id string|int
     *
     * @return void
     */
    function renderComment($id) {
        printf( '<!-- Call Now Button %1$s (https://callnowbutton.com) [renderer:cloud, id:%2$s]-->%3$s',
            esc_attr( CNB_VERSION ),
            esc_attr( $id ),
            "\n"
        );
    }

    /**
     *
     * Prints the script tag required to include the CallNowButton on this site.
     *
     * If the id is empty, no script tag will be printed.
     *
     * @param $id string|int
     *
     * @return void
     */
    function renderJs($id) {
        if ( $id ) {
            $url = $this->getUrl($id);
            // We echo this ourselves instead of via "wp_enqueue_script",
            // since we want/seed the async feature, which is unsupported by "wp_enqueue_script"
            // phpcs:ignore WordPress.WP
            printf( '<script data-cnb-version="%1$s" type="text/javascript" async="async" src="%2$s"></script>%3$s',
                esc_attr( CNB_VERSION ),
                esc_url( $url ),
                "\n"
            );
        }
    }

    public function render() {
        $id = $this->getId();
        $this->renderComment($id);
        $this->renderJs($id);
    }

    public function register() {
        add_action( 'wp_head', array( $this, 'render' ) );
    }
}
