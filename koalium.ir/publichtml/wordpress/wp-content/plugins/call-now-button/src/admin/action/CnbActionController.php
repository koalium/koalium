<?php

namespace cnb\admin\action;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\admin\api\CnbAdminCloud;
use cnb\admin\api\CnbAppRemote;
use cnb\admin\models\CnbUser;
use cnb\CnbHeaderNotices;
use cnb\notices\CnbNotice;
use cnb\utils\CnbUtils;
use WP_Error;

class CnbActionController {

    /**
     * Used by the Ajax call inside button-overview
     *
     * @param $action_id string
     * @param $cnb_cloud_notifications array
     *
     * @return CnbAction|WP_Error|null
     */
    function deleteWithId( $action_id, &$cnb_cloud_notifications = array() ) {
        if ( ( new CnbUtils() )->cnb_check_ajax_referer( 'cnb_delete_action' ) ) {
            $action     = new CnbAction();
            $action->id = $action_id;

            return CnbAdminCloud::cnb_delete_action( $cnb_cloud_notifications, $action );
        }

        return null;
    }

    /**
     * The caller should call this via `jQuery.post(ajaxurl, data)`
     *
     * @return void
     */
    public function delete_ajax() {
        do_action( 'cnb_init', __METHOD__ );
        $cnb_utils  = new CnbUtils();
        $cnb_remote = new CnbAppRemote();
        // Action ID
        $action_id = $cnb_utils->get_post_val( 'id', null );
        $button_id = $cnb_utils->get_post_val( 'bid', null );

        $result     = $this->deleteWithId( $action_id );
        // Instead of sending just the actual result (which is currently ignored anyway)
        // We sent both the result and an updated button so the preview code can re-render the button
        $return = array(
            'result' => $result,
            'button' => $cnb_remote->get_button( $button_id )->toArray( false ),
        );
        wp_send_json( $return );
        do_action( 'cnb_finish' );
        wp_die();
    }

	/**
	 * Via the quick action "Delete" (called admin_post_cnb_delete_action), to be able to delete an Action.
	 *
	 * Since "admin-post.php" is used, that means there is no output (and we can/should safely redirect to the Button overview after deleting).
	 *
	 * @return void
	 */
	public function delete() {
		$cnb_utils = new CnbUtils();
		$id        = $cnb_utils->get_query_val( 'id', null );
		$nonce     = $cnb_utils->get_query_val( '_wpnonce', null );
		$action    = 'cnb_delete_action';

		if ( ! wp_verify_nonce( $nonce, $action ) ) {
			do_action( 'cnb_finish' );
			wp_die( esc_html__( 'Invalid nonce specified' ), esc_html__( 'Error' ), array(
				'response'  => 403,
				'back_link' => true,
			) );
		}

		$cnb_cloud_notifications = array();
		$action                  = new CnbAction();
		$action->id              = $id;
		CnbAdminCloud::cnb_delete_action( $cnb_cloud_notifications, $action );

		// Save notices
		$transient_id = (new CnbHeaderNotices())->generate_notice_id();
		set_transient( $transient_id, $cnb_cloud_notifications, HOUR_IN_SECONDS );

		// Create link
		$redirect_link =
			add_query_arg(
				array(
					'page'     => 'call-now-button-actions',
					'tid'      => $transient_id,
					'_wpnonce' => wp_create_nonce( $transient_id ),
				),
				admin_url( 'admin.php' ) );
		$redirect_url  = esc_url_raw( $redirect_link );
		do_action( 'cnb_finish' );
		wp_safe_redirect( $redirect_url );
	}

    /**
     * This is called to create an Action
     * via `call-now-button.php#cnb_create_action`
     */
    public function create() {
        do_action( 'cnb_init', __METHOD__ );
        $cnb_cloud_notifications = array();
        $nonce                   = filter_input( INPUT_POST, '_wpnonce', @FILTER_SANITIZE_STRING );
        $action                  = 'cnb-action-edit';
        $nonce_verified          = wp_verify_nonce( $nonce, $action );
        $cbn_utils               = new CnbUtils();
        $cnb_remote              = new CnbAppRemote();
        if ( $nonce_verified ) {
            $actions   = filter_input(
                INPUT_POST,
                'actions',
                @FILTER_SANITIZE_STRING,
                FILTER_REQUIRE_ARRAY | FILTER_FLAG_NO_ENCODE_QUOTES );
            $action_id = filter_input( INPUT_POST, 'action_id', @FILTER_SANITIZE_STRING );
            $action    = CnbAction::fromObject( $actions[ $action_id ] );

            // Do the processing
            $new_action    = CnbAdminCloud::cnb_create_action( $cnb_cloud_notifications, $action );
            $new_action_id = $new_action->id;

            $bid = filter_input( INPUT_POST, 'bid', @FILTER_SANITIZE_STRING );
            if ( ! empty( $bid ) ) {
                // Tie this new Action to the provided Button
                $button = $cnb_remote->get_button( $bid );
                if ( ! ( $button instanceof WP_Error ) ) {
                    $button->actions[] = $new_action;

                    CnbAdminCloud::cnb_update_button( $cnb_cloud_notifications, $button );
                } else {
                    $message                   = CnbAdminCloud::cnb_admin_get_error_message( 'create', 'action', $button );
                    $cnb_cloud_notifications[] = $message;
                }
            }

            // redirect the user to the appropriate page
            $transient_id = (new CnbHeaderNotices())->generate_notice_id();
            set_transient( $transient_id, $cnb_cloud_notifications, HOUR_IN_SECONDS );

            // Create link
            $bid = $cbn_utils->get_query_val( 'bid', null );
            $url = admin_url( 'admin.php' );

            if ( ! empty( $bid ) ) {
                $redirect_link =
                    add_query_arg(
                        array(
                            'page'      => 'call-now-button',
                            'action'    => 'edit',
                            'id'        => $bid,
                            'tid'       => $transient_id,
	                        '_wpnonce' => wp_create_nonce( $transient_id ),
                        ),
                        $url );
            } else {
                $redirect_link =
                    add_query_arg(
                        array(
                            'page'   => 'call-now-button-actions',
                            'action' => 'edit',
                            'id'     => $new_action_id,
                            'tid'    => $transient_id,
                            'bid'    => $bid,
                            '_wpnonce' => wp_create_nonce( $transient_id ),
                        ),
                        $url );
            }
            $redirect_url = esc_url_raw( $redirect_link );
            do_action( 'cnb_finish' );
            wp_safe_redirect( $redirect_url );
            exit;
        } else {
            do_action( 'cnb_finish' );
            wp_die( esc_html__( 'Invalid nonce specified' ), esc_html__( 'Error' ), array(
                'response'  => 403,
                'back_link' => true,
            ) );
        }
    }

    public function update() {
        do_action( 'cnb_init', __METHOD__ );
        $nonce          = filter_input( INPUT_POST, '_wpnonce', @FILTER_SANITIZE_STRING );
        $action         = 'cnb-action-edit';
        $nonce_verified = wp_verify_nonce( $nonce, $action );
        $cnb_utils      = new CnbUtils();
        if ( $nonce_verified ) {
            // sanitize the input
            $actions                 = filter_input(
                INPUT_POST,
                'actions',
                @FILTER_SANITIZE_STRING,
                FILTER_REQUIRE_ARRAY | FILTER_FLAG_NO_ENCODE_QUOTES );
            $result                  = '';
            $cnb_cloud_notifications = array();

            foreach ( $actions as $action ) {
                $processed_action = CnbAction::fromObject( $action );
				if ( is_wp_error( $processed_action ) ) {
					$cnb_cloud_notifications[] = CnbAdminCloud::cnb_admin_get_error_message( 'update', 'action', $processed_action );
				} else {
					// do the processing
					$result = CnbAdminCloud::cnb_update_action( $cnb_cloud_notifications, $processed_action );
				}
            }

            // redirect the user to the appropriate page
            $transient_id = (new CnbHeaderNotices())->generate_notice_id();
            set_transient( $transient_id, $cnb_cloud_notifications, HOUR_IN_SECONDS );

            // Create link
            $bid = $cnb_utils->get_query_val( 'bid', null );
            $url = admin_url( 'admin.php' );
            if ( ! empty( $bid ) ) {
                $redirect_link =
                    add_query_arg(
                        array(
                            'page'     => 'call-now-button',
                            'action'   => 'edit',
                            'id'       => $bid,
                            'tid'      => $transient_id,
                            '_wpnonce' => wp_create_nonce( $transient_id ),
                        ),
                        $url );
            } else {
                $redirect_link =
                    add_query_arg(
                        array(
                            'page'     => CNB_SLUG . '-actions',
                            'action'   => 'edit',
                            'id'       => $result->id,
                            'tid'      => $transient_id,
                            'bid'      => $bid,
                            '_wpnonce' => wp_create_nonce( $transient_id ),
                        ),
                        $url );
            }
            $redirect_url = esc_url_raw( $redirect_link );
            do_action( 'cnb_finish' );
            wp_safe_redirect( $redirect_url );
            exit;
        } else {
            do_action( 'cnb_finish' );
            wp_die( esc_html__( 'Invalid nonce specified' ), esc_html__( 'Error' ), array(
                'response'  => 403,
                'back_link' => true,
            ) );
        }
    }

    /**
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
     * so in this case: bulk-cnb_list_actions
     *
     * @return void
     */
    public function handle_bulk_actions() {
        do_action( 'cnb_init', __METHOD__ );
        $cnb_utils      = new CnbUtils();
        $nonce          = $cnb_utils->get_post_val( '_wpnonce' );
        $action         = 'bulk-cnb_list_actions';
        $nonce_verified = wp_verify_nonce( $nonce, $action );
        if ( $nonce_verified ) {
            $actionIds = filter_input( INPUT_POST, 'cnb_list_action', @FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY );
            if ( $cnb_utils->get_post_val( 'bulk-action' ) === 'delete' ) {
                $cnb_cloud_notifications = array();
                foreach ( $actionIds as $actionId ) {
                    $cnbAction     = new CnbAction();
                    $cnbAction->id = $actionId;
                    CnbAdminCloud::cnb_delete_action( $cnb_cloud_notifications, $cnbAction );
                }
                // Create notice for link (and yes - we ignore the content of $cnb_cloud_notifications here, we just use it to count)
                $notice       = new CnbNotice( 'success', '<p>' . count( $cnb_cloud_notifications ) . ' Action(s) deleted.</p>' );
                $transient_id = (new CnbHeaderNotices())->generate_notice_id();
                set_transient( $transient_id, array( $notice ), HOUR_IN_SECONDS );

                // Create link
                $url           = admin_url( 'admin.php' );
                $redirect_link =
                    add_query_arg(
                        array(
                            'page'     => 'call-now-button-actions',
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
                    esc_html__( 'Unknown Bulk action specified' ),
                    esc_html__( 'Cannot process Bulk action' ),
                    array(
                        'response'  => 403,
                        'link_text' => esc_html( 'Go back to the Actions overview' ),
                        'link_url'  => esc_url_raw( admin_url( 'admin.php' ) . '?page=' . CNB_SLUG . '-actions' ),
                    )
                );
            }
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
	 * Only users with the CHAT_USER role can create CHAT actions
	 *
	 * @param string[] $action_types
	 *
	 * @return string[]
	 */
	function filter_action_types( $action_types ) {
		$chat_enabled = (new CnbUtils())->is_chat_api_enabled();

		// remove CHAT key if chat is not enabled for this user
		if ( !$chat_enabled ) {
			unset( $action_types['CHAT'] );
		}

		return $action_types;
	}
}
