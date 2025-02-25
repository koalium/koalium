<?php
/**
 * Filters for affiliate links.
 */

namespace Extendify;

defined('ABSPATH') || die('No direct access.');

/**
 * The affiliate class.
 */
class Affiliate
{
    /**
     * Affiliate data
     *
     * @var array $data
     */
    protected $data = [];

    /**
     * Initiate the class.
     */
    public function __construct()
    {
        $this->data = PartnerData::getPartnerData();

        $this->wpforms();
        $this->aioseo();
        $this->monsterInsights();
    }

    /**
     * Add the affiliate links to WPForms.
     *
     * @return void
     */
    private function wpforms()
    {
        if (! $this->isEnabled('wpforms-lite')) {
            return;
        }

        add_filter('wpforms_upgrade_link', function ($url) {
            return sprintf(
                'http://www.shareasale.com/r.cfm?B=837827&U=3909268&M=64312&urllink=%s&afftrack=%s',
                rawurlencode($url),
                Config::$partnerId
            );
        }, PHP_INT_MAX);
    }

    /**
     * Add the affiliate links to AIOSEO.
     *
     * @return void
     */
    private function aioseo()
    {
        if (! $this->isEnabled('all-in-one-seo-pack')) {
            return;
        }

        add_filter('aioseo_upgrade_link', function ($url) {
            return sprintf(
                'https://shareasale.com/r.cfm?b=1491200&u=3909268&m=94778&urllink=%s&afftrack=%s',
                rawurlencode($url),
                Config::$partnerId
            );
        }, PHP_INT_MAX);
    }

    /**
     * Add the affiliate links to MonsterInsights.
     *
     * @return void
     */
    private function monsterInsights()
    {
        if (! $this->isEnabled('google-analytics-for-wordpress')) {
            return;
        }

        add_filter('monsterinsights_shareasale_id', function () {
            return 3909268;
        }, PHP_INT_MAX );

        add_filter('monsterinsights_shareasale_redirect_entire_url', function ($url) {
            return sprintf(
                'https://shareasale.com/r.cfm?b=966004&u=3909268&m=69975&urllink=%s&afftrack=%s',
                rawurlencode($url),
                Config::$partnerId
            );
        }, PHP_INT_MAX, 1);
    }

    /**
     * Check to see if the affiliation is enabled for the plugin.
     *
     * @param string $pluginSlug The plugin slug.
     * @return boolean
     */
    private function isEnabled($pluginSlug)
    {
        return !array_key_exists('blockAffiliate_' . $pluginSlug, $this->data);
    }

}
