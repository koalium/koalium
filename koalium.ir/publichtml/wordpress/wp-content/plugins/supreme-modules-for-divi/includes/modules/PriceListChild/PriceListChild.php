<?php

class DSM_PriceList_Child extends ET_Builder_Module {

	public $slug       = 'dsm_pricelist_child';
	public $vb_support = 'on';
	public $type       = 'child';

	protected $module_credits = array(
		'module_uri' => 'https://divisupreme.com/',
		'author'     => 'Divi Supreme',
		'author_uri' => 'https://divisupreme.com/',
	);

	public function init() {
		$this->name                        = esc_html__( 'Price List Item', 'dsm-supreme-modules-for-divi' );
		$this->advanced_setting_title_text = esc_html__( 'Price List Item', 'dsm-supreme-modules-for-divi' );
		$this->settings_text               = esc_html__( 'Price List Item Settings', 'dsm-supreme-modules-for-divi' );
		$this->child_title_var             = 'admin_title';
		$this->child_title_fallback_var    = 'title';

		$this->settings_modal_toggles = array(
			'general'    => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'dsm-supreme-modules-for-divi' ),
					'link'         => esc_html__( 'Link', 'dsm-supreme-modules-for-divi' ),
					'image'        => esc_html__( 'Image', 'dsm-supreme-modules-for-divi' ),
				),
			),
			'advanced'   => array(
				'toggles' => array(
					'icon_settings' => esc_html__( 'Image', 'dsm-supreme-modules-for-divi' ),
					'text'          => array(
						'title'    => esc_html__( 'Text', 'dsm-supreme-modules-for-divi' ),
						'priority' => 49,
					),
					'width'         => array(
						'title'    => esc_html__( 'Sizing', 'dsm-supreme-modules-for-divi' ),
						'priority' => 65,
					),
				),
			),
			'custom_css' => array(
				'toggles' => array(
					'attributes' => array(
						'title'    => esc_html__( 'Attributes', 'dsm-supreme-modules-for-divi' ),
						'priority' => 95,
					),
				),
			),
		);
	}

	public function get_advanced_fields_config() {
		return array(
			'fonts'           => false,
			'text'            => array(
				'use_text_orientation'  => false,
				'use_background_layout' => false,
				'css'                   => array(
					'text_shadow' => '%%order_class%% .dsm_pricelist_item_wrapper',
				),
			),
			'borders'         => array(
				'default' => array(),
				'image'   => array(
					'css'          => array(
						'main' => array(
							'border_radii'  => '%%order_class%% .dsm-pricelist-image img',
							'border_styles' => '%%order_class%% .dsm-pricelist-image img',
						),
					),
					'label_prefix' => esc_html__( 'Image', 'dsm-supreme-modules-for-divi' ),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'icon_settings',
				),
			),
			'box_shadow'      => array(
				'default' => array(),
				'image'   => array(
					'label'             => esc_html__( 'Image Box Shadow', 'dsm-supreme-modules-for-divi' ),
					'option_category'   => 'layout',
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'icon_settings',
					'css'               => array(
						'main' => '%%order_class%% .dsm-pricelist-image img',
					),
					'default_on_fronts' => array(
						'color'    => '',
						'position' => '',
					),
				),
			),
			'margin_padding'  => array(
				'css' => array(
					'main'      => '%%order_class%%',
					'important' => 'all',
				),
			),
			'button'          => false,
			'filters'         => array(
				'child_filters_target' => array(
					'tab_slug'    => 'advanced',
					'toggle_slug' => 'icon_settings',
				),
			),
			'icon_settings'   => array(
				'css' => array(
					'main' => '%%order_class%% .dsm-pricelist-image img',
				),
			),
			'position_fields' => false,
		);
	}

	public function get_fields() {
		$et_accent_color = et_builder_accent_color();

		return array(
			'module_id'       => array(
				'label'           => esc_html__( 'CSS ID', 'dsm-supreme-modules-for-divi' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'description'     => esc_html__( "Assign a unique CSS ID to the element which can be used to assign custom CSS styles from within your child theme or from within Divi's custom CSS inputs.", 'dsm-supreme-modules-for-divi' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class'    => array(
				'label'           => esc_html__( 'CSS Class', 'dsm-supreme-modules-for-divi' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'description'     => esc_html__( "Assign any number of CSS Classes to the element, separated by spaces, which can be used to assign custom CSS styles from within your child theme or from within Divi's custom CSS inputs.", 'dsm-supreme-modules-for-divi' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'admin_title'     => array(
				'label'       => esc_html__( 'Admin Label', 'dsm-supreme-modules-for-divi' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the business hours item in the builder for easy identification.', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug' => 'admin_label',
			),
			'price'           => array(
				'label'            => esc_html__( 'Price', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Add the price of the item', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'      => 'main_content',
				'default'          => '$8',
				'default_on_front' => '$8',
				'dynamic_content'  => 'text',
				'mobile_options'   => true,

			),
			'title'           => array(
				'label'            => esc_html__( 'Title', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Text entered here will appear as title.', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'      => 'main_content',
				'default_on_front' => 'The title of the first pricing item',
				'dynamic_content'  => 'text',
				'mobile_options'   => true,

			),
			'image'           => array(
				'label'              => esc_html__( 'Image', 'dsm-supreme-modules-for-divi' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => esc_attr__( 'Upload an image', 'dsm-supreme-modules-for-divi' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'dsm-supreme-modules-for-divi' ),
				'update_text'        => esc_attr__( 'Set As Image', 'dsm-supreme-modules-for-divi' ),
				'depends_show_if'    => 'off',
				'description'        => esc_html__( 'Upload an image to display at the top of your blurb.', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'        => 'image',
				'dynamic_content'    => 'image',
			),
			'alt'             => array(
				'label'           => esc_html__( 'Image Alt Text', 'dsm-supreme-modules-for-divi' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Define the HTML ALT text for your image here.', 'dsm-supreme-modules-for-divi' ),
				'depends_show_if' => 'off',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'attributes',
				'dynamic_content' => 'text',
			),
			'content'         => array(
				'label'           => esc_html__( 'Content', 'dsm-supreme-modules-for-divi' ),
				'type'            => 'tiny_mce',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Content entered here will appear inside the module.', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
				'mobile_options'  => true,

			),
			'image_max_width' => array(
				'label'            => esc_html__( 'Image Width', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'range',
				'option_category'  => 'layout',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'icon_settings',
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
			'image_spacing'   => array(
				'label'            => esc_html__( 'Image Gap Spacing', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'range',
				'option_category'  => 'layout',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'icon_settings',
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

		);
	}

	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		$fields['image_spacing'] = array(
			'margin-right' => '%%order_class%%.dsm_pricelist_child .dsm-pricelist-image',
		);

		$fields['image_max_width'] = array(
			'max-width' => '%%order_class%%.dsm_pricelist_child .dsm-pricelist-image',
		);

		return $fields;
	}

	public function render( $attrs, $content, $render_slug ) {
		$multi_view                  = et_pb_multi_view_options( $this );
		$title                       = $this->props['title'];
		$price                       = $this->props['price'];
		$image                       = $this->props['image'];
		$alt                         = $this->props['alt'];
		$image_spacing_hover         = $this->get_hover_value( 'image_spacing' );
		$image_spacing               = $this->props['image_spacing'];
		$image_spacing_tablet        = $this->props['image_spacing_tablet'];
		$image_spacing_phone         = $this->props['image_spacing_phone'];
		$image_spacing_last_edited   = $this->props['image_spacing_last_edited'];
		$image_max_width_hover       = $this->get_hover_value( 'image_max_width' );
		$image_max_width             = $this->props['image_max_width'];
		$image_max_width_tablet      = $this->props['image_max_width_tablet'];
		$image_max_width_phone       = $this->props['image_max_width_phone'];
		$image_max_width_last_edited = $this->props['image_max_width_last_edited'];

		if ( '' !== $image_max_width_tablet || '' !== $image_max_width_phone || '' !== $image_max_width ) {
			$image_max_width_responsive_active = et_pb_get_responsive_status( $image_max_width_last_edited );

			$image_max_width_values = array(
				'desktop' => $image_max_width,
				'tablet'  => $image_max_width_responsive_active ? $image_max_width_tablet : '',
				'phone'   => $image_max_width_responsive_active ? $image_max_width_phone : '',
			);

			et_pb_responsive_options()->generate_responsive_css( $image_max_width_values, '%%order_class%%.dsm_pricelist_child .dsm-pricelist-image', 'max-width', $render_slug );
		}
		if ( et_builder_is_hover_enabled( 'image_max_width', $this->props ) ) {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => $this->add_hover_to_order_class( '%%order_class%%.dsm_pricelist_child .dsm-pricelist-image' ),
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

			et_pb_responsive_options()->generate_responsive_css( $image_spacing_values, '%%order_class%%.dsm_pricelist_child .dsm-pricelist-image', 'margin-right', $render_slug );
		}
		if ( et_builder_is_hover_enabled( 'image_spacing', $this->props ) ) {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => $this->add_hover_to_order_class( '%%order_class%%.dsm_pricelist_child .dsm-pricelist-image' ),
					'declaration' => sprintf(
						'margin-right: %1$s;',
						esc_html( $image_spacing_hover )
					),
				)
			);
		}

		if ( '' !== $title ) {
			$title = $multi_view->render_element(
				array(
					'tag'     => 'div',
					'content' => '{{title}}',
					'attrs'   => array(
						'class' => 'dsm-pricelist-title et_pb_module_header',
					),
				)
			);
		}

		if ( '' !== $price ) {
			$price = $multi_view->render_element(
				array(
					'tag'     => 'div',
					'content' => '{{price}}',
					'attrs'   => array(
						'class' => 'dsm-pricelist-price',
					),
				)
			);
		}

		if ( '' !== $this->content ) {
			$content = $multi_view->render_element(
				array(
					'tag'     => 'div',
					'content' => '{{content}}',
					'attrs'   => array(
						'class' => 'dsm-pricelist-description',
					),
				)
			);
		}

		$image = ( '' !== trim( $image ) ) ? sprintf(
			'<img src="%1$s" alt="%2$s" />',
			esc_url( $image ),
			esc_attr( $alt )
			// esc_attr( " et_pb_animation_{$animation}" )
		) : '';

		// Images: Add CSS Filters and Mix Blend Mode rules (if set)
		$generate_css_image_filters = '';
		if ( $image && array_key_exists( 'icon_settings', $this->advanced_fields ) && array_key_exists( 'css', $this->advanced_fields['icon_settings'] ) ) {
			$generate_css_image_filters = $this->generate_css_filters(
				$render_slug,
				'child_',
				self::$data_utils->array_get( $this->advanced_fields['icon_settings']['css'], 'main', '%%order_class%%' )
			);
		}

		$image = $image ? sprintf(
			'<div class="dsm-pricelist-image%2$s">%1$s</div>',
			$image,
			esc_attr( $generate_css_image_filters )
		) : '';

		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$this->module_id( true );

		// Module classnames.
		$this->remove_classname( 'et_pb_module' );
		// Render module content.
		return sprintf(
			'%7$s
			%6$s
			%3$s
			<div class="dsm_pricelist_item_wrapper%5$s">
				<div class="dsm-pricelist-header">
					%1$s
					<div class="dsm-pricelist-separator"></div>
					%2$s
				</div>
				%4$s
				%5$s
			</div>',
			$title,
			$price,
			$image,
			'' !== $this->content ? $content : '',
			$this->get_text_orientation_classname(),
			$video_background,
			$parallax_image_background
		);
	}
}

new DSM_PriceList_Child();
