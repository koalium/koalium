<?php
/**
 * Plugin Name:  Redirection for Contact Form 7
 * Description:  The ultimate add-on for Contact Form 7 - redirect to any page after submission, fire scripts, save submissions in database, and much more options to make Contact Form 7 powerful than ever.
 * Version:      3.1.10
 * Author:       Themeisle
 * Author URI:   http://themeisle.com
 * License:      GPLv3 or later
 * License URI:  https://www.gnu.org/licenses/gpl-3.0.html
 * Contributors: codeinwp, themeisle, yuvalsabar, regevlio
 * Requires at least: 5.1
 *
 * Text Domain: wpcf7-redirect
 * Domain Path: /lang
 *
 * WordPress Available:  yes
 * Requires License:    no
 *
 * @package Redirection for Contact Form 7
 * @category Contact Form 7 Add-on
 * @author Themeisle
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'CF7_REDIRECT_DEBUG' ) ) {
	define( 'CF7_REDIRECT_DEBUG', get_option( 'wpcf_debug' ) ? true : false );
}

define( 'WPCF7_PRO_REDIRECT_PLUGIN_VERSION', '3.1.10' );
define( 'WPCF7_PRO_MIGRATION_VERSION', '1' );
define( 'WPCF7_PRO_REDIRECT_CLASSES_PATH', plugin_dir_path( __FILE__ ) . 'classes/' );
define( 'WPCF7_PRO_REDIRECT_PATH', plugin_dir_path( __FILE__ ) );
define( 'WPCF7_BASENAME', basename( WPCF7_PRO_REDIRECT_PATH ) );

require_once 'licensing_fs.php';
require_once WPCF7_PRO_REDIRECT_CLASSES_PATH . 'class-wpcf7r-action.php';
require_once WPCF7_PRO_REDIRECT_CLASSES_PATH . 'class-wpcf7r-utils.php';
require_once WPCF7_PRO_REDIRECT_CLASSES_PATH . 'class-wpcf7r-actions.php';
require_once WPCF7_PRO_REDIRECT_CLASSES_PATH . 'class-wpcf7r-survey.php';
require_once 'class-wpcf7-redirect.php';

$vendor_file = WPCF7_PRO_REDIRECT_PATH . 'vendor/autoload.php';
if ( is_readable( $vendor_file ) ) {
	include_once $vendor_file;
}

add_action( 'admin_init', 'wpcf7r_activation_process' );

/**
 * Handle plugin upgrade migration
 */
function wpcf7r_activation_process() {
	if ( get_option( 'wpcf7_redirect_version' ) !== WPCF7_PRO_REDIRECT_PLUGIN_VERSION ) {
		$extensions_url = admin_url( wpcf7_get_freemius_addons_path() );

		update_option( 'wpcf7_redirect_dismiss_banner', 0 );

		update_option( 'wpcf7_redirect_version', WPCF7_PRO_REDIRECT_PLUGIN_VERSION );

		update_option(
			'wpcf7_redirect_notifications',
			array(
				'notice-success wpcf7r-notice' => '<p><span class="dashicons dashicons-feedback"></span> <a href="' . $extensions_url . '">  Redirection for Contact Form 7 - check out our new forms extensions - don\'t miss it!</a></p><p>Also, we will be happy if you can take a few moments and <a href="https://wordpress.org/support/plugin/wpcf7-redirect/reviews/" target="_blank">rate our plugin</a>.</p>',
			)
		);
	}
}

require_once plugin_dir_path( __FILE__ ) . 'wpcf7r-functions.php';

/**
 * WPCF7R initialization
 */
function wpcf7_redirect_pro_init() {
	// globals.
	global $cf7_redirect;

	// initialize.
	if ( ! isset( $cf7_redirect ) ) {
		$cf7_redirect = new Wpcf7_Redirect();
		$cf7_redirect->init();
	}

	add_filter( 'themeisle_sdk_products', function ( $products ) {
		$products[] = __FILE__;

		return $products;
	} );
	add_filter( 'themeisle_sdk_hide_dashboard_widget', '__return_false' );

	add_filter(
		'themeisle_sdk_compatibilities/' . WPCF7_BASENAME,
		function ( $compatibilities ) {
			$required = '3.1.5';
			$tested   = '3.2';
			$compatibilities['wpcf7rApi'] = [
				'basefile'  => defined( 'WPCF7_ACTION_API_PATH' ) ? WPCF7_ACTION_API_PATH . 'init.php' : '',
				'required'  => $required,
				'tested_up' => $tested,
			];
			$compatibilities['wpcf7rConditionalLogic'] = [
				'basefile'  => defined( 'WPCF7_ACTION_CONDITIONAL_LOGIC_PATH' ) ? WPCF7_ACTION_CONDITIONAL_LOGIC_PATH . 'init.php' : '',
				'required'  => $required,
				'tested_up' => $tested,
			];
			$compatibilities['wpcf7rCreatePost'] = [
				'basefile'  => defined( 'WPCF7_ACTION_CREATE_POST_PATH' ) ? WPCF7_ACTION_CREATE_POST_PATH . 'init.php' : '',
				'required'  => $required,
				'tested_up' => $tested,
			];
			$compatibilities['wpcf7rHubspot'] = [
				'basefile'  => defined( 'WPCF7_ACTION_HUBSPOT_PATH' ) ? WPCF7_ACTION_HUBSPOT_PATH . 'init.php' : '',
				'required'  => $required,
				'tested_up' => $tested,
			];
			$compatibilities['wpcf7rMailchimp'] = [
				'basefile'  => defined( 'WPCF7_ACTION_MAILCHIMP_PATH' ) ? WPCF7_ACTION_MAILCHIMP_PATH . 'init.php' : '',
				'required'  => $required,
				'tested_up' => $tested,
			];
			$compatibilities['wpcf7rPaypal'] = [
				'basefile'  => defined( 'WPCF7_ACTION_PAYPAL_PATH' ) ? WPCF7_ACTION_PAYPAL_PATH . 'init.php' : '',
				'required'  => $required,
				'tested_up' => $tested,
			];
			$compatibilities['wpcf7rPdf'] = [
				'basefile'  => defined( 'WPCF7_ACTION_CREATE_PDF_PATH' ) ? WPCF7_ACTION_CREATE_PDF_PATH . 'init.php' : '',
				'required'  => $required,
				'tested_up' => $tested,
			];
			$compatibilities['wpcf7rPopup'] = [
				'basefile'  => defined( 'WPCF7_ACTION_POPUP_PATH' ) ? WPCF7_ACTION_POPUP_PATH . 'init.php' : '',
				'required'  => $required,
				'tested_up' => $tested,
			];
			$compatibilities['wpcf7rSalesforce'] = [
				'basefile'  => defined( 'WPCF7_ACTION_SALESFORCE_PATH' ) ? WPCF7_ACTION_SALESFORCE_PATH . 'init.php' : '',
				'required'  => $required,
				'tested_up' => $tested,
			];
			$compatibilities['wpcf7rStripe'] = [
				'basefile'  => defined( 'WPCF7_ACTION_STRIPE_PATH' ) ? WPCF7_ACTION_STRIPE_PATH . 'init.php' : '',
				'required'  => $required,
				'tested_up' => $tested,
			];
			$compatibilities['wpcf7rTwilio'] = [
				'basefile'  => defined( 'WPCF7R_TWILIO_PATH' ) ? WPCF7R_TWILIO_PATH . 'init.php' : '',
				'required'  => $required,
				'tested_up' => $tested,
			];

			return $compatibilities;
		}
	);

	// return.
	return $cf7_redirect;
}

wpcf7_redirect_pro_init();
