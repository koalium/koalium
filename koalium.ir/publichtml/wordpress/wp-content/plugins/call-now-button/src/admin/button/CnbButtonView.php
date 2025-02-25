<?php

namespace cnb\admin\button;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\admin\domain\CnbDomain;
use cnb\utils\CnbAdminFunctions;
use cnb\notices\CnbAdminNotices;
use cnb\utils\CnbUtils;
use WP_Error;

class CnbButtonView {
    function header() {
        echo 'Buttons ';
    }

    function get_modal_link() {
        $url = admin_url( 'admin.php' );

        return
            add_query_arg(
                array(
                    'TB_inline' => 'true',
                    'inlineId'  => 'cnb-add-new-modal',
                    'height'    => '452',
                    // 433 + 19 (19 for PRO message) seems ideal -> To hide the scrollbar. 500 to include validation errors
                    'page'      => 'call-now-button',
                    'action'    => 'new',
                    'type'      => 'single',
                    'id'        => 'new',
                ),
                $url );
    }

    public function cnb_create_new_button() {
        $url = $this->get_modal_link();
        printf(
            '<a href="%s" title="%s" class="thickbox open-plugin-details-modal cnb-button-overview-modal-add-new %s" data-title="%s">%s</a>',
            esc_url( $url ),
            esc_html__( 'Create new button' ),
            'page-title-action',
            esc_html__( 'Choose a Button type' ),
            esc_html__( 'Add New' )
        );
    }

    /**
     * Used by the button-table, in case there are no buttons to render.
     *
     * @return void
     */
    public function render_lets_create_one_link() {
        $url = $this->get_modal_link();
        printf(
            '<a href="%s" title="%s" class="thickbox open-plugin-details-modal cnb-button-overview-modal-add-new" data-title="%s">%s</a>',
            esc_url( $url ),
            esc_html__( 'Create new button' ),
            esc_html__( 'Choose a Button type' ),
            esc_html__( 'Let\'s create one!' )
        );
    }

    /**
     * @param $domain CnbDomain|WP_Error
     * @param $table Cnb_Button_List_Table
     *
     * @return void
     */
    private function set_button_filter( $domain, $table ) {
        $cnb_options = get_option( 'cnb' );
        if ( isset( $cnb_options['show_all_buttons_for_domain'] )
            && $cnb_options['show_all_buttons_for_domain'] != 1
            && $domain != null
            && ! ( $domain instanceof WP_Error ) ) {
            $table->setOption( 'filter_buttons_for_domain', $domain->id );
        }
    }

    public function BlackFridayNotice( $domain ) {
        global $cnb_coupon;
        if ( $domain !== null && ! ( $domain instanceof WP_Error ) && $domain->type !== 'PRO' ) {
            $cnb_utils = new CnbUtils();
            if ( $cnb_coupon !== null && ! is_wp_error( $cnb_coupon ) ) {
                $promoMessage = ' Upgrade to PRO with coupon code <strong><code>' . esc_html( $cnb_coupon->code ) . '</code></strong> to get 40% off your first bill!';
                $upgrade_url  = $cnb_utils->get_cnb_domain_upgrade();
                if ( isset( $upgrade_url ) && $upgrade_url ) {
                    $promoMessage .= ' <a style="color:#00d600; font-weight:600;" href="' . esc_url( $upgrade_url ) . '">Click here!</a>';
                }
                if ( $cnb_coupon->code === 'BLACKFRIDAY22WP' ) {
                    $message = '<p>💰 <strong>BLACK FRIDAY DEAL!</strong> 💰' . $promoMessage . '</p>';
                    CnbAdminNotices::get_instance()->blackfriday( $message );
                } elseif ( $cnb_coupon->code === 'CYBERMONDAY22WP' ) {
                    $message = '<p>🤖 <strong>CYBER MONDAY DEAL!</strong> 🤖' . $promoMessage . '</p>';
                    CnbAdminNotices::get_instance()->blackfriday( $message );
                }
            }
        }
    }

    function render() {
        global $cnb_domain;

        //Prepare Table of elements
        $wp_list_table = new Cnb_Button_List_Table();

        // Set filter
        $this->set_button_filter( $cnb_domain, $wp_list_table );

        // If users come to this page before activating, we need the -settings/-premium-activation JS for the activation notice
        wp_enqueue_script( CNB_SLUG . '-settings' );
	    wp_enqueue_script( CNB_SLUG . '-premium-activation' );
	    wp_enqueue_script( CNB_SLUG . '-button-overview' );

        add_action( 'cnb_header_name', array( $this, 'header' ) );

        $data = $wp_list_table->prepare_items();

        if ( ! is_wp_error( $data ) && $cnb_domain && ! is_wp_error( $cnb_domain ) ) {
            add_action( 'cnb_after_header', array( $this, 'cnb_create_new_button' ) );

            // Check if we should warn about inactive buttons
            $views        = $wp_list_table->get_views();
            $active_views = isset( $views['active'] ) ? $views['active'] : '';
            if ( false !== strpos( $active_views, '(0)' ) ) {
                $message = '<p><span class="dashicons dashicons-info-outline"></span> You have no active buttons!</p>';
                CnbAdminNotices::get_instance()->warning( $message );
            }
        }
        $this->BlackFridayNotice( $cnb_domain );

        wp_enqueue_script( CNB_SLUG . '-form-bulk-rewrite' );
        do_action( 'cnb_header' );

        echo '<div class="cnb-two-column-section">';
        echo '<div class="cnb-body-column">';
        echo '<div class="cnb-body-content">';

        printf( '<form class="cnb_list_event" action="%s" method="post">', esc_url( admin_url( 'admin-post.php' ) ) );
        echo '<input type="hidden" name="page" value="call-now-button-buttons" />';
        echo '<input type="hidden" name="action" value="cnb_buttons_bulk" />';
        $wp_list_table->views();
        $wp_list_table->display();
        echo '</form>';
        echo '</div>';
        echo '</div>';

        $this->render_promos();
        echo '</div>';

        // Do not add the modal code if something is wrong
        if ( ! is_wp_error( $data ) ) {
            $this->render_thickbox();
            $this->render_thickbox_quick_action();
        }
        do_action( 'cnb_footer' );
    }

    private function render_promos() {
        global $cnb_domain;
        $cnb_utils   = new CnbUtils();
        $upgrade_url = $cnb_utils->get_cnb_domain_upgrade();
        $support_url = $cnb_utils->get_support_url( '', 'promobox-need-help', 'Help Center' );
        $faq_url     = $cnb_utils->get_support_url( 'wordpress/#faq', 'promobox-need-help', 'FAQ' );
        if ( isset( $upgrade_url ) && $upgrade_url ) {
            echo '<div class="cnb-postbox-container cnb-side-column"> <!-- Sidebar promo boxes -->';
            if ( $cnb_domain !== null && ! ( $cnb_domain instanceof WP_Error ) && $cnb_domain->type !== 'PRO' ) {
                $promoboxes = range( 1, 3 );
                shuffle( $promoboxes );
                $promoItem             = array_rand( $promoboxes );
                $schedule_illustration = plugins_url('resources/images/scheduler.png', CNB_PLUGINS_URL_BASE );
                $custom_image          = plugins_url('resources/images/custom-image.jpg', CNB_PLUGINS_URL_BASE );
                if ( $promoItem == 1 ) {
                    ( new CnbAdminFunctions() )->cnb_promobox(
                        'green',
                        'Schedule your buttons',
                        '<h4 class="cnb-center">Show a call button during office hours</h4>' .
                        '<div class="cnb-center" style="padding: 10px 30px"><img src="' . esc_url( $schedule_illustration ) . '" alt="Upgrade your domain to PRO with an extra discount" style="max-width:300px; width:100%; height:auto;" /></div>' .
                        '<h4 class="cnb-center">A mail button when you\'re off.</h4>',
                        'clock',
                        'Try PRO 14 days free',
                        'Upgrade',
                        $upgrade_url
                    );
                } elseif ( $promoItem == 2 ) {
                    ( new CnbAdminFunctions() )->cnb_promobox(
                        'green',
                        'Professional features',
                        '<p>
                            <span class="dashicons dashicons-yes cnb-green"></span> Button scheduler<br>
                            <span class="dashicons dashicons-yes cnb-green"></span> Multi-action buttons<br>
                            <span class="dashicons dashicons-yes cnb-green"></span> Icon picker & custom images<br>
                            <span class="dashicons dashicons-yes cnb-green"></span> Advanced display rules<br>
                            <span class="dashicons dashicons-yes cnb-green"></span> Geo targeting<br>
                            <span class="dashicons dashicons-yes cnb-green"></span> Set scroll height for buttons to appear<br>
                            <span class="dashicons dashicons-yes cnb-green"></span> Slide-in content windows<br>
                            <span class="dashicons dashicons-yes cnb-green"></span> Integrate your Intercom chat</p><h3>And much more!</h3>',
                        'performance',
                        '<strong>Try it 14 days free!</strong>',
                        'Upgrade',
                        $upgrade_url
                    );
                } else {
                    ( new CnbAdminFunctions() )->cnb_promobox(
                        'green',
                        'Customize your buttons',
                        '<h4>Unlock more icons...</h4>' .
                        '<p>Upgrade to Pro to enable an icon picker for your actions.</p>' .
                        '<h4>...or personalize with Custom Images</h4>' .
                        '<div class="cnb-center" style="padding: 0 34px"><img src="' . esc_url( $custom_image ) . '" alt="Custom button images" style="max-width:246px; width:100%; height:auto;" /></div>' .
                        '<p>With custom images you can add your own image to your buttons. For example a headshot on a contact button.</p>',
                        'art',
                        '<strong>Try it 14 days free!</strong>',
                        'Upgrade',
                        $upgrade_url
                    );
                }
            }
            $support_illustration = plugins_url('resources/images/support.png', CNB_PLUGINS_URL_BASE );
            ( new CnbAdminFunctions() )->cnb_promobox(
                'blue',
                'Need help?',
                '<p>Please head over to our <strong>Help Center</strong> for all your questions and support needs.</p>

                      <div class="cnb-right" style="padding: 10px 10px 10px 70px"><img src="' . esc_url( $support_illustration ) . '" alt="Our Help Center and support options" style="max-width:300px; width:100%; height:auto;" /></div>',
                'welcome-learn-more',
                '',
                'Open Help Center',
                $support_url
            );
            echo '</div>';
        }
        echo '<br class="clear">';
    }

    /**
     * @return void
     */
    private function render_thickbox( ) {
        global $cnb_domain;

        if ( ! $cnb_domain || is_wp_error( $cnb_domain ) ) return;

        add_thickbox();
        echo '<div id="cnb-add-new-modal" style="display:none;"><div>';

        // Create a dummy button
        $button = CnbButton::createDummyButton( $cnb_domain );

        $options = array( 'modal_view' => true, 'submit_button_text' => 'Next' );
        ( new CnbButtonViewEdit() )->render_form( $button, $cnb_domain, $options );
        echo '</div></div>';

    }

    private function render_thickbox_quick_action() {
        $cnb_utils = new CnbUtils();
        $action    = $cnb_utils->get_query_val( 'action', null );
        if ( $action === 'new' ) {
            ?>
            <script>jQuery(function () {
                    setTimeout(cnb_button_overview_add_new_click);
                });</script>
            <?php
        }

        // Change the click into an actual "onClick" event
        // But only on the button-overview page and Action is not set or to "new"
        if ( $action === 'new' || $action === null ) {
            ?>
            <script>jQuery(function () {
                    const ele = jQuery("li.toplevel_page_call-now-button li:contains('Add New') a");
                    ele.attr('href', '#');
                    ele.on("click", cnb_button_overview_add_new_click)
                });</script>
            <?php
        }
    }
}
