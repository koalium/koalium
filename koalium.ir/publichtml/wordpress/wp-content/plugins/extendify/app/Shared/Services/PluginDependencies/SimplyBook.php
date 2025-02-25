<?php
/**
 * SimplyBook pattern replacement.
 */

namespace Extendify\Shared\Services\PluginDependencies;

defined('ABSPATH') || die('No direct access.');

/**
 * SimplyBook pattern replacement class.
 */
class SimplyBook
{
    /**
     * The plugin slug.
     *
     * @var string
     */
    public static $slug = 'simplybook/simplybook.php';

    /**
     * Replace the placeholder for SimplyBook.
     *
     * @param mixed  $code    - The code data.
     * @param string $key     - The plugin key.
     * @param string $newCode - The plugin pattern code.
     * @return mixed
     */
    public static function create($code, $key, $newCode)
    {
        if ($key !== 'simple' || !preg_match('/\[simplybook_widget\]|wp:simplybook\/widget/m', $newCode)) {
            return $code;
        }

        require_once ABSPATH . 'wp-admin/includes/plugin.php';

        // If the plugin is already installed and active, we don't need to install it again.
        if (!is_plugin_active(self::$slug)) {
            $response = PluginInstaller::installPlugin('simplybook', self::$slug);
            if (is_wp_error($response)) {
                return $response;
            }
        }

        return $newCode;
    }
}
