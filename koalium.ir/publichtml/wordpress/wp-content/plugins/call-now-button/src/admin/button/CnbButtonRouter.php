<?php

namespace cnb\admin\button;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\utils\CnbUtils;

class CnbButtonRouter {

    /**
     * Decides to either render the overview or the edit view
     *
     * @return void
     */
    public function render() {
        do_action( 'cnb_init', __METHOD__ );
        $action = ( new CnbUtils() )->get_query_val( 'action', null );
        switch ( $action ) {
            case 'edit':
                ( new CnbButtonViewEdit() )->render();
                break;
            case 'enable':
            case 'disable':
                ( new CnbButtonController() )->enable_disable();
                ( new CnbButtonView() )->render();
                break;
            case 'new':
            default:
                ( new CnbButtonView() )->render();
                break;
        }
        do_action( 'cnb_finish' );
    }
}
