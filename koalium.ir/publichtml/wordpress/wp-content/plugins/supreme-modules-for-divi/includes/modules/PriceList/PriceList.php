<?php

class DSM_PriceList extends ET_Builder_Module {

	public $slug       = 'dsm_pricelist';
	public $vb_support = 'on';
	public $child_slug = 'dsm_pricelist_child';
	public $icon_path;

	protected $module_credits = array(
		'module_uri' => 'https://divisupreme.com/',
		'author'     => 'Divi Supreme',
		'author_uri' => 'https://divisupreme.com/',
	);

	public function init() {
		$this->name      = esc_html__( 'Supreme Price List', 'dsm-supreme-modules-for-divi' );
		$this->icon_path = plugin_dir_path( __FILE__ ) . 'icon.svg';
		// Toggle settings
		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'dsm-supreme-modules-for-divi' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'separator' => array(
						'title'    => esc_html__( 'Separator', 'dsm-supreme-modules-for-divi' ),
						'priority' => 70,
					),
					'image'     => array(
						'title'    => esc_html__( 'Image', 'dsm-supreme-modules-for-divi' ),
						'priority' => 69,
					),
				),
			),
		);
	}

	public function get_advanced_fields_config() {
		return array(
			'fonts'      => array(
				'header'  => array(
					'label'             => esc_html__( 'Title', 'dsm-supreme-modules-for-divi' ),
					'css'               => array(
						'main' => '%%order_class%% .dsm-pricelist-title',
					),
					'font_size'         => array(
						'default' => '26px',
					),
					'line_height'       => array(
						'default' => '1em',
					),
					'letter_spacing'    => array(
						'default' => '0px',
					),
					'hide_header_level' => true,
					'hide_text_align'   => true,
				),
				'content' => array(
					'label'           => esc_html__( 'Description', 'dsm-supreme-modules-for-divi' ),
					'css'             => array(
						'main' => '%%order_class%% .dsm-pricelist-description',
					),
					'font_size'       => array(
						'default' => '14px',
					),
					'line_height'     => array(
						'default' => '1em',
					),
					'letter_spacing'  => array(
						'default' => '0px',
					),
					'hide_text_align' => true,
				),
				'price'   => array(
					'label'           => esc_html__( 'Price', 'dsm-supreme-modules-for-divi' ),
					'css'             => array(
						'main' => '%%order_class%% .dsm-pricelist-price',
					),
					'font_size'       => array(
						'default' => '18px',
					),
					'line_height'     => array(
						'default' => '1em',
					),
					'letter_spacing'  => array(
						'default' => '0px',
					),
					'hide_text_align' => true,
				),
			),
			'text'       => array(
				'use_text_orientation'  => false,
				'use_background_layout' => false,
				'css'                   => array(
					'text_shadow' => '%%order_class%% .dsm_pricelist_child',
				),
			),
			'borders'    => array(
				'default'     => array(
					'css' => array(
						'main' => array(
							'border_radii'  => '%%order_class%%',
							'border_styles' => '%%order_class%%',
						),
					),
				),
				'image'       => array(
					'css'          => array(
						'main' => array(
							'border_radii'  => '%%order_class%% .dsm-pricelist-image img',
							'border_styles' => '%%order_class%% .dsm-pricelist-image img',
						),
					),
					'label_prefix' => esc_html__( 'Image', 'dsm-supreme-modules-for-divi' ),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'image',
				),
				'image_price' => array(
					'css'          => array(
						'main' => array(
							'border_radii'  => '%%order_class%% .dsm-pricelist-price',
							'border_styles' => '%%order_class%% .dsm-pricelist-price',
						),
					),
					'label_prefix' => esc_html__( 'Price', 'dsm-supreme-modules-for-divi' ),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'price',
				),
			),
			'box_shadow' => array(
				'default'     => array(
					'css' => array(
						'main' => '%%order_class%%',
					),
				),
				'image'       => array(
					'label'             => esc_html__( 'Image Box Shadow', 'dsm-supreme-modules-for-divi' ),
					'option_category'   => 'layout',
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'image',
					'css'               => array(
						'main' => '%%order_class%% .dsm-pricelist-image img',
					),
					'default_on_fronts' => array(
						'color'    => '',
						'position' => '',
					),
				),
				'image_price' => array(
					'label'             => esc_html__( 'Price Box Shadow', 'dsm-supreme-modules-for-divi' ),
					'option_category'   => 'layout',
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'price',
					'css'               => array(
						'main' => '%%order_class%% .dsm-pricelist-price',
					),
					'default_on_fronts' => array(
						'color'    => '',
						'position' => '',
					),
				),
			),
		);
	}

	public function get_fields() {
		return array(
			'content_orientation'    => array(
				'label'           => esc_html__( 'Vertical Alignment', 'dsm-supreme-modules-for-divi' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options'         => array(
					'flex-start' => esc_html__( 'Top', 'dsm-supreme-modules-for-divi' ),
					'center'     => esc_html__( 'Center', 'dsm-supreme-modules-for-divi' ),
					'flex-end'   => esc_html__( 'Bottom', 'dsm-supreme-modules-for-divi' ),
				),
				'default'         => 'flex-start',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'text',
				'description'     => esc_html__( 'This setting determines the vertical alignment of your content. Your content can either be align to the top, vertically centered, or aligned to the bottom.', 'dsm-supreme-modules-for-divi' ),
			),
			'item_bottom_gap'        => array(
				'label'            => esc_html__( 'Item Bottom Spacing', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'range',
				'option_category'  => 'configuration',
				'default'          => '25px',
				'default_on_front' => '25px',
				'default_unit'     => 'px',
				'range_settings'   => array(
					'min'  => '0',
					'max'  => '60',
					'step' => '1',
				),
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'width',
				'allow_empty'      => true,
				'mobile_options'   => true,
				'responsive'       => true,
				'hover'            => 'tabs',
			),
			'separator_style'        => array(
				'label'           => esc_html__( 'Style', 'dsm-supreme-modules-for-divi' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'default'         => 'dotted',
				'options'         => et_divi_divider_style_choices(),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'separator',
			),
			'separator_weight'       => array(
				'label'            => esc_html__( 'Weight', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'range',
				'option_category'  => 'configuration',
				'default'          => '2px',
				'default_on_front' => '2px',
				'default_unit'     => 'px',
				'range_settings'   => array(
					'min'  => '0',
					'max'  => '10',
					'step' => '1',
				),
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'separator',
			),
			'separator_color'        => array(
				'default'     => '#333',
				'label'       => esc_html__( 'Color', 'dsm-supreme-modules-for-divi' ),
				'type'        => 'color-alpha',
				'description' => esc_html__( 'Here you can define a custom color for your separator.', 'dsm-supreme-modules-for-divi' ),
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'separator',
			),
			'separator_gap'          => array(
				'label'            => esc_html__( 'Gap Spacing', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'range',
				'option_category'  => 'configuration',
				'default'          => '10px',
				'default_on_front' => '10px',
				'default_unit'     => 'px',
				'range_settings'   => array(
					'min'  => '0',
					'max'  => '40',
					'step' => '1',
				),
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'separator',
				'mobile_options'   => true,
				'responsive'       => true,
				'hover'            => 'tabs',
			),
			'image_max_width'        => array(
				'label'            => esc_html__( 'Image Width', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'range',
				'option_category'  => 'layout',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'image',
				'validate_unit'    => true,
				'depends_show_if'  => 'off',
				'default'          => '50%',
				'default_unit'     => '%',
				'default_on_front' => '',
				'allow_empty'      => true,
				'range_settings'   => array(
					'min'  => '0',
					'max'  => '50',
					'step' => '1',
				),
				'mobile_options'   => true,
				'responsive'       => true,
				'hover'            => 'tabs',
			),
			'image_spacing'          => array(
				'label'            => esc_html__( 'Image Gap Spacing', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'range',
				'option_category'  => 'layout',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'image',
				'validate_unit'    => true,
				'default'          => '25px',
				'default_unit'     => 'px',
				'default_on_front' => '',
				'allow_empty'      => true,
				'range_settings'   => array(
					'min'  => '0',
					'max'  => '50',
					'step' => '1',
				),
				'mobile_options'   => true,
				'responsive'       => true,
				'hover'            => 'tabs',
			),
			'price_background_color' => array(
				'label'          => esc_html__( 'Price Background Color', 'dsm-supreme-modules-for-divi' ),
				'type'           => 'color-alpha',
				'description'    => esc_html__( 'Here you can define a custom background color for your price.', 'dsm-supreme-modules-for-divi' ),
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'price',
				'mobile_options' => true,
				'responsive'     => true,
				'hover'          => 'tabs',
			),
			'price_padding'          => array(
				'label'            => esc_html__( 'Padding', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'custom_padding',
				'option_category'  => 'layout',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'price',
				'validate_unit'    => true,
				'default'          => '',
				'default_unit'     => 'px',
				'default_on_front' => '',
				'mobile_options'   => true,
				'responsive'       => true,
				'hover'            => 'tabs',
			),
		);
	}

	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		$fields['image_spacing'] = array(
			'margin-right' => '%%order_class%% .dsm-pricelist-image',
		);

		$fields['image_max_width'] = array(
			'max-width' => '%%order_class%% .dsm-pricelist-image',
		);

		$fields['price_background_color'] = array(
			'background-color' => '%%order_class%% .dsm-pricelist-price',
		);

		$fields['price_padding'] = array(
			'padding' => '%%order_class%% .dsm-pricelist-price',
		);

		$fields['separator_gap'] = array(
			'margin-left'  => '%%order_class%% .dsm-pricelist-separator',
			'margin-right' => '%%order_class%% .dsm-pricelist-separator',
		);

		$fields['item_bottom_gap'] = array(
			'padding-bottom' => '%%order_class%% .dsm_pricelist_child:not(:last-child)',
		);

		return $fields;
	}

	public function render( $attrs, $content, $render_slug ) {
		$content_orientation                = $this->props['content_orientation'];
		$separator_style                    = $this->props['separator_style'];
		$separator_weight                   = $this->props['separator_weight'];
		$separator_color                    = $this->props['separator_color'];
		$separator_gap_hover                = $this->get_hover_value( 'separator_gap' );
		$separator_gap                      = $this->props['separator_gap'];
		$separator_gap_tablet               = $this->props['separator_gap_tablet'];
		$separator_gap_phone                = $this->props['separator_gap_phone'];
		$separator_gap_last_edited          = $this->props['separator_gap_last_edited'];
		$item_bottom_gap_hover              = $this->get_hover_value( 'item_bottom_gap' );
		$item_bottom_gap                    = $this->props['item_bottom_gap'];
		$item_bottom_gap_tablet             = $this->props['item_bottom_gap_tablet'];
		$item_bottom_gap_phone              = $this->props['item_bottom_gap_phone'];
		$item_bottom_gap_last_edited        = $this->props['item_bottom_gap_last_edited'];
		$image_spacing_hover                = $this->get_hover_value( 'image_spacing' );
		$image_spacing                      = $this->props['image_spacing'];
		$image_spacing_tablet               = $this->props['image_spacing_tablet'];
		$image_spacing_phone                = $this->props['image_spacing_phone'];
		$image_spacing_last_edited          = $this->props['image_spacing_last_edited'];
		$image_max_width_hover              = $this->get_hover_value( 'image_max_width' );
		$image_max_width                    = $this->props['image_max_width'];
		$image_max_width_tablet             = $this->props['image_max_width_tablet'];
		$image_max_width_phone              = $this->props['image_max_width_phone'];
		$image_max_width_last_edited        = $this->props['image_max_width_last_edited'];
		$price_background_color_hover       = $this->get_hover_value( 'price_background_color' );
		$price_background_color             = $this->props['price_background_color'];
		$price_background_color_tablet      = $this->props['price_background_color_tablet'];
		$price_background_color_phone       = $this->props['price_background_color_phone'];
		$price_background_color_last_edited = $this->props['price_background_color_last_edited'];
		$price_padding                      = $this->props['price_padding'];
		$price_padding_hover                = $this->get_hover_value( 'price_padding' );
		$price_padding_values               = et_pb_responsive_options()->get_property_values( $this->props, 'price_padding' );
		$price_padding_tablet               = isset( $price_padding_values['tablet'] ) ? $price_padding_values['tablet'] : '';
		$price_padding_phone                = isset( $price_padding_values['phone'] ) ? $price_padding_values['phone'] : '';
		$price_padding_last_edited          = $this->props['price_padding_last_edited'];

		if ( '' !== $item_bottom_gap_tablet || '' !== $item_bottom_gap_phone || '' !== $item_bottom_gap ) {
			$item_bottom_gap_responsive_active = et_pb_get_responsive_status( $item_bottom_gap_last_edited );

			$item_bottom_gap_values = array(
				'desktop' => $item_bottom_gap,
				'tablet'  => $item_bottom_gap_responsive_active ? $item_bottom_gap_tablet : '',
				'phone'   => $item_bottom_gap_responsive_active ? $item_bottom_gap_phone : '',
			);

			et_pb_responsive_options()->generate_responsive_css( $item_bottom_gap_values, '%%order_class%% .dsm_pricelist_child:not(:last-child)', 'padding-bottom', $render_slug );
		}
		if ( et_builder_is_hover_enabled( 'item_bottom_gap', $this->props ) ) {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => $this->add_hover_to_order_class( '%%order_class%% .dsm_pricelist_child:not(:last-child)' ),
					'declaration' => sprintf(
						'padding-bottom: %1$s;',
						esc_html( $item_bottom_gap_hover )
					),
				)
			);
		}

		if ( '' !== $image_max_width_tablet || '' !== $image_max_width_phone || '' !== $image_max_width ) {
			$image_max_width_responsive_active = et_pb_get_responsive_status( $image_max_width_last_edited );

			$image_max_width_values = array(
				'desktop' => $image_max_width,
				'tablet'  => $image_max_width_responsive_active ? $image_max_width_tablet : '',
				'phone'   => $image_max_width_responsive_active ? $image_max_width_phone : '',
			);

			et_pb_responsive_options()->generate_responsive_css( $image_max_width_values, '%%order_class%% .dsm-pricelist-image', 'max-width', $render_slug );
		}
		if ( et_builder_is_hover_enabled( 'image_max_width', $this->props ) ) {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => $this->add_hover_to_order_class( '%%order_class%% .dsm-pricelist-image' ),
					'declaration' => sprintf(
						'max-width: %1$s;',
						esc_html( $image_max_width_hover )
					),
				)
			);
		}

		if ( '' !== $image_spacing_tablet || '' !== $image_spacing_phone || '' !== $image_spacing ) {
			$image_spacing_responsive_active = et_pb_get_responsive_status( $image_spacing_last_edited );

			$image_spacing_values = array(
				'desktop' => $image_spacing,
				'tablet'  => $image_spacing_responsive_active ? $image_spacing_tablet : '',
				'phone'   => $image_spacing_responsive_active ? $image_spacing_phone : '',
			);

			et_pb_responsive_options()->generate_responsive_css( $image_spacing_values, '%%order_class%% .dsm-pricelist-image', 'margin-right', $render_slug );
		}
		if ( et_builder_is_hover_enabled( 'image_spacing', $this->props ) ) {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => $this->add_hover_to_order_class( '%%order_class%% .dsm-pricelist-image' ),
					'declaration' => sprintf(
						'margin-right: %1$s;',
						esc_html( $image_spacing_hover )
					),
				)
			);
		}

		if ( 'dotted' !== $separator_style ) {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%% .dsm-pricelist-separator',
					'declaration' => sprintf(
						'border-bottom-style: %1$s;',
						esc_attr( $separator_style )
					),
				)
			);
		}

		if ( '2px' !== $separator_weight ) {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%% .dsm-pricelist-separator',
					'declaration' => sprintf(
						'border-bottom-width: %1$s;',
						esc_attr( $separator_weight )
					),
				)
			);
		}

		if ( '' !== $separator_color ) {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%% .dsm-pricelist-separator',
					'declaration' => sprintf(
						'border-bottom-color: %1$s;',
						esc_html( $separator_color )
					),
				)
			);
		}

		if ( '' !== $separator_gap_tablet || '' !== $separator_gap_phone || '' !== $separator_gap ) {
			$separator_gap_responsive_active = et_pb_get_responsive_status( $separator_gap_last_edited );

			$separator_gap_values = array(
				'desktop' => $separator_gap,
				'tablet'  => $separator_gap_responsive_active ? $separator_gap_tablet : '',
				'phone'   => $separator_gap_responsive_active ? $separator_gap_phone : '',
			);

			et_pb_responsive_options()->generate_responsive_css( $separator_gap_values, '%%order_class%% .dsm-pricelist-separator', 'margin-left', $render_slug );
			et_pb_responsive_options()->generate_responsive_css( $separator_gap_values, '%%order_class%% .dsm-pricelist-separator', 'margin-right', $render_slug );
		}
		if ( et_builder_is_hover_enabled( 'separator_gap', $this->props ) ) {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => $this->add_hover_to_order_class( '%%order_class%% .dsm-pricelist-separator' ),
					'declaration' => sprintf(
						'margin-left: %1$s; margin-right: %1$s;',
						esc_html( $separator_gap_hover )
					),
				)
			);
		}

		$price_background_color_style        = sprintf( 'background-color: %1$s;', esc_attr( $price_background_color ) );
		$price_background_color_tablet_style = '' !== $price_background_color_tablet ? sprintf( 'background-color: %1$s;', esc_attr( $price_background_color_tablet ) ) : '';
		$price_background_color_phone_style  = '' !== $price_background_color_phone ? sprintf( 'background-color: %1$s;', esc_attr( $price_background_color_phone ) ) : '';
		$price_background_color_style_hover  = '';

		if ( et_builder_is_hover_enabled( 'price_background_color', $this->props ) ) {
			$price_background_color_style_hover = sprintf( 'background-color: %1$s;', esc_attr( $price_background_color_hover ) );
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => $this->add_hover_to_order_class( '%%order_class%% .dsm-pricelist-price' ),
					'declaration' => $price_background_color_style_hover,
				)
			);
		}

		if ( '' !== $price_background_color ) {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%% .dsm-pricelist-price',
					'declaration' => $price_background_color_style,
				)
			);
		}

		if ( '' !== $price_background_color_tablet_style ) {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%% .dsm-pricelist-price',
					'declaration' => $price_background_color_tablet_style,
					'media_query' => ET_Builder_Element::get_media_query( 'max_width_980' ),
				)
			);
		}

		if ( '' !== $price_background_color_phone_style ) {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%% .dsm-pricelist-price',
					'declaration' => $price_background_color_phone_style,
					'media_query' => ET_Builder_Element::get_media_query( 'max_width_767' ),
				)
			);
		}

		if ( 'flex-start' !== $content_orientation ) {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%% .dsm_pricelist_child>div',
					'declaration' => sprintf(
						'align-items: %1$s;',
						esc_attr( $content_orientation )
					),
				)
			);
		}

		$this->apply_custom_margin_padding(
			$render_slug,
			'price_padding',
			'padding',
			'%%order_class%% .dsm-pricelist-price'
		);

		// Render module content.
		$output = sprintf(
			'<div class="dsm_pricelist">%1$s</div>',
			et_core_sanitized_previously( $this->content )
		);

		if ( isset( get_option( 'dsm_settings_misc' )['dsm_dynamic_assets'] ) && ! empty( get_option( 'dsm_settings_misc' )['dsm_dynamic_assets'] ) && 'on' === get_option( 'dsm_settings_misc' )['dsm_dynamic_assets'] ) {
			if ( isset( get_option( 'dsm_settings_misc' )['dsm_dynamic_assets_compatibility'] ) && ! empty( get_option( 'dsm_settings_misc' )['dsm_dynamic_assets'] ) && 'on' === get_option( 'dsm_settings_misc' )['dsm_dynamic_assets_compatibility'] ) {
				wp_enqueue_style( 'dsm-pricelist', plugin_dir_url( __DIR__ ) . 'PriceList/style.css', array(), DSM_VERSION, 'all' );
			} else {
				add_filter( 'et_global_assets_list', array( $this, 'dsm_load_required_divi_assets' ), 10, 3 );
				add_filter( 'et_late_global_assets_list', array( $this, 'dsm_load_required_divi_assets' ), 10, 3 );
			}
		}

		return $output;
	}
	/**
	 * Apply Margin and Padding
	 */
	public function apply_custom_margin_padding( $function_name, $slug, $type, $class, $important = false ) {
		$slug_value                   = $this->props[ $slug ];
		$slug_value_tablet            = $this->props[ $slug . '_tablet' ];
		$slug_value_phone             = $this->props[ $slug . '_phone' ];
		$slug_value_last_edited       = $this->props[ $slug . '_last_edited' ];
		$slug_value_responsive_active = et_pb_get_responsive_status( $slug_value_last_edited );

		if ( isset( $slug_value ) && ! empty( $slug_value ) ) {
			ET_Builder_Element::set_style(
				$function_name,
				array(
					'selector'    => $class,
					'declaration' => et_builder_get_element_style_css( $slug_value, $type, $important ),
				)
			);
		}

		if ( isset( $slug_value_tablet ) && ! empty( $slug_value_tablet ) && $slug_value_responsive_active ) {
			ET_Builder_Element::set_style(
				$function_name,
				array(
					'selector'    => $class,
					'declaration' => et_builder_get_element_style_css( $slug_value_tablet, $type, $important ),
					'media_query' => ET_Builder_Element::get_media_query( 'max_width_980' ),
				)
			);
		}

		if ( isset( $slug_value_phone ) && ! empty( $slug_value_phone ) && $slug_value_responsive_active ) {
			ET_Builder_Element::set_style(
				$function_name,
				array(
					'selector'    => $class,
					'declaration' => et_builder_get_element_style_css( $slug_value_phone, $type, $important ),
					'media_query' => ET_Builder_Element::get_media_query( 'max_width_767' ),
				)
			);
		}
		if ( et_builder_is_hover_enabled( $slug, $this->props ) ) {
			if ( isset( $this->props[ $slug . '__hover' ] ) ) {
				$hover = $this->props[ $slug . '__hover' ];
				ET_Builder_Element::set_style(
					$function_name,
					array(
						'selector'    => $this->add_hover_to_order_class( $class ),
						'declaration' => et_builder_get_element_style_css( $hover, $type, $important ),
					)
				);
			}
		}
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

		// PriceList.
		if ( ! isset( $assets_list['dsm_pricelist'] ) ) {
			$assets_list['dsm_pricelist'] = array(
				'css' => plugin_dir_url( __DIR__ ) . 'PriceList/style.css',
			);
		}

		return $assets_list;
	}
}

new DSM_PriceList();
