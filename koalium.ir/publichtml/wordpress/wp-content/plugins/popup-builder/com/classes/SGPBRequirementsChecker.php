<?php
class SGPBRequirementsChecker
{
	public static function init()
	{
		self::checkPhpVersion();
	}

	public static function checkPhpVersion()
	{
		if (version_compare(PHP_VERSION, SG_POPUP_MINIMUM_PHP_VERSION, '<')) {
			/* translators: 1: SG POPUP MINIMUM PHP VERSION 2: PHP VERSION */
			wp_die(sprintf( esc_html__('Popup Builder plugin requires PHP version >= %1$s version required. You server using PHP version = %2$s', 'popup-builder'), esc_html(SG_POPUP_MINIMUM_PHP_VERSION), PHP_VERSION));
		}
	}
}

