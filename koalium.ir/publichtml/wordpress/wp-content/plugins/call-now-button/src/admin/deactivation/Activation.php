<?php

namespace cnb\admin\deactivation;

use cnb\admin\gettingstarted\GettingStartedController;
use cnb\CallNowButton;

/**
 * On Activation of our plugin.
 */
class Activation {

    /**
     * @var CallNowButton
     */
    private $cnb;

    private $activation_transient = 'call-now-button_activation_redirect';

    public function __construct() {
        $this->cnb = new CallNowButton();
    }

    /**
     * This is called /during/ the activation process.
     *
     * It is only used to register when it was last activated,
     * which is used in feedback forms.
     *
     * @param $network_wide bool ignored by this function
     * @param $set_redirect bool default true
     *
     * @return void
     */
    public static function onActivation($network_wide = false, $set_redirect = true) {
        $activation = new Activation();

        $activation->init_activation();
        $activation->capture_activation_time();
        if ($set_redirect) {
            $activation->ensure_redirect();
        }
    }

    /**
     * This has to called manually, since Activation is before the `admin_init` stage!
     *
     * @return void
     */
    private function init_activation() {
        $this->cnb->options_init();

    }
    private function capture_activation_time() {
        $options = get_option('cnb');
        $options['activation_time'] = time();
        update_option( 'cnb', $options );
    }

    private function ensure_redirect() {
        set_transient($this->activation_transient, true);
    }

    public function redirect_to_welcome_page() {
        $should_redirect = get_transient($this->activation_transient);
        if (!$should_redirect) {
            return;
        }
        delete_transient($this->activation_transient);

        $controller = new GettingStartedController();
        $menu_slug = $controller->get_slug();

        $url = admin_url( 'index.php' );
        $redirect_link =
            add_query_arg(
                array(
                    'page'   => $menu_slug,
                ),
                $url );

        $redirect_url = esc_url_raw( $redirect_link );
        wp_safe_redirect( $redirect_url );
        exit;
    }
}
