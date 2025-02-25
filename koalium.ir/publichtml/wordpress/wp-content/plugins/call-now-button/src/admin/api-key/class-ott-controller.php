<?php

namespace cnb\admin\apikey;

use cnb\admin\api\CnbAdminCloud;
use cnb\admin\api\CnbAppRemote;
use cnb\admin\api\CnbMigration;
use cnb\admin\models\CnbActivation;
use cnb\notices\CnbNotice;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

class OttController {

	private $activation;

	public function __construct() {
		$this->activation = new CnbActivation();
	}

	/**
	 * Called via the activation e-mail
	 *
	 * @return void
	 */
	public function activate() {
		$this->parse_api_and_ott_header();
		set_transient('cnb_activation', $this->activation, HOUR_IN_SECONDS);

		// Create link
		$url           = admin_url( 'admin.php' );
		$redirect_link =
			add_query_arg(
				array(
					'page' => 'call-now-button-activated',
				),
				$url );
		$redirect_url  = esc_url_raw( $redirect_link );
		do_action( 'cnb_finish' );
		wp_safe_redirect( $redirect_url );
	}

	private function parse_api_and_ott_header() {
		$cnb_options = get_option( 'cnb' );
		$adminCloud  = new CnbAdminCloud();
		$cnb_remote  = new CnbAppRemote();

		$this->get_api_key();

		// In case:
		// - there already is an API key (so no need to update)
		// - a token is provided anyway (api_key[_ott])
		// - the cloud is disabled (for some reason)
		// Then this re-enables it (and shows a warning that we did that)
		if ( ! empty( $cnb_options['api_key'] ) && ($this->activation->api_key || $this->activation->ott_key) && $cnb_options['cloud_enabled'] != 1 ) {
			$notice                      = new CnbNotice('warning', '<p>You have followed a link, but an API key is already present or the token has expired.</p><p>We have enabled <strong>Premium</strong>, but did not change the already present API key.</p>');
			$this->activation->notices[] = $notice;

			$options                  = array();
			$options['cloud_enabled'] = 1;
			update_option( 'cnb', $options );

			return;
		}

		$api_key_valid = $adminCloud->is_api_key_valid( $this->activation->api_key );

		// This is really the first time a user tries to activate a key, so:
		// - Check the key for validity
		// - If valid, enable cloud, set the API key, update domain/button
		if ( empty( $cnb_options['api_key'] ) && $this->activation->api_key ) {
			if ( ! $api_key_valid ) {
				$notice                               = new CnbNotice('error', '<p>This API key is invalid.</p>');
				$this->activation->notices[]          = $notice;
				$this->activation->activation_attempt = true;
				$this->activation->success            = false;

				return;
			}

			// This also enabled the cloud
			$options                  = array();
			$options['cloud_enabled'] = 1;
			$options['api_key']       = $this->activation->api_key;
			update_option( 'cnb', $options );

			// set "migration done"
			// We should really only do this once, so we need to save something in the settings to stop continuous migration.
			add_option( 'cnb_cloud_migration_done', true );
			$notice                      = new CnbNotice('success', '<p>Successfully connected to your NowButtons account.</p>' );
			$this->activation->notices[] = $notice;
		}

		// If an API key was passed (no matter the status of activation)
		if ( $this->activation->api_key && $api_key_valid ) {
			$migration = new CnbMigration();
			$migration->createOrUpdateDomainAndButton( $this->activation );
			// Reset the wp info (since the API key is now set/available)
			$cnb_remote->get_wp_info();
		}
	}

	private function get_api_key() {
		// Parse special header(s)
		$api_key_direct = filter_input( INPUT_GET, 'api_key', @FILTER_SANITIZE_STRING );
		$api_key_ott    = filter_input( INPUT_GET, 'api_key_ott', @FILTER_SANITIZE_STRING );

		if ( ! empty( $api_key_direct ) ) {
			$this->activation->api_key            = $api_key_direct;
			$this->activation->activation_attempt = true;
		}

		if ( ! empty( $api_key_ott ) ) {
			$this->activation->ott_key            = $api_key_ott;
			$this->activation->api_key            = $this->get_api_key_from_ott();
			$this->activation->activation_attempt = true;
		}
	}

	/**
	 * @return string|null The API key if found
	 */
	private function get_api_key_from_ott() {
		$cnb_remote                              = new CnbAppRemote();
		$api_key                                 = $cnb_remote->get_apikey_via_ott( $this->activation->ott_key );

		if ( $api_key === null ) {
			return null;
		}

		// Special case for expired OTTs
		if ( is_wp_error( $api_key ) ) {
			$code = 'CNB_ERROR';
			if ( $api_key->get_error_code() === $code ) {
				$messages = $api_key->get_error_messages( $code );
				if ( is_array( $messages ) && count( $messages ) && $api_key->get_error_messages( $code )[0] === 'Bad Request' ) {
					// This is most likely an expired key
					$message = '<p>A <em>one-time token</em> was provided, but that token is invalid or has expired.</p>';
					$notice                      = new CnbNotice( 'error', $message );
					$this->activation->notices[] = $notice;

					return null;
				}
			}

			$error_details = CnbAdminCloud::cnb_admin_get_error_message_details( $api_key );
			$message                             = '<p>We could not enable <strong>Premium</strong> with this <em>one-time token</em>.';
			$message                             .= ' <code>' . esc_html( $this->activation->ott_key ) . '</code> :-(.' . $error_details . '</p>';
			$notice                              = new CnbNotice( 'error', $message );
			$this->activation->notices[]         = $notice;

			return null;
		}

		return $api_key->key;
	}
}
