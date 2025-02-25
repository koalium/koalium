<?php

namespace cnb\admin\condition;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\utils\CnbUtils;

class CnbConditionRouter {
    /**
     * Decides to either render the overview or the edit view
     *
     * @return void
     */
    public function render() {
        do_action( 'cnb_init', __METHOD__ );
        $action = ( new CnbUtils() )->get_query_val( 'action', null );
        switch ( $action ) {
            case 'new':
            case 'edit':
                ( new CnbConditionViewEdit() )->render();
                break;
            default:
                ( new CnbConditionView() )->render();
                break;
        }
        do_action( 'cnb_finish' );
    }
}
