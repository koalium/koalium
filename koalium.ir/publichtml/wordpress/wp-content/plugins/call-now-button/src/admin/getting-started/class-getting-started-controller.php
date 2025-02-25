<?php

namespace cnb\admin\gettingstarted;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

class GettingStartedController {

    public function get_slug() {
        return CNB_SLUG . '-getting-started';
    }
}
