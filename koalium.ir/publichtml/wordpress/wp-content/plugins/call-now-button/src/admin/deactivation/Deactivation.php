<?php

namespace cnb\admin\deactivation;

use cnb\cron\Cron;

/**
 * On Deactivation of our plugin.
 */
class Deactivation {

    /**
     * This is called /during/ the deactivation process (so, not before - there is no change for output).
     *
     * @return void
     */
    static public function on_deactivation() {
        $cnb_cron = new Cron();
		$cnb_cron->unregister_hook();
    }
}
