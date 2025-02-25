<?php

namespace cnb\admin\button;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\admin\api\CnbAppRemote;
use cnb\admin\domain\CnbDomain;
use cnb\admin\models\ValidationMessage;
use cnb\admin\partials\Preview;
use cnb\utils\CnbAdminFunctions;
use WP_Error;

class CnbButtonViewEdit {
    /**
     * Renders the "Edit <type>" header
     *
     * @param $button CnbButton Used to determine type if available
     */
    function header( $button ) {
        if ( $button && ! is_wp_error( $button ) ) {
            $type = $button->type;
            $name = $button->name;
        } else {
	        $name = 'New Button';
	        $type = strtoupper( filter_input( INPUT_GET, 'type', @FILTER_SANITIZE_STRING ) );
        }

        // In case no type or Button could be found, bail early
	    if ( ! $type ) return;

        $adminFunctions = new CnbAdminFunctions();
        $buttonTypes    = $adminFunctions->cnb_get_button_types();
        $typeName       = $buttonTypes[ $type ];
        echo esc_html__( 'Editing ' ) . esc_html( $typeName ) . ' <span class="cnb_button_name">' . esc_html( $name ) . '</span>';
    }

    /**
     * @param $button CnbButton
     * @param $tab string
     *
     * @return string
     */
    private function get_tab_url( $button, $tab ) {
        $url = admin_url( 'admin.php' );

        return add_query_arg(
            array(
                'page'   => 'call-now-button',
                'action' => 'edit',
                'type'   => strtolower( $button->type ),
                'id'     => $button->id,
                'tab'    => $tab,
            ),
            $url );
    }

    /**
     *
     * This renders JUST the form (no tabs, preview phone, etc.) and is also used in button-overview for the "Add new" modal.
     *
     * @param $button CnbButton
     * @param $default_domain CnbDomain|WP_Error
     * @param $options array (modal_view (boolean), submit_button_text (string)
     *
     * @return void
     */
    public function render_form( $button, $default_domain, $options = array() ) {
        $adminFunctions = new CnbAdminFunctions();
        $button_edit_table = new Button_Edit_Table();

        $submit_button_text = array_key_exists( 'submit_button_text', $options ) ? $options['submit_button_text'] : '';
        $hide_on_modal      = array_key_exists( 'modal_view', $options ) && $options['modal_view'] === true;
        if ( $hide_on_modal ) {
            echo '<script type="text/javascript">cnb_hide_on_modal_set=1</script>';
        }

        // In case the API isn't working properly
        if ( ! $default_domain || is_wp_error( $default_domain ) ) {
            $default_domain     = new CnbDomain();
            $default_domain->id = 0;
        }

        wp_enqueue_script( 'jquery-ui-sortable' );
        wp_enqueue_script( CNB_SLUG . '-jquery-ui-touch-punch' );
        wp_enqueue_script( CNB_SLUG . '-action-type-to-icon-text' );
        wp_enqueue_script( CNB_SLUG . '-form-to-json' );
        wp_enqueue_script( CNB_SLUG . '-preview' );
        wp_enqueue_script( CNB_SLUG . '-client' );
        wp_enqueue_script( CNB_SLUG . '-button-edit' );
        wp_enqueue_script( CNB_SLUG . '-button-edit-icon-color' );
        wp_enqueue_script( CNB_SLUG . '-action-edit' );
        wp_enqueue_script( CNB_SLUG . '-condition-edit' );
        wp_enqueue_style( CNB_SLUG . '-client' );
	    (new Preview())->register_preview_data();
        ?>
        <form class="cnb-container <?php if ( ! $hide_on_modal ) { ?>cnb-validation<?php } ?>"
                action="<?php echo esc_url( admin_url( 'admin-post.php' ) ) ?>" method="post">
            <input type="hidden" name="page" value="call-now-button"/>
            <input type="hidden" name="action"
                    value="<?php echo $button->id === 'new' ? 'cnb_create_' . esc_attr( strtolower( $button->type ) ) . '_button' : 'cnb_update_' . esc_attr( strtolower( $button->type ) ) . '_button' ?>"/>
            <input type="hidden" name="_wpnonce_button"
                    value="<?php echo esc_attr( wp_create_nonce( 'cnb-button-edit' ) ) ?>"/>
            <input type="hidden" name="tab" value="<?php echo esc_attr( $adminFunctions->get_active_tab_name() ) ?>"/>

            <input type="hidden" name="button[id]" value="<?php echo esc_attr( $button->id ) ?>"/>
            <input type="hidden" name="button[type]" value="<?php echo esc_attr( $button->type ) ?>" id="button_type"/>
            <input type="hidden" name="button[active]" value="<?php echo esc_attr( $button->active ) ?>"/>
            <input type="hidden" name="button[domain]" value="<?php echo esc_attr( $default_domain->id ) ?>"/>

            <?php
            $button_edit_table->render_tab_basic_options($button, $hide_on_modal, $default_domain);
            $button_edit_table->render_tab_extra_options($button);
            $button_edit_table->render_tab_visibility($button);
            $button_edit_table->render_tab_visibility_condition_table($button, $hide_on_modal);
            ?>

            <?php submit_button( $submit_button_text ); ?>
            <div class="cnb_advanced_view">
                <p class="description" id="cnb-button-save-inactive-message"><span class="dashicons dashicons-warning"></span> Your button is <code>Inactive</code>, so it will not be visible until set the Button status to <code><strong>Active</strong></code></p>
                <p class="description" id="cnb-button-save-mobile-only-message"><span class="dashicons dashicons-info"></span> Your button is <code>Mobile only</code>, so it will not be visible on desktop. Change this on the <a onclick="cnb_switch_to_tab('visibility')">Visibility</a> tab.</p>
            </div>
        </form>
        <?php
    }

    function render() {
        global $wp_locale, $cnb_domain;

        $cnb_remote = new CnbAppRemote();
        $button    = new CnbButton();
        $button->id = filter_input( INPUT_GET, 'id', @FILTER_SANITIZE_STRING );

        if ( strlen( $button->id ) > 0 && $button->id !== 'new' ) {
            // Only set the Button is we could actually retrieve it
            $button_new = $cnb_remote->get_button( $button->id );
            if ($button_new) {
	            $button = $button_new;
            }
        } elseif ( $button->id === 'new' ) {
            $button->type   = strtoupper( filter_input( INPUT_GET, 'type', @FILTER_SANITIZE_STRING ) );
            $button->domain = $cnb_domain;
        }
        if ( is_wp_error( $button ) || $button->actions === null ) {
            $button->actions = array();
        }

        add_action( 'cnb_header_name', function () use ( $button ) {
            $this->header( $button );
        } );

        $adminFunctions = new CnbAdminFunctions();

        do_action( 'cnb_header' );

        if ( is_wp_error( $button ) ) {
            return;
        }

        // Preview date picker details
        // "w": 0 (for Sunday) through 6 (for Saturday)
        $currentDayOfWeek    = current_time( 'w' );
        $currentHourOfDay    = current_time( 'H' );
        $currentMinuteOfHour = current_time( 'i' );

        // Round to the nearest 15 in an extremely lazy way
        $currentMinuteOfHour = ( $currentMinuteOfHour < 45 ) ? '30' : '45';
        $currentMinuteOfHour = ( $currentMinuteOfHour < 30 ) ? '15' : $currentMinuteOfHour;
        $currentMinuteOfHour = ( $currentMinuteOfHour < 15 ) ? '00' : $currentMinuteOfHour;
        // END Preview date picker details

        $notices = ValidationMessage::get_validation_notices($button);
	    do_action('cnb_validation_notices', $notices, true);
        ?>

        <div class="cnb-two-column-section-preview">
            <div class="cnb-body-column">
                <div class="cnb-body-content">
                    <h2 class="nav-tab-wrapper">
                        <a href="<?php echo esc_url( $this->get_tab_url( $button, 'basic_options' ) ) ?>"
                            class="nav-tab <?php echo esc_attr( $adminFunctions->is_active_tab( 'basic_options' ) ) ?>"
                            data-tab-name="basic_options">Basics</a>
                        <?php if ( $button->id !== 'new' ) { ?>
                            <a href="<?php echo esc_url( $this->get_tab_url( $button, 'extra_options' ) ) ?>"
                                class="nav-tab <?php echo esc_attr( $adminFunctions->is_active_tab( 'extra_options' ) ) ?>"
                                data-tab-name="extra_options">Presentation</a>
                            <a href="<?php echo esc_url( $this->get_tab_url( $button, 'visibility' ) ) ?>"
                                class="nav-tab <?php echo esc_attr( $adminFunctions->is_active_tab( 'visibility' ) ) ?>"
                                data-tab-name="visibility">Visibility</a>
                            <?php if ( $button->type === 'SINGLE' ) { ?>
                                <a href="<?php echo esc_url( $this->get_tab_url( $button, 'scheduler' ) ) ?>"
                                    class="nav-tab <?php echo esc_attr( $adminFunctions->is_active_tab( 'scheduler' ) ) ?>"
                                    data-tab-name="scheduler">Schedule</a>
                            <?php } ?>
                        <?php } else { ?>
                            <a class="nav-tab"><i>Additional options available after saving</i></a>
                        <?php } ?>
                    </h2>
                    <?php $this->render_form( $button, $cnb_domain ); ?>
                </div> <!-- /cnb-body-content -->
            </div> <!-- /cnb-body-column -->
            <div class="cnb-side-column">
                <div id="phone-preview">
                    <div class="phone-outside double">
                        <div class="speaker single"></div>
                        <div class="phone-inside single">
                            <div class="cnb-preview-moment">
                                <label>
                                    <select class="call-now-button-preview-selector"
                                            id="call-now-button-preview-selector-day">
                                        <?php $days = array( 1, 2, 3, 4, 5, 6, 0 );
                                        foreach ( $days as $day ) {
                                            echo '<option value="' . esc_attr( $day ) . '" ' . selected( $currentDayOfWeek, $day ) . '>' . esc_attr( $wp_locale->get_weekday( $day ) ) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </label>

                                <label>
                                    <select class="call-now-button-preview-selector"
                                            id="call-now-button-preview-selector-hour">
                                        <?php
                                        foreach ( range( 0, 23 ) as $number ) {
                                            $number = $number < 10 ? '0' . $number : $number;
                                            echo '<option ' . selected( $currentHourOfDay, $number ) . '>' . esc_html( $number ) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </label>
                                :
                                <label>
                                    <select class="call-now-button-preview-selector"
                                            id="call-now-button-preview-selector-minute">
                                        <?php
                                        foreach ( range( 0, 45, 15 ) as $number ) {
                                            $number = $number < 10 ? '0' . $number : $number;
                                            echo '<option ' . selected( $currentMinuteOfHour, $number ) . '>' . esc_html( $number ) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </label>
                            </div>
                            <div id="cnb-button-preview"></div>
                        </div>
                        <div class="mic double"></div>
                    </div>
                </div>
            </div>
        </div>

        <?php
        do_action( 'cnb_footer' );
    }
}
