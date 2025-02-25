<?php

namespace cnb\admin\settings;

// don't load directly
use cnb\utils\CnbUtils;

defined( 'ABSPATH' ) || die( '-1' );

class CnbSettingsRouter {

    /**
     * Decides to either render the overview or the edit view
     *
     * @return void
     */
    public function render() {
		add_filter('cnb_admin_notice_filter', function ($notice) {
			if ($notice && $notice->name === 'cnb-timezone-missing') return null;
			return $notice;
		});
        do_action( 'cnb_init', __METHOD__ );

        $action = ( new CnbUtils() )->get_query_val( 'action', null );
        switch ( $action ) {
            case 'delete_all_settings':
                if (getenv('WORDPRESS_CALL_NOW_BUTTON_TESTS') == 1) {
                    $view = new Settings_Reset_view();
                    $view->render();
                }
                break;
            default:
                $view = new CnbSettingsViewEdit();
                $view->render();
                break;
        }

        do_action( 'cnb_finish' );
    }
}
