<?php
/**
 * Insights setup
 */

namespace Extendify;

defined('ABSPATH') || die('No direct access.');

use Extendify\Shared\Services\Sanitizer;

/**
 * Controller for handling various Insights related things.
 * WP code reviewers: This is used in another plugin and not invoked here.
 */
class Insights
{
    /**
     * An array of active tests. 'A' should be the control.
     * For weighted tests, try ['A', 'A', 'A', 'A', 'B']
     *
     * @var array
     */
    protected $activeTests = [];

    /**
     * Process the readme file to get version and name
     *
     * @return void
     */
    public function __construct()
    {
        // If there isn't a siteId, then create one.
        if (!\get_option('extendify_site_id', false)) {
            \update_option('extendify_site_id', \wp_generate_uuid4());
        }

        if (defined('EXTENDIFY_INSIGHTS_URL')
            && class_exists('ExtendifyInsights')
            && !\get_option('extendify_insights_checkedin_once', 0)
        ) {
            \update_option('extendify_insights_checkedin_once', gmdate('Y-m-d H:i:s'));
            // WP code reviewers: This job is defined in another plugin (i.e. it's opt-in).
            \add_action('init', function () {
                // Run this once but wait 10 minutes.
                \wp_schedule_single_event((time() + 10 * MINUTE_IN_SECONDS), 'extendify_insights');
                \spawn_cron();
            });
        }

        $this->setUpActiveTests();
        $this->filterExternalInsights();
    }

    /**
     * Returns the active tests for the user, and sets up tests as needed.
     *
     * @return void
     */
    public function setUpActiveTests()
    {
        // Make sure that the active tests are set.
        $currentTests = \get_option('extendify_active_tests', []);
        $newTests = array_map(function ($test) {
            // Pick from value randomly.
            return $test[array_rand($test)];
        }, array_diff_key($this->activeTests, $currentTests));
        $testsCombined = array_merge($currentTests, $newTests);
        if ($newTests) {
            \update_option('extendify_active_tests', Sanitizer::sanitizeArray($testsCombined));
        }
    }

    /**
     * Add additional data to the opt-in insights
     *
     * @return void
     */
    public function filterExternalInsights()
    {
        add_filter('extendify_insights_data', function ($data) {
            // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
            $readme = file_get_contents(EXTENDIFY_PATH . 'readme.txt');
            preg_match('/Stable tag: ([0-9.:]+)/', $readme, $version);

            $insights = array_merge($data, [
                'launch' => defined('EXTENDIFY_SHOW_ONBOARDING') && constant('EXTENDIFY_SHOW_ONBOARDING'),
                'launchRedirectedAt' => \get_option('extendify_attempted_redirect', null),
                'launchLoadedAt' => \get_option('extendify_launch_loaded', null),
                'partner' => defined('EXTENDIFY_PARTNER_ID') ? constant('EXTENDIFY_PARTNER_ID') : null,
                'siteCreatedAt' => SiteSettings::getSiteCreatedAt(),
                'assistRouterData' => \get_option('extendify_assist_router', null),
                'libraryData' => \get_option('extendify_library_site_data', null),
                'draftSettingsData' => \get_option('extendify_draft_settings', null),
                'activity' => \get_option('extendify_shared_activity', null),
                'extendifyVersion' => ($version[1] ?? null),
                'siteProfile' => \get_option('extendify_site_profile', null),
                'pluginSearchTerms' => \get_option('extendify_plugin_search_terms', []),
                'blockSearchTerms' => \get_option('extendify_block_search_terms', []),
            ]);
            return $insights;
        });
    }
}
