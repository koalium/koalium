<?php
/**
 * Helper class for making http requests
 * This is legacy code and currently only used for Launch goals
 */

namespace Extendify;

defined('ABSPATH') || die('No direct access.');

/**
 * Controller for http communication
 */
class Http
{
    /**
     * The api endpoint
     *
     * @var string
     */
    public $baseUrl = 'https://dashboard.extendify.com/api/onboarding';

    /**
     * Request data sent to the server
     *
     * @var array
     */
    public $data = [];

    /**
     * Any headers required
     *
     * @var array
     */
    public $headers = [];

    /**
     * The class instance.
     *
     * @var $instance
     */
    protected static $instance = null;

    /**
     * Set up the base object to send with every request
     *
     * @param \WP_REST_Request $request - The request.
     *
     * @return void
     */
    public function __construct($request)
    {
        // Redundant, but extra protection!
        if (!\wp_verify_nonce(sanitize_text_field(wp_unslash($request->get_header('x_wp_nonce'))), 'wp_rest')) {
            return;
        }

        $this->data = [
            'wp_language' => \get_locale(),
            'wp_theme' => \get_option('template'),
            'wp_version' => \get_bloginfo('version'),
            'mode' => Config::$environment,
            'library_version' => Config::$version,
            'wp_active_plugins' => $request->get_method() === 'POST' ? \get_option('active_plugins') : [],
            'is_block_theme' => function_exists('wp_is_block_theme') ? wp_is_block_theme() : false,
            'sdk_partner' => PartnerData::$id,
        ];

        if ($request->get_header('x_extendify_dev_mode') === 'true') {
            $this->data['devmode'] = true;
        }

        $this->headers = [
            'Accept' => 'application/json',
            'referer' => $request->get_header('referer'),
            'user_agent' => $request->get_header('user_agent'),
        ];
    }

    /**
     * Register dynamic routes
     *
     * @param string $endpoint - The endpoint.
     * @param array  $data     - The data to include.
     * @param array  $headers  - The headers to include.
     *
     * @return array
     */
    public function getHandler($endpoint, $data = [], $headers = [])
    {
        $url = \esc_url_raw(
            \add_query_arg(
                \urlencode_deep(\urldecode_deep(array_merge($this->data, $data))),
                $this->baseUrl . $endpoint
            )
        );

        $response = \wp_remote_get(
            $url,
            [
                'headers' => array_merge($this->headers, $headers),
            ]
        );

        if (\is_wp_error($response)) {
            return $response;
        }

        if ((int) $response['response']['code'] >= 500) {
            return new \WP_Error($response['response']['code'], $response['response']['message']);
        }

        $responseBody = \wp_remote_retrieve_body($response);
        return json_decode($responseBody, true);
    }

    /**
     * The caller
     *
     * @param string $name      - The name of the method to call.
     * @param array  $arguments - The arguments to pass in.
     *
     * @return mixed
     */
    public static function __callStatic($name, array $arguments)
    {
        if ($name === 'init') {
            self::$instance = new static($arguments[0]);
            return;
        }

        $name = "{$name}Handler";
        $r = self::$instance;

        return $r->$name(...$arguments);
    }
}
