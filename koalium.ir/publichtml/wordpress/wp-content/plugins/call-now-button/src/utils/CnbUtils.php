<?php

namespace cnb\utils;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\admin\models\CnbUser;
use stdClass;
use WP_Error;

class CnbUtils {
    /**
     * @param $object null|array|stdClass
     * @param $property
     *
     * @return mixed|null
     */
    public static function getPropertyOrNull( $object, $property ) {
        return CnbUtils::getPropertyOr( $object, $property, null);
    }

	public static function getPropertyOr( $object, $property, $default ) {
		if ( $object === null ) {
			return $default;
		}

		if ( is_array( $object ) && array_key_exists( $property, $object ) ) {
			return $object[ $property ];
		}

		if ( $object instanceof stdClass && property_exists( $object, $property ) ) {
			return $object->$property;
		}

		return $default;
	}

    /**
     * Returns true if the `active` flag is set and is enabled
     *
     * @param $cnb_options array the current set of Button Options
     *
     * @return bool
     */
    function isButtonActive( $cnb_options ) {
        $active = isset( $cnb_options['active'] ) && $cnb_options['active'] == 1;
        $cloud  = $this->isCloudActive( $cnb_options );

        return $active || $cloud;
    }

    /**
     * Checks if cloud_enabled is set and equal to 1 or "1", returns 1 if so (0 otherwise).
     *
     * Basically, a cast to an integer.
     *
     * @param $cnb_options array
     *
     * @return int
     */
    function isCloudActive( $cnb_options ) {
        return isset( $cnb_options['cloud_enabled'] ) && $cnb_options['cloud_enabled'] == 1 ? 1 : 0;
    }

    /**
     * Check if the Cloud ID (user or domain) is set
     *
     * @param $cnb_options array
     *
     * @return int|string int 0 if no ID is found, otherwise the string UUID is returned.
     */
    private function isCloudIdSet( $cnb_options ) {
        return isset( $cnb_options['cloud_use_id'] ) && ! empty( $cnb_options['cloud_use_id'] ) ? $cnb_options['cloud_use_id'] : 0;
    }

    /**
     * Get the default glyph for a particular action type.
     *
     * This should have the same content as the JS function cnbActiontypeToIcontext
     *
     * @param $actionType string
     *
     * @return string
     */
    function cnb_actiontype_to_icontext( $actionType ) {
        switch ( $actionType ) {
            case 'ANCHOR': return 'anchor';
	        case 'CHAT': return 'chat';
            case 'EMAIL': return 'email';
            case 'HOURS': return 'access_time';
            case 'LINK': return 'link';
            case 'MAP': return 'directions';
            case 'SMS': return 'chat';
            case 'WHATSAPP': return 'whatsapp';
            case 'FACEBOOK': return 'facebook_messenger';
            case 'SIGNAL': return 'signal';
            case 'TELEGRAM': return 'telegram';
            case 'IFRAME': return 'open_modal';
            case 'TALLY': return 'call3';
            case 'INTERCOM': return 'intercom';
	        case 'SKYPE': return 'skype';
	        case 'ZALO': return 'zalo';
	        case 'VIBER': return 'viber';
	        case 'LINE': return 'line';
	        case 'WECHAT': return 'wechat';
            case 'PHONE':
            default:
                return 'call';
        }
    }

    /**
     *
     * @param $utm_campaign string e.g. "footer-links"
     * @param $utm_term string e.g. "support"
     *
     * @return array
     */
    private function cnb_utm_params( $utm_campaign, $utm_term ) {
        return array(
            'utm_source' => 'wp-plugin_' . str_replace(' ', '' , CNB_NAME) . '_' . CNB_VERSION,
            'utm_medium' => 'referral',
            'utm_campaign' => $utm_campaign,
            'utm_term' => $utm_term,
        );
    }

    function get_support_url($path = '', $utm_campaign = null, $utm_term = null, $product = null) {
        $supportUrl = $product == 'legacy' ? CNB_SUPPORT_LEGACY : CNB_SUPPORT;
        return add_query_arg(
            $this->cnb_utm_params($utm_campaign, $utm_term),
            $supportUrl . $path);
    }

    function get_website_url($path = '', $utm_campaign = null, $utm_term = null) {
        return add_query_arg(
            $this->cnb_utm_params($utm_campaign, $utm_term),
            CNB_WEBSITE . $path);
    }

    function get_app_url($path = '', $utm_campaign = null, $utm_term = null) {
        return add_query_arg(
            $this->cnb_utm_params($utm_campaign, $utm_term),
            CNB_APP . $path);
    }

    function cnb_array_column( $array, $column_key, $index_key = null ) {
        if ( version_compare( PHP_VERSION, '7.0.0', '>=' ) ) {
            // phpcs:ignore PHPCompatibility.FunctionUse
            return array_column( $array, $column_key, $index_key );
        } else {
            // Convert objects to array, since PHP < 7 cannot deal with objects as the first argument
            $array_arr = array();
            foreach ( $array as $key => $value ) {
                $array_arr[ $key ] = (array) $value;
            }
            if ( ! function_exists( 'array_column' ) ) {
                return array_column_ext( $array_arr, $column_key, $index_key );
            }

            // phpcs:ignore PHPCompatibility.FunctionUse
            return array_column( $array_arr, $column_key, $index_key );
        }
    }

    function cnb_timestamp_to_string( $timestamp ) {
        $timestamp_parsed = strtotime( $timestamp );
        if ( $timestamp instanceof stdClass ) {
            return date( 'r', $timestamp->seconds );
        } else if ( $timestamp_parsed ) {
            return date( 'r', $timestamp_parsed );
        }

        return $timestamp;
    }

    /**
     * Same as check_ajax_referer, but does not die() by default
     *
     * @param string $action
     * @param bool $query_arg
     * @param bool $die
     *
     * @return false|int|mixed|void
     */
    function cnb_check_ajax_referer( $action, $query_arg = false, $die = false ) {
        return check_ajax_referer( $action, $query_arg, $die );
    }

    function get_cnb_domain_upgrade() {
        global $cnb_domain;
        if ( $cnb_domain && ! ( $cnb_domain instanceof WP_Error ) ) {
            $url = admin_url( 'admin.php' );

            return add_query_arg( array(
                'page'   => 'call-now-button-domains',
                'action' => 'upgrade',
                'id'     => $cnb_domain->id,
            ),
                $url );
        }

        return null;
    }

    function is_use_cloud( $cnb_options ) {
        $cloud_enabled = $this->isCloudActive( $cnb_options );
        $cloud_use_id  = $this->isCloudIdSet( $cnb_options );

        return $cloud_enabled && $cloud_use_id !== 0;
    }

    function is_valid_timezone_string( $timezone_string ) {
        // Do not allow: +00:00 variants
        $plus_variants_domain = strripos( $timezone_string, '+' ) !== false;
        // Do not allow: utc or gmt variants
        $utc_variants_domain = strripos( $timezone_string, 'utc' ) !== false
                                || strripos( $timezone_string, 'gmt' ) !== false;

        // We DO allow UTC itself as an exception
        $is_utc_exactly = strcasecmp( $timezone_string, 'utc' ) === 0;

        // It needs to have a slash (or be "utc" exactly)
        $has_slash = strripos( $timezone_string, '/' ) !== false;

        return $is_utc_exactly || ( ! ( $plus_variants_domain || $utc_variants_domain ) && $has_slash );
    }

    /**
     * Similar to WP_Query get_query_val, but for admin pages (since get_query_val does not work outside The Loop).
     *
     * @param $key string The query parameter to get
     * @param $default_if_missing string
     *
     * @return string sanitized value of the $_GET parameter
     */
    function get_query_val( $key, $default_if_missing = '' ) {
        // phpcs:ignore WordPress.Security
        if ( key_exists( $key, $_GET ) && ! empty( $_GET[ $key ] ) ) {
            // phpcs:ignore WordPress.Security
            return sanitize_text_field( wp_unslash( $_GET[ $key ] ) );
        }

        return $default_if_missing;
    }

    /**
     * Similar to WP_Query get_query_val, but for admin pages (since get_query_val does not work outside The Loop).
     *
     * @param $key string The POST parameter to get
     * @param $default_if_missing string
     *
     * @return string sanitized value of the $_GET parameter
     */
    function get_post_val( $key, $default_if_missing = '' ) {
        // phpcs:ignore WordPress.Security
        if ( key_exists( $key, $_POST ) && ! empty( $_POST[ $key ] ) ) {
            // phpcs:ignore WordPress.Security
            return sanitize_text_field( wp_unslash( $_POST[ $key ] ) );
        }

        return $default_if_missing;
    }

    /**
     * Check if the error_reporting settings is set and enabled.
     *
     * @return bool
     */
    function is_reporting_enabled() {
        $cnb_options = get_option( 'cnb' );
        return ( key_exists( 'error_reporting', $cnb_options ) && $cnb_options['error_reporting'] );
    }

	/**
	 * Feature flag for the new Chat API
	 */
	function is_chat_api_enabled() {
		/** @type CnbUser|WP_Error|null $cnb_user */
		global $cnb_user;

		if ( $cnb_user && ! is_wp_error( $cnb_user ) && $cnb_user->has_role( 'ROLE_CHAT_USER' ) ) {
			return true;
		}
		return false;
	}
}
