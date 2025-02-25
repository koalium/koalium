<?php

namespace cnb\admin\apikey;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\admin\api\CnbAdminCloud;
use cnb\admin\api\CnbAppRemote;
use cnb\CnbHeaderNotices;
use cnb\notices\CnbAdminNotices;
use cnb\notices\CnbNotice;
use cnb\utils\CnbUtils;

class CnbApiKeyController {
    /**
     * This is called via add_action to create a new API key
     */
    public function create() {
        do_action( 'cnb_init', __METHOD__ );
        $nonce = filter_input( INPUT_POST, '_wpnonce', @FILTER_SANITIZE_STRING );
        if ( isset( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( $nonce, 'cnb_apikey_create' ) ) {

            // sanitize the input
            $apikey_data = filter_input(
                INPUT_POST,
                'apikey',
                @FILTER_SANITIZE_STRING,
                FILTER_REQUIRE_ARRAY );

            $apikey       = new CnbApiKey();
            $apikey->name = $apikey_data['name'];

            // do the processing
            $cnb_cloud_notifications = array();
            CnbAdminCloud::cnb_create_apikey( $cnb_cloud_notifications, $apikey );

            // redirect the user to the appropriate page
            $transient_id = (new CnbHeaderNotices())->generate_notice_id();
            set_transient( $transient_id, $cnb_cloud_notifications, HOUR_IN_SECONDS );

            // Create link
            $url           = admin_url( 'admin.php' );
            $redirect_link =
                add_query_arg(
                    array(
                        'page'     => 'call-now-button-apikeys',
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
            wp_die( esc_html__( 'Invalid nonce specified' ), esc_html__( 'Error' ), array(
                'response'  => 403,
                'back_link' => true,
            ) );
        }
    }

	/**
	 * This is called via add_action to validate and update an API key.
	 * It is called as an "admin_post" action.
	 *
	 * @return void
	 */
	public function validate_and_update( ) {
		do_action( 'cnb_init', __METHOD__ );
		$nonce = filter_input( INPUT_POST, '_wpnonce', @FILTER_SANITIZE_STRING );
		if ( !wp_verify_nonce( $nonce, 'cnb_apikey_validate_and_update' ) ) {
			do_action( 'cnb_finish' );
			wp_die( esc_html__( 'Invalid nonce specified' ), esc_html__( 'Error' ), array(
				'response'  => 403,
				'back_link' => true,
			) );
		}

		$apikey = filter_input( INPUT_POST, 'api_key', @FILTER_SANITIZE_STRING );
		$admin_cloud = new CnbAdminCloud();
		if (!$admin_cloud->is_api_key_valid($apikey)) {
			$this->finish_validate_and_update(false);
		}

		$success = $this->update_api_key( $apikey );
		$this->finish_validate_and_update($success);
	}

	/**
	 * NOTE THAT THIS FUNCTION DOES NOT RETURN (it redirects, then exits)
	 *
	 * @param $success bool if false, then we redirect to the settings page with an error message
	 *
	 * @return void
	 */
	private function finish_validate_and_update( $success ) {
		// Setup message
		$transient_id = null;
		if (!$success) {
			$message = new CnbNotice( 'error', '<p>Updating the API key failed. Your key might no longer work with our system.</p><p>You can request a new key using the <strong>Option 1: Email activation </strong> method below.</p>' );

			$cnb_cloud_notifications = array();
			$cnb_cloud_notifications[] = $message;
			$transient_id = (new CnbHeaderNotices())->generate_notice_id();
			set_transient( $transient_id, $cnb_cloud_notifications, HOUR_IN_SECONDS );
		}

		// Create link
		$url           = admin_url( 'admin.php' );
		$redirect_link =
			add_query_arg(
				array(
					'page'     => 'call-now-button-settings',
					'tid'      => $transient_id,
					'_wpnonce' => wp_create_nonce( $transient_id ),
				),
				$url );
		$redirect_url  = esc_url_raw( $redirect_link );

		// Finish up and do redirect
		do_action( 'cnb_finish' );
		wp_safe_redirect( $redirect_url );
		exit;
	}

	/**
	 * Update settings table with the updated api_key
	 *
	 * @param $apikey string
	 *
	 * @return bool
	 */
	public function update_api_key( $apikey ) {
		$options            = array();
		$options['api_key'] = $apikey;
		return update_option( 'cnb', $options );
	}

    /**
     * This is the quick action where they can delete a single Action
     *
     * It is always called via/with $_GET parameters
     *
     * @return void
     */
    public function delete() {
        $cnb_utils      = new CnbUtils();
        $id             = $cnb_utils->get_query_val( 'id', null );
        $nonce          = $cnb_utils->get_query_val( '_wpnonce', null );
        $action         = 'cnb_delete_apikey';
        $nonce_verified = wp_verify_nonce( $nonce, $action );
        if ( $nonce_verified ) {
            $cnb_cloud_notifications = array();
            $apikey                  = new CnbApiKey();
            $adminNotices            = CnbAdminNotices::get_instance();
            $apikey->id              = $id;
            CnbAdminCloud::cnb_delete_apikey( $cnb_cloud_notifications, $apikey );

            $adminNotices->notices( $cnb_cloud_notifications );
        }
    }

	/**
	 * This is very similar to the <code>delete()</code> function.
	 *
	 * This always has to come via a $_POST request (specifically, via admin-post.php),
	 * so this should end in a redirect (or an error via wp_die)
	 *
	 * Big differences are:
	 * - This handles multiple IDs, versus 1
	 * - Instead of rendering the Notice, is it stored and the user redirected
	 *
	 * nonce name via WP_List_Table = bulk-{plural}
	 * so in this case: bulk-cnb_list_apikeys
	 *
	 * @return void
	 */
	public function handle_bulk_actions() {
		do_action( 'cnb_init', __METHOD__ );
		$cnb_utils      = new CnbUtils();
		$cnb_remote     = new CnbAppRemote();
		$nonce          = $cnb_utils->get_post_val( '_wpnonce' );
		$action         = 'bulk-cnb_list_apikeys';
		$nonce_verified = wp_verify_nonce( $nonce, $action );
		if ( $nonce_verified ) {
			$entityIds = filter_input( INPUT_POST, 'cnb_list_apikey', @FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY );
			if ( $cnb_utils->get_post_val( 'bulk-action' ) === 'delete' ) {
				foreach ( $entityIds as $entityId ) {
					$apikey     = new CnbApiKey();
					$apikey->id = $entityId;
					$cnb_remote->delete_apikey( $apikey );
				}

				// Create notice for link
				$notice       = new CnbNotice( 'success', '<p>' . count( $entityIds ) . ' Api key(s) deleted.</p>' );
				$transient_id = (new CnbHeaderNotices())->generate_notice_id();
				set_transient( $transient_id, array( $notice ), HOUR_IN_SECONDS );

				// Create link
				$url           = admin_url( 'admin.php' );
				$redirect_link =
					add_query_arg(
						array(
							'page'     => 'call-now-button-apikeys',
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
						'link_text' => esc_html( 'Go back to the API Key overview' ),
						'link_url'  => esc_url_raw( admin_url( 'admin.php' ) . '?page=' . CNB_SLUG . '-apikeys' ),
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

}
