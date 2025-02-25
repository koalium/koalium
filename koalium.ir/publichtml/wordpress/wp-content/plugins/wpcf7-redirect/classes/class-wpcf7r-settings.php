<?php
/**
 * Class WPCF7r_Settings file.
 *
 * @package wpcf7-redirect
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Contact form 7 Redirect Settings panel
 */
class WPCF7r_Settings {

	/**
	 * Page slug.
	 *
	 * @var string
	 */
	private $page_slug;

	/**
	 * Fields array.
	 *
	 * @var [array]
	 */
	public $fields;

	/**
	 * WPCF7r_Settings constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->page_slug = 'wpc7_redirect';

		add_action( 'admin_menu', array( $this, 'create_plugin_settings_page' ) );
		add_action( 'admin_init', array( $this, 'wpcf7r_register_options' ) );
		add_filter( 'plugin_row_meta', array( $this, 'register_plugin_links' ), 10, 2 );
	}

	/**
	 * Register plugin options
	 */
	public function wpcf7r_register_options() {
		$this->fields = array();

		$this->add_settings_section();

		foreach ( $this->fields as $field ) {
			$args = array();
			add_settings_field( $field['uid'], $field['label'], array( $this, 'field_callback' ), $this->page_slug, $field['section'], $field );

			register_setting( $this->page_slug, $field['uid'], $args );
		}
	}

	/**
	 * Add settings section
	 *
	 * @return void
	 */
	public function add_settings_section() {
		add_settings_section( 'settings_section', __( 'Global Settings', 'wpcf7-redirect' ), array( $this, 'section_callback' ), $this->page_slug );

		$this->fields = array_merge(
			$this->fields,
			array(
				array(
					'uid'          => 'wpcf_debug',
					'label'        => 'Debug',
					'section'      => 'settings_section',
					'type'         => 'checkbox',
					'options'      => false,
					'placeholder'  => '',
					'helper'       => '',
					'supplemental' => __( 'This will open the actions post type and display debug feature.', 'wpcf7-redirect' ),
					'default'      => '',
				),
			)
		);
	}

	/**
	 * A function for displaying a field on the admin settings page
	 *
	 * @param array $arguments Array of arguments to display the field.
	 */
	public function field_callback( $arguments ) {
		$value = get_option( $arguments['uid'] ); // Get the current value, if there is one.
		if ( ! $value ) { // If no value exists.
			$value = $arguments['default']; // Set to our default.
		}
		// Check which type of field we want.
		switch ( $arguments['type'] ) {
			case 'text': // If it is a text field.
			case 'password':
				printf( '<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" class="widefat" />', $arguments['uid'], $arguments['type'], $arguments['placeholder'], $value );
				break;
			case 'checkbox': // If it is a text field.
				$checked = checked( $value, '1', false );
				printf( '<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" class="widefat" %5$s/>', $arguments['uid'], $arguments['type'], $arguments['placeholder'], '1', $checked );
				break;
		}

		$helper       = $arguments['helper'];
		$supplimental = $arguments['supplemental'];

		// If there is help text.
		if ( $helper ) {
			printf( '<span class="helper"> %s</span>', $helper ); // Show it.
		}

		// If there is supplemental text.
		if ( $supplimental ) {
			printf( '<p class="description">%s</p>', esc_html( $supplimental ) ); // Show it.
		}
	}

	/**
	 * Main call for creating the settings page
	 */
	public function create_plugin_settings_page() {
		// Add the menu item and page.
		$page_title = 'Redirection settings';
		$capability = 'manage_options';
		$callback   = array( $this, 'plugin_settings_page_content' );

		$hook = add_submenu_page(
			'wpcf7',
			$page_title,
			$page_title,
			$capability,
			$this->page_slug,
			$callback
		);
		add_action( "load-$hook", array( WPCF7r_Survey::get_instance(), 'init' ) );
	}

	/**
	 * The setting page template HTML
	 */
	public function plugin_settings_page_content() {        ?>
		<section class="padbox">
			<div class="wrap wrap-wpcf7redirect">
				<h2>
					<span>
						<?php _e( 'Redirection For Contact Form 7', 'wpcf7-redirect' ); ?>
					</span>
				</h2>
				<div class="postbox">
					<div class="padbox">
						<form method="POST" action="options.php" name="wpcfr7_settings">
							<?php
							do_action( 'before_settings_fields' );
							settings_fields( $this->page_slug );
							do_settings_sections( $this->page_slug );
							submit_button();
							?>
						</form>
						<?php if ( is_wpcf7r_debug() ) : ?>
							<input type="button" name="reset_all" value="<?php _e( 'Reset all Settings - BE CAREFUL! this will delete all Redirection for Contact Form 7 data.', 'wpcf7-redirect' ); ?>" class="cf7-redirect-reset button button-secondary" />

						<?php endif; ?>
					</div>
				</div>
			</div>
		</section>
		<?php
	}

	/**
	 * Create a section on the admin settings page.
	 */
	public function section_callback() {
		return '';
	}

	/**
	 * Add a link to the options page to the plugin description block.
	 *
	 * @param array  $links Array of links.
	 * @param string $file Plugin file name.
	 *
	 * @return array $links Array of links.
	 */
	public function register_plugin_links( $links, $file ) {
		if ( WPCF7_PRO_REDIRECT_BASE_NAME === $file ) {
			$links[] = WPCF7r_Utils::get_settings_link();
		}
		return $links;
	}
}
