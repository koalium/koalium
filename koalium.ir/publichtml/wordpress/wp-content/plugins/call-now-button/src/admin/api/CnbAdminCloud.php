<?php

namespace cnb\admin\api;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\admin\action\CnbAction;
use cnb\admin\apikey\CnbApiKey;
use cnb\admin\button\CnbButton;
use cnb\admin\condition\CnbCondition;
use cnb\admin\domain\CnbDomain;
use cnb\admin\settings\CnbSettingsController;
use cnb\notices\CnbNotice;
use WP_Error;

class CnbAdminCloud {

    /**
     * @param $api_key string
     *
     * @return boolean true is the key is valid, false if not
     */
    public function is_api_key_valid( $api_key ) {
        global $cnb_api_key;
        $cnb_remote  = new CnbAppRemote();
        $cnb_api_key = $api_key;
        $user_info   = $cnb_remote->get_user();
        $cnb_api_key = null;

        return ! is_wp_error( $user_info );
    }

    /**
     * Called when Cloud Hosting is enabled via settings
     *
     * It tests if the API key is a valid API key or a valid OTT token
     * If it's an OTT, it overwrites the $options['api_key'] with the real token
     * If the key is valid, this function returns the CnbUser ID to use for cloud_use_id
     *
     * @param $options array
     *
     * @return string The ID to use for the Cloud Button
     */
    public function getCloudUseId( $options ) {
        global $cnb_api_key;
        $cnb_remote = new CnbAppRemote();
        // Check if an ID has already been set. If so, use if and continue
        if ( isset( $options['cloud_use_id'] ) && ! empty( $options['cloud_use_id'] ) ) {
            return null;
        }

        // If no API key is passed, there is nothing to do here
        if ( empty( $options['api_key'] ) ) {
            return null;
        }

        // Check if we can talk to the API via a key. If so, use the current user to be safe
        if ( $this->is_api_key_valid( $options['api_key'] ) ) {
            // Set this API key as the global
            $cnb_api_key = $options['api_key'];
            $domain      = $cnb_remote->get_wp_domain();
            $cnb_api_key = null;
            if ( $domain instanceof CnbDomain ) {
                return $domain->id;
            }
        }

        return null;
    }

    /**
     * Since this is only used by the ButtonController (and the update one at that),
     * we ignore notices for Actions and Conditions
     *
     * @param $button CnbButton|WP_Error
     * @param $actions (CnbAction|WP_Error)[]
     * @param $conditions (CnbCondition|WP_Error)[]     *
     * @return array
     */
    public static function cnb_update_button_and_conditions( $button, $actions = array(), $conditions = array() ) {
        $cnb_cloud_notifications = array();
        $ignore_notices          = array();
        $cnb_remote              = new CnbAppRemote();

        // 1: Update the Condition(s)
        $new_conditions = array();
        foreach ( $conditions as $condition ) {
	        if ( is_wp_error( $condition ) ) {
		        $cnb_cloud_notifications[] = CnbAdminCloud::cnb_admin_get_error_message( 'update', 'condition', $condition );
	        } else if ( $condition->delete == 'true' ) {
                // 2.1 Delete now unused Conditions
                $cnb_remote->delete_condition( $condition );
            } else if ( $condition->id === '' ) {
                // 2.2 Create new Conditions
                $new_conditions[] = self::cnb_create_condition( $ignore_notices, $condition );
	        } else if ( $condition->matchValue !== null ) {
		        // 2.3 Update existing Conditions
		        $new_conditions[] = self::cnb_update_condition( $ignore_notices, $condition );
	        } else {
		        // 2.4 No update needed, so pass on received (this happens on `CnbButtonController->update`, has nothing to do with Conditions)
		        $new_conditions[] = $condition;
	        }
        }

        // 2: Update the Action(s)
        $new_actions = array();
        foreach ( $actions as $action ) {
			if ( is_wp_error( $action ) ) {
				$cnb_cloud_notifications[] = CnbAdminCloud::cnb_admin_get_error_message( 'update', 'action', $action );
			} else if ( $action->delete == 'true' ) {
                // 2.1 Delete now unused Action
                $cnb_remote->delete_action( $action );
            } else if ( $action->id === null || $action->id === '' ) {
                // 2.2 Create new Action
                $new_actions[] = self::cnb_create_action( $ignore_notices, $action );
            } else if ( $action->id !== '' && isset( $action->actionType ) ) {
                // 2.3 Update existing Action (but only if it is provided fully, which is why "actionType" is tested for presence
                $new_actions[] = self::cnb_update_action( $ignore_notices, $action );
            } else {
                // 2.4 No update needed, so pass on the action as received
                $new_actions[] = $action;
            }
        }

        $button->actions    = $new_actions;
        $button->conditions = $new_conditions;

		// 3: Do not update anything is there are any validation errors already

		if (! empty( $cnb_cloud_notifications ) ) return $cnb_cloud_notifications;

        // 4: Update the Button
        self::cnb_update_button( $cnb_cloud_notifications, $button );

        return $cnb_cloud_notifications;
    }

    /**
     * @param $cnb_cloud_notifications
     * @param $button CnbButton Single Button object
     *
     * @return CnbButton|WP_Error
     */
    public static function cnb_update_button( &$cnb_cloud_notifications, $button ) {
        $cnb_remote = new CnbAppRemote();
        $result = $cnb_remote->update_button( $button );
        if ( $result instanceof WP_Error ) {
            $message = self::cnb_admin_get_error_message( 'update', 'button', $result );
        } else {
            $message = self::cnb_admin_get_success_message( 'updated', 'button', $result->name );
        }
        $cnb_cloud_notifications[] = $message;
        $cnb_cloud_notifications   = apply_filters( 'cnb_after_save', $cnb_cloud_notifications );

        return $result;
    }

    /**
     * @param $cnb_cloud_notifications array
     * @param $action CnbAction
     *
     * @return CnbAction|WP_Error
     */
    public static function cnb_update_action( &$cnb_cloud_notifications, $action ) {
        $cnb_remote = new CnbAppRemote();
        $result = $cnb_remote->update_action( $action );
        if ( $result instanceof WP_Error ) {
            $message = self::cnb_admin_get_error_message( 'update', 'action', $result );
        } else {
            $message = self::cnb_admin_get_success_message( 'updated', 'action', $result->actionValue );
        }
        $cnb_cloud_notifications[] = $message;

        return $result;
    }

    /**
     * @param array $cnb_cloud_notifications
     * @param CnbCondition $condition
     *
     * @return CnbCondition|WP_Error
     */
    public static function cnb_update_condition( &$cnb_cloud_notifications, $condition ) {
        $cnb_remote = new CnbAppRemote();
        $result = $cnb_remote->update_condition( $condition );
        if ( $result instanceof WP_Error ) {
            $message = self::cnb_admin_get_error_message( 'update', 'condition', $result );
        } else {
            $message = self::cnb_admin_get_success_message( 'updated', 'condition', $result->id );
        }
        $cnb_cloud_notifications[] = $message;

        return $result;
    }

    /**
     * @param $cnb_cloud_notifications array
     * @param $domain CnbDomain
     *
     * @return mixed|WP_Error
     */
    public static function cnb_update_domain( &$cnb_cloud_notifications, $domain ) {
        $cnb_remote = new CnbAppRemote();
        $result = $cnb_remote->update_domain( $domain );
        if ( $result instanceof WP_Error ) {
            $message = self::cnb_admin_get_error_message( 'update', 'domain', $result );
        } else {
            $message = self::cnb_admin_get_success_message( 'updated', 'domain', $result->name );
        }
        $cnb_cloud_notifications[] = $message;

        return $result;
    }

    /**
     * @param $cnb_cloud_notifications
     * @param $button CnbButton Single Button object
     *
     * @return CnbButton|WP_Error The created Button
     */
    public static function cnb_create_button( &$cnb_cloud_notifications, $button ) {
        $cnb_remote = new CnbAppRemote();
        $result = $cnb_remote->create_button( $button );
        if ( $result instanceof WP_Error ) {
            $message = self::cnb_admin_get_error_message( 'create', 'button', $result );
        } else {
            $message = self::cnb_admin_get_success_message( 'created', 'button', $result->name );
        }
        $cnb_cloud_notifications[] = $message;
        $cnb_cloud_notifications   = apply_filters( 'cnb_after_save', $cnb_cloud_notifications );

        return $result;
    }

    /**
     * @param $cnb_cloud_notifications array
     * @param $button CnbButton
     *
     * @return CnbButton|WP_Error
     */
    public static function cnb_delete_button( &$cnb_cloud_notifications, $button ) {
        $cnb_remote = new CnbAppRemote();
        $result = $cnb_remote->delete_button( $button );
        if ( $result instanceof WP_Error ) {
            $message = self::cnb_admin_get_error_message( 'delete', 'button', $result, 'with ID <code>' . esc_html( $button->id ) . '</code>' );
        } else {
            $message = self::cnb_admin_get_success_message( 'deleted', 'button', $result->name );
        }
        $cnb_cloud_notifications[] = $message;
        $cnb_cloud_notifications   = apply_filters( 'cnb_after_save', $cnb_cloud_notifications );

        return $result;
    }

    /**
     * @param $cnb_cloud_notifications array
     * @param $domain CnbDomain
     *
     * @return CnbDomain|WP_Error
     */
    public static function cnb_delete_domain( &$cnb_cloud_notifications, $domain ) {
        $cnb_remote = new CnbAppRemote();
        $result = $cnb_remote->delete_domain( $domain );
        if ( $result instanceof WP_Error ) {
            $message = self::cnb_admin_get_error_message( 'delete', 'domain', $result, 'with ID <code>' . esc_html( $domain->id ) . '</code>' );
        } else {
            $message = self::cnb_admin_get_success_message( 'deleted', 'domain', $result->name );
        }
        $cnb_cloud_notifications[] = $message;

        return $result;
    }

    /**
     * @param $cnb_cloud_notifications array
     * @param $action CnbAction The action to delete
     *
     * @return CnbAction|WP_Error
     */
    public static function cnb_delete_action( &$cnb_cloud_notifications, $action ) {
        $cnb_remote = new CnbAppRemote();
        $result = $cnb_remote->delete_action( $action );
        if ( $result instanceof WP_Error ) {
            $message = self::cnb_admin_get_error_message( 'delete', 'action', $result, 'with ID <code>' . esc_html( $action->id ) . '</code>' );
        } else {
            $message = self::cnb_admin_get_success_message( 'deleted', 'action', $result->actionValue );
        }
        $cnb_cloud_notifications[] = $message;

        return $result;
    }

    /**
     * @param $cnb_cloud_notifications array
     * @param $condition CnbCondition
     *
     * @return CnbCondition|WP_Error
     */
    public static function cnb_delete_condition( &$cnb_cloud_notifications, $condition ) {
        $cnb_remote = new CnbAppRemote();
        $result = $cnb_remote->delete_condition( $condition );
        if ( $result instanceof WP_Error ) {
            $message = self::cnb_admin_get_error_message( 'delete', 'condition', $result, 'with ID <code>' . esc_html( $condition->id ) . '</code>' );
        } else {
            $message = self::cnb_admin_get_success_message( 'deleted', 'condition', $result->id );
        }
        $cnb_cloud_notifications[] = $message;

        return $result;
    }

    /**
     * @param $cnb_cloud_notifications array
     * @param $apikey CnbApiKey
     *
     * @return CnbApiKey|WP_Error
     */
    public static function cnb_delete_apikey( &$cnb_cloud_notifications, $apikey ) {
        $cnb_remote = new CnbAppRemote();
        $result = $cnb_remote->delete_apikey( $apikey );
        if ( $result instanceof WP_Error ) {
            $message = self::cnb_admin_get_error_message( 'delete', 'apikey', $result, 'with ID <code>' . esc_html( $apikey->id ) . '</code>' );
        } else {
            $message = self::cnb_admin_get_success_message( 'deleted', 'apikey', $apikey->id );
        }
        $cnb_cloud_notifications[] = $message;

        return $result;
    }

    /**
     * @param $cnb_cloud_notifications CnbNotice[]
     * @param $domain CnbDomain
     *
     * @return CnbDomain|WP_Error
     */
    public static function cnb_create_domain( &$cnb_cloud_notifications, $domain ) {
        $cnb_remote = new CnbAppRemote();
        $result = $cnb_remote->create_domain( $domain );
        if ( $result instanceof WP_Error ) {
            $message = self::cnb_admin_get_error_message( 'create', 'domain', $result );
        } else {
            $message = self::cnb_admin_get_success_message( 'created', 'domain', $result->name );
        }
        $cnb_cloud_notifications[] = $message;

        return $result;
    }

    /**
     * @param $cnb_cloud_notifications array
     * @param $action CnbAction
     *
     * @return CnbAction|WP_Error
     */
    public static function cnb_create_action( &$cnb_cloud_notifications, $action ) {
        $cnb_remote = new CnbAppRemote();
        $result = $cnb_remote->create_action( $action );
        if ( $result instanceof WP_Error ) {
            $message = self::cnb_admin_get_error_message( 'create', 'action', $result );
        } else {
            $message = self::cnb_admin_get_success_message( 'created', 'action', $result->actionType );
        }
        $cnb_cloud_notifications[] = $message;

        return $result;
    }

    /**
     * @param $cnb_cloud_notifications array
     * @param $condition CnbCondition
     *
     * @return CnbCondition|WP_Error
     */
    public static function cnb_create_condition( &$cnb_cloud_notifications, $condition ) {
        $cnb_remote = new CnbAppRemote();
        $result = $cnb_remote->create_condition( $condition );
        if ( $result instanceof WP_Error ) {
            $message = self::cnb_admin_get_error_message( 'create', 'condition', $result );
        } else {
            $message = self::cnb_admin_get_success_message( 'created', 'condition', $result->filterType );
        }
        $cnb_cloud_notifications[] = $message;

        return $result;
    }

    /**
     * @param $cnb_cloud_notifications array
     * @param $apikey CnbApiKey
     *
     * @return CnbApiKey|WP_Error
     */
    public static function cnb_create_apikey( &$cnb_cloud_notifications, $apikey ) {
        $cnb_remote = new CnbAppRemote();
        $result = $cnb_remote->create_apikey( $apikey );
        if ( $result instanceof WP_Error ) {
            $message = self::cnb_admin_get_error_message( 'create', 'apikey', $result );
        } else {
            $message = new CnbNotice( 'success', '<p>Your new API key for <strong>' . esc_html( $result->name ) . '</strong> is <strong><code>' . esc_html( $result->key ) . '</code></strong>. This will not be shown again!</p>' );
        }
        $cnb_cloud_notifications[] = $message;

        return $result;
    }

    /**
     * @param $result WP_Error The WP_Error that was thrown
     *
     * @return string HTML with additional information (Content has been escaped already)
     */
    public static function cnb_admin_get_error_message_details( $result ) {
        if ( ! ( $result instanceof WP_Error ) ) {
            return '';
        }

        $error_codes = $result->get_error_codes();
        $codes       = '<p>Technical details:</p>';
        foreach ( $error_codes as $error_code ) {
            if ( $result->get_error_message( $error_code ) === '' ) {
                $codes .= '<p>Error code: <code>' . esc_html( $error_code ) . '.</code></p>';
            } else {
                $codes .= '<p>Error code: <code>' . esc_html( $error_code ) . '</code>, message: <code>' . esc_html( $result->get_error_message( $error_code ) ) . '</code></p>';
            }
        }

        $additional_details = '';
        // Get detail message if possible
        $details = $result->get_error_data( $result->get_error_code() );
        if ( $details ) {
            $details_obj = json_decode( $details );
            if ( json_last_error() == JSON_ERROR_NONE ) {
                if ( $details_obj->message ) {
                    $additional_details .= '<p>Additional details: <strong>' . esc_html( $details_obj->message ) . '</strong></p>';
                }
            } else {
                $additional_details .= '<p>Additional details: <strong>' . esc_html( $details ) . '</strong></p>';
            }
        }

        return $codes . $additional_details;

    }

    /**
     * NOTE: Currently only be called via button-overview, for a specific listing use case
     *
     * @param $button CnbButton The button array as created by the button-overview table class
     * @param $max int (optional) The maximum amount of Actions to retrieve
     *
     * @return array Array of Action objects, between 0 and $max items
     */
    public static function cnb_wp_get_actions_for_button( $button, $max = 3 ) {
        $count = 0;
        if ( $button->actions ) {
            $count = count( $button->actions );
        }
        $actionCount = min( $count, $max );
        $result      = array();
        if ( ! $button || $max <= 0 ) {
            return $result;
        }

        for ( $i = 0; $i < $actionCount; $i ++ ) {
            $result[] = $button->actions[ $i ];
        }

        return $result;
    }

    /**
     * @param $verb string one of created, updated, deleted
     * @param $type string one of button, action, condition
     * @param $result WP_Error The WP_Error that was thrown
     * @param $extra_info string Allows for some extra details to be added to the error message.
     *                          This contains HTML and should be escaped already when passed through.
     *
     * @return CnbNotice A WordPress error notice with all details filled out (Content has been escaped already)
     */
    public static function cnb_admin_get_error_message( $verb, $type, $result, $extra_info = '' ) {
        $error_details = self::cnb_admin_get_error_message_details( $result );
        $message       = '<p>We could not ' . $verb . ' the ' . $type . ' ' . $extra_info . ' :-(.</p>' . $error_details;

        return new CnbNotice( 'error', $message );
    }

    /**
     * @param $verb string one of created, updated, deleted
     * @param $type string one of button, action, condition
     * @param $id string The identifier of the $type (could be an actual ID, a name, etc)
     *
     * @return CnbNotice A WordPress success notice with all details filled out
     */
    public static function cnb_admin_get_success_message( $verb, $type, $id ) {
        $advanced    = '';
        if ( CnbSettingsController::is_advanced_view() ) {
            $advanced = ' at <strong>' . esc_html( CnbAppRemote::cnb_get_api_base() ) . '</strong>';
        }
        $message             = '<p>Your ' . $type . ' <strong>' . esc_html( $id ) . '</strong> has been ' . $verb . $advanced . '</p>';
        $notice              = new CnbNotice( 'success', $message );
        $notice->dismissable = true;

        return $notice;
    }
}
