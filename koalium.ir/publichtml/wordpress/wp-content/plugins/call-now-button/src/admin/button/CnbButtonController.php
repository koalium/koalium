<?php

namespace cnb\admin\button;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\admin\action\CnbAction;
use cnb\admin\api\CnbAdminCloud;
use cnb\admin\api\CnbAppRemote;
use cnb\admin\condition\CnbCondition;
use cnb\CnbHeaderNotices;
use cnb\notices\CnbAdminNotices;
use cnb\notices\CnbNotice;
use cnb\utils\CnbUtils;
use WP_Error;

class CnbButtonController {

    /**
     * This is called to update the button
     * via `call-now-button.php#cnb_create_<type>_button`
     */
    public function create() {
        do_action( 'cnb_init', __METHOD__ );
        /**
         * @param $button CnbButton|WP_Error
         * @param $actions (CnbAction|WP_Error)[]
         *
         * @return void
         */
        $inner = function ( $button, $actions ) {
            // Do the processing
            $cnb_cloud_notifications = array();
            $new_button              = CnbAdminCloud::cnb_create_button( $cnb_cloud_notifications, $button );

			if ($actions != null) {
				CnbAdminCloud::cnb_update_button_and_conditions( $new_button, $actions );
			}

	        // redirect the user to the appropriate page
            $tab          = filter_input( INPUT_POST, 'tab', @FILTER_SANITIZE_STRING );
            $transient_id = (new CnbHeaderNotices())->generate_notice_id();
            set_transient( $transient_id, $cnb_cloud_notifications, HOUR_IN_SECONDS );

            $new_button_type = null;
            $new_button_id   = null;
            if ( $new_button instanceof CnbButton ) {
                $new_button_type = strtolower( $new_button->type );
                $new_button_id   = $new_button->id;
            }

            // Create link
            $url           = admin_url( 'admin.php' );
            $redirect_link =
                add_query_arg(
                    array(
                        'page'     => 'call-now-button',
                        'action'   => 'edit',
                        'type'     => $new_button_type,
                        'id'       => $new_button_id,
                        'tid'      => $transient_id,
                        'tab'      => $tab,
                        '_wpnonce' => wp_create_nonce( $transient_id ),
                    ),
                    $url );
            $redirect_url  = esc_url_raw( $redirect_link );
            do_action( 'cnb_finish' );
            wp_safe_redirect( $redirect_url );
            exit;
        };
        $this->create_and_update( $inner );
        do_action( 'cnb_finish' );
    }

	/**
	 * This is called to update the button
	 * via `call-now-button.php#cnb_create_<type>_button`
	 */
	public function create_ajax() {
		do_action( 'cnb_init', __METHOD__ );
		/**
		 * @param $button CnbButton
		 * @param $actions CnbAction[]
		 *
		 * @return void
		 */
		$inner = function ( $button, $actions ) {
			// Do the processing
			$cnb_cloud_notifications = array();
			$new_button              = CnbAdminCloud::cnb_create_button( $cnb_cloud_notifications, $button );

			if ($actions != null) {
				CnbAdminCloud::cnb_update_button_and_conditions( $new_button, $actions );
			}

			// redirect the user to the appropriate page
			$tab          = filter_input( INPUT_POST, 'tab', @FILTER_SANITIZE_STRING );
			$transient_id = (new CnbHeaderNotices())->generate_notice_id();
			set_transient( $transient_id, $cnb_cloud_notifications, HOUR_IN_SECONDS );

			$new_button_type = null;
			$new_button_id   = null;
			if ( $new_button instanceof CnbButton ) {
				$new_button_type = strtolower( $new_button->type );
				$new_button_id   = $new_button->id;
			}

			// Create link
			$url           = admin_url( 'admin.php' );
			$redirect_link =
				add_query_arg(
					array(
						'page'     => 'call-now-button',
						'action'   => 'edit',
						'type'     => $new_button_type,
						'id'       => $new_button_id,
						'tid'      => $transient_id,
						'tab'      => $tab,
						'_wpnonce' => wp_create_nonce( $transient_id ),
					),
					$url );
			do_action( 'cnb_finish' );
			wp_send_json(array( 'redirect_link' => $redirect_link ));
			exit;
		};

		$this->create_and_update( $inner );
		do_action( 'cnb_finish' );
	}

    /**
     * This is called to update the button
     * via `call-now-button.php#cnb_update_<type>_button`
     */
    public function update() {
        do_action( 'cnb_init', __METHOD__ );
        /**
         * @param $button CnbButton|WP_Error
         * @param $actions (CnbAction|WP_Error)[]
         * @param $conditions (CnbCondition|WP_Error)[]
         *
         * @return void
         */
        $inner = function ( $button, $actions, $conditions ) {
            // do the processing
            $result = CnbAdminCloud::cnb_update_button_and_conditions( $button, $actions, $conditions );

            // redirect the user to the appropriate page
            $tab          = filter_input( INPUT_POST, 'tab', @FILTER_SANITIZE_STRING );
            $transient_id = (new CnbHeaderNotices())->generate_notice_id();
            set_transient( $transient_id, $result, HOUR_IN_SECONDS );

            // Create link
            $url           = admin_url( 'admin.php' );
            $redirect_link =
                add_query_arg(
                    array(
                        'page'     => 'call-now-button',
                        'action'   => 'edit',
                        'type'     => strtolower( $button->type ),
                        'id'       => $button->id,
                        'tid'      => $transient_id,
                        'tab'      => $tab,
                        '_wpnonce' => wp_create_nonce( $transient_id ),
                    ),
                    $url );
            $redirect_url  = esc_url_raw( $redirect_link );
            do_action( 'cnb_finish' );
            wp_safe_redirect( $redirect_url );
            exit;
        };
        $this->create_and_update( $inner );
        do_action( 'cnb_finish' );
    }

    private function create_and_update( $closure ) {
        $nonce = filter_input( INPUT_POST, '_wpnonce_button', @FILTER_SANITIZE_STRING );
        if ( isset( $_REQUEST['_wpnonce_button'] ) && wp_verify_nonce( $nonce, 'cnb-button-edit' ) ) {

            // sanitize the input
            $button     = filter_input(
                INPUT_POST,
                'button',
                @FILTER_SANITIZE_STRING,
                FILTER_REQUIRE_ARRAY | FILTER_FLAG_NO_ENCODE_QUOTES );
            $actions    = filter_input(
                INPUT_POST,
                'actions',
                @FILTER_SANITIZE_STRING,
                FILTER_REQUIRE_ARRAY | FILTER_FLAG_NO_ENCODE_QUOTES );
            $conditions = filter_input(
                INPUT_POST,
                'conditions',
                @FILTER_SANITIZE_STRING,
                FILTER_REQUIRE_ARRAY | FILTER_FLAG_NO_ENCODE_QUOTES );

            if ( $conditions === null ) {
                $conditions = array();
            }

            /** @var CnbAction[] $processed_actions */
            $processed_actions = array();
            if ( is_array( $actions ) ) {
                foreach ( $actions as $action ) {
                    $processed_actions[] = CnbAction::fromObject( $action );
                }
            }

            /** @var CnbCondition[] $processed_conditions */
            $processed_conditions = array();
            if ( is_array( $conditions ) ) {
                foreach ( $conditions as $condition ) {
                    $processed_conditions[] = CnbCondition::fromObject( $condition );
                }
            }

            $button['id']         = isset($button['id']) && $button['id'] !== 'new' ? $button['id'] : null;
            $button['actions']    = $processed_actions;
            $button['conditions'] = $processed_conditions;
            $processed_button     = CnbButton::fromObject( $button );

            // processing
            $closure( $processed_button, $processed_actions, $processed_conditions );
            // end processing
        } else {
            do_action( 'cnb_finish' );
            wp_die( esc_html__( 'Invalid nonce specified' ), esc_html__( 'Error' ), array(
                'response'  => 403,
                'back_link' => true,
            ) );
        }
    }

    /**
     * In addition to delete(), this also handles the bulk enable/disable action.
     *
     * This is very similar to the <code>delete()</code> function above.
     *
     * This always has to come via a $_POST request (specifically, via admin-post.php),
     * so this should end in a redirect (or an error via wp_die)
     *
     * Big differences are:
     * - This handles multiple IDs, versus 1
     * - Instead of rendering the Notice, is it stored and the user redirected
     *
     * nonce name via WP_List_Table = bulk-{plural}
     * so in this case: bulk-cnb_list_buttons
     *
     * @return void
     */
    public function handle_bulk_actions() {
        do_action( 'cnb_init', __METHOD__ );
        $cnb_utils      = new CnbUtils();
        $cnb_remote     = new CnbAppRemote();
        $nonce          = $cnb_utils->get_post_val( '_wpnonce' );
        $action         = 'bulk-cnb_list_buttons';
        $nonce_verified = wp_verify_nonce( $nonce, $action );

        if ( $nonce_verified ) {
            $buttonIds      = filter_input( INPUT_POST, 'cnb_list_button', @FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY );
            $current_action = filter_input( INPUT_POST, 'bulk-action', @FILTER_SANITIZE_STRING );

            switch ( $current_action ) {
                case 'enable':
                case 'disable':
                    $cnb_cloud_notifications = array();
                    foreach ( $buttonIds as $buttonId ) {
                        $button         = $cnb_remote->get_button( $buttonId );
                        $button->active = $current_action === 'enable';
                        CnbAdminCloud::cnb_update_button( $cnb_cloud_notifications, $button );
                    }
                    $action_name = $current_action . 'd';

                    // Create notice for link (and yes - we ignore the content of $cnb_cloud_notifications here, we just use it to count)
                    $notice = new CnbNotice( 'success', '<p>' . count( $cnb_cloud_notifications ) . ' Buttons ' . $action_name . '.</p>' );
                    break;
                case 'delete':
                    foreach ( $buttonIds as $buttonId ) {
                        $button     = new CnbButton();
                        $button->id = $buttonId;
                        $cnb_remote->delete_button( $button );
                    }
                    $notice = new CnbNotice( 'success', '<p>' . count( $buttonIds ) . ' Button(s) deleted.</p>' );
                    break;
                default:
                    $notice = null;
            }
            $transient_id = null;
            if ( $notice ) {
                $transient_id = (new CnbHeaderNotices())->generate_notice_id();
                set_transient( $transient_id, array( $notice ), HOUR_IN_SECONDS );
            }

            // Create link
            $url           = admin_url( 'admin.php' );
            $redirect_link =
                add_query_arg(
                    array(
                        'page'     => 'call-now-button',
                        'tid'      => $transient_id,
                        '_wpnonce' => wp_create_nonce( $transient_id ),
                    ),
                    $url );
            $redirect_url  = esc_url_raw( $redirect_link );
            do_action( 'cnb_finish' );
            wp_safe_redirect( $redirect_url );
            exit;
        } else {
            do_action( 'cnb_finish' );
            wp_die(
                esc_html__( 'Invalid nonce specified' ),
                esc_html__( 'Error' ),
                array(
                    'response'  => 403,
                    'back_link' => true,
                )
            );
        }
    }

    /**
     * Quick action, so actually render the Notice
     * @return void
     */
    public function enable_disable() {
        $cnb_utils = new CnbUtils();
        $cnb_remote = new CnbAppRemote();
        // "enable" or "disable"
        $action         = $cnb_utils->get_query_val( 'action', null );
        $id             = $cnb_utils->get_query_val( 'id', null );
        $nonce          = $cnb_utils->get_query_val( '_wpnonce', null );
        $nonce_verified = wp_verify_nonce( $nonce, 'cnb_enable_disable_button' );
        if ( $nonce_verified ) {
            $active      = $action === 'enable';
            $action_verb = $active ? 'enable' : 'disable';
            $action_name = $action_verb . 'd';

            $button         = $cnb_remote->get_button( $id );
            $button->active = $active;

            $updated_button = $cnb_remote->update_button( $button );

            if ( ! is_wp_error( $updated_button ) ) {
                $notice = new CnbNotice( 'success', '<p>Button <strong>' . esc_html( $updated_button->name ) . '</strong> ' . $action_name . '.</p>', true );
            } else {
                $notice = CnbAdminCloud::cnb_admin_get_error_message( $action_verb, 'button', $updated_button );
            }
            CnbAdminNotices::get_instance()->notice( $notice );
        }
    }

	/**
	 * Via the quick action "Delete" (called admin_post_cnb_delete_button), to be able to delete a Button.
	 *
	 * Since "admin-post.php" is used, that means there is no output (and we can/should safely redirect to the Button overview after deleting).
	 *
	 * @return void
	 */
	public function delete() {
		do_action( 'cnb_init', __METHOD__ );
		$cnb_utils = new CnbUtils();
		$id        = $cnb_utils->get_query_val( 'id', null );
		$nonce     = $cnb_utils->get_query_val( '_wpnonce', null );
		$action    = 'cnb_delete_button';

		if ( ! wp_verify_nonce( $nonce, $action ) ) {
			do_action( 'cnb_finish' );
			wp_die( esc_html__( 'Invalid nonce specified' ), esc_html__( 'Error' ), array(
				'response'  => 403,
				'back_link' => true,
			) );
		}

		$cnb_cloud_notifications = array();
		$button                  = new CnbButton();
		$button->id              = $id;
		CnbAdminCloud::cnb_delete_button( $cnb_cloud_notifications, $button );

		// Save notices
		$transient_id = (new CnbHeaderNotices())->generate_notice_id();
		set_transient( $transient_id, $cnb_cloud_notifications, HOUR_IN_SECONDS );

		// Create link
		$redirect_link =
			add_query_arg(
				array(
					'page'     => 'call-now-button',
					'tid'      => $transient_id,
					'_wpnonce' => wp_create_nonce( $transient_id ),
				),
				admin_url( 'admin.php' ) );
		$redirect_url  = esc_url_raw( $redirect_link );
		do_action( 'cnb_finish' );
		wp_safe_redirect( $redirect_url );
	}
}
