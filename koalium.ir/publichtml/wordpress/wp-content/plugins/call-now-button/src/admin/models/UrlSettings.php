<?php

namespace cnb\admin\settings;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\utils\CnbUtils;

class UrlSettings {
	private $css_location;
	private $js_location;
	private $user_root;
	private $static_root;
	private $chat_root;
	/**
	 * @var string One of GCS, R2
	 */
	private $storage_type;

	public function __construct( $css_location, $js_location, $user_root, $static_root, $chat_root, $storage_type ) {
		$this->css_location  = $css_location;
		$this->js_location   = $js_location;
		$this->user_root     = $user_root;
		$this->static_root   = $static_root;
		$this->chat_root     = $chat_root;
		$this->storage_type  = $storage_type;
	}

	public function get_css_location() {
		return $this->css_location;
	}

	public function get_js_location() {
		return $this->js_location;
	}

	public function get_user_root() {
		return $this->user_root;
	}

	public function get_static_root() {
		return $this->static_root;
	}

	public function get_chat_root() {
		return $this->chat_root;
	}

	public function get_storage_type() {
		return $this->storage_type;
	}

	public static function fromObject( $object ) {
		if ( is_wp_error( $object ) ) {
			return $object;
		}

		return new UrlSettings(
			CnbUtils::getPropertyOrNull( $object, 'cssLocation' ),
			CnbUtils::getPropertyOrNull( $object, 'jsLocation' ),
			CnbUtils::getPropertyOrNull( $object, 'userRoot' ),
			CnbUtils::getPropertyOrNull( $object, 'staticRoot' ),
			CnbUtils::getPropertyOrNull( $object, 'chatRoot' ),
			CnbUtils::getPropertyOrNull( $object, 'storageType' )
		);
	}

	public function register_settings() {
		update_option('cnb_css_location', $this->css_location);
		update_option('cnb_js_location', $this->js_location);
		update_option('cnb_user_root', $this->user_root);
		update_option('cnb_static_root', $this->static_root);
		update_option('cnb_chat_root', $this->chat_root);
		update_option('cnb_storage_type', $this->storage_type);
	}

	public static function restoreFromOptions() {
		global $cnb_settings;

		$cnb_settings = new UrlSettings(
			get_option('cnb_css_location'),
			get_option('cnb_js_location'),
			get_option('cnb_user_root'),
			get_option('cnb_static_root'),
			get_option('cnb_chat_root'),
			get_option('cnb_storage_type')
		);
	}
}
