<?php

class DSM_Icon_List_Child extends ET_Builder_Module {

	public $slug            = 'dsm_icon_list_child';
	public $vb_support      = 'on';
	public $type            = 'child';
	public $child_title_var = 'text';
	public $icon_element_selector;
	public $icon_element_classname;

	protected $module_credits = array(
		'module_uri' => 'https://divisupreme.com/',
		'author'     => 'Divi Supreme',
		'author_uri' => 'https://divisupreme.com/',
	);

	public function init() {
		$this->name                        = esc_html__( 'Icon List Item', 'dsm-supreme-modules-for-divi' );
		$this->child_title_var             = 'admin_title';
		$this->child_title_fallback_var    = 'text';
		$this->advanced_setting_title_text = esc_html__( 'Icon List Item', 'dsm-supreme-modules-for-divi' );
		$this->settings_text               = esc_html__( 'Icon List Settings', 'dsm-supreme-modules-for-divi' );
		$this->icon_element_selector       = '%%order_class%%.dsm_icon_list_child .dsm_icon_list_icon';
		$this->icon_element_classname      = 'dsm_icon_list_icon';

		$this->settings_modal_toggles = array(
			'general'    => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Content', 'dsm-supreme-modules-for-divi' ),
					'tooltip'      => esc_html__( 'Tooltip', 'dsm-supreme-modules-for-divi' ),
					'link'         => esc_html__( 'Link', 'dsm-supreme-modules-for-divi' ),
				),
			),
			'advanced'   => array(
				'toggles' => array(
					'icon_settings'    => esc_html__( 'Icon', 'dsm-supreme-modules-for-divi' ),
					'image_settings'   => esc_html__( 'Image', 'dsm-supreme-modules-for-divi' ),
					'tooltip_settings' => esc_html__( 'Tooltip', 'dsm-supreme-modules-for-divi' ),
					'text'             => array(
						'title'    => esc_html__( 'Text', 'dsm-supreme-modules-for-divi' ),
						'priority' => 49,
					),
					'width'            => array(
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
			'fonts'           => array(
				'text'    => array(
					'css'               => array(
						'main'        => '%%order_class%%.dsm_icon_list_child, %%order_class%%.dsm_icon_list_child a',
						'line_height' => '%%order_class%%.dsm_icon_list_child .dsm_icon_list_text',
						'important'   => 'all',
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
					'hide_header_level' => true,
					'hide_text_align'   => true,
					'hide_text_shadow'  => false,
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'text',
				),
				'tooltip' => array(
					'label'          => esc_html__( 'Tooltip', 'dsm-supreme-modules-for-divi' ),
					'css'            => array(
						'main'      => '%%order_class%% .dsm_icon_list_child_tooltip_wrapper.tippy-tooltip',
						'important' => 'all',
					),
					'font_size'      => array(
						'default' => '13px',
					),
					'line_height'    => array(
						'default' => '1.4em',
					),
					'letter_spacing' => array(
						'default' => '0px',
					),
					'tab_slug'       => 'advanced',
					'toggle_slug'    => 'tooltip_settings',
				),
			),
			'text'            => array(
				'use_text_orientation'  => false,
				'use_background_layout' => false,
				'css'                   => array(
					'text_shadow' => '%%order_class%%.dsm_icon_list_child .dsm_icon_list_child',
				),
			),
			'borders'         => array(
				'default' => array(
					'css' => array(
						'main' => array(
							'border_radii'  => '%%order_class%%.dsm_icon_list_child:not(.tippy-popper)',
							'border_styles' => '%%order_class%%.dsm_icon_list_child:not(.tippy-popper)',
						),
					),
				),
				'icon'    => array(
					'css'          => array(
						'main' => array(
							'border_radii'  => '%%order_class%%.dsm_icon_list_child .dsm_icon_list_icon',
							'border_styles' => '%%order_class%%.dsm_icon_list_child .dsm_icon_list_icon',
						),
					),
					'label_prefix' => esc_html__( 'Icon', 'dsm-supreme-modules-for-divi' ),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'icon_settings',
				),
				'image'   => array(
					'css'          => array(
						'main' => array(
							'border_radii'  => '%%order_class%%.dsm_icon_list_child .dsm_icon_list_image',
							'border_styles' => '%%order_class%%.dsm_icon_list_child .dsm_icon_list_image',
						),
					),
					'label_prefix' => esc_html__( 'Image', 'dsm-supreme-modules-for-divi' ),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'image_settings',

				),
			),
			'box_shadow'      => array(
				'default' => array(),
				'icon'    => array(
					'label'             => esc_html__( 'Icon Box Shadow', 'dsm-supreme-modules-for-divi' ),
					'option_category'   => 'layout',
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'icon_settings',

					'css'               => array(
						'main' => '%%order_class%%.dsm_icon_list_child .dsm_icon_list_icon',
					),
					'default_on_fronts' => array(
						'color'    => '',
						'position' => '',
					),

				),
				'image'   => array(
					'label'             => esc_html__( 'Image Box Shadow', 'dsm-supreme-modules-for-divi' ),
					'option_category'   => 'layout',
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'image_settings',

					'css'               => array(
						'main' => '%%order_class%%.dsm_icon_list_child .dsm_icon_list_image',
					),
					'default_on_fronts' => array(
						'color'    => '',
						'position' => '',
					),

				),
			),
			'margin_padding'  => array(
				'css' => array(
					'main'      => '%%order_class%%:not(.tippy-popper)',
					'important' => 'all',
				),
			),
			'button'          => false,
			'link_options'    => false,
			'position_fields' => false,
		);
	}

	public function get_fields() {
		$et_accent_color = et_builder_accent_color();
		return array(
			'admin_title'              => array(
				'label'       => esc_html__( 'Admin Label', 'dsm-supreme-modules-for-divi' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the icon list item in the builder for easy identification.', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug' => 'admin_label',
			),
			'text'                     => array(
				'label'            => esc_html__( 'Text', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'toggle_slug'      => 'main_content',
				'default'          => 'Icon List Item',
				'default_on_front' => 'Icon List Item',
				'dynamic_content'  => 'text',
			),
			'use_icon'                 => array(
				'label'            => esc_html__( 'Use Icon', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'basic_option',
				'options'          => array(
					'off' => esc_html__( 'No', 'dsm-supreme-modules-for-divi' ),
					'on'  => esc_html__( 'Yes', 'dsm-supreme-modules-for-divi' ),
				),
				'toggle_slug'      => 'main_content',
				'affects'          => array(
					'font_icon',
					'icon_font_size',
					'icon_color',
					'icon_background_color',
					'icon_padding',
					'image',
					'alt',
					'image_max_width',
					'image_background_color',
					'image_padding',
				),
				'description'      => esc_html__( 'Here you can choose whether icon set below should be used.', 'dsm-supreme-modules-for-divi' ),
				'default_on_front' => 'on',
				'default'          => 'on',
			),
			'font_icon'                => array(
				'label'            => esc_html__( 'Icon', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'select_icon',
				'option_category'  => 'basic_option',
				'class'            => array( 'et-pb-font-icon' ),
				'toggle_slug'      => 'main_content',
				'description'      => esc_html__( 'Choose an icon to display with your text.', 'dsm-supreme-modules-for-divi' ),
				'depends_show_if'  => 'on',
				// 'mobile_options'      => true,
				'hover'            => 'tabs',
				'default'          => '&#x50;||divi||400',
				'default_on_front' => '&#x50;||divi||400',
			),
			'icon_color'               => array(
				'default'         => $et_accent_color,
				'label'           => esc_html__( 'Icon Color', 'dsm-supreme-modules-for-divi' ),
				'type'            => 'color-alpha',
				'description'     => esc_html__( 'Here you can define a custom color for your icon.', 'dsm-supreme-modules-for-divi' ),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'icon_settings',
				'depends_show_if' => 'on',
				'hover'           => 'tabs',
				'mobile_options'  => true,
			),
			'icon_background_color'    => array(
				'label'           => esc_html__( 'Icon Background Color', 'dsm-supreme-modules-for-divi' ),
				'type'            => 'color-alpha',
				'description'     => esc_html__( 'Here you can define a custom background color for your icon.', 'dsm-supreme-modules-for-divi' ),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'icon_settings',
				'hover'           => 'tabs',
				'mobile_options'  => true,
				'depends_show_if' => 'on',
			),
			'icon_padding'             => array(
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
				'depends_show_if' => 'on',
			),
			'icon_font_size'           => array(
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
				'depends_show_if'  => 'on',
				'responsive'       => true,
				'hover'            => 'tabs',
			),
			'image'                    => array(
				'label'              => esc_html__( 'Image', 'dsm-supreme-modules-for-divi' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => esc_attr__( 'Upload an image', 'dsm-supreme-modules-for-divi' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'dsm-supreme-modules-for-divi' ),
				'update_text'        => esc_attr__( 'Set As Image', 'dsm-supreme-modules-for-divi' ),
				'depends_show_if'    => 'off',
				'description'        => esc_html__( 'Upload an image to display with your text', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'        => 'main_content',
				'mobile_options'     => true,
				'hover'              => 'tabs',
				'dynamic_content'    => 'image',
			),
			'alt'                      => array(
				'label'           => esc_html__( 'Image Alt Text', 'dsm-supreme-modules-for-divi' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Define the HTML ALT text for your image here.', 'dsm-supreme-modules-for-divi' ),
				'depends_show_if' => 'off',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'attributes',
				'dynamic_content' => 'text',
			),
			'image_background_color'   => array(
				'label'           => esc_html__( 'Image Background Color', 'dsm-supreme-modules-for-divi' ),
				'type'            => 'color-alpha',
				'description'     => esc_html__( 'Here you can define a custom background color for your image.', 'dsm-supreme-modules-for-divi' ),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'image_settings',
				'hover'           => 'tabs',
				'mobile_options'  => true,
				'depends_show_if' => 'off',
			),
			'image_padding'            => array(
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
				'depends_show_if' => 'off',
			),
			'use_tooltip'              => array(
				'label'            => esc_html__( 'Use Tooltip', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'basic_option',
				'options'          => array(
					'off' => esc_html__( 'No', 'dsm-supreme-modules-for-divi' ),
					'on'  => esc_html__( 'Yes', 'dsm-supreme-modules-for-divi' ),
				),
				'toggle_slug'      => 'tooltip',
				'default_on_front' => 'off',
				'default'          => 'off',
			),
			'content'                  => array(
				'label'           => esc_html__( 'Content', 'dsm-supreme-modules-for-divi' ),
				'type'            => 'tiny_mce',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Tooltip Content entered here will show up.', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'     => 'tooltip',
				'show_if'         => array(
					'use_tooltip' => 'on',
				),
				'dynamic_content' => 'text',
			),
			'tooltip_placement'        => array(
				'label'            => esc_html__( 'Placement', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'select',
				'option_category'  => 'layout',
				'options'          => array(
					'top'    => esc_html__( 'Top', 'dsm-supreme-modules-for-divi' ),
					'right'  => esc_html__( 'Right', 'dsm-supreme-modules-for-divi' ),
					'bottom' => esc_html__( 'Bottom', 'dsm-supreme-modules-for-divi' ),
					'left'   => esc_html__( 'Left', 'dsm-supreme-modules-for-divi' ),
				),
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'tooltip_settings',
				'description'      => esc_html__( 'Here you can choose the placement of the tooltip.', 'dsm-supreme-modules-for-divi' ),
				'default_on_front' => 'right',
				'default'          => 'right',
				'mobile_options'   => false,
				'show_if'          => array(
					'use_tooltip' => 'on',
				),
			),
			'tooltip_background_color' => array(
				'default'        => 'rgba(34,34,34,0.9)',
				'label'          => esc_html__( 'Background Color', 'dsm-supreme-modules-for-divi' ),
				'type'           => 'color-alpha',
				'description'    => esc_html__( 'Here you can define a custom background color for your tooltip.', 'dsm-supreme-modules-for-divi' ),
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'tooltip_settings',
				'mobile_options' => true,
			),
			'tooltip_padding'          => array(
				'label'           => esc_html__( 'Tooltip Padding', 'dsm-supreme-modules-for-divi' ),
				'description'     => esc_html__( 'Here you can define a custom padding size for the tooltip.', 'dsm-supreme-modules-for-divi' ),
				'type'            => 'custom_padding',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'tooltip_settings',
				'default_unit'    => 'px',
				'allowed_units'   => array( '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
				'range_settings'  => array(
					'min'  => '1',
					'max'  => '50',
					'step' => '1',
				),
				'default'         => '8px|10px|8px|10px',
				'mobile_options'  => true,
				'responsive'      => true,
			),
			'tooltip_max_width'        => array(
				'label'            => esc_html__( 'Tooltip Max Width', 'dsm-supreme-modules-for-divi' ),
				'description'      => esc_html__( 'Adjust the width of the tooltip.', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'range',
				'option_category'  => 'layout',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'tooltip_settings',
				'mobile_options'   => true,
				'validate_unit'    => true,
				'default_on_front' => '180px',
				'default'          => '180px',
				'default_unit'     => 'px',
				'allowed_units'    => array( '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
				'allow_empty'      => true,
				'range_settings'   => array(
					'min'  => '0',
					'max'  => '320',
					'step' => '1',
				),
				'responsive'       => true,
				'show_if'          => array(
					'use_tooltip' => 'on',
				),
			),
			'url'                      => array(
				'label'           => esc_html__( 'Link URL', 'dsm-supreme-modules-for-divi' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'If you would like to make your Icon List a link, input your destination URL here.', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'     => 'link_options',
				'dynamic_content' => 'url',
			),
			'url_new_window'           => array(
				'label'            => esc_html__( 'Title Link Target', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'off' => esc_html__( 'In The Same Window', 'dsm-supreme-modules-for-divi' ),
					'on'  => esc_html__( 'In The New Tab', 'dsm-supreme-modules-for-divi' ),
				),
				'toggle_slug'      => 'link_options',
				'description'      => esc_html__( 'Here you can choose whether or not your link opens in a new window', 'dsm-supreme-modules-for-divi' ),
				'default_on_front' => 'off',
			),
			/*
			'text_indent'              => array(
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
			),*/
			'image_max_width'          => array(
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
			),
		);
	}

	public function get_transition_fields_css_props() {
		$fields               = parent::get_transition_fields_css_props();
		$fields['icon_color'] = array(
			'color' => '%%order_class%%.dsm_icon_list_child .dsm_icon_list_icon',
		);

		$fields['icon_background_color'] = array(
			'background-color' => '%%order_class%%.dsm_icon_list_child .dsm_icon_list_icon',
		);

		$fields['icon_font_size'] = array(
			'font-size' => '%%order_class%%.dsm_icon_list_child .dsm_icon_list_icon',
		);

		$fields['icon_padding'] = array(
			'padding' => '%%order_class%%.dsm_icon_list_child .dsm_icon_list_icon',
		);

		/*
		$fields['text_indent'] = array(
			'padding-left' => '%%order_class%%.dsm_icon_list_child .dsm_icon_list_text',
		);*/

		$fields['image_background_color'] = array(
			'background-color' => '%%order_class%%.dsm_icon_list_child .dsm_icon_list_image',
		);

		$fields['image_padding'] = array(
			'padding' => '%%order_class%%.dsm_icon_list_child .dsm_icon_list_image',
		);

		return $fields;
	}

	public function render( $attrs, $content, $render_slug ) {
		$text           = $this->props['text'];
		$image          = $this->props['image'];
		$alt            = $this->props['alt'];
		$url            = $this->props['url'];
		$url_new_window = $this->props['url_new_window'];

		$multi_view                 = et_pb_multi_view_options( $this );
		$use_icon                   = $this->props['use_icon'];
		$font_icon                  = $this->props['font_icon'];
		$icon_font_size             = $this->props['icon_font_size'];
		$icon_font_size_hover       = $this->get_hover_value( 'icon_font_size' );
		$icon_font_size_tablet      = $this->props['icon_font_size_tablet'];
		$icon_font_size_phone       = $this->props['icon_font_size_phone'];
		$icon_font_size_last_edited = $this->props['icon_font_size_last_edited'];

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
		$icon_padding_values      = et_pb_responsive_options()->get_property_values( $this->props, 'icon_padding' );
		$icon_padding_tablet      = isset( $icon_padding_values['tablet'] ) ? $icon_padding_values['tablet'] : '';
		$icon_padding_phone       = isset( $icon_padding_values['phone'] ) ? $icon_padding_values['phone'] : '';
		$icon_padding_last_edited = $this->props['icon_padding_last_edited'];

		/*
		$text_indent             = $this->props['text_indent'];
		$text_indent_hover       = $this->get_hover_value( 'text_indent' );
		$text_indent_tablet      = $this->props['text_indent_tablet'];
		$text_indent_phone       = $this->props['text_indent_phone'];
		$text_indent_last_edited = $this->props['text_indent_last_edited'];
		*/

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

		$use_tooltip                     = $this->props['use_tooltip'];
		$tooltip_placement               = $this->props['tooltip_placement'];
		$tooltip_background_color        = $this->props['tooltip_background_color'];
		$tooltip_background_color_hover  = $this->get_hover_value( 'tooltip_background_color' );
		$tooltip_background_color_values = et_pb_responsive_options()->get_property_values( $this->props, 'tooltip_background_color' );
		$tooltip_background_color_tablet = isset( $tooltip_background_color_values['tablet'] ) ? $tooltip_background_color_values['tablet'] : '';
		$tooltip_background_color_phone  = isset( $tooltip_background_color_values['phone'] ) ? $tooltip_background_color_values['phone'] : '';

		$tooltip_padding             = $this->props['tooltip_padding'];
		$tooltip_padding_hover       = $this->get_hover_value( 'tooltip_padding' );
		$tooltip_padding_values      = et_pb_responsive_options()->get_property_values( $this->props, 'tooltip_padding' );
		$tooltip_padding_tablet      = isset( $tooltip_padding_values['tablet'] ) ? $tooltip_padding_values['tablet'] : '';
		$tooltip_padding_phone       = isset( $tooltip_padding_values['phone'] ) ? $tooltip_padding_values['phone'] : '';
		$tooltip_padding_last_edited = $this->props['tooltip_padding_last_edited'];
		$tooltip_max_width           = $this->props['tooltip_max_width'];
		$tooltip_max_width_hover     = $this->get_hover_value( 'tooltip_max_width' );
		$tooltip_max_width_values    = et_pb_responsive_options()->get_property_values( $this->props, 'tooltip_max_width' );
		$tooltip_max_width_tablet    = isset( $tooltip_max_width_values['tablet'] ) ? $tooltip_max_width_values['tablet'] : '';
		$tooltip_max_width_phone     = isset( $tooltip_max_width_values['phone'] ) ? $tooltip_max_width_values['phone'] : '';

		$tooltip_background_color_selector = '%%order_class%% .dsm_icon_list_child_tooltip_wrapper.tippy-tooltip';
		$tooltip_selector                  = '%%order_class%% .dsm_icon_list_child_tooltip_wrapper.tippy-tooltip .tippy-content';
		$tooltip_padding_selector          = '%%order_class%% .dsm_icon_list_child_tooltip_wrapper.tippy-tooltip .tippy-content';

		$image_pathinfo = pathinfo( $image );
		$is_image_svg   = isset( $image_pathinfo['extension'] ) ? 'svg' === $image_pathinfo['extension'] : false;

		if ( '' !== $image_max_width_tablet || '' !== $image_max_width_phone || '' !== $image_max_width || $is_image_svg ) {
			$is_size_px = false;

			// If size is given in px, we want to override parent width.
			if (
				false !== strpos( $image_max_width, 'px' ) ||
				false !== strpos( $image_max_width_tablet, 'px' ) ||
				false !== strpos( $image_max_width_phone, 'px' )
			) {
				$is_size_px = true;
			}
			// SVG image overwrite. SVG image needs its value to be explicit.

			/*
			if ( '' === $image_max_width && $is_image_svg ) {
				$image_max_width = '100%';
			}*/

			// Image max width selector.
			$image_max_width_selectors       = array();
			$image_max_width_reset_selectors = array();
			$image_max_width_reset_values    = array();

			$image_max_width_selector = '.dsm_icon_list .dsm_icon_list_items %%order_class%%.dsm_icon_list_child .dsm_icon_list_image img';

			// Add image max width desktop selector if user sets different image/icon placement setting.
			if ( ! empty( $image_max_width_selectors ) ) {
				$image_max_width_selectors['desktop'] = $image_max_width_selector;
			}

			$image_max_width_property = ( $is_image_svg || $is_size_px ) ? 'width' : 'max-width';

			$image_max_width_responsive_active = et_pb_get_responsive_status( $image_max_width_last_edited );

			$image_max_width_values = array(
				'desktop' => $image_max_width,
				'tablet'  => $image_max_width_responsive_active ? $image_max_width_tablet : '',
				'phone'   => $image_max_width_responsive_active ? $image_max_width_phone : '',
			);

			$main_image_max_width_selector = $image_max_width_selector;

			// Overwrite image max width if there are image max width selectors for different devices.
			if ( ! empty( $image_max_width_selectors ) ) {
				$main_image_max_width_selector = $image_max_width_selectors;

				if ( ! empty( $image_max_width_selectors['tablet'] ) && empty( $image_max_width_values['tablet'] ) ) {
					$image_max_width_values['tablet'] = $image_max_width_responsive_active ? esc_attr( et_pb_responsive_options()->get_any_value( $this->props, 'image_max_width_tablet', '24px', true ) ) : esc_attr( $image_max_width );
				}

				if ( ! empty( $image_max_width_selectors['phone'] ) && empty( $image_max_width_values['phone'] ) ) {
					$image_max_width_values['phone'] = $image_max_width_responsive_active ? esc_attr( et_pb_responsive_options()->get_any_value( $this->props, 'image_max_width_phone', '24px', true ) ) : esc_attr( $image_max_width );
				}
			}

			et_pb_responsive_options()->generate_responsive_css( $image_max_width_values, $main_image_max_width_selector, $image_max_width_property, $render_slug );

			// Reset custom image max width styles.
			if ( ! empty( $image_max_width_selectors ) && ! empty( $image_max_width_reset_selectors ) ) {
				et_pb_responsive_options()->generate_responsive_css( $image_max_width_reset_values, $image_max_width_reset_selectors, $image_max_width_property, $render_slug, '', 'input' );
			}
		}

		$icon_selector = '%%order_class%%.dsm_icon_list_child .dsm_icon_list_wrapper>.dsm_icon_list_icon';
		$text_selector = '.dsm_icon_list .dsm_icon_list_items %%order_class%%.dsm_icon_list_child .dsm_icon_list_text';

		if ( et_builder_is_hover_enabled( 'icon_font_size', $this->props ) ) {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => $this->add_hover_to_order_class( $icon_selector ),
					'declaration' => sprintf(
						'font-size: %1$s;',
						esc_html( $icon_font_size_hover )
					),
				)
			);
		}

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

		// Images: Add CSS Filters and Mix Blend Mode rules (if set).
		$generate_css_image_filters = '';
		if ( $image && array_key_exists( 'icon_settings', $this->advanced_fields ) && array_key_exists( 'css', $this->advanced_fields['icon_settings'] ) ) {
			$generate_css_image_filters = $this->generate_css_filters(
				$render_slug,
				'child_',
				self::$data_utils->array_get( $this->advanced_fields['icon_settings']['css'], 'main', '%%order_class%%' )
			);
		}

		$image_classes = '';
		if ( 'off' === $use_icon ) {
			$image_selector = '.dsm_icon_list .dsm_icon_list_items %%order_class%%.dsm_icon_list_child .dsm_icon_list_image';
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

			$image = $multi_view->render_element(
				array(
					'tag'      => 'img',
					'attrs'    => array(
						'src' => '{{image}}',
						// 'class' => implode( ' ', $image_classes ),
						'alt' => $alt,
					),
					'required' => 'image',
				)
			);
			$image = $image ? sprintf(
				'<span class="dsm_icon_list_image%2$s">%1$s</span>',
				$image,
				esc_attr( $generate_css_image_filters )
			) : $image;

		} else {
			$icon_style        = sprintf( 'color: %1$s;', esc_attr( $icon_color ) );
			$icon_tablet_style = '' !== $icon_color_tablet ? sprintf( 'color: %1$s;', esc_attr( $icon_color_tablet ) ) : '';
			$icon_phone_style  = '' !== $icon_color_phone ? sprintf( 'color: %1$s;', esc_attr( $icon_color_phone ) ) : '';
			$icon_style_hover  = '';

			if ( et_builder_is_hover_enabled( 'icon_color', $this->props ) ) {
				$icon_style_hover = sprintf( 'color: %1$s;', esc_attr( $icon_color_hover ) );
			}

			if ( et_builder_accent_color() !== $icon_color ) {
				ET_Builder_Element::set_style(
					$render_slug,
					array(
						'selector'    => $icon_selector,
						'declaration' => $icon_style,
					)
				);
			}

			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => $icon_selector,
					'declaration' => $icon_tablet_style,
					'media_query' => ET_Builder_Element::get_media_query( 'max_width_980' ),
				)
			);

			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => $icon_selector,
					'declaration' => $icon_phone_style,
					'media_query' => ET_Builder_Element::get_media_query( 'max_width_767' ),
				)
			);

			if ( '' !== $icon_style_hover ) {
				ET_Builder_Element::set_style(
					$render_slug,
					array(
						'selector'    => $this->add_hover_to_order_class( $icon_selector ),
						'declaration' => $icon_style_hover,
					)
				);
			}

			$icon_background_style        = sprintf( 'background-color: %1$s;', esc_attr( $icon_background_color ) );
			$icon_background_tablet_style = '' !== $icon_background_color_tablet ? sprintf( 'background-color: %1$s;', esc_attr( $icon_background_color_tablet ) ) : '';
			$icon_background_phone_style  = '' !== $icon_background_color_phone ? sprintf( 'background-color: %1$s;', esc_attr( $icon_background_color_phone ) ) : '';
			$icon_background_style_hover  = '';

			if ( et_builder_is_hover_enabled( 'icon_background_color', $this->props ) ) {
				$icon_background_style_hover = sprintf( 'background-color: %1$s;', esc_attr( $icon_background_color_hover ) );
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

			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => $icon_selector,
					'declaration' => $icon_background_tablet_style,
					'media_query' => ET_Builder_Element::get_media_query( 'max_width_980' ),
				)
			);

			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => $icon_selector,
					'declaration' => $icon_background_phone_style,
					'media_query' => ET_Builder_Element::get_media_query( 'max_width_767' ),
				)
			);

			if ( '' !== $icon_background_style_hover ) {
				ET_Builder_Element::set_style(
					$render_slug,
					array(
						'selector'    => $this->add_hover_to_order_class( $icon_selector ),
						'declaration' => $icon_background_style_hover,
					)
				);
			}

			/*
			if ( '5px' !== $text_indent ) {
				$text_indent_responsive_active = et_pb_get_responsive_status( $text_indent_last_edited );

				$text_indent_values = array(
					'desktop' => $text_indent,
					'tablet'  => $text_indent_responsive_active ? $text_indent_tablet : '',
					'phone'   => $text_indent_responsive_active ? $text_indent_phone : '',
				);

				et_pb_responsive_options()->generate_responsive_css( $text_indent_values, $text_selector, 'padding-left', $render_slug );
			}

			if ( et_builder_is_hover_enabled( 'text_indent', $this->props ) ) {
				ET_Builder_Element::set_style(
					$render_slug,
					array(
						'selector'    => $this->add_hover_to_order_class( $text_selector ),
						'declaration' => sprintf(
							'padding-left: %1$s;',
							esc_html( $text_indent_hover )
						),
					)
				);
			}*/

			$icon_hover_selector = str_replace( $this->icon_element_classname, $this->icon_element_classname . ':hover', $this->icon_element_selector );

			// Font Icon Style.
			$this->generate_styles(
				array(
					'utility_arg'    => 'icon_font_family',
					'render_slug'    => $render_slug,
					'base_attr_name' => 'font_icon',
					'important'      => true,
					'selector'       => $this->icon_element_selector,
					'hover_selector' => $icon_hover_selector,
					'processor'      => array(
						'ET_Builder_Module_Helper_Style_Processor',
						'process_extended_icon',
					),
				)
			);

			// Font Icon Size Style.
			$this->generate_styles(
				array(
					'base_attr_name' => 'icon_font_size',
					'selector'       => $this->icon_element_selector,
					'css_property'   => 'font-size',
					'render_slug'    => $render_slug,
					'type'           => 'range',
					'hover_selector' => $icon_hover_selector,
				)
			);

			$image = $multi_view->render_element(
				array(
					'tag'     => 'span',
					'content' => '{{font_icon}}',
					'attrs'   => array(
						'class' => 'dsm_icon_list_icon',
					),
				)
			);
		}

		$text = $multi_view->render_element(
			array(
				'tag'     => 'span',
				'content' => '{{text}}',
				'attrs'   => array(
					'class' => 'dsm_icon_list_text',
				),
			)
		);

		$content = '';

		if ( '' !== $url ) {
			$content = sprintf(
				'<a href="%3$s"%4$s><span class="dsm_icon_list_wrapper">%2$s</span>%1$s</a>',
				et_core_esc_previously( $text ),
				$image,
				esc_url( $url ),
				'on' === $url_new_window ? ' target="_blank"' : ''
			);
		} else {
			$content = sprintf(
				'<span class="dsm_icon_list_wrapper">%2$s</span>%1$s',
				et_core_esc_previously( $text ),
				$image
			);
		}

		$tooltip = $multi_view->render_element(
			array(
				'tag'     => 'span',
				'content' => '{{content}}',
				'attrs'   => array(
					'class' => 'dsm_icon_list_tooltip_wrapper',
				),
			)
		);

		$order_class = self::get_module_order_class( $render_slug );

		if ( 'on' === $use_tooltip ) {
			$content = sprintf(
				'<div class="dsm_icon_list_tooltip" data-tippy-arrow="true" data-tippy-placement="%3$s" data-dsm-slug="%4$s">%1$s</div>%2$s',
				$content,
				$tooltip,
				esc_attr( $tooltip_placement ),
				esc_attr( $order_class )
			);

			$tooltip_max_width_style        = sprintf( 'max-width: %1$s;', esc_attr( $tooltip_max_width ) );
			$tooltip_max_width_tablet_style = '' !== $tooltip_max_width_tablet ? sprintf( 'max-width: %1$s;', esc_attr( $tooltip_max_width_tablet ) ) : '';
			$tooltip_max_width_phone_style  = '' !== $tooltip_max_width_phone ? sprintf( 'max-width: %1$s;', esc_attr( $tooltip_max_width_phone ) ) : '';

			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => $tooltip_selector,
					'declaration' => $tooltip_max_width_style,
				)
			);

			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => $tooltip_selector,
					'declaration' => $tooltip_max_width_tablet_style,
					'media_query' => ET_Builder_Element::get_media_query( 'max_width_980' ),
				)
			);

			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => $tooltip_selector,
					'declaration' => $tooltip_max_width_phone_style,
					'media_query' => ET_Builder_Element::get_media_query( 'max_width_767' ),
				)
			);

			if ( 'rgba(34,34,34,0.9)' !== $tooltip_background_color ) {
				$tooltip_background_color_style        = sprintf( 'background-color: %1$s;', esc_attr( $tooltip_background_color ) );
				$tooltip_background_color_tablet_style = '' !== $tooltip_background_color_tablet ? sprintf( 'background-color: %1$s;', esc_attr( $tooltip_background_color_tablet ) ) : '';
				$tooltip_background_color_phone_style  = '' !== $tooltip_background_color_phone ? sprintf( 'background-color: %1$s;', esc_attr( $tooltip_background_color_phone ) ) : '';

				ET_Builder_Element::set_style(
					$render_slug,
					array(
						'selector'    => $tooltip_background_color_selector,
						'declaration' => $tooltip_background_color_style,
					)
				);

				ET_Builder_Element::set_style(
					$render_slug,
					array(
						'selector'    => $tooltip_background_color_selector,
						'declaration' => $tooltip_background_color_tablet_style,
						'media_query' => ET_Builder_Element::get_media_query( 'max_width_980' ),
					)
				);

				ET_Builder_Element::set_style(
					$render_slug,
					array(
						'selector'    => $tooltip_background_color_selector,
						'declaration' => $tooltip_background_color_phone_style,
						'media_query' => ET_Builder_Element::get_media_query( 'max_width_767' ),
					)
				);

				ET_Builder_Element::set_style(
					$render_slug,
					array(
						'selector'    => '%%order_class%% .dsm_icon_list_child_tooltip_wrapper.tippy-tooltip[data-placement^=top]>.tippy-arrow',
						'declaration' => sprintf(
							'border-top-color: %1$s;',
							esc_attr( $tooltip_background_color )
						),
					)
				);
				ET_Builder_Element::set_style(
					$render_slug,
					array(
						'selector'    => '%%order_class%% .dsm_icon_list_child_tooltip_wrapper.tippy-tooltip[data-placement^=right]>.tippy-arrow',
						'declaration' => sprintf(
							'border-right-color: %1$s;',
							esc_attr( $tooltip_background_color )
						),
					)
				);
				ET_Builder_Element::set_style(
					$render_slug,
					array(
						'selector'    => '%%order_class%% .dsm_icon_list_child_tooltip_wrapper.tippy-tooltip[data-placement^=bottom]>.tippy-arrow',
						'declaration' => sprintf(
							'border-bottom-color: %1$s;',
							esc_attr( $tooltip_background_color )
						),
					)
				);
				ET_Builder_Element::set_style(
					$render_slug,
					array(
						'selector'    => '%%order_class%% .dsm_icon_list_child_tooltip_wrapper.tippy-tooltip[data-placement^=left]>.tippy-arrow',
						'declaration' => sprintf(
							'border-left-color: %1$s;',
							esc_attr( $tooltip_background_color_tablet )
						),
					)
				);
				// Tablet.
				if ( '' !== $tooltip_background_color_tablet ) {
					ET_Builder_Element::set_style(
						$render_slug,
						array(
							'selector'    => '%%order_class%% .dsm_icon_list_child_tooltip_wrapper.tippy-tooltip[data-placement^=top]>.tippy-arrow',
							'declaration' => sprintf(
								'border-top-color: %1$s;',
								esc_attr( $tooltip_background_color_tablet )
							),
							'media_query' => ET_Builder_Element::get_media_query( 'max_width_980' ),
						)
					);
					ET_Builder_Element::set_style(
						$render_slug,
						array(
							'selector'    => '%%order_class%% .dsm_icon_list_child_tooltip_wrapper.tippy-tooltip[data-placement^=right]>.tippy-arrow',
							'declaration' => sprintf(
								'border-right-color: %1$s;',
								esc_attr( $tooltip_background_color_tablet )
							),
							'media_query' => ET_Builder_Element::get_media_query( 'max_width_980' ),
						)
					);
					ET_Builder_Element::set_style(
						$render_slug,
						array(
							'selector'    => '%%order_class%% .dsm_icon_list_child_tooltip_wrapper.tippy-tooltip[data-placement^=bottom]>.tippy-arrow',
							'declaration' => sprintf(
								'border-bottom-color: %1$s;',
								esc_attr( $tooltip_background_color_tablet )
							),
							'media_query' => ET_Builder_Element::get_media_query( 'max_width_980' ),
						)
					);
					ET_Builder_Element::set_style(
						$render_slug,
						array(
							'selector'    => '%%order_class%% .dsm_icon_list_child_tooltip_wrapper.tippy-tooltip[data-placement^=left]>.tippy-arrow',
							'declaration' => sprintf(
								'border-left-color: %1$s;',
								esc_attr( $tooltip_background_color_tablet )
							),
							'media_query' => ET_Builder_Element::get_media_query( 'max_width_980' ),
						)
					);
				}
				// Phone.
				if ( '' !== $tooltip_background_color_phone ) {
					ET_Builder_Element::set_style(
						$render_slug,
						array(
							'selector'    => '%%order_class%% .dsm_icon_list_child_tooltip_wrapper.tippy-tooltip[data-placement^=top]>.tippy-arrow',
							'declaration' => sprintf(
								'border-top-color: %1$s;',
								esc_attr( $tooltip_background_color_phone )
							),
							'media_query' => ET_Builder_Element::get_media_query( 'max_width_767' ),
						)
					);
					ET_Builder_Element::set_style(
						$render_slug,
						array(
							'selector'    => '%%order_class%% .dsm_icon_list_child_tooltip_wrapper.tippy-tooltip[data-placement^=right]>.tippy-arrow',
							'declaration' => sprintf(
								'border-right-color: %1$s;',
								esc_attr( $tooltip_background_color_phone )
							),
							'media_query' => ET_Builder_Element::get_media_query( 'max_width_767' ),
						)
					);
					ET_Builder_Element::set_style(
						$render_slug,
						array(
							'selector'    => '%%order_class%% .dsm_icon_list_child_tooltip_wrapper.tippy-tooltip[data-placement^=bottom]>.tippy-arrow',
							'declaration' => sprintf(
								'border-bottom-color: %1$s;',
								esc_attr( $tooltip_background_color_phone )
							),
							'media_query' => ET_Builder_Element::get_media_query( 'max_width_767' ),
						)
					);
					ET_Builder_Element::set_style(
						$render_slug,
						array(
							'selector'    => '%%order_class%% .dsm_icon_list_child_tooltip_wrapper.tippy-tooltip[data-placement^=left]>.tippy-arrow',
							'declaration' => sprintf(
								'border-left-color: %1$s;',
								esc_attr( $tooltip_background_color_phone )
							),
							'media_query' => ET_Builder_Element::get_media_query( 'max_width_767' ),
						)
					);
				}
			}
			$this->apply_custom_margin_padding(
				$render_slug,
				'tooltip_padding',
				'padding',
				$tooltip_padding_selector
			);
		}

		$this->add_classname(
			array(
				'on' === $use_tooltip ? 'dsm_icon_list_child_tooltip' : '',
			)
		);

		$this->remove_classname(
			array(
				'et_pb_module',
				'et_pb_section_video',
				'et_pb_preload',
				'et_pb_section_parallax',
			)
		);

		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		if ( 'on' === $use_tooltip ) {
			wp_enqueue_script( 'dsm-icon-list' );
		}

		$output = sprintf(
			'%1$s',
			$content
		);

		return $output;
	}

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
	 * Wrap module's rendered output with proper module wrapper. Ensuring module has consistent
	 * wrapper output which compatible with module attribute and background insertion.
	 *
	 * @since 3.1
	 *
	 * @param string $output      Module's rendered output.
	 * @param string $render_slug Slug of module that is used for rendering output.
	 *
	 * @return string
	 */
	protected function _render_module_wrapper( $output = '', $render_slug = '' ) {
		$wrapper_settings    = $this->get_wrapper_settings( $render_slug );
		$slug                = $render_slug;
		$outer_wrapper_attrs = $wrapper_settings['attrs'];
		$inner_wrapper_attrs = $wrapper_settings['inner_attrs'];

		/**
		 * Filters the HTML attributes for the module's outer wrapper. The dynamic portion of the
		 * filter name, '$slug', corresponds to the module's slug.
		 *
		 * @since 3.23 Add support for responsive video background.
		 * @since 3.1
		 *
		 * @param string[]           $outer_wrapper_attrs
		 * @param ET_Builder_Element $module_instance
		 */
		$outer_wrapper_attrs = apply_filters( "et_builder_module_{$slug}_outer_wrapper_attrs", $outer_wrapper_attrs, $this );

		/**
		 * Filters the HTML attributes for the module's inner wrapper. The dynamic portion of the
		 * filter name, '$slug', corresponds to the module's slug.
		 *
		 * @since 3.1
		 *
		 * @param string[]           $inner_wrapper_attrs
		 * @param ET_Builder_Element $module_instance
		 */
		$inner_wrapper_attrs = apply_filters( "et_builder_module_{$slug}_inner_wrapper_attrs", $inner_wrapper_attrs, $this );

		return sprintf(
			'<li%1$s>
				%2$s
				%3$s
				%6$s
				%7$s
				%5$s
			</li>',
			et_html_attrs( $outer_wrapper_attrs ),
			$wrapper_settings['parallax_background'],
			$wrapper_settings['video_background'],
			et_html_attrs( $inner_wrapper_attrs ),
			$output,
			et_()->array_get( $wrapper_settings, 'video_background_tablet', '' ),
			et_()->array_get( $wrapper_settings, 'video_background_phone', '' )
		);
	}
	/**
	 * Filter multi view value.
	 *
	 * @since 3.27.1
	 *
	 * @see ET_Builder_Module_Helper_MultiViewOptions::filter_value
	 *
	 * @param mixed                                     $raw_value Props raw value.
	 * @param array                                     $args {
	 *                                         Context data.
	 *
	 *     @type string $context      Context param: content, attrs, visibility, classes.
	 *     @type string $name         Module options props name.
	 *     @type string $mode         Current data mode: desktop, hover, tablet, phone.
	 *     @type string $attr_key     Attribute key for attrs context data. Example: src, class, etc.
	 *     @type string $attr_sub_key Attribute sub key that availabe when passing attrs value as array such as styes. Example: padding-top, margin-botton, etc.
	 * }
	 * @param ET_Builder_Module_Helper_MultiViewOptions $multi_view Multiview object instance.
	 *
	 * @return mixed
	 */
	public function multi_view_filter_value( $raw_value, $args, $multi_view ) {
		$name = isset( $args['name'] ) ? $args['name'] : '';
		$mode = isset( $args['mode'] ) ? $args['mode'] : '';

		if ( $raw_value && 'font_icon' === $name ) {
			return et_pb_get_extended_font_icon_value( $raw_value, true );
		}

		$fields_need_escape = array(
			'button_text',
		);

		if ( $raw_value && in_array( $name, $fields_need_escape, true ) ) {
			return $this->_esc_attr( $multi_view->get_name_by_mode( $name, $mode ), 'none', $raw_value );
		}

		return $raw_value;
	}
}

new DSM_Icon_List_Child();
