<?php

namespace cnb\admin\gettingstarted;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

class GettingStartedRouter {

    public function render() {
        $view = new GettingStartedView();
        $view->render();
    }
}
