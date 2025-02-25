<?php

namespace cnb;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\admin\action\CnbActionController;
use cnb\admin\action\CnbActionRouter;
use cnb\admin\agency\CnbAgencyRouter;
use cnb\admin\api\CnbAppRemote;
use cnb\admin\api\CnbUserController;
use cnb\admin\apikey\CnbApiKeyController;
use cnb\admin\apikey\CnbApiKeyRouter;
use cnb\admin\apikey\OttController;
use cnb\admin\button\CnbButtonController;
use cnb\admin\button\CnbButtonRouter;
use cnb\admin\chat\CnbChatController;
use cnb\admin\chat\CnbChatRouter;
use cnb\admin\CnbAdminAjax;
use cnb\admin\condition\CnbConditionController;
use cnb\admin\condition\CnbConditionRouter;
use cnb\admin\deactivation\Activation;
use cnb\admin\domain\CnbDomainController;
use cnb\admin\domain\CnbDomainRouter;
use cnb\admin\templates\Template_Controller;
use cnb\admin\templates\Template_Router;
use cnb\admin\gettingstarted\GettingStartedController;
use cnb\admin\gettingstarted\GettingStartedRouter;
use cnb\admin\legacy\CnbLegacyEdit;
use cnb\admin\legacy\CnbLegacyUpgrade;
use cnb\admin\models\ValidationHooks;
use cnb\admin\profile\CnbProfileController;
use cnb\admin\profile\CnbProfileRouter;
use cnb\admin\settings\CnbSettingsController;
use cnb\admin\settings\CnbSettingsRouter;
use cnb\cron\Cron;
use cnb\notices\CnbAdminNotices;
use cnb\utils\Cnb_Sentry;
use cnb\utils\CnbAdminFunctions;
use cnb\utils\CnbUtils;

class CallNowButton {

    /**
     * Adds the plugin to the options menu
     */
    public function register_admin_pages() {
        global $wp_version;

        $cnb_options       = get_option( 'cnb' );
        $utils             = new CnbUtils();
        $cnb_cloud_hosting = $utils->isCloudActive( $cnb_options );
        $plugin_title      = apply_filters( 'cnb_plugin_title', CNB_NAME );

        $button_router = new CnbButtonRouter();
        $legacy_edit   = new CnbLegacyEdit();
        $menu_page_function = $cnb_cloud_hosting ?
            array( $button_router, 'render' ) :
            array( $legacy_edit, 'render' );

        $counter            = 0;
	    $menu_page_header   = $cnb_cloud_hosting ? 'Buttons' : 'Call Now Button';
        $menu_page_title    = $menu_page_header . '<span class="awaiting-mod" id="cnb-nav-counter" style="display: none">' . $counter . '</span>';

        $menu_page_position = $cnb_cloud_hosting ? 30 : 66;

        $header_notices = new CnbHeaderNotices();
        $has_changelog = $header_notices->upgrade_notice();
        $is_dismissed = CnbAdminNotices::get_instance()->is_dismissed($header_notices->cnb_get_upgrade_notice_dismiss_name());
        if ($has_changelog && !$is_dismissed) $counter++;

        // Detect errors (specific, - Premium enabled, but API key is not present yet)
        if ( $cnb_cloud_hosting && ! array_key_exists( 'api_key', $cnb_options ) ) {
            $counter = '!';
        }

		// Check if there is an outstanding payment
	    $cnb_remote = new CnbAppRemote();
	    $wp_info = $cnb_remote->get_subscription_data();
		if ($wp_info && $wp_info->has_outstanding_payment()) {
			$counter = '!';
		}

        if ( $counter ) {
	        $menu_page_header_small   = $cnb_cloud_hosting ? 'Buttons' : 'Call Now Bu...';
            $menu_page_title = $menu_page_header_small . ' <span class="awaiting-mod" id="cnb-nav-counter">' . $counter . '</span>';
        }

        // Oldest WordPress only has "smartphone", no "phone" (this is added in a later version)
        $cnb_free_icon = version_compare( $wp_version, '5.5.0', '<' ) ? 'dashicons-smartphone' : 'dashicons-phone';
        $icon_url = $cnb_cloud_hosting ? 'dashicons-marker' : $cnb_free_icon;

        add_menu_page(
            'Call Now Button - Overview',
            $menu_page_title,
            'manage_options',
            CNB_SLUG,
            $menu_page_function,
            $icon_url,
            $menu_page_position
        );

	    $api_key_router = new CnbApiKeyRouter();
        if ( $cnb_cloud_hosting ) {
            // Button overview
            add_submenu_page( CNB_SLUG, $plugin_title, 'All buttons', 'manage_options', CNB_SLUG, array( $button_router, 'render' ) );

	        add_submenu_page( CNB_SLUG, $plugin_title, 'Add New', 'manage_options', CNB_SLUG . '&action=new', array( $button_router, 'render' ) );

	        // Only for WordPress 5.2 and higher (Gutenberg + React 16.8)
	        $has_gutenberg = version_compare( $wp_version, '5.2.0', '>=' );
			if ($has_gutenberg) {
				$template_router = new Template_Router();
				$template_controller = new Template_Controller();
				add_submenu_page( CNB_SLUG, $plugin_title, 'Templates', 'manage_options', $template_controller->get_slug(), array(
					$template_router,
					'render',
				) );
			}

			$chat_controller = new CnbChatController();
			if ($chat_controller->has_chat_enabled()) {
				$chat_router = new CnbChatRouter();
				add_submenu_page( CNB_SLUG, $plugin_title, 'Live Chat', 'manage_options', $chat_router->get_slug(), array(
					$chat_router,
					'render',
				) );
			}

            $domain_router = new CnbDomainRouter();
            $action_router = new CnbActionRouter();
            $condition_router = new CnbConditionRouter();
            $profile_router = new CnbProfileRouter();

            $agency_router = new CnbAgencyRouter();
            if ( $utils->get_query_val( 'page' ) === 'call-now-button-agency' && $utils->get_query_val( 'action' ) === 'upgrade' ) {
                add_submenu_page( CNB_SLUG, $plugin_title, 'Agency plan', 'manage_options', CNB_SLUG . '-agency', array( $agency_router, 'render' ) );
            }

            if ( CnbSettingsController::is_advanced_view() ) {
                // Domain overview
                add_submenu_page( CNB_SLUG, $plugin_title, 'Domains', 'manage_options', CNB_SLUG . '-domains', array( $domain_router, 'render' ) );

                // Action overview
                add_submenu_page( CNB_SLUG, $plugin_title, 'Actions', 'manage_options', CNB_SLUG . '-actions', array( $action_router, 'render' ) );

                // Condition overview
                add_submenu_page( CNB_SLUG, $plugin_title, 'Conditions', 'manage_options', CNB_SLUG . '-conditions', array( $condition_router, 'render' ) );

                // Apikey overview
                add_submenu_page( CNB_SLUG, $plugin_title, 'API Keys', 'manage_options', CNB_SLUG . '-apikeys', array( $api_key_router, 'render' ) );

                // Profile edit
                add_submenu_page( CNB_SLUG, $plugin_title, 'Profile', 'manage_options', CNB_SLUG . '-profile', array( $profile_router, 'render' ) );
            } else {
                // Fake out Action overview
                if ( $utils->get_query_val( 'page' ) === 'call-now-button-actions' && $utils->get_query_val( 'action' ) ) {
                    add_submenu_page( CNB_SLUG, $plugin_title, 'Edit action', 'manage_options', CNB_SLUG . '-actions', array( $action_router, 'render' ) );
                }
                // Fake out Conditions overview
                if ( $utils->get_query_val( 'page' ) === 'call-now-button-conditions' && $utils->get_query_val( 'action' ) ) {
                    add_submenu_page( CNB_SLUG, $plugin_title, 'Edit condition', 'manage_options', CNB_SLUG . '-conditions', array( $condition_router, 'render' ) );
                }
                // Fake out Domain upgrade page
                if ( $utils->get_query_val( 'page' ) === 'call-now-button-domains' && $utils->get_query_val( 'action' ) === 'upgrade' ) {
                    add_submenu_page( CNB_SLUG, $plugin_title, 'Upgrade domain', 'manage_options', CNB_SLUG . '-domains', array( $domain_router, 'render' ) );
                }
	            if ( $utils->get_query_val( 'page' ) === 'call-now-button-domains' && $utils->get_query_val( 'action' ) === 'payment' ) {
		            add_submenu_page( CNB_SLUG, $plugin_title, 'Payment', 'manage_options', CNB_SLUG . '-domains', array( $domain_router, 'render' ) );
	            }
            }
        } else {
            // Legacy edit
            add_submenu_page( CNB_SLUG, $plugin_title, 'My button', 'manage_options', CNB_SLUG, array( $legacy_edit, 'render' ) );

            $legacy_upgrade =new CnbLegacyUpgrade();
            add_submenu_page( CNB_SLUG, $plugin_title, 'Unlock features', 'manage_options', CNB_SLUG . '-upgrade', array( $legacy_upgrade, 'render' ) );
        }

	    // Welcome after Activation
	    if ( $utils->get_query_val( 'page' ) === CNB_SLUG . '-activated' ) {
		    add_submenu_page( CNB_SLUG, $plugin_title, 'Cloud activation', 'manage_options', CNB_SLUG . '-activated', array( $api_key_router, 'render' ) );
	    }

	    // Settings pages
        $settings_router = new CnbSettingsRouter();
        add_submenu_page( CNB_SLUG, $plugin_title, 'Settings', 'manage_options', CNB_SLUG . '-settings', array( $settings_router, 'render' ) );
    }

    public function register_welcome_page() {
        $utils             = new CnbUtils();
        $controller = new GettingStartedController();
        $menu_slug = $controller->get_slug();

        if ( $utils->get_query_val( 'page' ) === $menu_slug ) {
            $getting_started_router = new GettingStartedRouter();
            add_dashboard_page(
                esc_html__( 'Welcome to Call Now Button' ),
                esc_html__( 'Call Now Button' ),
                'manage_options',
                $menu_slug,
                array( $getting_started_router, 'render' )
            );
        }
    }

    public function hide_welcome_page() {
        $controller = new GettingStartedController();
        remove_submenu_page('index.php', $controller->get_slug());
    }

    public function plugin_meta( $links, $file ) {
        $cnb_options       = get_option( 'cnb' );
        $cnb_utils         = new CnbUtils();
        $cnb_cloud_hosting = $cnb_utils->isCloudActive( $cnb_options );

        if ( $file == CNB_BASENAME ) {

            $url = admin_url( 'admin.php' );

            $button_link =
                add_query_arg(
                    array(
                        'page' => 'call-now-button',
                    ),
                    $url );

            $settings_link =
                add_query_arg(
                    array(
                        'page' => 'call-now-button-settings',
                    ),
                    $url );

            $link_name     = $cnb_cloud_hosting ? __( 'All buttons' ) : __( 'My button' );
            $cnb_new_links = array(
                sprintf( '<a href="%s">%s</a>', esc_url( $button_link ), $link_name ),
                sprintf( '<a href="%s">%s</a>', esc_url( $settings_link ), __( 'Settings' ) ),
                sprintf( '<a href="%s">%s</a>', esc_url( $cnb_utils->get_support_url('', 'wp-plugins-page', 'support') ), __( 'Support' ) ),
            );
            array_push(
                $links,
                $cnb_new_links[0],
                $cnb_new_links[1],
                $cnb_new_links[2]
            );
        }

        return $links;
    }

    public function plugin_add_action_link( $links ) {
        $cnb_options       = get_option( 'cnb' );
        $cnb_cloud_hosting = ( new CnbUtils() )->isCloudActive( $cnb_options );

        $link_name   = $cnb_cloud_hosting ? 'All buttons' : 'My button';
        $url         = admin_url( 'admin.php' );
        $button_link =
            add_query_arg(
                array(
                    'page' => 'call-now-button',
                ),
                $url );
        $button_url  = esc_url( $button_link );
        $button      = sprintf( '<a href="%s">%s</a>', $button_url, $link_name );
        $links['cnb_buttons'] =  $button;

        if ( ! $cnb_cloud_hosting ) {
            $link_name    = 'Get Premium';
            $upgrade_link =
                add_query_arg(
                    array(
                        'page' => 'call-now-button-upgrade',
                    ),
                    $url );
            $upgrade_url  = esc_url( $upgrade_link );
            $upgrade      = sprintf( '<a style="font-weight: bold;" href="%s">%s</a>', $upgrade_url, $link_name );
            array_unshift( $links, $upgrade );
        }

        return $links;
    }

    public function options_init() {
        // This ensures that we can validate and change/manipulate the "cnb" options before saving
        $settings_controller = new CnbSettingsController();
        register_setting(
            'cnb_options',
            'cnb',
            array(
                'type'              => 'array',
                'description'       => 'Settings for the Legacy and Cloud version of the Call Now Button',
                'sanitize_callback' => array( $settings_controller, 'validate_options' ),
                'default'           => $settings_controller->get_defaults(),
            ) );
    }

    /**
     * Only used by tests
     * @return void
     */
    public function unregister_options() {
        unregister_setting( 'cnb_options', 'cnb' );
    }

    public function register_styles_and_scripts() {
        wp_register_style(
            CNB_SLUG . '-styling',
            plugins_url('resources/style/call-now-button.css', CNB_PLUGINS_URL_BASE ),
            false,
            CNB_VERSION );
        // Original: https://code.jquery.com/ui/1.13.0/themes/base/jquery-ui.min.css
        wp_register_style(
            CNB_SLUG . '-jquery-ui',
            plugins_url('resources/style/jquery-ui.min.css', CNB_PLUGINS_URL_BASE ),
            false,
            '1.13.0' );
        // Original: https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.12/css/intlTelInput.min.css
        wp_register_style(
            CNB_SLUG . '-intl-tel-input',
            plugins_url('resources/style/intlTelInput.min.css', CNB_PLUGINS_URL_BASE ),
            false,
            '1.13.0' );
        wp_register_style(
            CNB_SLUG . '-client',
            CnbAppRemote::get_client_css(),
            array(),
            CNB_VERSION );

        wp_register_script(
            CNB_SLUG . '-call-now-button',
            plugins_url('resources/js/call-now-button.js', CNB_PLUGINS_URL_BASE ),
            array( 'wp-color-picker' ),
            CNB_VERSION,
            true );
        wp_register_script(
            CNB_SLUG . '-dismiss',
            plugins_url('resources/js/dismiss.js', CNB_PLUGINS_URL_BASE ),
            array( 'jquery', CNB_SLUG . '-call-now-button' ),
            CNB_VERSION,
            true );
        wp_register_script(
            CNB_SLUG . '-timezone-picker-fix',
            plugins_url('resources/js/timezone-picker-fix.js', CNB_PLUGINS_URL_BASE ),
            array( 'jquery', CNB_SLUG . '-call-now-button' ),
            CNB_VERSION,
            true );
        wp_register_script(
            CNB_SLUG . '-action-type-to-icon-text',
            plugins_url('resources/js/action-type-to-icon-text.js', CNB_PLUGINS_URL_BASE ),
            array( 'jquery', CNB_SLUG . '-call-now-button' ),
            CNB_VERSION,
            true );

        wp_register_script(
            CNB_SLUG . '-form-to-json',
            plugins_url('resources/js/form-to-json.js', CNB_PLUGINS_URL_BASE ),
            array( 'jquery', CNB_SLUG . '-call-now-button' ),
            CNB_VERSION,
            true );
        wp_register_script(
            CNB_SLUG . '-preview',
            plugins_url('resources/js/preview.js', CNB_PLUGINS_URL_BASE ),
            array( 'jquery', CNB_SLUG . '-call-now-button' ),
            CNB_VERSION,
            true );
        wp_register_script(
            CNB_SLUG . '-domain-upgrade',
            plugins_url('resources/js/domain-upgrade.js', CNB_PLUGINS_URL_BASE ),
            array( 'jquery', CNB_SLUG . '-call-now-button' ),
            CNB_VERSION,
            true );
        wp_register_script(
            CNB_SLUG . '-settings',
            plugins_url('resources/js/settings.js', CNB_PLUGINS_URL_BASE ),
            array( CNB_SLUG . '-call-now-button' ),
            CNB_VERSION,
            true );
	    wp_register_script(
		    CNB_SLUG . '-billing-portal',
		    plugins_url('resources/js/billing-portal.js', CNB_PLUGINS_URL_BASE ),
		    array( 'jquery' ),
		    CNB_VERSION,
		    true );
        wp_register_script(
            CNB_SLUG . '-premium-activation',
            plugins_url('resources/js/premium-activation.js', CNB_PLUGINS_URL_BASE ),
            array( CNB_SLUG . '-call-now-button' ),
            CNB_VERSION,
            true );
	    wp_register_script(
		    CNB_SLUG . '-button-overview',
		    plugins_url('resources/js/button-overview.js', CNB_PLUGINS_URL_BASE ),
		    array( CNB_SLUG . '-call-now-button' ),
		    CNB_VERSION,
		    true );        wp_register_script(
            CNB_SLUG . '-action-edit-scheduler',
            plugins_url('resources/js/action-edit-scheduler.js', CNB_PLUGINS_URL_BASE ),
            array( CNB_SLUG . '-call-now-button' ),
            CNB_VERSION,
            true );
        wp_register_script(
            CNB_SLUG . '-action-edit-fields',
            plugins_url('resources/js/action-edit-fields.js', CNB_PLUGINS_URL_BASE ),
            array( CNB_SLUG . '-call-now-button' ),
            CNB_VERSION,
            true );
        wp_register_script(
            CNB_SLUG . '-action-edit-facebook',
            plugins_url('resources/js/action-edit-facebook.js', CNB_PLUGINS_URL_BASE ),
            array( CNB_SLUG . '-call-now-button' ),
            CNB_VERSION,
            true );
	    wp_register_script(
		    CNB_SLUG . '-action-edit-viber',
		    plugins_url('resources/js/action-edit-viber.js', CNB_PLUGINS_URL_BASE ),
		    array( CNB_SLUG . '-call-now-button' ),
		    CNB_VERSION,
		    true );
        wp_register_script(
            CNB_SLUG . '-button-edit',
            plugins_url('resources/js/button-edit.js', CNB_PLUGINS_URL_BASE ),
            array( CNB_SLUG . '-call-now-button' ),
            CNB_VERSION,
            true );
        wp_register_script(
            CNB_SLUG . '-button-edit-icon-color',
            plugins_url('resources/js/button-edit-icon-color.js', CNB_PLUGINS_URL_BASE ),
            array( CNB_SLUG . '-button-edit' ),
            CNB_VERSION,
            true );
        wp_register_script(
            CNB_SLUG . '-action-edit',
            plugins_url('resources/js/action-edit.js', CNB_PLUGINS_URL_BASE ),
            array( CNB_SLUG . '-call-now-button' ),
            CNB_VERSION,
            true );
        wp_register_script(
            CNB_SLUG . '-form-bulk-rewrite',
            plugins_url('resources/js/form-bulk-rewrite.js', CNB_PLUGINS_URL_BASE ),
            array( CNB_SLUG . '-call-now-button' ),
            CNB_VERSION,
            true );
        wp_register_script(
            CNB_SLUG . '-profile',
            plugins_url('resources/js/profile.js', CNB_PLUGINS_URL_BASE ),
            array( CNB_SLUG . '-call-now-button' ),
            CNB_VERSION,
            true );
        wp_register_script(
            CNB_SLUG . '-legacy-edit',
            plugins_url('resources/js/legacy-edit.js', CNB_PLUGINS_URL_BASE ),
            array( CNB_SLUG . '-call-now-button' ),
            CNB_VERSION,
            true );
        wp_register_script(
            CNB_SLUG . '-jquery-ui-touch-punch',
            plugins_url('resources/js/jquery.ui.touch-punch.js', CNB_PLUGINS_URL_BASE ),
            array( CNB_SLUG . '-call-now-button', 'jquery-ui-sortable' ),
            'v1.0.8',
            true );
        wp_register_script(
            CNB_SLUG . '-condition-edit',
            plugins_url('resources/js/condition-edit.js', CNB_PLUGINS_URL_BASE ),
            array( CNB_SLUG . '-call-now-button' ),
            CNB_VERSION,
            true );
        wp_register_script(
            CNB_SLUG . '-settings-activated',
            plugins_url('resources/js/settings-activated.js', CNB_PLUGINS_URL_BASE ),
            array( CNB_SLUG . '-call-now-button' ),
            CNB_VERSION,
            true );
        wp_register_script(
            CNB_SLUG . '-error-reporting',
            plugins_url('resources/js/error-reporting.js', CNB_PLUGINS_URL_BASE ),
            array(),
            CNB_VERSION,
            true );
        wp_register_script(
            CNB_SLUG . '-tally',
            'https://tally.so/widgets/embed.js',
            array(),
            CNB_VERSION,
            true );

        // Special case: since the preview functionality depends on this,
        // and the source is always changing - we include it as external script
        wp_register_script(
            CNB_SLUG . '-client',
            CnbAppRemote::get_client_js(),
            array(),
            CNB_VERSION,
            true );

        // Original: https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.12/js/intlTelInput.min.js
        wp_register_script(
            CNB_SLUG . '-intl-tel-input',
            plugins_url('resources/js/intlTelInput.min.js', CNB_PLUGINS_URL_BASE ),
            null,
            '17.0.12',
            true );
        // Original: https://cdn.jsdelivr.net/npm/js-confetti@latest/dist/js-confetti.browser.js
        // Via https://github.com/loonywizard/js-confetti
        wp_register_script(
            CNB_SLUG . '-confetti',
            plugins_url('resources/js/js-confetti/js-confetti.browser.js', CNB_PLUGINS_URL_BASE ),
            array( CNB_SLUG . '-domain-upgrade' ),
            '0.11.0',
            true );

	    wp_register_script(
		    CNB_SLUG . '-templates-react-compiled',
		    plugins_url('build/index.js', CNB_PLUGINS_URL_BASE ),
		    array(
				'wp-element', // wp-element for React
			    // 'react',
			    'wp-block-editor', // wp-block-editor for @wordpress/components
			    // 'wp-blocks',
			    // 'wp-i18n',
		    ),
		    CNB_VERSION,
		    true );

	    wp_register_script(
		    (new Template_Controller())->get_slug(),
		    plugins_url('resources/js/templates.js', CNB_PLUGINS_URL_BASE ),
		    array( CNB_SLUG . '-templates-react-compiled' ),
		    CNB_VERSION,
		    true );
	    wp_register_script(
		    CNB_SLUG . '-chat',
		    plugins_url('resources/js/chat.js', CNB_PLUGINS_URL_BASE ),
		    array(),
		    CNB_VERSION,
		    true );
    }

    public function register_global_actions() {
        add_action( 'admin_menu', array( $this, 'register_admin_pages' ) );
        add_action( 'admin_menu', array( $this, 'register_welcome_page' ) );
        add_action( 'admin_head', array( $this, 'hide_welcome_page' ) );

        add_filter( 'plugin_row_meta', array( $this, 'plugin_meta' ), 10, 2 );
        add_filter( 'plugin_action_links_' . CNB_BASENAME, array( $this, 'plugin_add_action_link' ) );

        add_action( 'admin_init', array( $this, 'options_init' ) );
        add_action( 'admin_init', array( $this, 'register_styles_and_scripts' ) );

        $activation = new Activation();
        add_action( 'admin_init', array( $activation, 'redirect_to_welcome_page' ) );

        $settings_controller = new CnbSettingsController();
        add_filter( 'option_cnb', array( $settings_controller, 'post_option_cnb' ) );

        // This updates the internal version number, called by CnbAdminNotices::action_admin_init
        add_action( 'cnb_update_' . CNB_VERSION, array( $settings_controller, 'update_version' ) );

        $cnbSentry = new Cnb_Sentry();
        add_action('cnb_init', array( $cnbSentry, 'init' ), 10, 2);
        add_action('cnb_finish', array( $cnbSentry, 'finish' ));

        $cnb_remote = new CnbAppRemote();
        add_action('cnb_init', array( $cnb_remote, 'init' ), 9);

	    if ( CnbSettingsController::is_advanced_view() ) {
		    $cnb_validation = new ValidationHooks();
		    add_action( 'cnb_validation_notices', array( $cnb_validation, 'create_notice' ), 10, 2 );
	    }

		$action_controller = new CnbActionController();
		add_filter( 'cnb_get_action_types', array( $action_controller, 'filter_action_types' ) );

	    $admin_functions = new CnbAdminFunctions();
	    add_filter( 'cnb_get_condition_types', array( $admin_functions, 'filter_condition_types' ) );

    }

    public function register_header_and_footer() {
        // Generic header/footer
        $header = new CnbHeader();
        add_action( 'cnb_header', array( $header, 'render' ) );
        $footer = new CnbFooter();
        add_action( 'cnb_footer', array( $footer, 'render' ) );
    }

    /**
     * Page specific actions
     * @return void
     */
    public function register_admin_post_actions() {
        $button_controller = new CnbButtonController();
	    add_action( 'admin_post_cnb_create_button', array( $button_controller, 'create' ) );

        add_action( 'admin_post_cnb_create_single_button', array( $button_controller, 'create' ) );
        add_action( 'admin_post_cnb_create_multi_button', array( $button_controller, 'create' ) );
	    add_action( 'admin_post_cnb_create_full_button', array( $button_controller, 'create' ) );
	    add_action( 'admin_post_cnb_create_dots_button', array( $button_controller, 'create' ) );

        add_action( 'admin_post_cnb_update_single_button', array( $button_controller, 'update' ) );
        add_action( 'admin_post_cnb_update_multi_button', array( $button_controller, 'update' ) );
	    add_action( 'admin_post_cnb_update_full_button', array( $button_controller, 'update' ) );
	    add_action( 'admin_post_cnb_update_dots_button', array( $button_controller, 'update' ) );

	    add_action( 'admin_post_cnb_delete_button', array( $button_controller, 'delete' ) );
	    add_action( 'admin_post_cnb_buttons_bulk', array( $button_controller, 'handle_bulk_actions' ) );

        $api_key_controller = new CnbApiKeyController();
	    add_action( 'admin_post_cnb_apikey_create', array( $api_key_controller, 'create' ) );
	    add_action( 'admin_post_cnb_apikey_validate_and_update', array( $api_key_controller, 'validate_and_update' ) );
        add_action( 'admin_post_cnb_apikey_bulk', array( $api_key_controller, 'handle_bulk_actions' ) );
		// example GET: /wp-admin/admin-post.php?action=cnb_apikey_activate&api_key_ott=<key>>
	    $ott_key_controller = new OttController();
	    add_action( 'admin_post_cnb_apikey_activate', array( $ott_key_controller, 'activate' ) );

        $condition_controller = new CnbConditionController();
        add_action( 'admin_post_cnb_create_condition', array( $condition_controller, 'create' ) );
	    add_action( 'admin_post_cnb_update_condition', array( $condition_controller, 'update' ) );
	    add_action( 'admin_post_cnb_delete_condition', array( $condition_controller, 'delete' ) );
        add_action( 'admin_post_cnb_conditions_bulk', array( $condition_controller, 'handle_bulk_actions' ) );

        $action_controller = new CnbActionController();
        add_action( 'admin_post_cnb_create_action', array( $action_controller, 'create' ) );
	    add_action( 'admin_post_cnb_update_action', array( $action_controller, 'update' ) );
	    add_action( 'admin_post_cnb_delete_action', array( $action_controller, 'delete' ) );
        add_action( 'admin_post_cnb_actions_bulk', array( $action_controller, 'handle_bulk_actions' ) );

        $domain_controller = new CnbDomainController();
        add_action( 'admin_post_cnb_create_domain', array( $domain_controller, 'create' ) );
	    add_action( 'admin_post_cnb_update_domain', array( $domain_controller, 'update' ) );
	    add_action( 'admin_post_cnb_delete_domain', array( $domain_controller, 'delete' ) );
        add_action( 'admin_post_cnb_domains_bulk', array( $domain_controller, 'handle_bulk_actions' ) );

        $profile_controller = new CnbProfileController();
        add_action( 'admin_post_cnb_profile_edit', array( $profile_controller, 'update' ) );

        if (getenv('WORDPRESS_CALL_NOW_BUTTON_TESTS') == 1) {
            $settings_controller = new CnbSettingsController();
            add_action( 'admin_post_cnb_delete_all_settings', array( $settings_controller, 'delete_all_settings' ) );
            add_action( 'admin_post_cnb_set_default_settings', array( $settings_controller, 'set_default_settings' ) );
            add_action( 'admin_post_cnb_set_changelog_version', array( $settings_controller, 'override_changelog_version' ) );
        }
    }

    public function register_ajax_actions() {
        $ajax_controller = new CnbAdminAjax();
        add_action( 'wp_ajax_cnb_time_format', array( $ajax_controller, 'time_format' ) );
        add_action( 'wp_ajax_cnb_settings_profile_save', array( $ajax_controller, 'settings_profile_save' ) );
        add_action( 'wp_ajax_cnb_get_checkout', array( $ajax_controller, 'domain_upgrade_get_checkout' ) );
        add_action( 'wp_ajax_cnb_get_agency_checkout', array( $ajax_controller, 'agency_upgrade_get_checkout' ) );
        add_action( 'wp_ajax_cnb_email_activation', array( $ajax_controller, 'cnb_email_activation' ) );
        add_action( 'wp_ajax_cnb_get_plans', array( $ajax_controller, 'get_plans' ) );
        add_action( 'wp_ajax_cnb_get_billing_portal', array( $ajax_controller, 'get_billing_portal' ) );
        add_action( 'wp_ajax_cnb_request_billing_portal', array( $ajax_controller, 'request_billing_portal' ) );
	    add_action( 'wp_ajax_cnb_get_domain_status', array( $ajax_controller, 'get_domain_status' ) );

        $action_controller = new CnbActionController();
        add_action( 'wp_ajax_cnb_delete_action', array( $action_controller, 'delete_ajax' ) );

        $condition_controller = new CnbConditionController();
        add_action( 'wp_ajax_cnb_delete_condition', array( $condition_controller, 'delete_ajax' ) );

        $domain_controller = new CnbDomainController();
        add_action( 'wp_ajax_cnb_domain_timezone_change', array( $domain_controller, 'update_timezone' ) );

        $admin_controller = CnbAdminNotices::get_instance();
        add_action( 'wp_ajax_cnb_hide_notice', array( $admin_controller, 'hide_notice' ) );

	    $button_controller = new CnbButtonController();
	    add_action( 'wp_ajax_cnb_create_button', array( $button_controller, 'create_ajax' ) );

		$user_controller = new CnbUserController();
	    add_action( 'wp_ajax_cnb_set_user_storage_solution', array( $user_controller, 'set_storage_solution' ) );

		$chat_controller = new CnbChatController();
	    add_action( 'wp_ajax_cnb_create_chat_token', array( $chat_controller, 'create_chat_token_ajax' ) );
    }

	/**
	 * Register the CallNowButton Cron jobs, which run regularly to update the internal state.
	 *
	 * @return void
	 */
	public function register_cron() {
		$cron = new Cron();
		$cron->register_hook();
	}
}
