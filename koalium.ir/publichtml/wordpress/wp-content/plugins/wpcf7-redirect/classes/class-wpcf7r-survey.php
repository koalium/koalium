<?php
/**
 * Class WPCF7r_Survey file.
 *
 * @package WPCF7_Redirect
 */

defined( 'ABSPATH' ) || exit;

/**
 * Contact form 7 redirect utilities
 */
class WPCF7r_Survey {

	/**
	 * Reference to singleton insance.
	 *
	 * @var [WPCF7r_Survey]
	 */
	public static $instance = null;

	/**
	 * Init hooks.
	 */
	public function init() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Get instance
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Get the data used for the survey.
	 *
	 * @return array
	 * @see survey.js
	 */
	public function get_survey_metadata() {

		$days_since_install = round( ( time() - get_option( 'redirection_for_contact_form_7_install', 0 ) ) / DAY_IN_SECONDS );
		$install_category   = 0;
		if ( 0 === $days_since_install || 1 === $days_since_install ) {
			$install_category = 0;
		} elseif ( 1 < $days_since_install && 8 > $days_since_install ) {
			$install_category = 7;
		} elseif ( 8 <= $days_since_install && 31 > $days_since_install ) {
			$install_category = 30;
		} elseif ( 30 < $days_since_install && 90 > $days_since_install ) {
			$install_category = 90;
		} elseif ( 90 <= $days_since_install ) {
			$install_category = 91;
		}

		$attributes = array(
			'free_version'       => WPCF7_PRO_REDIRECT_PLUGIN_VERSION,
			'days_since_install' => $install_category,
			'pro_version'        => WPCF7_PRO_REDIRECT_PLUGIN_VERSION,
			'plan'               => 0,
			'license_status'     => 'invalid',
		);

		$user_id = 'wpcf7r_' . preg_replace( '/[^\w\d]*/', '', get_site_url() ); // Use a normalized version of the site URL as a user ID for free users.

		$available_addons = array(
			'wpcf7r-api',
			'wpcf7r-conditional-logic',
			'wpcf7r-create-post',
			'wpcf7r-hubspot',
			'wpcf7r-mailchimp',
			'wpcf7r-paypal',
			'wpcf7r-pdf',
			'wpcf7r-popup',
			'wpcf7r-salesforce',
			'wpcf7r-stripe',
			'wpcf7r-twilio',
		);

		$plugins = get_plugins();
		$plugins = array_keys( $plugins );

		foreach ( $available_addons as $addon ) {
			if ( ! in_array( $addon . '/init.php', $plugins, true ) ) {
				continue;
			}

			if ( ! is_plugin_active( $addon . '/init.php' ) ) {
				continue;
			}

			if ( $attributes['plan'] > 0 ) {
				break;
			}

			$prefix_name  = str_replace( '-', '_', $addon );
			$license_data = get_option( $prefix_name . '_license_data', array() );

			if ( ! empty( $license_data->key ) ) {
				$user_id = 'wpcf7r_' . $license_data->key;
			}

			$attributes['pro_version']    = $this->get_plugin_version( WP_PLUGIN_DIR . '/' . $addon . '/init.php' );
			$attributes['plan']           = $this->plan_category( $license_data );
			$attributes['license_status'] = ! empty( $license_data->license ) ? $license_data->license : 'invalid';
		}

		return array(
			'userId'     => $user_id,
			'attributes' => $attributes,
		);
	}

	/**
	 * Enqueue scripts.
	 */
	public function enqueue_scripts() {

		if ( defined( 'CYPRESS_TESTING' ) ) {
			return;
		}

		$survey_handler = apply_filters( 'themeisle_sdk_dependency_script_handler', 'survey' );
		if ( empty( $survey_handler ) ) {
			return;
		}

		$survey_metadata = $this->get_survey_metadata();

		do_action( 'themeisle_sdk_dependency_enqueue_script', 'survey' );
		wp_enqueue_script( 'wpcf7r_survey', WPCF7_PRO_REDIRECT_ASSETS_PATH . 'js/survey.js', array( $survey_handler ), WPCF7_PRO_REDIRECT_PLUGIN_VERSION, true );
		wp_localize_script( 'wpcf7r_survey', 'wpcf7rSurveyData', $survey_metadata );

		if ( 0 === $survey_metadata['attributes']['plan'] ) {
			do_action( 'themeisle_sdk_load_banner', 'rfc7r' );
		}
	}

	/**
	 * Get plugin version from plugin data.
	 *
	 * @param string $plugin_path Plugin path.
	 *
	 * @return string
	 */
	public function get_plugin_version( $plugin_path = '' ) {
		$plugin_data = get_plugin_data( $plugin_path );
		return ! empty( $plugin_data['Version'] ) ? $plugin_data['Version'] : '';
	}

	/**
	 * Get the plan category for the product plan ID.
	 *
	 * @param object $license_data The license data.
	 * @return int
	 */
	private static function plan_category( $license_data ) {

		if ( ! isset( $license_data->plan ) || ! is_numeric( $license_data->plan ) ) {
			return 0; // Free.
		}

		$plan             = (int) $license_data->plan;
		$current_category = -1;

		$categories = array(
			'1' => array( 1, 4, 9 ), // Personal.
			'2' => array( 2, 5, 8 ), // Business/Developer.
			'3' => array( 3, 6, 7, 10 ), // Agency.
		);

		foreach ( $categories as $category => $plans ) {
			if ( in_array( $plan, $plans, true ) ) {
				$current_category = (int) $category;
				break;
			}
		}

		return $current_category;
	}
}
