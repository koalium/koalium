<?php
/**
 * Controls Draft
 */

namespace Extendify\Shared\Controllers;

defined('ABSPATH') || die('No direct access.');

use Extendify\Shared\Services\Sanitizer;

/**
 * The controller for interacting with user settings
 */
class UserSettingsController
{
    /**
     * Persist the data
     *
     * @param \WP_REST_Request $request - The request.
     * @return \WP_REST_Response
     */
    public static function updateUserMeta($request)
    {
        $params = $request->get_json_params();
        \update_user_meta(\get_current_user_id(), 'extendify_' . $params['option'], Sanitizer::sanitizeUnknown($params['value']));

        return new \WP_REST_Response(['success' => true]);
    }
}
