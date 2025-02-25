<?php

class DSM_GradientText extends ET_Builder_Module {

	public $slug       = 'dsm_gradient_text';
	public $vb_support = 'on';
	public $icon_path;

	protected $module_credits = array(
		'module_uri' => 'https://divisupreme.com/',
		'author'     => 'Divi Supreme',
		'author_uri' => 'https://divisupreme.com/',
	);

	public function init() {
		$this->name      = esc_html__( 'Supreme Gradient Text', 'dsm-supreme-modules-for-divi' );
		$this->icon_path = plugin_dir_path( __FILE__ ) . 'icon.svg';
		// Toggle settings
		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'dsm-supreme-modules-for-divi' ),
				),
			),
			'advanced' => array(
				'toggles' => array(),
			),
		);
	}

	public function get_advanced_fields_config() {
		return array(
			'fonts'          => array(
				'header' => array(
					'label'          => esc_html__( 'Title', 'dsm-supreme-modules-for-divi' ),
					'css'            => array(
						'main' => '%%order_class%% h1.dsm-gradient-text, %%order_class%% h2.dsm-gradient-text, %%order_class%% h3.dsm-gradient-text, %%order_class%% h4.dsm-gradient-text, %%order_class%% h5.dsm-gradient-text, %%order_class%% h6.dsm-gradient-text',
					),
					'font_size'      => array(
						'default' => '30px',
					),
					'line_height'    => array(
						'default' => '1em',
					),
					'letter_spacing' => array(
						'default' => '0px',
					),
					'header_level'   => array(
						'default' => 'h1',
					),
				),
			),
			'text'           => array(
				'use_text_orientation'  => true,
				'use_background_layout' => false,
				'css'                   => array(
					'text_shadow' => '%%order_class%%',
				),
				'options'               => array(
					'background_layout' => array(
						'default' => 'light',
					),
				),
			),
			'background'     => array(
				'css'     => array(
					'main' => '%%order_class%% .dsm-gradient-text',
				),
				'options' => array(
					'use_background_color'            => array(
						'default' => 'off',
					),
					'use_background_video'            => array(
						'default' => 'off',
					),
					'use_background_color_gradient'   => array(
						'default' => 'on',
					),
					'background_color_gradient_start' => array(
						'default' => 'rgba(131,0,233,0.78)',
					),
					'background_color_gradient_end'   => array(
						'default' => 'rgba(41,196,169,0.62)',
					),
					'parallax_method'                 => array(
						'default' => 'off',
					),
				),
			),
			'borders'        => array(
				'default' => array(
					'css' => array(
						'main' => array(
							'border_radii'  => '%%order_class%%',
							'border_styles' => '%%order_class%%',
						),
					),
				),
			),
			'box_shadow'     => array(
				'default' => array(
					'css' => array(
						'main' => '%%order_class%%',
					),
				),
			),
			'margin_padding' => array(
				'css' => array(
					'main'      => "{$this->main_css_element}",
					'important' => array( 'custom_margin' ),
				),
			),
		);
	}

	public function get_fields() {
		return array(
			'gradient_text' => array(
				'label'            => esc_html__( 'Gradient Text', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'toggle_slug'      => 'main_content',
				'default_on_front' => 'Supreme Gradient Text',
			),
		);
	}

	public function render( $attrs, $content, $render_slug ) {
		$gradient_text = $this->props['gradient_text'];
		$header_level  = $this->props['header_level'];

		if ( '' !== $gradient_text ) {
			$gradient_text = sprintf(
				'<%1$s class="dsm-gradient-text et_pb_module_header">%2$s</%1$s>',
				et_pb_process_header_level( $header_level, 'h1' ),
				$gradient_text
			);
		}

		$this->add_classname(
			array(
				$this->get_text_orientation_classname(),
			)
		);

		// Render module content.
		$output = sprintf(
			'%1$s',
			$gradient_text
		);

		if ( isset( get_option( 'dsm_settings_misc' )['dsm_dynamic_assets'] ) && ! empty( get_option( 'dsm_settings_misc' )['dsm_dynamic_assets'] ) && 'on' === get_option( 'dsm_settings_misc' )['dsm_dynamic_assets'] ) {
			if ( isset( get_option( 'dsm_settings_misc' )['dsm_dynamic_assets_compatibility'] ) && ! empty( get_option( 'dsm_settings_misc' )['dsm_dynamic_assets'] ) && 'on' === get_option( 'dsm_settings_misc' )['dsm_dynamic_assets_compatibility'] ) {
				wp_enqueue_style( 'dsm-gradient-text', plugin_dir_url( __DIR__ ) . 'GradientText/style.css', array(), DSM_VERSION, 'all' );
			} else {
				add_filter( 'et_global_assets_list', array( $this, 'dsm_load_required_divi_assets' ), 10, 3 );
				add_filter( 'et_late_global_assets_list', array( $this, 'dsm_load_required_divi_assets' ), 10, 3 );
			}
		}

		return $output;
	}

	/**
	 * Force load global styles.
	 *
	 * @param array $assets_list Current global assets on the list.
	 *
	 * @return array
	 */
	public function dsm_load_required_divi_assets( $assets_list, $assets_args, $instance ) {
		$assets_prefix     = et_get_dynamic_assets_path();
		$all_shortcodes    = $instance->get_saved_page_shortcodes();
		$this->_cpt_suffix = et_builder_should_wrap_styles() && ! et_is_builder_plugin_active() ? '_cpt' : '';

		if ( ! isset( $assets_list['et_jquery_magnific_popup'] ) ) {
			$assets_list['et_jquery_magnific_popup'] = array(
				'css' => "{$assets_prefix}/css/magnific_popup.css",
			);
		}

		if ( ! isset( $assets_list['et_pb_overlay'] ) ) {
			$assets_list['et_pb_overlay'] = array(
				'css' => "{$assets_prefix}/css/overlay{$this->_cpt_suffix}.css",
			);
		}

		// GradientText.
		if ( ! isset( $assets_list['dsm_gradient_text'] ) ) {
			$assets_list['dsm_gradient_text'] = array(
				'css' => plugin_dir_url( __DIR__ ) . 'GradientText/style.css',
			);
		}

		return $assets_list;
	}
}

new DSM_GradientText();
