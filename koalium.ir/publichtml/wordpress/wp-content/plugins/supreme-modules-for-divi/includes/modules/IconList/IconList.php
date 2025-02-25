<?php

class DSM_Icon_List extends ET_Builder_Module {

	public $slug       = 'dsm_icon_list';
	public $vb_support = 'on';
	public $child_slug = 'dsm_icon_list_child';
	public $icon_path;

	protected $module_credits = array(
		'module_uri' => 'https://divisupreme.com/',
		'author'     => 'Divi Supreme',
		'author_uri' => 'https://divisupreme.com/',
	);

	public function init() {
		$this->name      = esc_html__( 'Supreme Icon List', 'dsm-supreme-modules-for-divi' );
		$this->icon_path = plugin_dir_path( __FILE__ ) . 'icon.svg';
		// Toggle settings.
		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Icon Lists', 'dsm-supreme-modules-for-divi' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'icon_settings'  => array(
						'title'    => esc_html__( 'Icon', 'dsm-supreme-modules-for-divi' ),
						'priority' => 70,
					),
					'image_settings' => array(
						'title'    => esc_html__( 'Image', 'dsm-supreme-modules-for-divi' ),
						'priority' => 70,
					),
					'list'           => array(
						'title'    => esc_html__( 'List Items', 'dsm-supreme-modules-for-divi' ),
						'priority' => 70,
					),
				),
			),
		);
	}

	public function get_advanced_fields_config() {
		return array(
			'fonts'          => array(
				'text' => array(
					'label'             => esc_html__( '', 'dsm-supreme-modules-for-divi' ),
					'css'               => array(
						'main'        => ' %%order_class%% .dsm_icon_list_child, %%order_class%% .dsm_icon_list_child a',
						'line_height' => "{$this->main_css_element} .dsm_icon_list_text",
					),
					'font_size'         => array(
						'default' => '14px',
					),
					'line_height'       => array(
						'default' => '1.7em',
					),
					'letter_spacing'    => array(
						'default' => '0px',
					),
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'text',
					'hide_header_level' => true,
					'hide_text_align'   => true,
					'hide_text_shadow'  => false,
				),
			),
			'text'           => array(
				'use_text_orientation'  => false,
				'use_background_layout' => false,
				'css'                   => array(
					'text_shadow' => '%%order_class%% .dsm_icon_list_child',
				),
			),
			'borders'        => array(
				'default'   => array(
					'css' => array(
						'main' => array(
							'border_radii'  => '%%order_class%%',
							'border_styles' => '%%order_class%%',
						),
					),
				),
				'icon'      => array(
					'css'          => array(
						'main' => array(
							'border_radii'  => '%%order_class%% .dsm_icon_list_items .dsm_icon_list_icon',
							'border_styles' => '%%order_class%% .dsm_icon_list_items .dsm_icon_list_icon',
						),
					),
					'label_prefix' => esc_html__( 'Icon', 'dsm-supreme-modules-for-divi' ),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'icon_settings',
				),
				'image'     => array(
					'css'          => array(
						'main' => array(
							'border_radii'  => '%%order_class%% .dsm_icon_list_items .dsm_icon_list_image',
							'border_styles' => '%%order_class%% .dsm_icon_list_items .dsm_icon_list_image',
						),
					),
					'label_prefix' => esc_html__( 'Image', 'dsm-supreme-modules-for-divi' ),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'image_settings',
				),
				'list_item' => array(
					'css'          => array(
						'main' => array(
							'border_radii'  => '%%order_class%% .dsm_icon_list_items .dsm_icon_list_child',
							'border_styles' => '%%order_class%% .dsm_icon_list_items .dsm_icon_list_child',
						),
					),
					'label_prefix' => esc_html__( 'List Item', 'dsm-supreme-modules-for-divi' ),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'list',
				),
			),
			'box_shadow'     => array(
				'default'   => array(
					'css' => array(
						'main' => '%%order_class%%',
					),
				),
				'icon'      => array(
					'label'             => esc_html__( 'Icon Box Shadow', 'dsm-supreme-modules-for-divi' ),
					'option_category'   => 'layout',
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'icon_settings',
					'css'               => array(
						'main' => '%%order_class%% .dsm_icon_list_items .dsm_icon_list_icon',
					),
					'default_on_fronts' => array(
						'color'    => '',
						'position' => '',
					),
				),
				'image'     => array(
					'label'             => esc_html__( 'Image Box Shadow', 'dsm-supreme-modules-for-divi' ),
					'option_category'   => 'layout',
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'image_settings',
					'css'               => array(
						'main' => '%%order_class%% .dsm_icon_list_items .dsm_icon_list_image',
					),
					'default_on_fronts' => array(
						'color'    => '',
						'position' => '',
					),
				),
				'list_item' => array(
					'label'             => esc_html__( 'List Item Box Shadow', 'dsm-supreme-modules-for-divi' ),
					'option_category'   => 'layout',
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'list',
					'css'               => array(
						'main' => '%%order_class%% .dsm_icon_list_items .dsm_icon_list_child',
					),
					'default_on_fronts' => array(
						'color'    => '',
						'position' => '',
					),
				),
			),
			'margin_padding' => array(
				'css' => array(
					'main'      => "{$this->main_css_element}",
					'important' => array( 'custom_margin' ),
				),
			),
			'button'         => false,
		);
	}

	public function get_fields() {
		$et_accent_color = et_builder_accent_color();
		return array(
			'icon_color'              => array(
				'default'        => $et_accent_color,
				'label'          => esc_html__( 'Icon Color', 'dsm-supreme-modules-for-divi' ),
				'type'           => 'color-alpha',
				'description'    => esc_html__( 'Here you can define a custom color for your icon.', 'dsm-supreme-modules-for-divi' ),
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'icon_settings',
				'hover'          => 'tabs',
				'mobile_options' => true,
			),
			'icon_background_color'   => array(
				'label'          => esc_html__( 'Icon Background Color', 'dsm-supreme-modules-for-divi' ),
				'type'           => 'color-alpha',
				'description'    => esc_html__( 'Here you can define a custom background color for your icon.', 'dsm-supreme-modules-for-divi' ),
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'icon_settings',
				'hover'          => 'tabs',
				'mobile_options' => true,
			),
			'icon_gap_width'          => array(
				'label'           => esc_html__( 'Icon Gap Width', 'dsm-supreme-modules-for-divi' ),
				'description'     => esc_html__( 'Here you can define a custom gap width for the icon.', 'dsm-supreme-modules-for-divi' ),
				'type'            => 'range',
				'option_category' => 'font_option',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'icon_settings',
				'default_unit'    => 'em',
				'allowed_units'   => array( '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
				'range_settings'  => array(
					'min'  => '1',
					'max'  => '10',
					'step' => '0.1',
				),
				'mobile_options'  => true,
				'responsive'      => true,
				'hover'           => 'tabs',
			),
			'icon_padding'            => array(
				'label'           => esc_html__( 'Icon Padding', 'dsm-supreme-modules-for-divi' ),
				'description'     => esc_html__( 'Here you can define a custom padding size for the icon.', 'dsm-supreme-modules-for-divi' ),
				'type'            => 'range',
				'option_category' => 'font_option',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'icon_settings',
				'default_unit'    => 'px',
				'allowed_units'   => array( '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
				'range_settings'  => array(
					'min'  => '1',
					'max'  => '30',
					'step' => '1',
				),
				'mobile_options'  => true,
				'responsive'      => true,
				'hover'           => 'tabs',
			),
			'icon_font_size'          => array(
				'label'            => esc_html__( 'Icon Font Size', 'dsm-supreme-modules-for-divi' ),
				'description'      => esc_html__( 'Control the size of the icon by increasing or decreasing the font size.', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'range',
				'option_category'  => 'font_option',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'icon_settings',
				'default'          => '14px',
				'default_unit'     => 'px',
				'default_on_front' => '',
				'allowed_units'    => array( '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
				'range_settings'   => array(
					'min'  => '1',
					'max'  => '120',
					'step' => '1',
				),
				'mobile_options'   => true,
				'responsive'       => true,
				'hover'            => 'tabs',
			),
			'list_layout'             => array(
				'label'           => esc_html__( 'Layout', 'dsm-supreme-modules-for-divi' ),
				'description'     => esc_html__( 'The list layout can be either vertical or horizontal.', 'dsm-supreme-modules-for-divi' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options'         => array(
					'vertical'   => esc_html__( 'Vertical', 'dsm-supreme-modules-for-divi' ),
					'horizontal' => esc_html__( 'Horizontal', 'dsm-supreme-modules-for-divi' ),
				),
				'default'         => 'vertical',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'list',
			),
			'list_alignment'          => array(
				'label'           => esc_html__( 'Alignment', 'dsm-supreme-modules-for-divi' ),
				'description'     => esc_html__( 'The List can be placed either above, below or in the center of the module.', 'dsm-supreme-modules-for-divi' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options'         => array(
					'flex-start' => esc_html__( 'Left', 'dsm-supreme-modules-for-divi' ),
					'center'     => esc_html__( 'Center', 'dsm-supreme-modules-for-divi' ),
					'flex-end'   => esc_html__( 'Right', 'dsm-supreme-modules-for-divi' ),
				),
				'default'         => 'flex-start',
				'mobile_options'  => true,
				'responsive'      => true,
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'list',
			),
			'list_vertical_alignment' => array(
				'label'           => esc_html__( 'Vertical Alignment', 'dsm-supreme-modules-for-divi' ),
				'description'     => esc_html__( 'The List can be placed either above, below or in the center of the module.', 'dsm-supreme-modules-for-divi' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options'         => array(
					'flex-start' => esc_html__( 'Top', 'dsm-supreme-modules-for-divi' ),
					'center'     => esc_html__( 'Vertically Centered', 'dsm-supreme-modules-for-divi' ),
					'flex-end'   => esc_html__( 'Bottom', 'dsm-supreme-modules-for-divi' ),
				),
				'default'         => 'center',
				'mobile_options'  => true,
				'responsive'      => true,
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'list',
			),
			'list_direction'          => array(
				'label'           => esc_html__( 'Direction', 'dsm-supreme-modules-for-divi' ),
				'description'     => esc_html__( 'The list direction can be either left-to-right or right-to-left.', 'dsm-supreme-modules-for-divi' ),
				'type'            => 'select',
				'option_category' => 'layout',
				'options'         => array(
					'ltr' => esc_html__( 'Left to Right', 'dsm-supreme-modules-for-divi' ),
					'rtl' => esc_html__( 'Right to Left', 'dsm-supreme-modules-for-divi' ),
				),
				'default'         => 'ltr',
				'mobile_options'  => true,
				'responsive'      => true,
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'list',
			),
			'list_space_between'      => array(
				'label'            => esc_html__( 'Space Between', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'range',
				'option_category'  => 'configuration',
				'default'          => '0px',
				'default_on_front' => '0px',
				'default_unit'     => 'px',
				'range_settings'   => array(
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				),
				'mobile_options'   => true,
				'responsive'       => true,
				'hover'            => 'tabs',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'list',
			),
			'list_background'         => array(
				'label'          => esc_html__( 'Background Color', 'dsm-supreme-modules-for-divi' ),
				'type'           => 'color-alpha',
				'description'    => esc_html__( 'Here you can define a custom color for your list items.', 'dsm-supreme-modules-for-divi' ),
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'list',
				'hover'          => 'tabs',
				'mobile_options' => true,
			),
			'list_padding'            => array(
				'label'           => esc_html__( 'Padding', 'dsm-supreme-modules-for-divi' ),
				'type'            => 'custom_padding',
				'mobile_options'  => true,
				'hover'           => 'tabs',
				'option_category' => 'layout',
				'description'     => esc_html__( 'Adjust padding to specific values, or leave blank to use the default padding.', 'dsm-supreme-modules-for-divi' ),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'list',
				'allowed_units'   => array( '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
			),
			'text_indent'             => array(
				'label'            => esc_html__( 'Text Indent', 'dsm-supreme-modules-for-divi' ),
				'description'      => esc_html__( 'Here you can add padding between the icons and the text.', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'range',
				'option_category'  => 'font_option',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'text',
				'default'          => '5px',
				'default_unit'     => 'px',
				'default_on_front' => '5px',
				'allowed_units'    => array( '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
				'range_settings'   => array(
					'min'  => '1',
					'max'  => '100',
					'step' => '1',
				),
				'mobile_options'   => true,
				'responsive'       => true,
				'hover'            => 'tabs',
			),
			'image_background_color'  => array(
				'label'           => esc_html__( 'Image Background Color', 'dsm-supreme-modules-for-divi' ),
				'type'            => 'color-alpha',
				'description'     => esc_html__( 'Here you can define a custom background color for your image.', 'dsm-supreme-modules-for-divi' ),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'image_settings',
				'hover'           => 'tabs',
				'mobile_options'  => true,
				'depends_show_if' => 'on',
			),
			'image_padding'           => array(
				'label'           => esc_html__( 'Image Padding', 'dsm-supreme-modules-for-divi' ),
				'description'     => esc_html__( 'Here you can define a custom padding size for the image.', 'dsm-supreme-modules-for-divi' ),
				'type'            => 'range',
				'option_category' => 'font_option',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'image_settings',
				'default_unit'    => 'px',
				'allowed_units'   => array( '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
				'range_settings'  => array(
					'min'  => '1',
					'max'  => '30',
					'step' => '1',
				),
				'mobile_options'  => true,
				'responsive'      => true,
				'hover'           => 'tabs',
				'depends_show_if' => 'on',
			),
			'image_max_width'         => array(
				'label'            => esc_html__( 'Image Width', 'dsm-supreme-modules-for-divi' ),
				'description'      => esc_html__( 'Adjust the width of the image.', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'range',
				'option_category'  => 'layout',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'image_settings',
				'mobile_options'   => true,
				'validate_unit'    => true,
				'depends_show_if'  => 'off',
				'allowed_units'    => array( '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
				'default'          => '24px',
				'default_unit'     => 'px',
				'default_on_front' => '',
				'allow_empty'      => true,
				'range_settings'   => array(
					'min'  => '0',
					'max'  => '200',
					'step' => '1',
				),
				'responsive'       => true,
				'depends_show_if'  => 'on',
			),
		);
	}

	public function get_transition_fields_css_props() {
		$fields               = parent::get_transition_fields_css_props();
		$fields['icon_color'] = array(
			'color' => '%%order_class%% .dsm_icon_list_child .dsm_icon_list_icon',
		);

		$fields['icon_background_color'] = array(
			'background-color' => '%%order_class%% .dsm_icon_list_child .dsm_icon_list_icon',
		);

		$fields['icon_font_size'] = array(
			'font-size' => '%%order_class%% .dsm_icon_list_child .dsm_icon_list_icon',
		);

		$fields['icon_padding'] = array(
			'padding' => '%%order_class%% .dsm_icon_list_child .dsm_icon_list_icon',
		);

		$fields['text_indent'] = array(
			'padding' => '%%order_class%% .dsm_icon_list_child .dsm_icon_list_text',
		);

		$fields['list_space_between'] = array(
			'margin-bottom' => '%%order_class%% .dsm_icon_list_items .dsm_icon_list_child:not(:last-child)',
		);

		$fields['list_background'] = array(
			'background-color' => '%%order_class%% .dsm_icon_list_items .dsm_icon_list_child',
		);

		$fields['list_padding'] = array(
			'padding' => '%%order_class%% .dsm_icon_list_items .dsm_icon_list_child',
		);

		$fields['image_background_color'] = array(
			'background-color' => '%%order_class%% .dsm_icon_list_items .dsm_icon_list_image',
		);

		$fields['image_padding'] = array(
			'padding' => '%%order_class% .dsm_icon_list_items .dsm_icon_list_image',
		);

		return $fields;
	}

	public function render( $attrs, $content, $render_slug ) {
		$multi_view = et_pb_multi_view_options( $this );

		$icon_color_hover       = $this->get_hover_value( 'icon_color' );
		$icon_color             = $this->props['icon_color'];
		$icon_color_tablet      = $this->props['icon_color_tablet'];
		$icon_color_phone       = $this->props['icon_color_phone'];
		$icon_color_last_edited = $this->props['icon_color_last_edited'];

		$icon_background_color_hover       = $this->get_hover_value( 'icon_background_color' );
		$icon_background_color             = $this->props['icon_background_color'];
		$icon_background_color_tablet      = $this->props['icon_background_color_tablet'];
		$icon_background_color_phone       = $this->props['icon_background_color_phone'];
		$icon_background_color_last_edited = $this->props['icon_background_color_last_edited'];

		$icon_padding             = $this->props['icon_padding'];
		$icon_padding_hover       = $this->get_hover_value( 'icon_padding' );
		$icon_padding_tablet      = $this->props['icon_padding_tablet'];
		$icon_padding_phone       = $this->props['icon_padding_phone'];
		$icon_padding_last_edited = $this->props['icon_padding_last_edited'];

		$icon_gap_width             = $this->props['icon_gap_width'];
		$icon_gap_width_hover       = $this->get_hover_value( 'icon_gap_width' );
		$icon_gap_width_tablet      = $this->props['icon_gap_width_tablet'];
		$icon_gap_width_phone       = $this->props['icon_gap_width_phone'];
		$icon_gap_width_last_edited = $this->props['icon_gap_width_last_edited'];

		$icon_font_size             = $this->props['icon_font_size'];
		$icon_font_size_hover       = $this->get_hover_value( 'icon_font_size' );
		$icon_font_size_tablet      = $this->props['icon_font_size_tablet'];
		$icon_font_size_phone       = $this->props['icon_font_size_phone'];
		$icon_font_size_last_edited = $this->props['icon_font_size_last_edited'];

		$list_space_between_hover       = $this->get_hover_value( 'list_space_between' );
		$list_space_between             = $this->props['list_space_between'];
		$list_space_between_tablet      = $this->props['list_space_between_tablet'];
		$list_space_between_phone       = $this->props['list_space_between_phone'];
		$list_space_between_last_edited = $this->props['list_space_between_last_edited'];

		$list_background_hover       = $this->get_hover_value( 'list_background' );
		$list_background             = $this->props['list_background'];
		$list_background_tablet      = $this->props['list_background_tablet'];
		$list_background_phone       = $this->props['list_background_phone'];
		$list_background_last_edited = $this->props['list_background_last_edited'];

		$list_padding_hover       = $this->get_hover_value( 'list_padding' );
		$list_padding             = $this->props['list_padding'];
		$list_padding_values      = et_pb_responsive_options()->get_property_values( $this->props, 'list_padding' );
		$list_padding_size_tablet = isset( $list_padding_values['tablet'] ) ? $list_padding_values['tablet'] : '';
		$list_padding_size_phone  = isset( $list_padding_values['phone'] ) ? $list_padding_values['phone'] : '';

		$list_layout = $this->props['list_layout'];

		$text_indent             = $this->props['text_indent'];
		$text_indent_hover       = $this->get_hover_value( 'text_indent' );
		$text_indent_tablet      = $this->props['text_indent_tablet'];
		$text_indent_phone       = $this->props['text_indent_phone'];
		$text_indent_last_edited = $this->props['text_indent_last_edited'];

		$image_background_color_hover       = $this->get_hover_value( 'image_background_color' );
		$image_background_color             = $this->props['image_background_color'];
		$image_background_color_tablet      = $this->props['image_background_color_tablet'];
		$image_background_color_phone       = $this->props['image_background_color_phone'];
		$image_background_color_last_edited = $this->props['image_background_color_last_edited'];

		$image_padding             = $this->props['image_padding'];
		$image_padding_hover       = $this->get_hover_value( 'image_padding' );
		$image_padding_values      = et_pb_responsive_options()->get_property_values( $this->props, 'image_padding' );
		$image_padding_tablet      = isset( $image_padding_values['tablet'] ) ? $image_padding_values['tablet'] : '';
		$image_padding_phone       = isset( $image_padding_values['phone'] ) ? $image_padding_values['phone'] : '';
		$image_padding_last_edited = $this->props['image_padding_last_edited'];

		$image_max_width             = $this->props['image_max_width'];
		$image_max_width_tablet      = $this->props['image_max_width_tablet'];
		$image_max_width_phone       = $this->props['image_max_width_phone'];
		$image_max_width_last_edited = $this->props['image_max_width_last_edited'];

		$list_alignment             = $this->props['list_alignment'];
		$list_alignment_tablet      = $this->props['list_alignment_tablet'];
		$list_alignment_phone       = $this->props['list_alignment_phone'];
		$list_alignment_last_edited = $this->props['list_alignment_last_edited'];

		$list_direction             = $this->props['list_direction'];
		$list_direction_tablet      = $this->props['list_direction_tablet'];
		$list_direction_phone       = $this->props['list_direction_phone'];
		$list_direction_last_edited = $this->props['list_direction_last_edited'];

		$list_vertical_alignment             = $this->props['list_vertical_alignment'];
		$list_vertical_alignment_tablet      = $this->props['list_vertical_alignment_tablet'];
		$list_vertical_alignment_phone       = $this->props['list_vertical_alignment_phone'];
		$list_vertical_alignment_last_edited = $this->props['list_vertical_alignment_last_edited'];

		$icon_selector = '%%order_class%% .dsm_icon_list_items .dsm_icon_list_icon';
		$text_selector = '%%order_class%% .dsm_icon_list_items .dsm_icon_list_text';

		$font_size_responsive_active = et_pb_get_responsive_status( $icon_font_size_last_edited );

		$font_size_values = array(
			'desktop' => $icon_font_size,
			'tablet'  => $font_size_responsive_active ? $icon_font_size_tablet : '',
			'phone'   => $font_size_responsive_active ? $icon_font_size_phone : '',
		);

		et_pb_responsive_options()->generate_responsive_css( $font_size_values, $icon_selector, 'font-size', $render_slug );

		if ( '' !== $icon_padding ) {
			$icon_padding_responsive_active = et_pb_get_responsive_status( $icon_padding_last_edited );

			$icon_padding_values = array(
				'desktop' => $icon_padding,
				'tablet'  => $icon_padding_responsive_active ? $icon_padding_tablet : '',
				'phone'   => $icon_padding_responsive_active ? $icon_padding_phone : '',
			);

			et_pb_responsive_options()->generate_responsive_css( $icon_padding_values, $icon_selector, 'padding', $render_slug );

			if ( et_builder_is_hover_enabled( 'icon_padding', $this->props ) ) {
				ET_Builder_Element::set_style(
					$render_slug,
					array(
						'selector'    => $this->add_hover_to_order_class( $icon_selector ),
						'declaration' => sprintf(
							'padding: %1$s;',
							esc_html( $icon_padding_hover )
						),
					)
				);
			}
		}

		if ( et_builder_is_hover_enabled( 'icon_gap_width', $this->props ) ) {
			$icon_style_hover = sprintf( 'color: %1$s;', esc_attr( $icon_gap_width_hover ) );
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => $this->add_hover_to_order_class( $icon_selector ),
					'declaration' => $icon_style_hover,
				)
			);
		}

		if ( '' !== $icon_gap_width ) {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => $icon_selector,
					'declaration' => sprintf( 'width: %1$s;', esc_attr( $icon_gap_width ) ),
				)
			);
		}

		if ( '' !== $icon_gap_width_tablet ) {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => $icon_selector,
					'declaration' => '' !== $icon_gap_width_tablet ? sprintf( 'width: %1$s;', esc_attr( $icon_gap_width_tablet ) ) : '',
					'media_query' => ET_Builder_Element::get_media_query( 'max_width_980' ),
				)
			);
		}

		if ( '' !== $icon_gap_width_phone ) {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => $icon_selector,
					'declaration' => '' !== $icon_gap_width_phone ? sprintf( 'width: %1$s;', esc_attr( $icon_gap_width_phone ) ) : '',
					'media_query' => ET_Builder_Element::get_media_query( 'max_width_767' ),
				)
			);
		}

		$icon_style        = sprintf( 'color: %1$s;', esc_attr( $icon_color ) );
		$icon_tablet_style = '' !== $icon_color_tablet ? sprintf( 'color: %1$s;', esc_attr( $icon_color_tablet ) ) : '';
		$icon_phone_style  = '' !== $icon_color_phone ? sprintf( 'color: %1$s;', esc_attr( $icon_color_phone ) ) : '';
		$icon_style_hover  = '';

		if ( et_builder_is_hover_enabled( 'icon_color', $this->props ) ) {
			$icon_style_hover = sprintf( 'color: %1$s;', esc_attr( $icon_color_hover ) );
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => $this->add_hover_to_order_class( $icon_selector ),
					'declaration' => $icon_style_hover,
				)
			);
		}

		if ( '' !== $icon_color ) {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => $icon_selector,
					'declaration' => $icon_style,
				)
			);
		}

		if ( '' !== $icon_color_tablet ) {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => $icon_selector,
					'declaration' => $icon_tablet_style,
					'media_query' => ET_Builder_Element::get_media_query( 'max_width_980' ),
				)
			);
		}

		if ( '' !== $icon_color_phone ) {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => $icon_selector,
					'declaration' => $icon_phone_style,
					'media_query' => ET_Builder_Element::get_media_query( 'max_width_767' ),
				)
			);
		}

		$icon_background_style        = sprintf( 'background-color: %1$s;', esc_attr( $icon_background_color ) );
		$icon_background_tablet_style = '' !== $icon_background_color_tablet ? sprintf( 'background-color: %1$s;', esc_attr( $icon_background_color_tablet ) ) : '';
		$icon_background_phone_style  = '' !== $icon_background_color_phone ? sprintf( 'background-color: %1$s;', esc_attr( $icon_background_color_phone ) ) : '';
		$icon_background_style_hover  = '';

		if ( et_builder_is_hover_enabled( 'icon_background_color', $this->props ) ) {
			$icon_background_style_hover = sprintf( 'background-color: %1$s;', esc_attr( $icon_background_color_hover ) );
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => $this->add_hover_to_order_class( $icon_selector ),
					'declaration' => $icon_background_style_hover,
				)
			);
		}

		if ( '' !== $icon_background_color ) {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => $icon_selector,
					'declaration' => $icon_background_style,
				)
			);
		}

		if ( '' !== $icon_background_tablet_style ) {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => $icon_selector,
					'declaration' => $icon_background_tablet_style,
					'media_query' => ET_Builder_Element::get_media_query( 'max_width_980' ),
				)
			);
		}

		if ( '' !== $icon_background_phone_style ) {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => $icon_selector,
					'declaration' => $icon_background_phone_style,
					'media_query' => ET_Builder_Element::get_media_query( 'max_width_767' ),
				)
			);
		}

		if ( 'ltr' === $list_direction ) {
			if ( '5px' !== $text_indent ) {
				$text_indent_responsive_active = et_pb_get_responsive_status( $text_indent_last_edited );

				$text_indent_values = array(
					'desktop' => $text_indent,
					'tablet'  => $text_indent_responsive_active ? $text_indent_tablet : '',
					'phone'   => $text_indent_responsive_active ? $text_indent_phone : '',
				);

				et_pb_responsive_options()->generate_responsive_css( $text_indent_values, '%%order_class%% .dsm_icon_list_ltr_direction .dsm_icon_list_child .dsm_icon_list_text', 'padding-left', $render_slug );
			}

			if ( et_builder_is_hover_enabled( 'text_indent', $this->props ) ) {
				ET_Builder_Element::set_style(
					$render_slug,
					array(
						'selector'    => $this->add_hover_to_order_class( '%%order_class%% .dsm_icon_list_ltr_direction .dsm_icon_list_child .dsm_icon_list_text' ),
						'declaration' => sprintf(
							'padding-left: %1$s;',
							esc_html( $text_indent_hover )
						),
					)
				);
			}
		} else {
			if ( '5px' !== $text_indent ) {
				$text_indent_responsive_active = et_pb_get_responsive_status( $text_indent_last_edited );

				$text_indent_values = array(
					'desktop' => $text_indent,
					'tablet'  => $text_indent_responsive_active ? $text_indent_tablet : '',
					'phone'   => $text_indent_responsive_active ? $text_indent_phone : '',
				);

				et_pb_responsive_options()->generate_responsive_css( $text_indent_values, '%%order_class%% .dsm_icon_list_right_direction .dsm_icon_list_child .dsm_icon_list_text', 'padding-right', $render_slug );
			}

			if ( et_builder_is_hover_enabled( 'text_indent', $this->props ) ) {
				ET_Builder_Element::set_style(
					$render_slug,
					array(
						'selector'    => $this->add_hover_to_order_class( '%%order_class%% .dsm_icon_list_right_direction .dsm_icon_list_child .dsm_icon_list_text' ),
						'declaration' => sprintf(
							'padding-right: %1$s;',
							esc_html( $text_indent_hover )
						),
					)
				);
			}
		}

		if ( 'horizontal' === $list_layout ) {
			if ( 'center' === $list_alignment ) {
				$list_alignment = 'center';
			} elseif ( 'flex-end' === $list_alignment ) {
				$list_alignment = 'right';
			} else {
				$list_alignment = 'left';
			}

			if ( 'center' === $list_alignment_tablet ) {
				$list_alignment_tablet = 'center';
			} elseif ( 'flex-end' === $list_alignment_tablet ) {
				$list_alignment_tablet = 'right';
			} else {
				$list_alignment_tablet = 'left';
			}

			if ( 'center' === $list_alignment_phone ) {
				$list_alignment_phone = 'center';
			} elseif ( 'flex-end' === $list_alignment_phone ) {
				$list_alignment_phone = 'right';
			} else {
				$list_alignment_phone = 'left';
			}
		}

		$list_alignment_layout_tablet_values = 'vertical' === $list_layout ? sprintf( 'justify-content: %1$s;', esc_attr( $list_alignment_tablet ) ) : sprintf( 'text-align: %1$s;', esc_attr( $list_alignment_tablet ) );
		$list_alignment_layout_phone_values  = 'vertical' === $list_layout ? sprintf( 'justify-content: %1$s;', esc_attr( $list_alignment_phone ) ) : sprintf( 'text-align: %1$s;', esc_attr( $list_alignment_phone ) );

		$list_alignment_layout_style    = 'vertical' === $list_layout ? sprintf( 'justify-content: %1$s;', esc_attr( $list_alignment ) ) : sprintf( 'text-align: %1$s;', esc_attr( $list_alignment ) );
		$list_alignment_style           = sprintf( 'justify-content: %1$s;', esc_attr( $list_alignment ) );
		$list_alignment_tablet_style    = '' !== $list_alignment_tablet ? esc_attr( $list_alignment_layout_tablet_values ) : '';
		$list_alignment_phone_style     = '' !== $list_alignment_phone ? esc_attr( $list_alignment_layout_phone_values ) : '';
		$list_alignment_layout_selector = 'vertical' === $list_layout ? '%%order_class%% .dsm_icon_list_items .dsm_icon_list_child, %%order_class%% .dsm_icon_list_items .dsm_icon_list_child a' : '%%order_class%% .dsm_icon_list_items.dsm_icon_list_layout_horizontal';

		if ( 'flex-start' !== $list_alignment ) {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => $list_alignment_layout_selector,
					'declaration' => $list_alignment_layout_style,
				)
			);
		}

		ET_Builder_Element::set_style(
			$render_slug,
			array(
				'selector'    => $list_alignment_layout_selector,
				'declaration' => $list_alignment_tablet_style,
				'media_query' => ET_Builder_Element::get_media_query( 'max_width_980' ),
			)
		);

		ET_Builder_Element::set_style(
			$render_slug,
			array(
				'selector'    => $list_alignment_layout_selector,
				'declaration' => $list_alignment_phone_style,
				'media_query' => ET_Builder_Element::get_media_query( 'max_width_767' ),
			)
		);

		$list_vertical_alignment_style        = sprintf( 'align-items: %1$s;', esc_attr( $list_vertical_alignment ) );
		$list_vertical_alignment_tablet_style = '' !== $list_vertical_alignment_tablet ? sprintf( 'align-items: %1$s;', esc_attr( $list_vertical_alignment_tablet ) ) : '';
		$list_vertical_alignment_phone_style  = '' !== $list_vertical_alignment_phone ? sprintf( 'align-items: %1$s;', esc_attr( $list_vertical_alignment_phone ) ) : '';

		if ( 'center' !== $list_vertical_alignment ) {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%% .dsm_icon_list_items .dsm_icon_list_child, %%order_class%% .dsm_icon_list_items .dsm_icon_list_child a',
					'declaration' => $list_vertical_alignment_style,
				)
			);
		}

		ET_Builder_Element::set_style(
			$render_slug,
			array(
				'selector'    => '%%order_class%% .dsm_icon_list_items .dsm_icon_list_child, %%order_class%% .dsm_icon_list_items .dsm_icon_list_child a',
				'declaration' => $list_vertical_alignment_tablet_style,
				'media_query' => ET_Builder_Element::get_media_query( 'max_width_980' ),
			)
		);

		ET_Builder_Element::set_style(
			$render_slug,
			array(
				'selector'    => '%%order_class%% .dsm_icon_list_items .dsm_icon_list_child, %%order_class%% .dsm_icon_list_items .dsm_icon_list_child a',
				'declaration' => $list_vertical_alignment_phone_style,
				'media_query' => ET_Builder_Element::get_media_query( 'max_width_767' ),
			)
		);

		$list_direction_style        = sprintf( 'direction: %1$s;', esc_attr( $list_direction ) );
		$list_direction_tablet_style = '' !== $list_direction_tablet ? sprintf( 'direction: %1$s;', esc_attr( $list_direction_tablet ) ) : '';
		$list_direction_phone_style  = '' !== $list_direction_phone ? sprintf( 'direction: %1$s;', esc_attr( $list_direction_phone ) ) : '';

		if ( 'ltr' !== $list_direction ) {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%% .dsm_icon_list_items .dsm_icon_list_child',
					'declaration' => $list_direction_style,
				)
			);
		}

		ET_Builder_Element::set_style(
			$render_slug,
			array(
				'selector'    => '%%order_class%% .dsm_icon_list_items .dsm_icon_list_child',
				'declaration' => $list_direction_tablet_style,
				'media_query' => ET_Builder_Element::get_media_query( 'max_width_980' ),
			)
		);

		ET_Builder_Element::set_style(
			$render_slug,
			array(
				'selector'    => '%%order_class%% .dsm_icon_list_items .dsm_icon_list_child',
				'declaration' => $list_direction_phone_style,
				'media_query' => ET_Builder_Element::get_media_query( 'max_width_767' ),
			)
		);

		if ( '' !== $list_space_between ) {
			$list_space_between_responsive_active = et_pb_get_responsive_status( $list_space_between_last_edited );

			$list_space_between_values = array(
				'desktop' => $list_space_between,
				'tablet'  => $list_space_between_responsive_active ? $list_space_between_tablet : '',
				'phone'   => $list_space_between_responsive_active ? $list_space_between_phone : '',
			);

			$check_layout = 'vertical' === $list_layout ? 'margin-bottom' : 'margin-right';

			et_pb_responsive_options()->generate_responsive_css( $list_space_between_values, '%%order_class%% .dsm_icon_list_items .dsm_icon_list_child:not(:last-child)', $check_layout, $render_slug );

			if ( et_builder_is_hover_enabled( 'list_space_between', $this->props ) ) {
				ET_Builder_Element::set_style(
					$render_slug,
					array(
						'selector'    => $this->add_hover_to_order_class( '%%order_class%% .dsm_icon_list_items .dsm_icon_list_child:not(:last-child)' ),
						'declaration' => sprintf(
							'margin-bottom: %1$s;',
							esc_html( $list_space_between_hover )
						),
					)
				);
			}
		}

		if ( '' !== $list_background ) {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%% .dsm_icon_list_items .dsm_icon_list_child',
					'declaration' => sprintf(
						'background-color: %1$s;',
						esc_html( $list_background )
					),
				)
			);
		}

		if ( '' !== $list_background_tablet ) {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%% .dsm_icon_list_items .dsm_icon_list_child',
					'declaration' => sprintf(
						'background-color: %1$s;',
						esc_html( $list_background_tablet )
					),
					'media_query' => ET_Builder_Element::get_media_query( 'max_width_980' ),
				)
			);
		}

		if ( '' !== $list_background_phone ) {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%% .dsm_icon_list_items .dsm_icon_list_child',
					'declaration' => sprintf(
						'background-color: %1$s;',
						esc_html( $list_background_phone )
					),
					'media_query' => ET_Builder_Element::get_media_query( 'max_width_767' ),
				)
			);
		}

		if ( et_builder_is_hover_enabled( 'list_background', $this->props ) ) {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => $this->add_hover_to_order_class( '%%order_class%% .dsm_icon_list_items .dsm_icon_list_child' ),
					'declaration' => sprintf(
						'background-color: %1$s;',
						esc_html( $list_background_hover )
					),
				)
			);
		}

		$image_selector = '%%order_class%% .dsm_icon_list_items .dsm_icon_list_image';
		if ( '' !== $image_padding ) {
			$image_padding_responsive_active = et_pb_get_responsive_status( $image_padding_last_edited );

			$image_padding_values = array(
				'desktop' => $image_padding,
				'tablet'  => $image_padding_responsive_active ? $image_padding_tablet : '',
				'phone'   => $image_padding_responsive_active ? $image_padding_phone : '',
			);

			et_pb_responsive_options()->generate_responsive_css( $image_padding_values, $image_selector, 'padding', $render_slug );

			if ( et_builder_is_hover_enabled( 'image_padding', $this->props ) ) {
				ET_Builder_Element::set_style(
					$render_slug,
					array(
						'selector'    => $this->add_hover_to_order_class( $image_selector ),
						'declaration' => sprintf(
							'padding: %1$s;',
							esc_html( $image_padding_hover )
						),
					)
				);
			}
		}

		$image_background_style        = sprintf( 'background-color: %1$s;', esc_attr( $image_background_color ) );
		$image_background_tablet_style = '' !== $image_background_color_tablet ? sprintf( 'background-color: %1$s;', esc_attr( $image_background_color_tablet ) ) : '';
		$image_background_phone_style  = '' !== $image_background_color_phone ? sprintf( 'background-color: %1$s;', esc_attr( $image_background_color_phone ) ) : '';
		$image_background_style_hover  = '';

		if ( et_builder_is_hover_enabled( 'image_background_color', $this->props ) ) {
			$image_background_style_hover = sprintf( 'background-color: %1$s;', esc_attr( $image_background_color_hover ) );
		}

		if ( '' !== $image_background_color ) {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => $image_selector,
					'declaration' => $image_background_style,
				)
			);
		}

		ET_Builder_Element::set_style(
			$render_slug,
			array(
				'selector'    => $image_selector,
				'declaration' => $image_background_tablet_style,
				'media_query' => ET_Builder_Element::get_media_query( 'max_width_980' ),
			)
		);

		ET_Builder_Element::set_style(
			$render_slug,
			array(
				'selector'    => $image_selector,
				'declaration' => $image_background_phone_style,
				'media_query' => ET_Builder_Element::get_media_query( 'max_width_767' ),
			)
		);

		if ( '' !== $image_background_style_hover ) {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => $this->add_hover_to_order_class( $image_selector ),
					'declaration' => $image_background_style_hover,
				)
			);
		}

		if ( '26px' !== $image_max_width ) {
			$image_max_width_responsive_active = et_pb_get_responsive_status( $image_max_width_last_edited );

			$image_max_width_values = array(
				'desktop' => $image_max_width,
				'tablet'  => $image_max_width_responsive_active ? $image_max_width_tablet : '',
				'phone'   => $image_max_width_responsive_active ? $image_max_width_phone : '',
			);

			et_pb_responsive_options()->generate_responsive_css( $image_max_width_values, '%%order_class%% .dsm_icon_list_items .dsm_icon_list_image img', 'width', $render_slug );

		}

		$this->apply_custom_margin_padding(
			$render_slug,
			'list_padding',
			'padding',
			'%%order_class%% .dsm_icon_list_items .dsm_icon_list_child'
		);

		// Render module content.
		$output = sprintf(
			'<ul class="dsm_icon_list_items dsm_icon_list_%2$s_direction dsm_icon_list_layout_%3$s">%1$s</ul>',
			$this->content,
			esc_attr( $list_direction ),
			esc_attr( $list_layout )
		);

		if ( isset( get_option( 'dsm_settings_misc' )['dsm_dynamic_assets'] ) && ! empty( get_option( 'dsm_settings_misc' )['dsm_dynamic_assets'] ) && 'on' === get_option( 'dsm_settings_misc' )['dsm_dynamic_assets'] ) {
			if ( isset( get_option( 'dsm_settings_misc' )['dsm_dynamic_assets_compatibility'] ) && ! empty( get_option( 'dsm_settings_misc' )['dsm_dynamic_assets'] ) && 'on' === get_option( 'dsm_settings_misc' )['dsm_dynamic_assets_compatibility'] ) {
				wp_enqueue_style( 'dsm-icon-list', plugin_dir_url( __DIR__ ) . 'IconList/style.css', array(), DSM_VERSION, 'all' );
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

		// IconList.
		if ( ! isset( $assets_list['dsm_icon_list'] ) ) {
			$assets_list['dsm_icon_list'] = array(
				'css' => plugin_dir_url( __DIR__ ) . 'IconList/style.css',
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

new DSM_Icon_List();
