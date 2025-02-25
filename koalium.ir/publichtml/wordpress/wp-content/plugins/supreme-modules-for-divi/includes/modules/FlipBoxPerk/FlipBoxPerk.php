<?php

class DSM_FlipBox_Perk extends ET_Builder_Module {

	public $slug       = 'dsm_flipbox';
	public $vb_support = 'on';
	public $child_slug = 'dsm_flipbox_child';
	public $icon_path;

	protected $module_credits = array(
		'module_uri' => 'https://divisupreme.com/',
		'author'     => 'Divi Supreme',
		'author_uri' => 'https://divisupreme.com/',
	);

	public function init() {
		$this->name      = esc_html__( 'Supreme Flipbox', 'dsm-supreme-modules-for-divi' );
		$this->icon_path = plugin_dir_path( __FILE__ ) . 'icon.svg';

		// Toggle settings
		$this->settings_modal_toggles = array(
			'general' => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'dsm-supreme-modules-for-divi' ),
				),
			),
		);
	}

	public function get_advanced_fields_config() {
		return array(
			'fonts'       => array(
				'header'  => array(
					'label'          => esc_html__( 'Title', 'dsm-supreme-modules-for-divi' ),
					'css'            => array(
						'main' => '%%order_class%% h1.et_pb_module_header, %%order_class%% h2.et_pb_module_header, %%order_class%% h3.et_pb_module_header, %%order_class%% h4.et_pb_module_header, %%order_class%% h5.et_pb_module_header, %%order_class%% h6.et_pb_module_header',
					),
					'font_size'      => array(
						'default' => '18px',
					),
					'line_height'    => array(
						'default' => '1em',
					),
					'letter_spacing' => array(
						'default' => '0px',
					),
					'header_level'   => array(
						'default' => 'h4',
					),
				),
				'content' => array(
					'label'          => esc_html__( 'Body', 'dsm-supreme-modules-for-divi' ),
					'css'            => array(
						'main' => '%%order_class%% .dsm-content',
					),
					'font_size'      => array(
						'default' => '14px',
					),
					'line_height'    => array(
						'default' => '1.7em',
					),
					'letter_spacing' => array(
						'default' => '0px',
					),
					'block_elements' => array(
						'tabbed_subtoggles' => true,
						'bb_icons_support'  => true,
						'css'               => array(
							'main' => "{$this->main_css_element} .dsm-content",
						),
					),
				),
				'subhead' => array(
					'label'          => esc_html__( 'Subhead', 'dsm-supreme-modules-for-divi' ),
					'css'            => array(
						'main' => '%%order_class%% .dsm-subtitle',
					),
					'font_size'      => array(
						'default' => '14px',
					),
					'line_height'    => array(
						'default' => '1em',
					),
					'letter_spacing' => array(
						'default' => '0px',
					),
				),
			),
			'text'        => array(
				'use_text_orientation'  => false,
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
			'borders'     => array(
				'default' => array(
					'css' => array(
						'main' => array(
							'border_radii'  => '%%order_class%% .dsm_flipbox_child',
							'border_styles' => '%%order_class%% .dsm_flipbox_child',
						),
					),
				),
			),
			'box_shadow'  => array(
				'default' => array(
					'css' => array(
						'main' => '%%order_class%% .dsm_flipbox_child',
					),
				),
			),
			'text_shadow' => array(
				// Don't add text-shadow fields since they already are via font-options.
				'default' => false,
			),
		);
	}

	public function get_fields() {
		return array(
			'flipbox_trigger'         => array(
				'label'           => esc_html__( 'Trigger Animation On', 'dsm-supreme-modules-for-divi' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'default'         => 'hover',
				'options'         => array(
					'hover' => esc_html__( 'Hover', 'dsm-supreme-modules-for-divi' ),
					'click' => esc_html__( 'Click', 'dsm-supreme-modules-for-divi' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'animation',
				'description'     => esc_html__( 'This will only work on the frontend as Visual Builder Clicking feature is not recommended.', 'dsm-supreme-modules-for-divi' ),
			),
			'flipbox_click'           => array(
				'label'           => esc_html__( 'Click on', 'dsm-supreme-modules-for-divi' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'default'         => 'whole',
				'options'         => array(
					'whole'  => esc_html__( 'As Whole', 'dsm-supreme-modules-for-divi' ),
					'button' => esc_html__( 'Button', 'dsm-supreme-modules-for-divi' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'animation',
				'show_if'         => array(
					'flipbox_trigger' => 'click',
				),
			),
			'flipbox_type'            => array(
				'label'           => esc_html__( 'Flipbox Type', 'dsm-supreme-modules-for-divi' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'default'         => 'flip',
				'options'         => array(
					'flip'     => esc_html__( 'Flip', 'dsm-supreme-modules-for-divi' ),
					'slide'    => esc_html__( 'Slide', 'dsm-supreme-modules-for-divi' ),
					'zoom-in'  => esc_html__( 'Zoom In', 'dsm-supreme-modules-for-divi' ),
					'zoom-out' => esc_html__( 'Zoom Out', 'dsm-supreme-modules-for-divi' ),
					'fade'     => esc_html__( 'Fade', 'dsm-supreme-modules-for-divi' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'animation',
			),
			'flipbox_effect'          => array(
				'label'           => esc_html__( 'Flipbox Effect', 'dsm-supreme-modules-for-divi' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'default'         => 'right',
				'options'         => array(
					'left'  => esc_html__( 'Flip Left', 'dsm-supreme-modules-for-divi' ),
					'right' => esc_html__( 'Flip Right', 'dsm-supreme-modules-for-divi' ),
					'up'    => esc_html__( 'Flip Up', 'dsm-supreme-modules-for-divi' ),
					'down'  => esc_html__( 'Flip Down', 'dsm-supreme-modules-for-divi' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'animation',
				'show_if'         => array(
					'flipbox_type' => 'flip',
				),
			),
			'flipbox_3d'              => array(
				'label'           => esc_html__( '3D Effect', 'dsm-supreme-modules-for-divi' ),
				'type'            => 'yes_no_button',
				'option_category' => 'basic_option',
				'options'         => array(
					'off' => esc_html__( 'No', 'dsm-supreme-modules-for-divi' ),
					'on'  => esc_html__( 'Yes', 'dsm-supreme-modules-for-divi' ),
				),
				'default'         => 'off',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'animation',
				'show_if'         => array(
					'flipbox_type' => 'flip',
				),
			),
			'flipbox_slide_effect'    => array(
				'label'           => esc_html__( 'Flipbox Slide Effect', 'dsm-supreme-modules-for-divi' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'default'         => 'slide-up',
				'options'         => array(
					'slide-up'    => esc_html__( 'Slide Up', 'dsm-supreme-modules-for-divi' ),
					'slide-down'  => esc_html__( 'Slide Down', 'dsm-supreme-modules-for-divi' ),
					'slide-left'  => esc_html__( 'Slide Left', 'dsm-supreme-modules-for-divi' ),
					'slide-right' => esc_html__( 'Slide Right', 'dsm-supreme-modules-for-divi' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'animation',
				'show_if'         => array(
					'flipbox_type' => 'slide',
				),
			),
			'flipbox_zoom_out_effect' => array(
				'label'           => esc_html__( 'Flipbox Zoom Out Effect', 'dsm-supreme-modules-for-divi' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'default'         => 'zoom-out',
				'options'         => array(
					'zoom-out'       => esc_html__( 'Zoom Out', 'dsm-supreme-modules-for-divi' ),
					'zoom-out-up'    => esc_html__( 'Zoom Out Up', 'dsm-supreme-modules-for-divi' ),
					'zoom-out-down'  => esc_html__( 'Zoom Out Down', 'dsm-supreme-modules-for-divi' ),
					'zoom-out-left'  => esc_html__( 'Zoom Out Left', 'dsm-supreme-modules-for-divi' ),
					'zoom-out-right' => esc_html__( 'Zoom Out Right', 'dsm-supreme-modules-for-divi' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'animation',
				'show_if'         => array(
					'flipbox_type' => 'zoom-out',
				),
			),
			'flipbox_speed'           => array(
				'label'            => esc_html__( 'Animation Speed (in s)', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'range',
				'option_category'  => 'configuration',
				'default'          => '0.6s',
				'default_on_front' => '0.6s',
				'default_unit'     => 's',
				'validate_unit'    => true,
				'allowed_units'    => array( 's' ),
				'range_settings'   => array(
					'min'  => '0',
					'max'  => '10',
					'step' => '0.1',
				),
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'animation',
			),
			'flipbox_height'          => array(
				'label'            => esc_html__( 'Height', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'range',
				'option_category'  => 'layout',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'width',
				'mobile_options'   => true,
				'validate_unit'    => true,
				'default'          => '200px',
				'default_unit'     => 'px',
				'default_on_front' => '',
				'range_settings'   => array(
					'min'  => '1',
					'max'  => '1200',
					'step' => '1',
				),
				'responsive'       => true,
			),
		);
	}

	function before_render() {
		global $dsm_parent_level;

		$dsm_parent_level = array(
			'header_level' => $this->props['header_level'],
		);
	}

	public function render( $attrs, $content, $render_slug ) {
		$flipbox_type               = $this->props['flipbox_type'];
		$flipbox_effect             = $this->props['flipbox_effect'];
		$flipbox_3d                 = $this->props['flipbox_3d'];
		$flipbox_slide_effect       = $this->props['flipbox_slide_effect'];
		$flipbox_zoom_out_effect    = $this->props['flipbox_zoom_out_effect'];
		$flipbox_speed              = $this->props['flipbox_speed'];
		$flipbox_height             = $this->props['flipbox_height'];
		$flipbox_height_tablet      = $this->props['flipbox_height_tablet'];
		$flipbox_height_phone       = $this->props['flipbox_height_phone'];
		$flipbox_height_last_edited = $this->props['flipbox_height_last_edited'];
		$flipbox_trigger            = $this->props['flipbox_trigger'];
		$flipbox_click              = $this->props['flipbox_click'];

		if ( '' !== $flipbox_height_tablet || '' !== $flipbox_height_phone || '' !== $flipbox_height ) {
			$flipbox_height_responsive_active = et_pb_get_responsive_status( $flipbox_height_last_edited );

			$flipbox_height_values = array(
				'desktop' => $flipbox_height,
				'tablet'  => $flipbox_height_responsive_active ? $flipbox_height_tablet : '',
				'phone'   => $flipbox_height_responsive_active ? $flipbox_height_phone : '',
			);

			et_pb_responsive_options()->generate_responsive_css( $flipbox_height_values, '%%order_class%% .dsm-flipbox', 'height', $render_slug );
		}

		if ( '0.6s' !== $flipbox_speed ) {
			if ( 'flip' === $flipbox_type ) {
				ET_Builder_Element::set_style(
					$render_slug,
					array(
						'selector'    => '%%order_class%% .dsm_flipbox_child',
						'declaration' => sprintf(
							'transition: transform %1$s ease-in-out;',
							esc_attr( $flipbox_speed )
						),
					)
				);
			} elseif ( 'fade' === $flipbox_type ) {
				ET_Builder_Element::set_style(
					$render_slug,
					array(
						'selector'    => '%%order_class%% .dsm_flipbox_child',
						'declaration' => sprintf(
							'transition: opacity %1$s ease-in-out;',
							esc_attr( $flipbox_speed )
						),
					)
				);
			} elseif ( 'zoom-in' === $flipbox_type ) {
				ET_Builder_Element::set_style(
					$render_slug,
					array(
						'selector'    => '%%order_class%% .dsm_flipbox_child',
						'declaration' => sprintf(
							'transition: all %1$s ease-in-out;',
							esc_attr( $flipbox_speed )
						),
					)
				);
			} elseif ( 'zoom-out' === $flipbox_type ) {
				ET_Builder_Element::set_style(
					$render_slug,
					array(
						'selector'    => '%%order_class%% .dsm-flipbox-zoom .dsm_flipbox_child',
						'declaration' => sprintf(
							'transition: all %1$s ease-in-out;',
							esc_attr( $flipbox_speed )
						),
					)
				);
			} elseif ( 'slide' === $flipbox_type ) {
				ET_Builder_Element::set_style(
					$render_slug,
					array(
						'selector'    => '%%order_class%% .dsm_flipbox_child',
						'declaration' => sprintf(
							'transition: transform %1$s ease-in-out;',
							esc_attr( $flipbox_speed )
						),
					)
				);
			}
		}

		if ( 'click' === $flipbox_trigger ) {
			wp_enqueue_script( 'dsm-flipbox-click' );
		}

		// Render module content.
		$output = sprintf(
			'<div class="dsm-flipbox%2$s%3$s%4$s%5$s%6$s%7$s dsm_flipbox_%8$s%9$s">%1$s</div>',
			et_core_sanitized_previously( $this->content ),
			( 'flip' === $flipbox_type ? esc_attr( " dsm-flipbox-effect-{$flipbox_effect}" ) : '' ),
			( 'flip' === $flipbox_type && 'off' !== $flipbox_3d ? ' dsm-flipbox-3d' : '' ),
			( 'zoom-in' === $flipbox_type ? ' dsm-flipbox-zoom-in' : '' ),
			( 'fade' === $flipbox_type ? ' dsm-flipbox-fade' : '' ),
			( 'zoom-out' === $flipbox_type ? esc_attr( " dsm-flipbox-zoom dsm-flipbox-{$flipbox_zoom_out_effect}" ) : '' ),
			( 'slide' === $flipbox_type ? esc_attr( " dsm-flipbox-{$flipbox_slide_effect}" ) : '' ),
			esc_attr( $flipbox_trigger ),
			( 'click' === $flipbox_trigger ? esc_attr( " dsm_flipbox_trigger_{$flipbox_click}" ) : '' )
		);

		if ( isset( get_option( 'dsm_settings_misc' )['dsm_dynamic_assets'] ) && ! empty( get_option( 'dsm_settings_misc' )['dsm_dynamic_assets'] ) && 'on' === get_option( 'dsm_settings_misc' )['dsm_dynamic_assets'] ) {
			if ( isset( get_option( 'dsm_settings_misc' )['dsm_dynamic_assets_compatibility'] ) && ! empty( get_option( 'dsm_settings_misc' )['dsm_dynamic_assets'] ) && 'on' === get_option( 'dsm_settings_misc' )['dsm_dynamic_assets_compatibility'] ) {
				wp_enqueue_style( 'dsm-flipbox', plugin_dir_url( __DIR__ ) . 'FlipBoxPerk/style.css', array(), DSM_VERSION, 'all' );
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

		// Flipbox.
		if ( ! isset( $assets_list['dsm_flipbox'] ) ) {
			$assets_list['dsm_flipbox'] = array(
				'css' => plugin_dir_url( __DIR__ ) . 'FlipBoxPerk/style.css',
			);
		}
		if ( ! isset( $assets_list['et_icons_all'] ) ) {
			$assets_list['et_icons_all'] = array(
				'css' => "{$assets_prefix}/css/icons_all.css",
			);
		}

		if ( ! isset( $assets_list['et_icons_fa'] ) ) {
			$assets_list['et_icons_fa'] = array(
				'css' => "{$assets_prefix}/css/icons_fa_all.css",
			);
		}

		return $assets_list;
	}
}

new DSM_FlipBox_Perk();
