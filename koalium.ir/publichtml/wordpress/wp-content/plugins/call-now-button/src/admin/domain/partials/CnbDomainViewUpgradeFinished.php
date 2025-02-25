<?php

namespace cnb\admin\domain;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\admin\api\CnbAppRemote;
use cnb\notices\CnbAdminNotices;
use cnb\notices\CnbNotice;
use cnb\utils\CnbUtils;

class CnbDomainViewUpgradeFinished {

    /**
     * @return string
     */
    private function get_settings_url() {
        $url      = admin_url( 'admin.php' );
        $tab_link =
            add_query_arg(
                array(
                    'page' => 'call-now-button-settings',
                ),
                $url );

        return esc_url( $tab_link );
    }

    /**
     * @return string
     */
    private function get_button_overview_url() {
        $url = admin_url( 'admin.php' );

        return add_query_arg(
            array(
                'page' => 'call-now-button',
            ),
            $url );
    }


    private function render_lets_go() {
        $templates_link   =
            add_query_arg(
                array(
                    'page'    => 'call-now-button-templates',
                ),
                admin_url( 'admin.php' ) );
        printf( '
            <div class="cnb-get-started cnb-plan-features cnb-center top-50">
            <h1 class="cnb-center"><strong>Let\'s get started</strong></h1><hr>
            <div class="cnb-flexbox">
              <div>
                <h2>Manage your buttons</h2>
                <p>
                  <a class="button button-primary" href="%1$s">Button overview</a>
                </p>
              </div>
              <div>
                <h2>Check your Settings</h2>
                <p><a class="button button-primary" href="%2$s">Open settings
                  </a></p>
              </div>
              <div>
                <h2>Administration</h2>
                <p><a class="button button-primary" href="#" onclick="return cnb_goto_billing_portal()">Invoices</a></p>
              </div>
            </div>
            <h2 class="cnb-center top-50"><strong>Or start with a template</strong></h2>
            <div class="cnb-templates cnb-flexbox">
              <div>
                <a href="' . esc_url( $templates_link ) . '">
                    <img src="' . esc_url( plugins_url( 'resources/images/templates/5-noicon-buttonbar-template.png', CNB_PLUGINS_URL_BASE)) . '" alt="Buttonbar template">
                </a>
              </div>
              <div>
                <a href="' . esc_url( $templates_link ) . '">
                    <img src="' . esc_url( plugins_url( 'resources/images/templates/multibutton-3-template.png', CNB_PLUGINS_URL_BASE)) . '" alt="Multibutton template">
                </a>
              </div>  
              <div>
                <a href="' . esc_url( $templates_link ) . '">
                    <img src="' . esc_url( plugins_url( 'resources/images/templates/wa_chat-template.png', CNB_PLUGINS_URL_BASE)) . '" alt="WhatsApp Chat template">
                </a>
              </div>              
            </div>
            <p class="cnb-center"><a href="' . esc_url( $templates_link ) . '">Explore all templates...</a></p>
            </div>',
            esc_url( $this->get_button_overview_url() ),
            esc_url( $this->get_settings_url() )
            );
    }

    /**
     * @param $domain CnbDomain
     * @param $notice CnbNotice
     *
     * @return void
     */
    function render( $domain, $notice = null ) {
        if($domain->type != 'PRO') {
            echo '<p>Your domain <strong>' . esc_html( $domain->name ) . '</strong> ';
            echo 'is currently on the <code>' . esc_html( $domain->type ) . '</code> cloud plan.</p>';
        }

        // Render notice if JUST upgraded and show general information about domain (instead of upgrade form)
        if ( $notice ) {
            wp_enqueue_script( CNB_SLUG . '-confetti' );
            wp_enqueue_script( CNB_SLUG . '-settings' );
            $cnb_utils = new CnbUtils();
            CnbAdminNotices::get_instance()->renderNotice( $notice );
            ?>
            <h1 class="cnb-upgrade-title">
                <span style="font-size:30px; width:38px;" class="dashicons dashicons-yes-alt"></span>
                Your domain <b><?php echo esc_html( $domain->name ); ?></b> was successfully upgraded to PRO!
            </h1>
            <div class="cnb-welcome-blocks">
                <div class="cnb-block">
                <h1 style="padding-top:0">Congratulations!</h1>
                <p style="font-size:16px;">Your domain is now on the PRO plan! This means you have access to every single feature including the scheduler, multi-action buttons, advanced display rules and much much more.</p>
                <p style="font-size:16px;">If you have any questions, take a look at our <a target="_blank" href="<?php echo esc_url( $cnb_utils->get_support_url('wordpress/', 'upgrade-success-page', 'help-center') ); ?>">help center</a> or feel free to email us directly at <a href="mailto:hello@nowbuttons.com">hello@nowbuttons.com</a>.</p>                
                </div>
            </div>

            <?php
            $this->render_lets_go();
            echo  '<br><br><br>';
            add_filter('admin_footer_text', array( $this, 'render_confetti_image_credits' ));
        }
    }

    /**
     * Add credits for using the animated confetti image to the page footer
     *
     * @return void
     */
    function render_confetti_image_credits() {
        echo '<span id="footer-thankyou">Thanks to <a href="https://lordicon.com/" target="_blank">Lordicon</a> for the confetti animation.</span><script>jQuery(() => {cnb_confetti()})</script>';
    }
}
