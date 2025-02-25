<?php

namespace cnb\admin\api;

use cnb\admin\action\CnbAction;
use cnb\admin\action\CnbActionSchedule;
use cnb\admin\button\CnbButton;
use cnb\admin\button\CnbButtonOptions;
use cnb\admin\condition\CnbCondition;
use cnb\admin\domain\CnbDomain;
use cnb\admin\domain\CnbDomainController;
use cnb\admin\domain\CnbDomainProperties;
use cnb\admin\models\CnbActivation;
use cnb\notices\CnbNotice;
use WP_Error;

class CnbMigration {

    /**
     * @param $cnbActivation CnbActivation
     *
     * @return void
     */
    public function createOrUpdateDomainAndButton( $cnbActivation ) {
        // Patch domain
        $domain                = $this->createOrUpdateDomain( $cnbActivation );
        $cnbActivation->domain = $domain;

        // Only migrate if this domain does not have any buttons
        $button = null;
        if ( $this->shouldCreateButton( $domain ) ) {
            // Create / migrate legacy button
            $button = $this->createButton( $domain );
        }
        $cnbActivation->button  = $button;
        $cnbActivation->success = ! is_wp_error( $domain ) && ! is_wp_error( $button );
    }

    /**
     * Returns true if the account is valid, but this domain has no buttons yet
     *
     * @param $domain CnbDomain
     *
     * @return bool
     */
    private function shouldCreateButton( $domain ) {
        $cnb_remote = new CnbAppRemote();
        if ( is_wp_error( $domain ) ) {
            return false;
        }

        // Check if any buttons are for the current WP domain
        $buttons = $cnb_remote->get_buttons();
        if ( is_wp_error( $buttons ) ) {
            // Could not retrieve Buttons
            return false;
        }
        if ( $buttons === null ) {
            // Could not retrieve Buttons
            return false;
        }

        $cnb_cloud_buttons_this_domain = array_filter( $buttons, function ( $button ) use ( $domain ) {
            return $button->domain->id === $domain->id;
        } );

        return count( $cnb_cloud_buttons_this_domain ) === 0;
    }

    /**
     * Update the CallNowButton Cloud with a domain matching the WordPress Domain
     *
     * @param $cnbActivation CnbActivation
     *
     * @return CnbDomain
     */
    public function createOrUpdateDomain( $cnbActivation ) {
        $cnb_cloud_notifications = array();
        $cnb_remote = new CnbAppRemote();

        $domain = $cnb_remote->get_wp_domain();
        if ( is_wp_error( $domain ) ) {
            $cnbActivation->domain_action = 'created';

            return $this->patchDomain( $cnb_cloud_notifications );
        }
        $cnbActivation->domain_action = 'updated';

        return $this->patchDomain( $cnb_cloud_notifications, $domain );
    }

    /**
     * This can be used to patch the domain matching this WordPress instance's main domain
     *
     * @param $cnb_cloud_notifications array
     * @param $existing_domain CnbDomain|null
     *
     * @return CnbDomain|WP_Error
     */
    private function patchDomain( &$cnb_cloud_notifications, $existing_domain = null ) {
        $cnb_options = get_option( 'cnb' );

        $domain                                   = $existing_domain !== null ? $existing_domain : new CnbDomain();
        $domain->name                             = ( new CnbAppRemote() )->cnb_clean_site_url();
        $domain->trackGA                          = isset( $cnb_options['tracking'] ) && $cnb_options['tracking'] != 0;
        $domain->trackConversion                  = isset( $cnb_options['conversions'] ) && $cnb_options['conversions'] != 0;
        $domain->properties                       = $existing_domain !== null ? $existing_domain->properties : new CnbDomainProperties();
        $domain->properties->allowMultipleButtons = $existing_domain !== null ? $existing_domain->properties->allowMultipleButtons : true;
        $domain->properties->zindex               = ( new CnbDomainController() )->order_to_zindex( $cnb_options['z-index'] );
        $domain->properties->scale                = $cnb_options['zoom'];

        if ( $existing_domain !== null ) {
            return CnbAdminCloud::cnb_update_domain( $cnb_cloud_notifications, $domain );
        }

        return CnbAdminCloud::cnb_create_domain( $cnb_cloud_notifications, $domain );
    }

    /**
     * Update the CallNowButton Cloud with the current settings
     *
     * @param $domain CnbDomain
     *
     * @return CnbButton
     */
    public function createButton( $domain ) {
        $cnb_options = get_option( 'cnb' );

        // We can skip all this if the phonenumber is empty and the button is disabled
        if ( empty( $cnb_options['number'] ) ) {
            return null;
        }

        /**
         * During migration, we don't care about notifications
         */
        $ignore_notifications = array();
        // 1: Create action
        $action = $this->cnb_wp_create_action( $ignore_notifications, $cnb_options );

        // 2: Create condition
        $condition = $this->cnb_wp_create_condition( $ignore_notifications, $cnb_options );

        // 3: Create button
        return $this->cnb_wp_create_button( $ignore_notifications, $domain, $action, $condition, $cnb_options );
    }

    /**
     * Used to convert the "legacy" button into a functional CnbAction
     *
     * @param $cnb_cloud_notifications array
     * @param $options array
     *
     * @return CnbAction|WP_Error
     */
    private function cnb_wp_create_action( &$cnb_cloud_notifications, $options ) {
        $action                       = new CnbAction();
        $action->actionType           = 'PHONE';
        $action->actionValue          = $options['number'];
        $action->labelText            = $options['text'];
        $action->backgroundColor      = $options['color'];
        $action->iconColor            = $options['iconcolor'];
        $action->iconEnabled          = isset( $options['hideIcon'] ) && $options['hideIcon'] == 1 ? false : true;
        $action->schedule             = new CnbActionSchedule();
        $action->schedule->showAlways = true;

        return CnbAdminCloud::cnb_create_action( $cnb_cloud_notifications, $action );
    }

    /**
     * Used to convert the "legacy" button into a functional CnbCondition
     *
     * @param $cnb_cloud_notifications array
     * @param $options array
     *
     * @return CnbCondition|WP_Error|null
     */
    private function cnb_wp_create_condition( &$cnb_cloud_notifications, $options ) {
        // frontpage (if == 1, condition: don't show on /)
        if ( ! isset( $options['frontpage'] ) || $options['frontpage'] != 1 ) {
            return null;
        }

        $condition                = new CnbCondition();
        $condition->conditionType = 'URL';
        $condition->filterType    = 'EXCLUDE';
        $condition->matchType     = 'EXACT';
        $condition->matchValue    = get_home_url();

        return CnbAdminCloud::cnb_create_condition( $cnb_cloud_notifications, $condition );
    }

    /**
     *
     * @param $cnb_cloud_notifications CnbNotice[] (likely ignored, but passed for good measure)
     * @param $domain CnbDomain
     * @param $action CnbAction
     * @param $condition CnbCondition
     * @param $options array the global cnb_options array
     *
     * @return CnbButton|WP_Error
     */
    public function cnb_wp_create_button( &$cnb_cloud_notifications, $domain, $action, $condition, $options ) {
        $appearance  = 'default';
        $type        = 'single';

        switch ( $options['appearance'] ) {
            case 'right':
                $appearance = 'BOTTOM_RIGHT';
                break;
            case 'left':
                $appearance = 'BOTTOM_LEFT';
                break;
            case 'middle':
                $appearance = 'BOTTOM_CENTER';
                break;
            case 'mright':
                $appearance = 'MIDDLE_RIGHT';
                break;
            case 'mleft':
                $appearance = 'MIDDLE_LEFT';
                break;
            case 'tright':
                $appearance = 'TOP_RIGHT';
                break;
            case 'tleft':
                $appearance = 'TOP_LEFT';
                break;
            case 'tmiddle':
                $appearance = 'TOP_CENTER';
                break;

            // The 2 "full" options
            case 'full':
                $appearance = 'BOTTOM_CENTER';
                $type       = 'full';
                break;
            case 'tfull':
                $appearance = 'TOP_CENTER';
                $type       = 'full';
                break;
        }


        $iconBackgroundColor = null;
        $iconColor           = null;

        $conditions = array();
        if ( $condition != null && isset( $condition->id ) ) {
            $conditions[] = $condition;
        }

        $actions = array();
        if ( $action != null && isset( $action->id ) ) {
            $actions[] = $action;

            $iconBackgroundColor = $action->backgroundColor;
            $iconColor           = $action->iconColor;
        }

        $displayMode         = isset( $options['displaymode'] ) ? $options['displaymode'] : 'MOBILE_ONLY';

        // 'active' is based on the status of enabled and if the number has a value
        $has_a_number = ! empty( $action->actionValue );
        $is_enabled   = $options && array_key_exists( 'active', $options ) && $options['active'] == 1;

        $button                               = new CnbButton();
        $button->name                         = 'Button created via WordPress plugin';
        $button->domain                       = $domain;
        $button->active                       = ( $is_enabled && $has_a_number );
        $button->actions                      = $actions;
        $button->conditions                   = $conditions;
        $button->type                         = $type;
        $button->options                      = new CnbButtonOptions();
        $button->options->placement           = $appearance;
        $button->options->iconBackgroundColor = $iconBackgroundColor;
        $button->options->iconColor           = $iconColor;
        $button->options->displayMode         = $displayMode;

        return CnbAdminCloud::cnb_create_button( $cnb_cloud_notifications, $button );
    }
}
