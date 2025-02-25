<?php

namespace cnb\renderer;

/**
 * The Noop Renderer only outputs a comment that the plugin is used, but does nothing useful.
 */
class NoopRenderer extends Renderer {

    /**
     * Outputs a comment with the current version of the Plugin
     *
     * @return void
     */
    public function render() {
        printf( '<!-- Call Now Button %1$s (https://callnowbutton.com) [renderer:noop]-->%2$s',
            esc_attr( CNB_VERSION ),
            "\n"
        );
    }

    /**
     * @inheritDoc
     */
    function register() {
        add_action( 'wp_head', array( $this, 'render' ) );
    }
}
