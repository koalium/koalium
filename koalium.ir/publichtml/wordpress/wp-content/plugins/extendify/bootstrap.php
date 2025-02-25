<?php
/**
 * Bootstrap the application
 */

defined('ABSPATH') || die('No direct access.');

use Extendify\AdminPageRouter;
use Extendify\Affiliate;
use Extendify\Assist\Admin as AssistAdmin;
use Extendify\Config;
use Extendify\Draft\Admin as DraftAdmin;
use Extendify\HelpCenter\Admin as HelpCenterAdmin;
use Extendify\Insights;
use Extendify\Launch\Admin as LaunchAdmin;
use Extendify\Library\Admin as LibraryAdmin;
use Extendify\Library\Frontend as LibraryFrontend;
use Extendify\PartnerData;
use Extendify\Shared\Admin as SharedAdmin;
use Extendify\Shared\DataProvider\ResourceData;
use Extendify\Shared\Services\Import\ImagesImporter;
use Extendify\Shared\Services\VersionMigrator;

if (!defined('EXTENDIFY_REQUIRED_CAPABILITY')) {
    define('EXTENDIFY_REQUIRED_CAPABILITY', 'manage_options');
}

if (!defined('EXTENDIFY_PATH')) {
    define('EXTENDIFY_PATH', \plugin_dir_path(__FILE__));
}

if (!defined('EXTENDIFY_URL')) {
    define('EXTENDIFY_URL', \plugin_dir_url(__FILE__));
}

if (!defined('EXTENDIFY_PLUGIN_BASENAME')) {
    define('EXTENDIFY_PLUGIN_BASENAME', \plugin_basename(__DIR__ . '/extendify.php'));
}

if (is_readable(EXTENDIFY_PATH . 'vendor/autoload.php')) {
    require EXTENDIFY_PATH . 'vendor/autoload.php';
}

// This file should have no dependencies and always load.
new LibraryFrontend();
// This file hooks into an external task and should always load.
new Insights();
// This class set up the image import check scheduler.
new ImagesImporter();

// Set up scheduled cache (if opt-in).
ResourceData::scheduleCache();

// Run various database updates depending on the plugin version.
new VersionMigrator();

if (current_user_can(EXTENDIFY_REQUIRED_CAPABILITY)) {
    // The config class will collect information about the
    // partner and plugin, so it's easier to access.
    new Config();
    if (!defined('EXTENDIFY_DEVMODE')) {
        define('EXTENDIFY_DEVMODE', Config::$environment === 'DEVELOPMENT');
    }

    // This class handles the admin pages required for the plugin.
    new AdminPageRouter();
    // This class will fetch and cache partner data to be used
    // throughout every class below.
    new PartnerData();
    // This is a global "loader" class that loads in any assets that are shared everywhere.
    new SharedAdmin();
    // This class will handle loading library assets.
    new LibraryAdmin();

    // Only load these if the partner ID is set. These are all opt-in features.
    if ((Config::$partnerId && Config::$partnerId !== 'no-partner') || constant('EXTENDIFY_DEVMODE')) {
        // This class will update links based on the partner's specifications.
        new Affiliate();
        // The remaining classes handle loading assets for each individual products.
        // They are essentially asset loading classes.
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        if (isset($_GET['page']) && $_GET['page'] === 'extendify-assist') {
            // Load only on Assist.
            new AssistAdmin();
        }

        // Don't load on Launch.
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        if (!isset($_GET['page']) || $_GET['page'] !== 'extendify-launch') {
            new HelpCenterAdmin();
            if (PartnerData::setting('showDraft') || constant('EXTENDIFY_DEVMODE')) {
                new DraftAdmin();
            }
        } elseif (Config::$showLaunch) {
            new LaunchAdmin();
        }
    }//end if

    // This loads in all the REST API routes used by the plugin.
    require EXTENDIFY_PATH . 'routes/api.php';
}//end if

// This file is used to update the plugin and removed before w.org release.
if (is_readable(EXTENDIFY_PATH . '/updater.php')) {
    require EXTENDIFY_PATH . 'updater.php';
}

add_action('init', function () {
    load_plugin_textdomain('extendify-local', false, dirname(plugin_basename(__FILE__)) . '/languages/php');
});

// To cover legacy conflicts.
// phpcs:ignore
class ExtendifySdk
{
}
