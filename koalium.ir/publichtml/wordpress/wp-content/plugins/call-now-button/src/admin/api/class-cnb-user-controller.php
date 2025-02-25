<?php


namespace cnb\admin\api;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use WP_Error;

class CnbUserController {

	/**
	 * Called via Ajax
	 *
	 *
	 * @return mixed|WP_Error
	 */
	public function set_storage_solution( ) {
		do_action( 'cnb_init', __METHOD__ );

		$nonce = filter_input( INPUT_POST, '_ajax_nonce', @FILTER_SANITIZE_STRING );
		if ( ! wp_verify_nonce( $nonce, 'cnb-user' ) ) {
			wp_send_json( new WP_Error( 'invalid_nonce', __( 'Invalid nonce', 'call-now-button' ) ) );
			do_action( 'cnb_finish' );
			wp_die();
		}

		$storage_type = filter_input( INPUT_POST, 'storage_type', @FILTER_SANITIZE_STRING );
		if ($storage_type !== 'GCS' && $storage_type !== 'R2') {
			wp_send_json( new WP_Error( 'Invalid storage type', __( 'Invalid storage type', 'call-now-button' ) ) );
			do_action( 'cnb_finish' );
			wp_die();
		}

		$remote = new CnbAppRemote();
		$result = $remote->set_user_storage_type( $storage_type );

		// if this is a success, also ensure that these settings are updated
		if ( ! is_wp_error( $result ) ) {
			$remote = new CnbAppRemote();
			$remote->get_wp_info();
		}

		wp_send_json($result);
		do_action( 'cnb_finish' );
		wp_die();
	}
}
