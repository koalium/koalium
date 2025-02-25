<?php

namespace cnb\admin\api;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\admin\action\CnbAction;
use cnb\admin\apikey\CnbApiKey;
use cnb\admin\button\CnbButton;
use cnb\admin\condition\CnbCondition;
use cnb\admin\domain\CnbDomain;
use cnb\admin\domain\SubscriptionStatus;
use cnb\admin\models\CnbAgencyPlan;
use cnb\admin\models\CnbPlan;
use cnb\admin\models\CnbUser;
use cnb\admin\models\ValidationMessageWithId;
use cnb\admin\settings\StripeBillingPortal;
use cnb\admin\settings\StripeRequestBillingPortalResponse;
use cnb\admin\settings\UrlSettings;
use cnb\admin\user\CnbUserCache;
use cnb\coupons\CnbPromotionCode;
use cnb\cron\Cron;
use cnb\utils\CnbUtils;
use JsonSerializable;
use WP_Error;

class CnbAppRemote {

	private static $transient_prefix = 'call-now-button_CnbAppRemote_';

	/**
	 * By creating a proxy method, we can easily stub this for testing
	 *
	 * It also needs to be public for the tests/stub to work, it seems?
	 *
	 * @return string Site URL with optional path appended.
	 */
	public function get_site_url() {
		/** @noinspection PhpFullyQualifiedNameUsageInspection */
		return \get_site_url();
	}

	/**
	 * Return a cleaned up version of the Site URL.
	 *
	 * Removes protocol, port and path (and lowercases it)
	 *
	 * Examples:
	 * - https://www.Example.org:8080/test becomes example.org
	 * - https://subdomaIN.eXAMple.prg:8080/test becomes subdomain.example.org
	 *
	 * @return string
	 */
	public function cnb_clean_site_url() {
		$siteUrl = $this->get_site_url();

		$url = wp_parse_url( $siteUrl, PHP_URL_HOST );
		if ( $url ) {
			return
				preg_replace( '/^www\./', '',
					trim(
						strtolower( $url ) )
					, 1 );
		}

		// Fallback behavior
		// Order:
		// 1: Strip everything after // (so to remove a potential protocol like http(s)://
		// 2: Strip the port if found, via :1234
		// 3: Strip everything after /, so that "example.org/test" becomes "example.org"
		// 4: Lowercase & trim everything
		// 5: Remove a potential "www." prefix

		return
			preg_replace( '/^www\./', '',
				trim(
					strtolower( preg_replace( '/\/.*/', '',
						preg_replace( '/:\d+/', '',
							preg_replace( '/.*\/\//', '', $siteUrl, 1 ), 1 ), 1 ) )
				)
				, 1 );
	}

	/**
	 * @return string usually "https://api.callnowbutton.com"
	 */
	public static function cnb_get_api_base() {
		$cnb_options = get_option( 'cnb' );

		return isset( $cnb_options['api_base'] ) ? $cnb_options['api_base'] : 'https://api.callnowbutton.com';
	}

	/**
	 * @return string usually "https://user.callnowbutton.com"
	 */
	public static function cnb_get_user_base() {
		UrlSettings::restoreFromOptions();
		/** @type UrlSettings $cnb_settings */
		global $cnb_settings;

		if ($cnb_settings && $cnb_settings->get_user_root()) {
			return $cnb_settings->get_user_root();
		}

		// This needs to /only/ be the fallback
		return str_replace( 'api', 'user', CnbAppRemote::cnb_get_api_base() );
	}

	/**
	 * @return string usually "https://static.callnowbutton.com"
	 */
	public static function cnb_get_static_base() {
		UrlSettings::restoreFromOptions();
		/** @type UrlSettings $cnb_settings */
		global $cnb_settings;

		if ($cnb_settings && $cnb_settings->get_static_root()) {
			return $cnb_settings->get_static_root();
		}

		// This needs to /only/ be the fallback
		return str_replace( 'api', 'static', CnbAppRemote::cnb_get_api_base() );
	}

	/**
	 * @return string usually "https://static.callnowbutton.com/js/client.js
	 */
	public static function get_client_js() {
		UrlSettings::restoreFromOptions();
		/** @type UrlSettings $cnb_settings */
		global $cnb_settings;

		if ($cnb_settings && $cnb_settings->get_js_location()) {
			return $cnb_settings->get_js_location();
		}

		// This needs to /only/ be the fallback
		return CnbAppRemote::cnb_get_static_base() . '/js/client.js';
	}

	/**
	 * @return string usually "https://static.callnowbutton.com/css/main.css"
	 */
	public static function get_client_css() {
		UrlSettings::restoreFromOptions();
		/** @type UrlSettings $cnb_settings */
		global $cnb_settings;

		if ($cnb_settings && $cnb_settings->get_css_location()) {
			return $cnb_settings->get_css_location();
		}

		// This needs to /only/ be the fallback
		return CnbAppRemote::cnb_get_static_base() . '/css/main.css';
	}

	/**
	 * @return string usually "https://chat.nowbuttons.com"
	 */
	public function get_chat_url() {
		UrlSettings::restoreFromOptions();
		/** @type UrlSettings $cnb_settings */
		global $cnb_settings;

		if ($cnb_settings && $cnb_settings->get_chat_root()) {
			return $cnb_settings->get_chat_root();
		}

		// This needs to /only/ be the fallback
		return str_replace( 'api', 'chat', CnbAppRemote::cnb_get_api_base() );
	}

	/**
	 * @return int|false false if not found, otherwise the current cache key
	 */
	public static function cnb__get_transient_base() {
		$val = get_transient( self::$transient_prefix . self::cnb_get_api_base() );
		if ( $val ) {
			return (int) $val;
		}

		return false;
	}

	/**
	 * Set the cache key.
	 *
	 * @param string|int|null $time Should not be added, but can be used to force a base (mostly used for testing).
	 */
	public static function cnb_incr_transient_base( $time = null ) {
		/** @noinspection PhpTernaryExpressionCanBeReducedToShortVersionInspection */
		$value = $time ? $time : time();
		set_transient( self::$transient_prefix . self::cnb_get_api_base(), $value );
	}

	public static function cnb_get_transient_base() {
		return self::cnb__get_transient_base() . self::cnb_get_api_base();
	}

	private static function cnb_remote_get_args( $authenticated = true ) {
		global $cnb_api_key;
		$cnb_options = get_option( 'cnb' );
		$api_key     = isset( $cnb_options['api_key'] ) ? $cnb_options['api_key'] : false;

		// Special case, we also need to be able to temporarily overwrite the API key
		// This is done by functions by setting the special global "$cnb_api_key"
		if ( isset( $cnb_api_key ) && ! empty( $cnb_api_key ) ) {
			$api_key = $cnb_api_key;
		}

		$headers = array(
			'Content-Type'         => 'application/json',
			'X-CNB-Plugin-Version' => CNB_VERSION,
		);

		if ( $authenticated ) {
			if ( ! $api_key ) {
				return new WP_Error( 'CNB_API_NOT_SETUP_YET' );
			}
			$header_name  = 'X-CNB-Api-Key';
			$header_value = $api_key;

			$headers[ $header_name ] = $header_value;
		}

		return array(
			'headers' => $headers,
		);
	}

	private static function cnb_remote_handle_response( $response ) {
		global $wp_version;
		if ( $response instanceof WP_Error ) {
			if ( version_compare( $wp_version, '5.6.0', '>=' ) ) {
				$error = new WP_Error( 'CNB_UNKNOWN_REMOTE_ERROR', 'There was an issue communicating with the CallNowButton API. Please see the detailed error message from the response below.' );
				$error->merge_from( $response );

				return $error;
			}

			return $response;
		}
		$response_code = wp_remote_retrieve_response_code( $response );
		if ( $response_code == 403 ) {
			$response_message = wp_remote_retrieve_response_message( $response );
			if ( $response_message == 'Forbidden' && str_contains( wp_remote_retrieve_body ( $response ), 'Access Denied' ) ) {
				return new WP_Error( 'CNB_API_KEY_INVALID', $response_message );
			}
		}
		if ( $response_code == 404 ) {
			return new WP_Error( 'CNB_ENTITY_NOT_FOUND', wp_remote_retrieve_response_message( $response ) );
		}
		// 402 == Payment required
		if ( $response_code == 402 ) {
			$body = json_decode( $response['body'] );

			return new WP_Error( 'CNB_PAYMENT_REQUIRED', wp_remote_retrieve_response_message( $response ), $body->message );
		}
		if ( $response_code != 200 ) {
			return new WP_Error( 'CNB_ERROR', wp_remote_retrieve_response_message( $response ), wp_remote_retrieve_body ( $response ) );
		}

		return json_decode( wp_remote_retrieve_body ( $response ) );
	}

	/**
	 * DELETE, PATCH support.
	 *
	 * Includes Trace support
	 *
	 * @param $url string
	 * @param $parsed_args array
	 *
	 * @return array|WP_Error
	 */
	private static function cnb_wp_request( $url, $parsed_args ) {
		$http = _wp_http_get_object();

		$context  = __METHOD__ . '<' . $parsed_args['method'] . '>';
		$timer    = new RemoteTrace( $url, $context );
		$response = $http->request( $url, $parsed_args );
		$timer->end();

		return $response;
	}

	/**
	 * DELETE is missing from WordPress Core.
	 *
	 * This is inspired by https://developer.wordpress.org/reference/functions/wp_remote_post/
	 *
	 * @param $url string
	 * @param $args array
	 *
	 * @return array|WP_Error
	 */
	private static function wp_remote_delete( $url, $args = array() ) {
		$defaults    = array( 'method' => 'DELETE' );
		$parsed_args = wp_parse_args( $args, $defaults );

		return self::cnb_wp_request( $url, $parsed_args );
	}

	/**
	 * PATCH is missing from WordPress Core.
	 *
	 * This is inspired by https://developer.wordpress.org/reference/functions/wp_remote_post/
	 *
	 * @param $url string
	 * @param $args array
	 *
	 * @return array|WP_Error
	 */
	private static function wp_remote_patch( $url, $args = array() ) {
		$defaults    = array( 'method' => 'PATCH' );
		$parsed_args = wp_parse_args( $args, $defaults );

		return self::cnb_wp_request( $url, $parsed_args );
	}

	/**
	 * @param $rest_endpoint string
	 * @param $body array|JsonSerializable will be JSON encoded, can be `array` (or class with `JsonSerializable`?)
	 *
	 * @return mixed|WP_Error
	 */
	private static function cnb_remote_patch( $rest_endpoint, $body ) {
		$args = self::cnb_remote_get_args();
		if ( $args instanceof WP_Error ) {
			return $args;
		}

		if ( $body != null ) {
			$args['body'] = wp_json_encode( $body );
		}

		$url      = self::cnb_get_api_base() . $rest_endpoint;
		$response = self::wp_remote_patch( $url, $args );
		self::cnb_incr_transient_base();
		do_action( 'cnb_after_button_changed' );

		return self::cnb_remote_handle_response( $response );
	}

	private static function cnb_remote_delete( $rest_endpoint ) {
		$args = self::cnb_remote_get_args();
		if ( $args instanceof WP_Error ) {
			return $args;
		}

		$url      = self::cnb_get_api_base() . $rest_endpoint;
		$response = self::wp_remote_delete( $url, $args );
		self::cnb_incr_transient_base();
		do_action( 'cnb_after_button_changed' );

		return self::cnb_remote_handle_response( $response );
	}

	public static function cnb_remote_post( $rest_endpoint, $body = null, $authenticated = true ) {
		$args = self::cnb_remote_get_args( $authenticated );
		if ( $args instanceof WP_Error ) {
			return $args;
		}

		if ( $body != null ) {
			$args['body'] = wp_json_encode( $body );
		}

		$url      = self::cnb_get_api_base() . $rest_endpoint;
		$timer    = new RemoteTrace( $url, __METHOD__ );
		$response = wp_remote_post( $url, $args );
		self::cnb_incr_transient_base();
		do_action( 'cnb_after_button_changed' );
		$timer->end();

		return self::cnb_remote_handle_response( $response );
	}

	public static function cnb_remote_get( $rest_endpoint, $authenticated = true ) {
		$cnb_remote = new CnbAppRemote();
		$url      = self::cnb_get_api_base() . $rest_endpoint;
		return $cnb_remote->cnb_get($url, $authenticated);
	}

	public function cnb_get( $rest_endpoint, $authenticated = true ) {
		$cnb_get_cache = new CnbGet();
		$args          = self::cnb_remote_get_args( $authenticated );
		if ( $args instanceof WP_Error ) {
			return $args;
		}

		$url      = $rest_endpoint;
		$timer    = new RemoteTrace( $url, __METHOD__ );
		$response = $cnb_get_cache->get( $url, $args );
		$timer->setCacheHit( $cnb_get_cache->isLastCallCached() );
		$timer->end();

		return self::cnb_remote_handle_response( $response );
	}

	/**
	 * In case the cloud is enabled, retrieve all the needed information from the remote server.
	 *
	 * @return void
	 */
	public function init() {
		$cnb_options = get_option( 'cnb' );
		$cnb_utils   = new CnbUtils();

		if ( ! $cnb_utils->isCloudActive( $cnb_options ) ) {
			return;
		}

		$this->get_wp_info();
	}

	/***
	 * Sets all WordPress needed information globally
	 *
	 * @return void
	 *
	 * @global CnbUser|WP_Error|null $cnb_user the User corresponding to the current API key
	 * @global CnbDomain|null $cnb_domain the Domain corresponding to the current clean site URL
	 * @global CnbButton[]|null $cnb_buttons the Buttons corresponding to the current Domain
	 * @global CnbDomain[]|null $cnb_domains the Domains corresponding to the current User
	 * @global CnbPromotionCode|null $cnb_coupon the Coupon currently active
	 * @global CnbPlan[]|null $cnb_plans the Plans currently active
     * @global CnbAgencyPlan[]|null $cnb_agency_plans the Agency Plans currently active
	 * @global ValidationMessageWithId[]|null $cnb_validation_messages all Validation messages for this account
	 * @global SubscriptionStatus|null $cnb_subscription_data Subscription status for the current domain
	 */
	public function get_wp_info() {
		global
		$cnb_user,
		$cnb_domain,
		$cnb_buttons,
		$cnb_domains,
		$cnb_coupon,
		$cnb_plans,
        $cnb_agency_plans,
		$cnb_validation_messages,
		$cnb_subscription_data,
		$cnb_settings;

		$rest_endpoint = '/v1/wp/all/' . $this->cnb_clean_site_url();

		$data = self::cnb_remote_get( $rest_endpoint );
		if ( $data === null || is_wp_error( $data ) ) {
			$cnb_user = CnbUser::fromObject( $data );
			return;
		}

		$cnb_user                = CnbUser::fromObject( $data->user );
		$cnb_domain              = CnbDomain::fromObject( $data->currentDomain );
		$cnb_domains             = CnbDomain::fromObjects( $data->domains );
		$cnb_buttons             = CnbButton::fromObjects( $data->buttons );
		$cnb_coupon              = CnbPromotionCode::fromObject( $data->coupon );
		$cnb_plans               = CnbPlan::fromObjects( $data->plans );
        $cnb_agency_plans        = CnbAgencyPlan::fromObjects( $data->agencyPlans );
		$cnb_validation_messages = ValidationMessageWithId::fromObjects( $data->validationMessages );
		$cnb_settings            = UrlSettings::fromObject($data->settings);
		// This might not be available in each API call, depending on environment settings
		if ( isset( $data->subscriptionStatusData ) ) {
			$cnb_subscription_data = SubscriptionStatus::from_object( $data->subscriptionStatusData );
			$this->save_subscription_data($cnb_subscription_data);
		}
		if ( isset( $cnb_user ) && ! is_wp_error( $cnb_user ) ) {
			$cnb_user_cache = new CnbUserCache();
			$cnb_user_cache->save_user_data( $cnb_user );
		}

		// This updates the internal options, so that the new settings (if any) can be rendered on the front-end
		if ( $cnb_settings ) {
			$cnb_settings->register_settings();
		}
	}

	/**
	 * Stores the SubscriptionStatus to a local (transient) store, so that #get_subscription_data
	 * can retrieve it.
	 *
	 * @param $cnb_subscription_data SubscriptionStatus
	 *
	 * @return void
	 */
	private function save_subscription_data($cnb_subscription_data) {
		if ($cnb_subscription_data && !is_wp_error($cnb_subscription_data)) {
			$hook_name = (new Cron())->get_hook_name();
			set_transient( $hook_name, $cnb_subscription_data, DAY_IN_SECONDS );
		}
	}

	/**
	 * Get the SubscriptionStatus without a call to the remote API, instead relying on the
	 * local transient store.
	 *
	 * @return bool|SubscriptionStatus
	 */
	public function get_subscription_data() {
		$hook_name = (new Cron())->get_hook_name();
		return get_transient($hook_name);
	}

	public function get_subscription_status( $domainId ) {
		$rest_endpoint = '/v1/subscription/domain/' . $domainId;
		$data = self::cnb_remote_get( $rest_endpoint );
		return SubscriptionStatus::from_object( $data );
	}

	/**
	 * @param $id string
	 *
	 * @return CnbButton|WP_Error
	 */
	public function get_button( $id ) {
		global $cnb_buttons;
		// This usually means the API was to slow return anything
		if ( empty($cnb_buttons) ) {
			return new WP_Error( 'WP_RETRIEVE_ERROR', 'Could not retrieve buttons for ID <code>' . esc_html( $id ) . '</code>. Please refresh the page.' );
		}

		foreach ( $cnb_buttons as $button ) {
			if ( $button->id === $id ) {
				return $button;
			}
		}

		return new WP_Error( 'WP_RETRIEVE_ERROR', 'Could not retrieve button with ID <code>' . esc_html( $id ) . '</code>. Please refresh the page.' );
	}

	/**
	 * @param $id string
	 *
	 * @return CnbAction|WP_Error
	 */
	public function get_action( $id ) {
		global $cnb_buttons;
		/**
		 * @type $button CnbButton
		 */
		foreach ( $cnb_buttons as $button ) {
			foreach ( $button->actions as $action ) {
				if ( $action->id === $id ) {
					return $action;
				}
			}
		}

		return null;
	}

	/**
	 * @param $id
	 *
	 * @return CnbDomain|WP_Error
	 */
	public function get_domain( $id ) {
		global $cnb_domains;
		foreach ( $cnb_domains as $domain ) {
			if ( $domain->id === $id ) {
				return $domain;
			}
		}

		return null;
	}

	/**
	 * @param $id string
	 *
	 * @return CnbButton|null
	 */
	public function get_button_for_action( $id ) {
		global $cnb_buttons;
		/**
		 * @type $button CnbButton
		 */
		foreach ( $cnb_buttons as $button ) {
			foreach ( $button->actions as $action ) {
				if ( $action->id === $id ) {
					return $button;
				}
			}
		}

		return null;
	}

	/**
	 * @param $id string
	 *
	 * @return CnbButton|null
	 */
	public function get_button_for_condition( $id ) {
		global $cnb_buttons;
		/**
		 * @type $button CnbButton
		 */
		foreach ( $cnb_buttons as $button ) {
			foreach ( $button->conditions as $condition ) {
				if ( $condition->id === $id ) {
					return $button;
				}
			}
		}

		return null;
	}

	/**
	 * Returns the User corresponding to the current API key
	 *
	 * @return CnbUser|WP_Error
	 *
	 * @global CnbUser|WP_Error|null $cnb_user the User corresponding to the current API key
	 *
	 */
	public function get_user() {
		global $cnb_user;
		$rest_endpoint = '/v1/user';

		$user = CnbUser::fromObject( self::cnb_remote_get( $rest_endpoint ) );
		// Only set the global if the User is successfully retrieved
		if ( $user instanceof CnbUser ) {
			$cnb_user = $user;
		}

		return $user;
	}

	/**
	 * @param $user CnbUser
	 *
	 * @return CnbUser|WP_Error
	 */
	public function update_user( $user ) {
		$rest_endpoint = '/v1/user';

		return CnbUser::fromObject( self::cnb_remote_patch( $rest_endpoint, $user ) );
	}

	/**
	 * Opt-in to Marketing e-mails
	 *
	 * @return void
	 */
	public function enable_email_opt_in() {
		$rest_endpoint = '/v1/user/emailPreference';
		self::cnb_remote_post( $rest_endpoint );
	}

	/**
	 * Remove the opt-in (basically, opt-out) from the user, preventing Marketing e-mails from being sent
	 *
	 * @return void
	 */
	public function disable_email_opt_in() {
		$rest_endpoint = '/v1/user/emailPreference';
		self::cnb_remote_delete( $rest_endpoint );
	}

	/**
	 * This returns the domain matching the WordPress domain
	 *
	 * @return CnbDomain|WP_Error
	 *
	 * @global CnbDomain|null $cnb_domain the domain matching the WordPress domain
	 *
	 */
	public function get_wp_domain() {
		global $cnb_domain;
		$cnbAppRemote  = new CnbAppRemote();
		$rest_endpoint = '/v1/domain/byName/' . $cnbAppRemote->cnb_clean_site_url();

		$domain = CnbDomain::fromObject( self::cnb_remote_get( $rest_endpoint ) );
		// Only set the global if the CnbDomain is successfully retrieved
		if ( $domain instanceof CnbDomain ) {
			$cnb_domain = $domain;
		}

		return $domain;
	}

	/**
	 * This does not (yet) actually return CnbButton, but a stdclass that resembles it.
	 *
	 * @return CnbButton[]|WP_Error
	 */
	public function get_buttons() {
		$rest_endpoint = '/v1/button';

		return CnbButton::fromObjects( self::cnb_remote_get( $rest_endpoint ) );
	}

	/**
	 * @return CnbAction[]|WP_Error
	 */
	public function get_actions() {
		$rest_endpoint = '/v1/action';

		return CnbAction::fromObjects( self::cnb_remote_get( $rest_endpoint ) );
	}

	/**
	 * @return CnbCondition[]|WP_Error
	 */
	public function get_conditions() {
		$rest_endpoint = '/v1/condition';

		return CnbCondition::fromObjects( self::cnb_remote_get( $rest_endpoint ) );
	}

	/**
	 * @param $id string
	 *
	 * @return CnbCondition|WP_Error
	 */
	public function get_condition( $id ) {
		$rest_endpoint = '/v1/condition/' . $id;

		return CnbCondition::fromObject( self::cnb_remote_get( $rest_endpoint ) );
	}

	/**
	 * @param $ott string a one-time token to retrieve an API key
	 *
	 * @return CnbApiKey|WP_Error
	 */
	public function get_apikey_via_ott( $ott ) {
		$rest_endpoint = '/v1/apikey/ott/' . $ott;

		return CnbApiKey::fromObject( self::cnb_remote_get( $rest_endpoint, false ) );
	}

	/**
	 * @return CnbApiKey[]|WP_Error
	 */
	public function get_apikeys() {
		$rest_endpoint = '/v1/apikey';

		return CnbApiKey::fromObjects( self::cnb_remote_get( $rest_endpoint ) );
	}

	/**
	 * @param $button CnbButton
	 *
	 * @return CnbButton|WP_Error
	 */
	public function update_button( $button ) {
		// Find the ID in the options
		if ( ! $button->id ) {
			return new WP_Error( 'CNB_BUTTON_ID_MISSING', 'buttonId expected, but not found' );
		}

		$rest_endpoint = '/v1/button/' . $button->id;

		return CnbButton::fromObject( self::cnb_remote_patch( $rest_endpoint, $button ) );
	}

	/**
	 * @param $domain CnbDomain
	 *
	 * @return CnbDomain|WP_Error
	 */
	public function update_domain( $domain ) {
		// Find the ID in the options
		if ( ! $domain->id ) {
			return new WP_Error( 'CNB_DOMAIN_ID_MISSING', 'domainId expected, but not found' );
		}

		$rest_endpoint = '/v1/domain/' . $domain->id;

		return CnbDomain::fromObject( self::cnb_remote_patch( $rest_endpoint, $domain ) );
	}

	/**
	 * @param $button CnbButton
	 *
	 * @return CnbButton|WP_Error
	 */
	public function delete_button( $button ) {
		if ( ! $button->id ) {
			return new WP_Error( 'CNB_BUTTON_ID_MISSING', 'buttonId expected, but not found' );
		}

		$rest_endpoint = '/v1/button/' . $button->id;

		$delete_result = CnbDeleteResult::fromObject( self::cnb_remote_delete( $rest_endpoint ) );
		if ( $delete_result->is_success() ) {
			return CnbButton::fromObject( $delete_result->object );
		}

		return $delete_result->get_error();
	}

	/**
	 * @param $domain CnbDomain
	 *
	 * @return CnbDomain|WP_Error
	 */
	public function delete_domain( $domain ) {
		if ( ! $domain->id ) {
			return new WP_Error( 'CNB_DOMAIN_ID_MISSING', 'domainId expected, but not found' );
		}

		$rest_endpoint = '/v1/domain/' . $domain->id;

		$delete_result = CnbDeleteResult::fromObject( self::cnb_remote_delete( $rest_endpoint ) );
		if ( $delete_result->is_success() ) {
			return CnbDomain::fromObject( $delete_result->object );
		}

		return $delete_result->get_error();
	}

	/**
	 * @param $condition CnbCondition
	 *
	 * @return CnbCondition|WP_Error
	 */
	public function delete_condition( $condition ) {
		// Find the ID in the options
		if ( ! $condition->id ) {
			return new WP_Error( 'CNB_CONDITION_ID_MISSING', 'conditionId expected, but not found' );
		}

		$rest_endpoint = '/v1/condition/' . $condition->id;

		$delete_result = CnbDeleteResult::fromObject( self::cnb_remote_delete( $rest_endpoint ) );
		if ( $delete_result->is_success() ) {
			return CnbCondition::fromObject( $delete_result->object );
		}

		return $delete_result->get_error();
	}

	/**
	 * @param $action CnbAction
	 *
	 * @return CnbAction|WP_Error
	 */
	public function delete_action( $action ) {
		// Find the ID in the options
		if ( ! $action->id ) {
			return new WP_Error( 'CNB_ACTION_ID_MISSING', 'actionId expected, but not found' );
		}

		$rest_endpoint = '/v1/action/' . $action->id;

		$delete_result = CnbDeleteResult::fromObject( self::cnb_remote_delete( $rest_endpoint ) );
		if ( $delete_result->is_success() ) {
			return CnbAction::fromObject( $delete_result->object );
		}

		return $delete_result->get_error();
	}

	/**
	 * @param $apikey CnbApiKey
	 *
	 * @return CnbApiKey|WP_Error
	 */
	public function delete_apikey( $apikey ) {
		// Find the ID in the options
		$apikeyId = $apikey->id;

		if ( ! $apikeyId ) {
			return new WP_Error( 'CNB_APIKEY_ID_MISSING', 'apikeyId expected, but not found' );
		}

		$rest_endpoint = '/v1/apikey/' . $apikeyId;

		$delete_result = CnbDeleteResult::fromObject( self::cnb_remote_delete( $rest_endpoint ) );
		if ( $delete_result->is_success() ) {
			return CnbApiKey::fromObject( $delete_result->object );
		}

		return $delete_result->get_error();
	}

	/**
	 * @param $action CnbAction
	 *
	 * @return CnbAction|WP_Error
	 */
	public function update_action( $action ) {
		// Find the action ID in the options
		if ( ! $action->id ) {
			return new WP_Error( 'CNB_ACTION_ID_MISSING', 'actionId expected, but not found' );
		}

		$rest_endpoint = '/v1/action/' . $action->id;

		return CnbAction::fromObject( self::cnb_remote_patch( $rest_endpoint, $action ) );
	}

	/**
	 * @param $domain CnbDomain
	 *
	 * @return CnbDomain|WP_Error
	 */
	public function create_domain( $domain ) {
		if ( $domain->id ) {
			return new WP_Error( 'CNB_DOMAIN_ID_FOUND', 'no domainId expected, but one was given' );
		}

		$rest_endpoint = '/v1/domain';

		return CnbDomain::fromObject( self::cnb_remote_post( $rest_endpoint, $domain ) );
	}

	/**
	 * @param $button CnbButton Single Button object
	 *
	 * @return CnbButton|WP_Error
	 */
	public function create_button( $button ) {
		if ( $button->id ) {
			return new WP_Error( 'CNB_BUTTON_ID_FOUND', 'no buttonId expected, but one was given' );
		}

		$rest_endpoint = '/v1/button';

		return CnbButton::fromObject( self::cnb_remote_post( $rest_endpoint, $button ) );
	}

	/**
	 * @param $action CnbAction
	 *
	 * @return CnbAction|WP_Error
	 */
	public function create_action( $action ) {
		if ( $action->id ) {
			return new WP_Error( 'CNB_ACTION_ID_FOUND', 'no actionId expected, but one was given' );
		}

		$rest_endpoint = '/v1/action';

		return CnbAction::fromObject( self::cnb_remote_post( $rest_endpoint, $action ) );
	}

	/**
	 * @param $condition CnbCondition
	 *
	 * @return CnbCondition|WP_Error
	 */
	public function create_condition( $condition ) {
		if ( $condition->id ) {
			return new WP_Error( 'CNB_CONDITION_ID_FOUND', 'no conditionId expected, but one was given' );
		}

		$rest_endpoint = '/v1/condition';

		return CnbCondition::fromObject( self::cnb_remote_post( $rest_endpoint, $condition ) );
	}

	/**
	 * @param $condition CnbCondition
	 *
	 * @return CnbCondition|WP_Error
	 */
	public function update_condition( $condition ) {
		if ( ! $condition->id ) {
			return new WP_Error( 'CNB_CONDITION_ID_MISSING', 'conditionId expected, but not found' );
		}

		$rest_endpoint = '/v1/condition/' . $condition->id;

		return CnbCondition::fromObject( self::cnb_remote_patch( $rest_endpoint, $condition ) );
	}

	/**
	 * @param $apikey CnbApiKey
	 *
	 * @return CnbApiKey|WP_Error
	 */
	public function create_apikey( $apikey ) {
		$rest_endpoint = '/v1/apikey';

		return CnbApiKey::fromObject( self::cnb_remote_post( $rest_endpoint, $apikey ) );
	}

	/**
	 * @return StripeBillingPortal
	 */
	public function create_billing_portal() {
		$return_link =
			add_query_arg(
				array(
					'page' => 'call-now-button-settings',
					'tab'  => 'account_options',
				),
				admin_url( 'admin.php' ) );

		$body          = array(
			'returnUrl' => $return_link,
		);
		$rest_endpoint = '/v1/stripe/createBillingPortal';

		return StripeBillingPortal::fromObject( self::cnb_remote_post( $rest_endpoint, $body ) );
	}

    /**
     * @return StripeRequestBillingPortalResponse
     */
    public function request_billing_portal() {
        $rest_endpoint = '/v1/stripe/requestBillingPortal';

        return StripeRequestBillingPortalResponse::fromObject( self::cnb_remote_post( $rest_endpoint ) );
    }

	/**
	 * Data model:
	 * {
	 * "email": "jasper+wp-signup-test-02@studiostacks.com",
	 * "domain": "http://www.button.local:8000/",
	 * "adminUrl": "http://www.button.local:8000/wp-admin",
	 * "version": 2,
	 * }
	 *
	 * Version 2 is the admin-post.php version
	 *
	 * @param $admin_email string Email address of the user signing up
	 * @param $admin_url string URL (including the /wp-admin portion)
	 */
	public function create_email_activation( $admin_email, $admin_url ) {
		$cnbAppRemote = new CnbAppRemote();
		$body         = array(
			'email'    => $admin_email,
			'domain'   => $cnbAppRemote->cnb_clean_site_url(),
			'adminUrl' => $admin_url,
			'version'  => 2,
		);

		$rest_endpoint = '/v1/user/wp';

		return self::cnb_remote_post( $rest_endpoint, $body, false );
	}

	/**
	 * @param $storage_type string GCS or R2
	 *
	 * @return mixed|WP_Error
	 */
	public function set_user_storage_type ( $storage_type ) {
		$rest_endpoint = '/v1/user/settings/storage/' . $storage_type;
		$body = '';
		return self::cnb_remote_post( $rest_endpoint, $body );
	}
}
