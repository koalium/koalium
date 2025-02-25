<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    Wpcf7_Redirect
 * @subpackage Wpcf7_Redirect
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wpcf7_Redirect
 * @subpackage Wpcf7_Redirect
 * @author     Lior Regev <regevlio@gmail.com>
 */
class Wpcf7_Redirect {

	/**
	 * Instance of the main plugin class object.
	 *
	 * @var [object]
	 */
	public $cf7_redirect_base;

	/**
	 * Constructor
	 */
	public function init() {
		$this->define();
		$this->load_dependencies();
		$this->cf7_redirect_base = new WPCF7R_Base();

		add_action( 'plugins_loaded', array( $this, 'notice_to_remove_old_plugin' ) );

		add_filter( 'redirection_for_contact_form_7_about_us_metadata', array( $this, 'about_page' ) );
		add_action( 'admin_menu', array( $this, 'create_plugin_addons_upsell' ) );
		add_action( 'admin_head', array( $this, 'handle_upgrade_link' ) );
        add_filter( 'wpcf7_get_extensions', array( $this, 'filter_deprecated_addons' ) );

		add_filter( 'redirection_for_contact_form_7_float_widget_metadata', array( $this, 'float_widget_data' )	);
		add_filter( 'wpcf7_redirect_float_widget_metadata', array( $this, 'float_widget_data' )	);
	}

	/**
     * Add script for upgrade link to open in new tab and preserve the color.
	 * @return void
	 */
    public function handle_upgrade_link() {
        $url = tsdk_utmify( wpcf7_redirect_upgrade_url(), 'admin', 'admin_menu' );
	    echo '<script type="text/javascript">
        jQuery(document).ready( function($) {
            $( "ul#adminmenu a[href$=\''. $url . '\']" ).attr( \'target\', \'_blank\' );
            $( "ul#adminmenu a[href$=\''. $url . '\']" ).css( \'color\', \'#adff2e\' );
        });
        </script>';
    }

	/**
     * Get float widget data.
	 * @return array
	 */
	public function float_widget_data() {
        $has_legacy = apply_filters( 'wpcf7r_legacy_used', false );

		return array(
			'nice_name'          => 'Redirect for Contact Form 7',
			'logo'               => esc_url_raw( WPCF7_PRO_REDIRECT_BUILD_PATH . 'images/wpcf7-help.png' ),
			'primary_color'      => '#4580ff',
			'pages'              => array( 'contact_page_wpcf7r-addons-upsell', 'contact_page_wpc7_redirect' ),
			'has_upgrade_menu'   => false,
			'premium_support_link' => $has_legacy ? 'https://users.freemius.com/login' : '',
			'upgrade_link'       => tsdk_utmify( '', 'floatWidget' ),
			'documentation_link' => tsdk_utmify( 'https://docs.themeisle.com/collection/2014-redirection-for-contact-form-7', 'floatWidget' )
		);
	}

	/**
     * Filter addons that are deprecated.
     *
	 * @param array $addons List of addons.
	 *
	 * @return array
	 */
    public function filter_deprecated_addons( $addons ) {
        $deprecated = [
            'wpcf7r-custom-errors',
            'wpcf7r-login',
            'wpcf7r-register',
            'wpcf7r-monday',
            'wpcf7r-slack',
            'wpcf7r-eliminate-duplicates',
        ];

        return array_filter( $addons, function( $key ) use ( $deprecated ) {
            return ! in_array( $key, $deprecated );
        }, ARRAY_FILTER_USE_KEY );
    }

	/**
     * Create plugin addons upsell page.
     *
	 * @return void
	 */
	public function create_plugin_addons_upsell() {
		$page_title = __( '↳ Add-Ons', 'wpcf7-redirect' );
		$capability = 'manage_options';
		$slug	    = 'wpcf7r-addons-upsell';
		$callback   = array( $this, 'plugin_addons_upsell_page_content' );

		$hook = add_submenu_page(
			'wpcf7',
			$page_title,
			$page_title,
			$capability,
			$slug,
			$callback
		);

		add_action( "load-$hook", array( WPCF7r_Survey::get_instance(), 'init' ) );

        if ( ! $this->check_upgrade_available() ) {
            return;
        }
        $upgrade_page_title = __( '↳ Upgrade ➤', 'wpcf7-redirect' );
		$upgrade_slug       = 'wpcf7r-upgrade';
		add_submenu_page(
			'wpcf7',
			$upgrade_page_title,
			$upgrade_page_title,
			$capability,
			$upgrade_slug,
			$callback,
            5
		);

        global $submenu;
        $submenu['wpcf7'][5] = array( $upgrade_page_title, $capability, tsdk_utmify( wpcf7_redirect_upgrade_url(), 'admin', 'admin_menu' ) );
	}

	/**
     * Plugin addons upsell page content.
	 * @return void
	 */
	public function plugin_addons_upsell_page_content() {
        $addons = wpcf7_get_extensions();
		?>
        <style>
            html, body {
                margin: 0;
                padding: 0;
            }
            .wpcf7r-addons {
                padding: 20px;
            }
            .wpcf7r-extensions-wrap {
                display: flex;
                flex-direction: row;
                flex-wrap: wrap;
                align-content: flex-start;
                justify-content: start;
                align-items: stretch;
                gap: 12px;
            }
            .wpcf7r-extensions-wrap .extension {
                display: flex;
                flex-direction: column;
                align-items: flex-start;
                justify-content: center;
                background-color: #FFF;
                padding: 12px;
                width: 300px;
            }
            .wpcf7r-extensions-wrap .extension .title {
                display: flex;
                align-items: center;
                gap: 8px;
            }
            .wpcf7r-extensions-wrap .extension .title img {
                width: 32px;
            }
			.wpcf7r-banner:has(.tsdk-banner-cta) {
				margin-bottom: 40px;
			}
        </style>
		<section class="wpcf7r-addons">
			<div id="tsdk_banner" class="wpcf7r-banner"></div>
            <h1><?php _e( 'Redirection For Contact Form 7 Addons', 'wpcf7-redirect' ); ?></h1>
            <div id="wpcf7r-extensions" class="wpcf7r-extensions-wrap">
                <?php
                foreach ( $addons as $addon ) {
                    ?>
                    <div class="extension">
                        <span class="title">
                            <span><img src="<?php echo esc_url( $addon['icon'] ); ?>" alt="<?php echo esc_attr( $addon['name'] ); ?>"></span>
                            <h3><?php echo esc_attr( $addon['title'] ); ?></h3>
                        </span>
                        <p><?php echo esc_attr( $addon['description'] ); ?></p>
	                    <?php if ( ! isset( $addon['active'] ) || ! $addon['active'] ) : ?>
                            <a href="<?php echo tsdk_utmify( wpcf7_redirect_upgrade_url(), 'wpcf7r-upsell', 'addons' ); ?>" target="_blank" class="button button-primary"><?php _e( 'Get Addon', 'wpcf7-redirect' ); ?></a>
	                    <?php else: ?>
                            <a href="<?php echo esc_url( network_admin_url( 'options-general.php' ) ); ?>" target="_self" class="button button-secondary"><?php _e( 'License', 'wpcf7-redirect' ); ?></a>
		                <?php endif; ?>
                    </div>
                    <?php
                }
                ?>
            </div>
		</section>
		<?php
		do_action( 'admin_print_footer_scripts' );
        do_action( 'in_admin_footer' );
        exit;
	}

    /**
     * Check if upgrade is available
     *
     * @return bool
     */
	private function check_upgrade_available() {
		$available_addons = [
			'wpcf7r-api',
			'wpcf7r-conditional-logic',
			'wpcf7r-create-post',
			'wpcf7r-hubspot',
			'wpcf7r-mailchimp',
			'wpcf7r-paypal',
			'wpcf7r-pdf',
			'wpcf7r-popup',
			'wpcf7r-salesforce',
			'wpcf7r-stripe',
			'wpcf7r-twilio',
		];

        $display_upgrade = true;

        $plugins = get_plugins();
        $plugins = array_keys( $plugins );

        // if an addon is present default to not display the upgrade link.
        foreach ( $available_addons as $addon ) {
            if ( in_array( $addon . '/init.php', $plugins, true ) ) {
                $display_upgrade = false;
                break;
            }
        }

		foreach ( $available_addons as $addon ) {
			if ( 'valid' !== tsdk_lstatus( WP_PLUGIN_DIR . '/' . $addon. '/init.php' ) ) {
				return true;
			}
		}
		return $display_upgrade;
	}

	/**
     * Add the about page.
     *
	 * @return array
	 */
	public function about_page() {
		return [
			'location'         => 'wpcf7',
			'logo'             => esc_url_raw( WPCF7_PRO_REDIRECT_BUILD_PATH . 'images/icon-128x128.png' )
		];
	}

	/**
	 * Load dependencies
	 */
	public function load_dependencies() {
		// Load all actions.
		foreach ( glob( WPCF7_PRO_REDIRECT_BASE_PATH . 'modules/*.php' ) as $filename ) {
			require_once $filename;
		}
		require_once WPCF7_PRO_REDIRECT_CLASSES_PATH . 'class-wpcf7r-base.php';
	}

	/**
	 * Notice to remove old plugin
	 */
	public function notice_to_remove_old_plugin() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		if ( is_plugin_active( 'cf7-to-api/cf7-to-api.php' ) ) {
			add_action( 'admin_notices', 'wpcf7_remove_contact_form_7_to_api' );
		}
	}

	/**
	 * Defines
	 */
	public function define() {
		define( 'WPCF7_PRO_REDIRECT_BASE_NAME', plugin_basename( __FILE__ ) );
		define( 'WPCF7_PRO_REDIRECT_BASE_PATH', plugin_dir_path( __FILE__ ) );
		define( 'WPCF7_PRO_REDIRECT_BASE_URL', plugin_dir_url( __FILE__ ) );
		define( 'WPCF7_PRO_REDIRECT_PLUGINS_PATH', plugin_dir_path( __DIR__ ) );
		define( 'WPCF7_PRO_REDIRECT_TEMPLATE_PATH', WPCF7_PRO_REDIRECT_BASE_PATH . 'templates/' );
		define( 'WPCF7_PRO_REDIRECT_ACTIONS_TEMPLATE_PATH', WPCF7_PRO_REDIRECT_CLASSES_PATH . 'actions/html/' );
		define( 'WPCF7_PRO_REDIRECT_ADDONS_PATH', WPCF7_PRO_REDIRECT_PLUGINS_PATH . 'wpcf7r-addons/' );
		define( 'WPCF7_PRO_REDIRECT_ACTIONS_PATH', WPCF7_PRO_REDIRECT_CLASSES_PATH . 'actions/' );
		define( 'WPCF7_PRO_REDIRECT_FIELDS_PATH', WPCF7_PRO_REDIRECT_TEMPLATE_PATH . 'fields/' );
		define( 'WPCF7_PRO_REDIRECT_POPUP_TEMPLATES_PATH', WPCF7_PRO_REDIRECT_TEMPLATE_PATH . 'popups/' );
		define( 'WPCF7_PRO_REDIRECT_POPUP_TEMPLATES_URL', WPCF7_PRO_REDIRECT_BASE_URL . '/templates/popups/' );
		define( 'WPCF7_PRO_REDIRECT_ASSETS_PATH', WPCF7_PRO_REDIRECT_BASE_URL . 'assets/' );
		define( 'WPCF7_PRO_REDIRECT_BUILD_PATH', WPCF7_PRO_REDIRECT_BASE_URL . 'build/' );

		define( 'QFORM_BASE', WPCF7_PRO_REDIRECT_BASE_PATH . 'form/' );
	}
}
