<?php

class DSM_Button extends ET_Builder_Module {

	public $slug       = 'dsm_button';
	public $vb_support = 'on';
	public $icon_path;

	protected $module_credits = array(
		'module_uri' => 'https://divisupreme.com/',
		'author'     => 'Divi Supreme',
		'author_uri' => 'https://divisupreme.com/',
	);

	public function init() {
		$this->name             = esc_html__( 'Supreme Button', 'dsm-supreme-modules-for-divi' );
		$this->plural           = esc_html__( 'Supreme Buttons', 'dsm-supreme-modules-for-divi' );
		$this->icon_path        = plugin_dir_path( __FILE__ ) . 'icon.svg';
		$this->main_css_element = '%%order_class%%';

		$this->custom_css_fields = array(
			'main_element' => array(
				'label'                    => esc_html__( 'Main Element', 'dsm-supreme-modules-for-divi' ),
				'no_space_before_selector' => true,
			),
		);

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Text', 'dsm-supreme-modules-for-divi' ),
					'links'        => array(
						'sub_toggles'       => array(
							'button_one_tab' => array(
								'name' => esc_html__( 'Button #1', 'dsm-supreme-modules-for-divi' ),
							),
							'button_two_tab' => array(
								'name' => esc_html__( 'Button #2', 'dsm-supreme-modules-for-divi' ),
							),
						),
						'tabbed_subtoggles' => true,
						'title'             => esc_html__( 'Links', 'dsm-supreme-modules-for-divi' ),
					),
					'separator'    => esc_html__( 'Separator', 'dsm-supreme-modules-for-divi' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'alignment' => esc_html__( 'Alignment', 'dsm-supreme-modules-for-divi' ),
					'text'      => array(
						'title'    => esc_html__( 'Text', 'dsm-supreme-modules-for-divi' ),
						'priority' => 49,
					),
					'lightbox'  => array(
						'sub_toggles'       => array(
							'button_one_tab' => array(
								'name' => esc_html__( 'Button #1', 'dsm-supreme-modules-for-divi' ),
							),
							'button_two_tab' => array(
								'name' => esc_html__( 'Button #2', 'dsm-supreme-modules-for-divi' ),
							),
						),
						'tabbed_subtoggles' => true,
						'title'             => esc_html__( 'Lightbox', 'dsm-supreme-modules-for-divi' ),
					),
					'tooltip'   => array(
						'sub_toggles'       => array(
							'button_one_tab' => array(
								'name' => esc_html__( 'Tooltip #1', 'dsm-supreme-modules-for-divi' ),
							),
							'button_two_tab' => array(
								'name' => esc_html__( 'Tooltip #2', 'dsm-supreme-modules-for-divi' ),
							),
						),
						'tabbed_subtoggles' => true,
						'title'             => esc_html__( 'Tooltip Text', 'dsm-supreme-modules-for-divi' ),
						'priority'          => 60,
					),
				),
			),
		);
	}

	public function get_advanced_fields_config() {
		return array(
			'fonts'          => array(
				'separator_text' => array(
					'label'          => esc_html__( 'Separator', 'dsm-supreme-modules-for-divi' ),
					'css'            => array(
						'main' => "{$this->main_css_element} .dsm-button-separator-text",
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
				'tooltip_one'    => array(
					'label'           => esc_html__( 'Tooltip #1', 'dsm-supreme-modules-for-divi' ),
					'css'             => array(
						'main' => "{$this->main_css_element} .dsm_button_one.dsm-tooltip[data-dsm-tooltip]:after",
					),
					'font_size'       => array(
						'default' => '12px',
					),
					'line_height'     => array(
						'default' => '1em',
					),
					'letter_spacing'  => array(
						'default' => '0px',
					),
					'show_if'         => array(
						'button_one_tooltip' => 'on',
					),
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'tooltip',
					'sub_toggle'      => 'button_one_tab',
					'hide_text_align' => true,
				),
				'tooltip_two'    => array(
					'label'           => esc_html__( 'Tooltip #2', 'dsm-supreme-modules-for-divi' ),
					'css'             => array(
						'main' => "{$this->main_css_element} .dsm_button_two.dsm-tooltip[data-dsm-tooltip]:after",
					),
					'font_size'       => array(
						'default' => '12px',
					),
					'line_height'     => array(
						'default' => '1.7em',
					),
					'letter_spacing'  => array(
						'default' => '0px',
					),
					'show_if'         => array(
						'button_two_tooltip' => 'on',
					),
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'tooltip',
					'sub_toggle'      => 'button_two_tab',
					'hide_text_align' => true,
				),
			),
			'borders'        => array(
				'default' => false,
			),
			'button'         => array(
				'button_one' => array(
					'label'          => esc_html__( 'Button One', 'dsm-supreme-modules-for-divi' ),
					'css'            => array(
						'main' => "{$this->main_css_element} .et_pb_button_one.et_pb_button",
					),
					'box_shadow'     => array(
						'css' => array(
							'main' => '%%order_class%% .et_pb_button_one',
						),
					),
					'margin_padding' => array(
						'css' => array(
							'important' => 'all',
						),
					),
				),
				'button_two' => array(
					'label'          => esc_html__( 'Button Two', 'dsm-supreme-modules-for-divi' ),
					'css'            => array(
						'main' => "{$this->main_css_element} .et_pb_button_two.et_pb_button",
					),
					'box_shadow'     => array(
						'css' => array(
							'main' => '%%order_class%% .et_pb_button_two',
						),
					),
					'margin_padding' => array(
						'css'           => array(
							'important' => 'all',
						),
						'custom_margin' => array(
							'default' => '|||20px|false|false',
						),
					),
				),
			),
			'margin_padding' => array(
				'css' => array(
					'padding'   => "{$this->main_css_element}, {$this->main_css_element}:hover",
					'margin'    => "{$this->main_css_element}.dsm_button",
					'important' => 'all',
				),
			),
			'text'           => array(
				'use_text_orientation'  => false,
				'use_background_layout' => true,
				'options'               => array(
					'background_layout' => array(
						'default_on_front' => 'light',
						'hover'            => 'tabs',
					),
				),
			),
			'text_shadow'    => array(
				'default' => false,
			),
			'background'     => false,
			'max_width'      => false,
			'link_options'   => false,
		);
	}

	public function get_custom_css_fields_config() {
		return array(
			'button_1' => array(
				'label'    => esc_html__( 'Button One', 'dsm-supreme-modules-for-divi' ),
				'selector' => '.et_pb_button_one.et_pb_button',
			),
			'button_2' => array(
				'label'    => esc_html__( 'Button Two', 'dsm-supreme-modules-for-divi' ),
				'selector' => '.et_pb_button_two.et_pb_button',
			),
		);
	}


	public function get_fields() {
		$dsm_animation_type_list = array(
			'fade'              => esc_html__( 'Fade', 'dsm-supreme-modules-for-divi' ),
			'fade-in-direction' => esc_html__( 'Fade In Direction', 'dsm-supreme-modules-for-divi' ),
		);
		return array(
			'button_one_id'                      => array(
				'label'           => esc_html__( 'Button #1 ID', 'dsm-supreme-modules-for-divi' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'description'     => esc_html__( "Assign a unique CSS ID to Button #1 which can be used to assign custom CSS styles from within your child theme or from within Divi's custom CSS inputs.", 'dsm-supreme-modules-for-divi' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'button_one_css'                     => array(
				'label'           => esc_html__( 'Button #1 CSS Class', 'dsm-supreme-modules-for-divi' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'description'     => esc_html__( "Assign any number of CSS Classes to the element, separated by spaces, which can be used to assign custom CSS styles from within your child theme or from within Divi's custom CSS inputs.", 'dsm-supreme-modules-for-divi' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'button_two_id'                      => array(
				'label'           => esc_html__( 'Button #2 ID', 'dsm-supreme-modules-for-divi' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'description'     => esc_html__( "Assign a unique CSS ID to Button #2 which can be used to assign custom CSS styles from within your child theme or from within Divi's custom CSS inputs.", 'dsm-supreme-modules-for-divi' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'button_two_css'                     => array(
				'label'           => esc_html__( 'Button #2 CSS Class', 'dsm-supreme-modules-for-divi' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'description'     => esc_html__( "Assign any number of CSS Classes to the element, separated by spaces, which can be used to assign custom CSS styles from within your child theme or from within Divi's custom CSS inputs.", 'dsm-supreme-modules-for-divi' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'button_one_text'                    => array(
				'label'           => esc_html__( 'Button #1 Text', 'dsm-supreme-modules-for-divi' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Enter the text for the Button.', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
			),
			'button_one_url_type'                => array(
				'label'            => esc_html__( 'Link Type for Button #1', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'url'          => esc_html__( 'URL Link', 'dsm-supreme-modules-for-divi' ),
					'download'     => esc_html__( 'Download File', 'dsm-supreme-modules-for-divi' ),
					'email'        => esc_html__( 'Email', 'dsm-supreme-modules-for-divi' ),
					'phone'        => esc_html__( 'Phone', 'dsm-supreme-modules-for-divi' ),
					'sms'          => esc_html__( 'SMS', 'dsm-supreme-modules-for-divi' ),
					'fb_messenger' => esc_html__( 'Facebook Messenger', 'dsm-supreme-modules-for-divi' ),
					'skype'        => esc_html__( 'Skype', 'dsm-supreme-modules-for-divi' ),
					'whatsapp'     => esc_html__( 'WhatsApp', 'dsm-supreme-modules-for-divi' ),
					'telegram'     => esc_html__( 'Telegram', 'dsm-supreme-modules-for-divi' ),
				),
				'description'      => esc_html__( 'Choose the type of URL', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'      => 'links',
				'sub_toggle'       => 'button_one_tab',
				'default_on_front' => 'url',
			),
			'button_one_email_address'           => array(
				'label'            => esc_html__( 'Email', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Enter the Email Address for Button #1.', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'      => 'links',
				'sub_toggle'       => 'button_one_tab',
				'default_on_front' => '',
				'show_if'          => array(
					'button_one_url_type' => 'email',
				),
				'dynamic_content'  => 'text',
			),
			'button_one_email_cc'                => array(
				'label'            => esc_html__( 'CC', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Enter the carbon copy email address for Button #1.', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'      => 'links',
				'sub_toggle'       => 'button_one_tab',
				'default_on_front' => '',
				'show_if'          => array(
					'button_one_url_type' => 'email',
				),
				'dynamic_content'  => 'text',
			),
			'button_one_email_bcc'               => array(
				'label'            => esc_html__( 'BCC', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Enter the blind carbon copy email address for Button #1.', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'      => 'links',
				'sub_toggle'       => 'button_one_tab',
				'default_on_front' => '',
				'show_if'          => array(
					'button_one_url_type' => 'email',
				),
				'dynamic_content'  => 'text',
			),
			'button_one_email_subject'           => array(
				'label'           => esc_html__( 'Subject', 'dsm-supreme-modules-for-divi' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Enter the default subject of the email for Button #1.', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'     => 'links',
				'sub_toggle'      => 'button_one_tab',
				'show_if'         => array(
					'button_one_url_type' => 'email',
				),
				'dynamic_content' => 'text',
			),
			'button_one_email_msg'               => array(
				'label'            => esc_html__( 'Message', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'textarea',
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Enter the default body message of the email for Button #1.', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'      => 'links',
				'sub_toggle'       => 'button_one_tab',
				'default_on_front' => '',
				'show_if'          => array(
					'button_one_url_type' => 'email',
				),
				'dynamic_content'  => 'text',
			),
			'button_one_phone_number'            => array(
				'label'            => esc_html__( 'Phone Number', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Enter the phone number to call for Button #1 (This will usually work on mobile phones)', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'      => 'links',
				'sub_toggle'       => 'button_one_tab',
				'default_on_front' => '',
				'show_if'          => array(
					'button_one_url_type' => 'phone',
				),
				'dynamic_content'  => 'text',
			),
			'button_one_sms_number'              => array(
				'label'            => esc_html__( 'Phone Number', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Enter the phone number to SMS for Button #1 (This will usually work on mobile phones)', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'      => 'links',
				'sub_toggle'       => 'button_one_tab',
				'default_on_front' => '',
				'show_if'          => array(
					'button_one_url_type' => 'sms',
				),
				'dynamic_content'  => 'text',
			),
			'button_one_sms_body_text'           => array(
				'label'            => esc_html__( 'Body Text', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'textarea',
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Pre-Populate SMS Text for Button #1 (This will usually work on mobile phones)', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'      => 'links',
				'sub_toggle'       => 'button_one_tab',
				'default_on_front' => '',
				'show_if'          => array(
					'button_one_url_type' => 'sms',
				),
				'dynamic_content'  => 'text',
			),
			'button_one_fb_messenger'            => array(
				'label'            => esc_html__( 'Username', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Enter a person, page, or bot username.', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'      => 'links',
				'sub_toggle'       => 'button_one_tab',
				'default_on_front' => '',
				'show_if'          => array(
					'button_one_url_type' => 'fb_messenger',
				),
				'dynamic_content'  => 'text',
			),
			'button_one_skype'                   => array(
				'label'            => esc_html__( 'Username', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Enter the Skype username for Button #1.', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'      => 'links',
				'sub_toggle'       => 'button_one_tab',
				'default_on_front' => '',
				'show_if'          => array(
					'button_one_url_type' => 'skype',
				),
				'dynamic_content'  => 'text',
			),
			'button_one_skype_action_type'       => array(
				'label'            => esc_html__( 'Action Type', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'chat' => esc_html__( 'Chat', 'dsm-supreme-modules-for-divi' ),
					'call' => esc_html__( 'Call', 'dsm-supreme-modules-for-divi' ),
					/*
					'userinfo'  => esc_html__( 'Show Skype Profile', 'dsm-supreme-modules-for-divi' ),
					'sendfile'  => esc_html__( 'Send a File', 'dsm-supreme-modules-for-divi' ),
					'add'  => esc_html__( 'Add', 'dsm-supreme-modules-for-divi' ),
					'voicemail'  => esc_html__( 'Voicemail', 'dsm-supreme-modules-for-divi' ),
					*/
				),
				'description'      => esc_html__( 'Choose the action type for Skype', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'      => 'links',
				'sub_toggle'       => 'button_one_tab',
				'default_on_front' => 'chat',
				'show_if'          => array(
					'button_one_url_type' => 'skype',
				),
			),
			'button_one_whatsapp_number'         => array(
				'label'            => esc_html__( 'Number', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Enter the phone number to message directly via WhatsApp message for Button #1.', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'      => 'links',
				'sub_toggle'       => 'button_one_tab',
				'default_on_front' => '',
				'show_if'          => array(
					'button_one_url_type' => 'whatsapp',
				),
				'dynamic_content'  => 'text',
			),
			'button_one_whatsapp_msg'            => array(
				'label'            => esc_html__( 'Message', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'textarea',
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Enter the default body message of the WhatsApp for Button #1.', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'      => 'links',
				'sub_toggle'       => 'button_one_tab',
				'default_on_front' => '',
				'show_if'          => array(
					'button_one_url_type' => 'whatsapp',
				),
				'dynamic_content'  => 'text',
			),
			'button_one_telegram'                => array(
				'label'            => esc_html__( 'Telegram Username', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Enter the Telegram Username.', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'      => 'links',
				'sub_toggle'       => 'button_one_tab',
				'default_on_front' => '',
				'show_if'          => array(
					'button_one_url_type' => 'telegram',
				),
				'dynamic_content'  => 'text',
			),
			'button_one_telegram_msg'            => array(
				'label'            => esc_html__( 'Message', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'textarea',
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Enter the default body message of the Telegram for Button #1.', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'      => 'links',
				'sub_toggle'       => 'button_one_tab',
				'default_on_front' => '',
				'show_if'          => array(
					'button_one_url_type' => 'telegram',
				),
				'dynamic_content'  => 'text',
			),
			'button_one_url'                     => array(
				'label'           => esc_html__( 'Button #1 URL', 'dsm-supreme-modules-for-divi' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Enter the URL for the Button.', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'     => 'links',
				'sub_toggle'      => 'button_one_tab',
				'show_if'         => array(
					'button_one_url_type' => 'url',
				),
				'dynamic_content' => 'url',
			),
			'button_one_url_new_window'          => array(
				'label'            => esc_html__( 'Url Opens', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'off' => esc_html__( 'In The Same Window', 'dsm-supreme-modules-for-divi' ),
					'on'  => esc_html__( 'In The New Tab', 'dsm-supreme-modules-for-divi' ),
				),
				'toggle_slug'      => 'links',
				'sub_toggle'       => 'button_one_tab',
				'description'      => esc_html__( 'Here you can choose whether or not your link opens in a new window', 'dsm-supreme-modules-for-divi' ),
				'default_on_front' => 'off',
				'show_if'          => array(
					'button_two_url_type' => 'url',
				),
				'show_if_not'      => array(
					'button_one_image_popup' => 'on',
					'button_one_video_popup' => 'on',
				),
			),
			'button_one_download_file'           => array(
				'label'              => esc_html__( 'Download File', 'dsm-supreme-modules-for-divi' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'data_type'          => '',
				'upload_button_text' => esc_attr__( 'Upload a file', 'dsm-supreme-modules-for-divi' ),
				'choose_text'        => esc_attr__( 'Choose a file', 'dsm-supreme-modules-for-divi' ),
				'update_text'        => esc_attr__( 'Set As Download File', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'        => 'links',
				'sub_toggle'         => 'button_one_tab',
				'show_if'            => array(
					'button_one_url_type' => 'download',
				),
			),
			'button_one_image_popup'             => array(
				'label'            => esc_html__( 'Open as Image Lightbox', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'off' => esc_html__( 'No', 'dsm-supreme-modules-for-divi' ),
					'on'  => esc_html__( 'Yes', 'dsm-supreme-modules-for-divi' ),
				),
				'toggle_slug'      => 'links',
				'sub_toggle'       => 'button_one_tab',
				'description'      => esc_html__( 'Here you can choose whether or not the button should open in Lightbox. Note: if you select to open the button in Lightbox, url options below will be ignored.', 'dsm-supreme-modules-for-divi' ),
				'default_on_front' => 'off',
				'show_if'          => array(
					'button_one_url_type' => 'url',
				),
				'show_if_not'      => array(
					'button_one_video_popup' => 'on',
				),
			),
			'button_one_image_src'               => array(
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => esc_attr__( 'Upload an image', 'dsm-supreme-modules-for-divi' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'dsm-supreme-modules-for-divi' ),
				'update_text'        => esc_attr__( 'Set As Image', 'dsm-supreme-modules-for-divi' ),
				'hide_metadata'      => true,
				'description'        => esc_html__( 'Upload your desired image for Button One Image Lightbox, or type in the URL to the image you would like to display.', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'        => 'links',
				'sub_toggle'         => 'button_one_tab',
				'show_if'            => array(
					'button_one_url_type'    => 'url',
					'button_one_image_popup' => 'on',
				),
				'dynamic_content'    => 'image',
			),
			'button_one_lightbox_close_color'    => array(
				'label'        => esc_html__( 'Close Color', 'dsm-supreme-modules-for-divi' ),
				'description'  => esc_html__( 'Here you can define a custom color for the lightbox close button.', 'dsm-supreme-modules-for-divi' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'default'      => 'rgba(255,255,255,0.2)',
				'tab_slug'     => 'advanced',
				'toggle_slug'  => 'lightbox',
				'sub_toggle'   => 'button_one_tab',
				'hover'        => 'tabs',
			),
			'button_one_lightbox_max_width'      => array(
				'label'          => esc_html__( 'Max Width', 'dsm-supreme-modules-for-divi' ),
				'description'    => esc_html__( 'Setting a maximum width will prevent your lightbox from ever surpassing the defined width value. Maximum width can be used in combination with the standard width setting. Maximum width supersedes the normal width value.', 'dsm-supreme-modules-for-divi' ),
				'type'           => 'range',
				'default'        => 'none',
				'default_tablet' => 'none',
				'default_unit'   => 'px',
				'allowed_values' => et_builder_get_acceptable_css_string_values( 'max-width' ),
				'range_settings' => array(
					'min'  => '100',
					'max'  => '1200',
					'step' => '1',
				),
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'lightbox',
				'sub_toggle'     => 'button_one_tab',
			),
			'button_one_video_popup'             => array(
				'label'            => esc_html__( 'Open as Video Lightbox', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'off' => esc_html__( 'No', 'dsm-supreme-modules-for-divi' ),
					'on'  => esc_html__( 'Yes', 'dsm-supreme-modules-for-divi' ),
				),
				'toggle_slug'      => 'links',
				'sub_toggle'       => 'button_one_tab',
				'description'      => esc_html__( 'Put the Video link on the Button #1 URL. Copy the video URL link and paste it here. Support: YouTube, Vimeo and Dailymotion.', 'dsm-supreme-modules-for-divi' ),
				'default_on_front' => 'off',
				'show_if'          => array(
					'button_one_url_type' => 'url',
				),
				'show_if_not'      => array(
					'button_one_image_popup' => 'on',
				),
			),
			'button_one_tooltip'                 => array(
				'label'            => esc_html__( 'Use Tooltip', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'off' => esc_html__( 'No', 'dsm-supreme-modules-for-divi' ),
					'on'  => esc_html__( 'Yes', 'dsm-supreme-modules-for-divi' ),
				),
				'toggle_slug'      => 'links',
				'sub_toggle'       => 'button_one_tab',
				'description'      => esc_html__( 'This will show a tooltip on your Button #1.', 'dsm-supreme-modules-for-divi' ),
				'default_on_front' => 'off',
			),
			'button_one_tooltip_content'         => array(
				'label'            => esc_html__( 'Tooltip Content', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Enter the content for the your tooltip.', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'      => 'links',
				'sub_toggle'       => 'button_one_tab',
				'default_on_front' => 'Tooltip #1',
				'show_if'          => array(
					'button_one_tooltip' => 'on',
				),
				'dynamic_content'  => 'text',
			),
			'button_one_tooltip_arrow'           => array(
				'label'            => esc_html__( 'Show Arrow', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'off' => esc_html__( 'No', 'dsm-supreme-modules-for-divi' ),
					'on'  => esc_html__( 'Yes', 'dsm-supreme-modules-for-divi' ),
				),
				'toggle_slug'      => 'links',
				'sub_toggle'       => 'button_one_tab',
				'description'      => esc_html__( 'If enable, then an arrow will be added to the tooltip.', 'dsm-supreme-modules-for-divi' ),
				'default_on_front' => 'on',
				'show_if'          => array(
					'button_one_tooltip' => 'on',
				),
			),
			'button_one_tooltip_size'            => array(
				'label'            => esc_html__( 'Size', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'small'   => esc_html__( 'Small', 'dsm-supreme-modules-for-divi' ),
					'regular' => esc_html__( 'Regular', 'dsm-supreme-modules-for-divi' ),
					'large'   => esc_html__( 'Large', 'dsm-supreme-modules-for-divi' ),
				),
				'toggle_slug'      => 'links',
				'sub_toggle'       => 'button_one_tab',
				'default_on_front' => 'regular',
				'show_if'          => array(
					'button_one_tooltip' => 'on',
				),
				'description'      => esc_html__( 'The size of the tooltip.', 'dsm-supreme-modules-for-divi' ),
			),
			'button_one_tooltip_placement'       => array(
				'label'            => esc_html__( 'Placement', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'top'    => esc_html__( 'Top', 'dsm-supreme-modules-for-divi' ),
					'bottom' => esc_html__( 'Bottom', 'dsm-supreme-modules-for-divi' ),
					'left'   => esc_html__( 'Left', 'dsm-supreme-modules-for-divi' ),
					'right'  => esc_html__( 'Right', 'dsm-supreme-modules-for-divi' ),
				),
				'toggle_slug'      => 'links',
				'sub_toggle'       => 'button_one_tab',
				'default_on_front' => 'top',
				'show_if'          => array(
					'button_one_tooltip' => 'on',
				),
			),
			'button_one_tooltip_animation'       => array(
				'label'            => esc_html__( 'Animations', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => $dsm_animation_type_list,
				'default'          => 'fade',
				'default_on_front' => 'fade',
				'description'      => esc_html__( 'Here you can choose different types of animations for your tooltips.', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'      => 'links',
				'sub_toggle'       => 'button_one_tab',
				'show_if'          => array(
					'button_one_tooltip' => 'on',
				),
			),
			/*
			'button_one_tooltip_distance' => array(
				'label'             => esc_html__( 'Distance', 'dsm-supreme-modules-for-divi' ),
				'type'              => 'range',
				'option_category'   => 'layout',
				'mobile_options'  => false,
				'toggle_slug'      => 'links',
				'sub_toggle'  => 'button_one_tab',
				'default_unit'      => '',
				'default'           => '10',
				'responsive'      => false,
				'show_if' => array(
					'button_one_tooltip' => 'on',
				),
			),*/
			'button_one_tooltip_bg_color'        => array(
				'label'        => esc_html__( 'Background Color', 'dsm-supreme-modules-for-divi' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
				'toggle_slug'  => 'tooltip',
				'sub_toggle'   => 'button_one_tab',
				'show_if'      => array(
					'button_one_tooltip' => 'on',
				),
			),
			'button_two_text'                    => array(
				'label'           => esc_html__( 'Button #2 Text', 'dsm-supreme-modules-for-divi' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Enter the text for the Button.', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
			),
			'button_two_url_type'                => array(
				'label'            => esc_html__( 'Link Type for Button #2', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'url'          => esc_html__( 'URL Link', 'dsm-supreme-modules-for-divi' ),
					'download'     => esc_html__( 'Download File', 'dsm-supreme-modules-for-divi' ),
					'email'        => esc_html__( 'Email', 'dsm-supreme-modules-for-divi' ),
					'phone'        => esc_html__( 'Phone', 'dsm-supreme-modules-for-divi' ),
					'sms'          => esc_html__( 'SMS', 'dsm-supreme-modules-for-divi' ),
					'fb_messenger' => esc_html__( 'Facebook Messenger', 'dsm-supreme-modules-for-divi' ),
					'skype'        => esc_html__( 'Skype', 'dsm-supreme-modules-for-divi' ),
					'whatsapp'     => esc_html__( 'WhatsApp', 'dsm-supreme-modules-for-divi' ),
					'telegram'     => esc_html__( 'Telegram', 'dsm-supreme-modules-for-divi' ),
				),
				'description'      => esc_html__( 'Choose the type of URL', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'      => 'links',
				'sub_toggle'       => 'button_two_tab',
				'default_on_front' => 'url',
			),
			'button_two_email_address'           => array(
				'label'            => sprintf( esc_html__( 'Email', 'dsm-supreme-modules-for-divi' ), '#2' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Enter the Email Address for Button #2.', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'      => 'links',
				'sub_toggle'       => 'button_two_tab',
				'default_on_front' => '',
				'show_if'          => array(
					'button_two_url_type' => 'email',
				),
				'dynamic_content'  => 'text',
			),
			'button_two_email_cc'                => array(
				'label'            => sprintf( esc_html__( 'CC', 'dsm-supreme-modules-for-divi' ), '#2' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Enter the carbon copy email address for Button #2.', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'      => 'links',
				'sub_toggle'       => 'button_two_tab',
				'default_on_front' => '',
				'show_if'          => array(
					'button_two_url_type' => 'email',
				),
				'dynamic_content'  => 'text',
			),
			'button_two_email_bcc'               => array(
				'label'            => sprintf( esc_html__( 'BCC', 'dsm-supreme-modules-for-divi' ), '#2' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Enter the blind carbon copy email address for Button #2.', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'      => 'links',
				'sub_toggle'       => 'button_two_tab',
				'default_on_front' => '',
				'show_if'          => array(
					'button_two_url_type' => 'email',
				),
				'dynamic_content'  => 'text',
			),
			'button_two_email_subject'           => array(
				'label'           => sprintf( esc_html__( 'Subject', 'dsm-supreme-modules-for-divi' ), '#2' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Enter the default subject of the email for Button #1.', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'     => 'links',
				'sub_toggle'      => 'button_two_tab',
				'show_if'         => array(
					'button_two_url_type' => 'email',
				),
				'dynamic_content' => 'text',
			),
			'button_two_email_msg'               => array(
				'label'            => sprintf( esc_html__( 'Message', 'dsm-supreme-modules-for-divi' ), '#2' ),
				'type'             => 'textarea',
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Enter the default body message of the email for Button #2.', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'      => 'links',
				'sub_toggle'       => 'button_two_tab',
				'default_on_front' => '',
				'show_if'          => array(
					'button_two_url_type' => 'email',
				),
				'dynamic_content'  => 'text',
			),
			'button_two_phone_number'            => array(
				'label'            => sprintf( esc_html__( 'Phone Number', 'dsm-supreme-modules-for-divi' ), '#2' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Enter the phone number to call for Button #1 (This will usually work on mobile phones)', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'      => 'links',
				'sub_toggle'       => 'button_two_tab',
				'default_on_front' => '',
				'show_if'          => array(
					'button_two_url_type' => 'phone',
				),
				'dynamic_content'  => 'text',
			),
			'button_two_sms_number'              => array(
				'label'            => sprintf( esc_html__( 'Phone Number', 'dsm-supreme-modules-for-divi' ), '#2' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Enter the phone number to SMS for Button #1 (This will usually work on mobile phones)', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'      => 'links',
				'sub_toggle'       => 'button_two_tab',
				'default_on_front' => '',
				'show_if'          => array(
					'button_two_url_type' => 'sms',
				),
				'dynamic_content'  => 'text',
			),
			'button_two_sms_body_text'           => array(
				'label'            => sprintf( esc_html__( 'Body Text', 'dsm-supreme-modules-for-divi' ), '#2' ),
				'type'             => 'textarea',
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Pre-Populate SMS Text for Button #2 (This will usually work on mobile phones)', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'      => 'links',
				'sub_toggle'       => 'button_two_tab',
				'default_on_front' => '',
				'show_if'          => array(
					'button_two_url_type' => 'sms',
				),
				'dynamic_content'  => 'text',
			),
			'button_two_fb_messenger'            => array(
				'label'            => sprintf( esc_html__( 'Username', 'dsm-supreme-modules-for-divi' ), '#2' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Enter a person, page, or bot username.', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'      => 'links',
				'sub_toggle'       => 'button_two_tab',
				'default_on_front' => '',
				'show_if'          => array(
					'button_two_url_type' => 'fb_messenger',
				),
				'dynamic_content'  => 'text',
			),
			'button_two_skype'                   => array(
				'label'            => sprintf( esc_html__( 'Username', 'dsm-supreme-modules-for-divi' ), '#2' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Enter the Skype username for Button #2.', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'      => 'links',
				'sub_toggle'       => 'button_two_tab',
				'default_on_front' => '',
				'show_if'          => array(
					'button_two_url_type' => 'skype',
				),
				'dynamic_content'  => 'text',
			),
			'button_two_skype_action_type'       => array(
				'label'            => esc_html__( 'Action Type', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'chat' => esc_html__( 'Chat', 'dsm-supreme-modules-for-divi' ),
					'call' => esc_html__( 'Call', 'dsm-supreme-modules-for-divi' ),
					/*
					'userinfo'  => esc_html__( 'Show Skype Profile', 'dsm-supreme-modules-for-divi' ),
					'sendfile'  => esc_html__( 'Send a File', 'dsm-supreme-modules-for-divi' ),
					'add'  => esc_html__( 'Add', 'dsm-supreme-modules-for-divi' ),
					'voicemail'  => esc_html__( 'Voicemail', 'dsm-supreme-modules-for-divi' ),
					*/
				),
				'description'      => esc_html__( 'Choose the action type for Skype', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'      => 'links',
				'sub_toggle'       => 'button_two_tab',
				'default_on_front' => 'chat',
				'show_if'          => array(
					'button_two_url_type' => 'skype',
				),
			),
			'button_two_whatsapp_number'         => array(
				'label'            => sprintf( esc_html__( 'Number', 'dsm-supreme-modules-for-divi' ), '#2' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Enter the phone number to message directly via WhatsApp message for Button #2.', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'      => 'links',
				'sub_toggle'       => 'button_two_tab',
				'default_on_front' => '',
				'show_if'          => array(
					'button_two_url_type' => 'whatsapp',
				),
				'dynamic_content'  => 'text',
			),
			'button_two_whatsapp_msg'            => array(
				'label'            => sprintf( esc_html__( 'Message', 'dsm-supreme-modules-for-divi' ), '#2' ),
				'type'             => 'textarea',
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Enter the default body message of the WhatsApp for Button #2.', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'      => 'links',
				'sub_toggle'       => 'button_two_tab',
				'default_on_front' => '',
				'show_if'          => array(
					'button_two_url_type' => 'whatsapp',
				),
				'dynamic_content'  => 'text',
			),
			'button_two_telegram'                => array(
				'label'            => sprintf( esc_html__( 'Telegram Username', 'dsm-supreme-modules-for-divi' ), '#2' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Enter the Telegram Username.', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'      => 'links',
				'sub_toggle'       => 'button_two_tab',
				'default_on_front' => '',
				'show_if'          => array(
					'button_two_url_type' => 'telegram',
				),
				'dynamic_content'  => 'text',
			),
			'button_two_telegram_msg'            => array(
				'label'            => sprintf( esc_html__( 'Message', 'dsm-supreme-modules-for-divi' ), '#2' ),
				'type'             => 'textarea',
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Enter the default body message of the Telegram for Button #1.', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'      => 'links',
				'sub_toggle'       => 'button_two_tab',
				'default_on_front' => '',
				'show_if'          => array(
					'button_two_url_type' => 'telegram',
				),
				'dynamic_content'  => 'text',
			),
			'button_two_url'                     => array(
				'label'           => esc_html__( 'Button #2 URL', 'dsm-supreme-modules-for-divi' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Enter the URL for the Button.', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'     => 'links',
				'sub_toggle'      => 'button_two_tab',
				'show_if'         => array(
					'button_two_url_type' => 'url',
				),
				'dynamic_content' => 'url',
			),
			'button_two_url_new_window'          => array(
				'label'            => esc_html__( 'Url Opens', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'off' => esc_html__( 'In The Same Window', 'dsm-supreme-modules-for-divi' ),
					'on'  => esc_html__( 'In The New Tab', 'dsm-supreme-modules-for-divi' ),
				),
				'toggle_slug'      => 'links',
				'sub_toggle'       => 'button_two_tab',
				'show_if'          => array(
					'button_two_url_type' => 'url',
				),
				'description'      => esc_html__( 'Here you can choose whether or not your link opens in a new window', 'dsm-supreme-modules-for-divi' ),
				'default_on_front' => 'off',
				'show_if_not'      => array(
					'button_two_image_popup' => 'on',
					'button_two_video_popup' => 'on',
				),
			),
			'button_two_download_file'           => array(
				'label'              => esc_html__( 'Download File', 'dsm-supreme-modules-for-divi' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'data_type'          => '',
				'upload_button_text' => esc_attr__( 'Upload a file', 'dsm-supreme-modules-for-divi' ),
				'choose_text'        => esc_attr__( 'Choose a file', 'dsm-supreme-modules-for-divi' ),
				'update_text'        => esc_attr__( 'Set As Download File', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'        => 'links',
				'sub_toggle'         => 'button_two_tab',
				'show_if'            => array(
					'button_two_url_type' => 'download',
				),
			),
			'button_two_image_popup'             => array(
				'label'            => esc_html__( 'Open as Image Lightbox', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'off' => esc_html__( 'No', 'dsm-supreme-modules-for-divi' ),
					'on'  => esc_html__( 'Yes', 'dsm-supreme-modules-for-divi' ),
				),
				'toggle_slug'      => 'links',
				'sub_toggle'       => 'button_two_tab',
				'show_if'          => array(
					'button_two_url_type' => 'url',
				),
				'description'      => esc_html__( 'Here you can choose whether or not the button should open in Lightbox. Note: if you select to open the button in Lightbox, url options below will be ignored.', 'dsm-supreme-modules-for-divi' ),
				'default_on_front' => 'off',
				'show_if_not'      => array(
					'button_two_video_popup' => 'on',
				),
			),
			'button_two_image_src'               => array(
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => esc_attr__( 'Upload an image', 'dsm-supreme-modules-for-divi' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'dsm-supreme-modules-for-divi' ),
				'update_text'        => esc_attr__( 'Set As Image', 'dsm-supreme-modules-for-divi' ),
				'hide_metadata'      => true,
				'description'        => esc_html__( 'Upload your desired image for Button One Image Lightbox, or type in the URL to the image you would like to display.', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'        => 'links',
				'sub_toggle'         => 'button_two_tab',
				'show_if'            => array(
					'button_two_url_type' => 'url',
				),
				'show_if'            => array(
					'button_two_image_popup' => 'on',
				),
				'dynamic_content'    => 'image',
			),
			'button_two_lightbox_close_color'    => array(
				'label'        => esc_html__( 'Close Color', 'dsm-supreme-modules-for-divi' ),
				'description'  => esc_html__( 'Here you can define a custom color for the lightbox close button.', 'dsm-supreme-modules-for-divi' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'default'      => 'rgba(255,255,255,0.2)',
				'tab_slug'     => 'advanced',
				'toggle_slug'  => 'lightbox',
				'sub_toggle'   => 'button_two_tab',
				'hover'        => 'tabs',
			),
			'button_two_lightbox_max_width'      => array(
				'label'          => esc_html__( 'Max Width', 'dsm-supreme-modules-for-divi' ),
				'description'    => esc_html__( 'Setting a maximum width will prevent your lightbox from ever surpassing the defined width value. Maximum width can be used in combination with the standard width setting. Maximum width supersedes the normal width value.', 'dsm-supreme-modules-for-divi' ),
				'type'           => 'range',
				'default'        => 'none',
				'default_tablet' => 'none',
				'default_unit'   => 'px',
				'allowed_values' => et_builder_get_acceptable_css_string_values( 'max-width' ),
				'range_settings' => array(
					'min'  => '100',
					'max'  => '1200',
					'step' => '1',
				),
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'lightbox',
				'sub_toggle'     => 'button_two_tab',
				'mobile_options' => true,
			),
			'button_two_video_popup'             => array(
				'label'            => esc_html__( 'Open as Video Lightbox', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'off' => esc_html__( 'No', 'dsm-supreme-modules-for-divi' ),
					'on'  => esc_html__( 'Yes', 'dsm-supreme-modules-for-divi' ),
				),
				'toggle_slug'      => 'links',
				'sub_toggle'       => 'button_two_tab',
				'description'      => esc_html__( 'Put the Video link on the Button #2 URL. Copy the video URL link and paste it here. Support: YouTube, Vimeo and Dailymotion.', 'dsm-supreme-modules-for-divi' ),
				'default_on_front' => 'off',
				'show_if'          => array(
					'button_two_url_type' => 'url',
				),
				'show_if_not'      => array(
					'button_two_image_popup' => 'on',
				),
			),
			'button_two_tooltip'                 => array(
				'label'            => esc_html__( 'Use Tooltip', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'off' => esc_html__( 'No', 'dsm-supreme-modules-for-divi' ),
					'on'  => esc_html__( 'Yes', 'dsm-supreme-modules-for-divi' ),
				),
				'toggle_slug'      => 'links',
				'sub_toggle'       => 'button_two_tab',
				'description'      => esc_html__( 'This will show a tooltip on your Button #2.', 'dsm-supreme-modules-for-divi' ),
				'default_on_front' => 'off',
			),
			'button_two_tooltip_content'         => array(
				'label'            => esc_html__( 'Tooltip Content', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'text',
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Enter the content for the your tooltip.', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'      => 'links',
				'sub_toggle'       => 'button_two_tab',
				'default_on_front' => 'Tooltip #2',
				'show_if'          => array(
					'button_two_tooltip' => 'on',
				),
				'dynamic_content'  => 'text',
			),
			'button_two_tooltip_arrow'           => array(
				'label'            => esc_html__( 'Show Arrow', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'off' => esc_html__( 'No', 'dsm-supreme-modules-for-divi' ),
					'on'  => esc_html__( 'Yes', 'dsm-supreme-modules-for-divi' ),
				),
				'toggle_slug'      => 'links',
				'sub_toggle'       => 'button_two_tab',
				'description'      => esc_html__( 'If enable, then an arrow will be added to the tooltip.', 'dsm-supreme-modules-for-divi' ),
				'default_on_front' => 'on',
				'show_if'          => array(
					'button_two_tooltip' => 'on',
				),
			),
			'button_two_tooltip_size'            => array(
				'label'            => esc_html__( 'Size', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'small'   => esc_html__( 'Small', 'dsm-supreme-modules-for-divi' ),
					'regular' => esc_html__( 'Regular', 'dsm-supreme-modules-for-divi' ),
					'large'   => esc_html__( 'Large', 'dsm-supreme-modules-for-divi' ),
				),
				'toggle_slug'      => 'links',
				'sub_toggle'       => 'button_two_tab',
				'default_on_front' => 'regular',
				'show_if'          => array(
					'button_two_tooltip' => 'on',
				),
				'description'      => esc_html__( 'The size of the tooltip.', 'dsm-supreme-modules-for-divi' ),
			),
			'button_two_tooltip_placement'       => array(
				'label'            => esc_html__( 'Placement', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'top'    => esc_html__( 'Top', 'dsm-supreme-modules-for-divi' ),
					'bottom' => esc_html__( 'Bottom', 'dsm-supreme-modules-for-divi' ),
					'left'   => esc_html__( 'Left', 'dsm-supreme-modules-for-divi' ),
					'right'  => esc_html__( 'Right', 'dsm-supreme-modules-for-divi' ),
				),
				'toggle_slug'      => 'links',
				'sub_toggle'       => 'button_two_tab',
				'default_on_front' => 'top',
				'show_if'          => array(
					'button_two_tooltip' => 'on',
				),
			),
			'button_two_tooltip_animation'       => array(
				'label'            => esc_html__( 'Animations', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => $dsm_animation_type_list,
				'default'          => 'fade',
				'default_on_front' => 'fade',
				'description'      => esc_html__( 'Here you can choose different types of animations for your tooltips.', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'      => 'links',
				'sub_toggle'       => 'button_two_tab',
				'show_if'          => array(
					'button_two_tooltip' => 'on',
				),
			),
			/*
			'button_two_tooltip_distance' => array(
				'label'             => esc_html__( 'Distance', 'dsm-supreme-modules-for-divi' ),
				'type'              => 'range',
				'option_category'   => 'layout',
				'mobile_options'  => false,
				'toggle_slug'      => 'links',
				'sub_toggle'  => 'button_two_tab',
				'default_unit'      => '',
				'default'           => '10',
				'responsive'      => false,
				'show_if' => array(
					'button_two_tooltip' => 'on',
				),
			),*/
			'button_two_tooltip_bg_color'        => array(
				'label'        => esc_html__( 'Background Color', 'dsm-supreme-modules-for-divi' ),
				'type'         => 'color-alpha',
				'custom_color' => true,
				'tab_slug'     => 'advanced',
				'toggle_slug'  => 'tooltip',
				'sub_toggle'   => 'button_two_tab',
				'show_if'      => array(
					'button_two_tooltip' => 'on',
				),
			),
			'button_alignment'                   => array(
				'label'           => esc_html__( 'Button Alignment', 'dsm-supreme-modules-for-divi' ),
				'type'            => 'text_align',
				'option_category' => 'configuration',
				'options'         => et_builder_get_text_orientation_options( array( 'justified' ) ),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'alignment',
				'description'     => esc_html__( 'Here you can define the alignment of Button', 'dsm-supreme-modules-for-divi' ),
				'mobile_options'  => true,
			),
			'separator_text'                     => array(
				'label'           => esc_html__( 'Separator Text', 'dsm-supreme-modules-for-divi' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input your desired seprator text, or leave blank for no separator text in between the both buttons.', 'dsm-supreme-modules-for-divi' ),
				'toggle_slug'     => 'separator',
				'dynamic_content' => 'text',
			),
			'fullwidth_separator_text_on_mobile' => array(
				'label'            => esc_html__( 'Make Separator Text Fullwidth On Mobile', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'off' => esc_html__( 'No', 'dsm-supreme-modules-for-divi' ),
					'on'  => esc_html__( 'Yes', 'dsm-supreme-modules-for-divi' ),
				),
				'toggle_slug'      => 'separator',
				'description'      => esc_html__( 'This will make the Separator Text as fullwidth instead of inline-block.', 'dsm-supreme-modules-for-divi' ),
				'default_on_front' => 'off',
				'show_if'          => array(
					'remove_separator_text_on_mobile' => 'off',
				),
			),
			'remove_separator_text_on_mobile'    => array(
				'label'            => esc_html__( 'Remove Separator Text On Mobile', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'off' => esc_html__( 'No', 'dsm-supreme-modules-for-divi' ),
					'on'  => esc_html__( 'Yes', 'dsm-supreme-modules-for-divi' ),
				),
				'toggle_slug'      => 'separator',
				'description'      => esc_html__( 'This will remove Separator Text on mobile devices.', 'dsm-supreme-modules-for-divi' ),
				'default_on_front' => 'off',
			),
			'separator_gap'                      => array(
				'label'           => esc_html__( 'Text Separator Gap', 'dsm-supreme-modules-for-divi' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'mobile_options'  => true,
				'toggle_slug'     => 'width',
				'default_unit'    => 'px',
				'default'         => '10px',
				'responsive'      => true,
			),
			'button_one_hover_animation'         => array(
				'label'            => esc_html__( 'Button Hover #1 Animation', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'dsm-none'                   => esc_html__( 'None', 'dsm-supreme-modules-for-divi' ),
					'dsm-grow'                   => esc_html__( 'Grow', 'dsm-supreme-modules-for-divi' ),
					'dsm-grow-rotate'            => esc_html__( 'Grow Rotate', 'dsm-supreme-modules-for-divi' ),
					'dsm-shrink'                 => esc_html__( 'Shrink', 'dsm-supreme-modules-for-divi' ),
					'dsm-pulse'                  => esc_html__( 'Pulse', 'dsm-supreme-modules-for-divi' ),
					'dsm-pulse-grow'             => esc_html__( 'Pulse Grow', 'dsm-supreme-modules-for-divi' ),
					'dsm-pulse-shrink'           => esc_html__( 'Pulse Shrink', 'dsm-supreme-modules-for-divi' ),
					'dsm-push'                   => esc_html__( 'Push', 'dsm-supreme-modules-for-divi' ),
					'dsm-pop'                    => esc_html__( 'Pop', 'dsm-supreme-modules-for-divi' ),
					'dsm-bounce-in'              => esc_html__( 'Bounce In', 'dsm-supreme-modules-for-divi' ),
					'dsm-bounce-out'             => esc_html__( 'Bounce Out', 'dsm-supreme-modules-for-divi' ),
					'dsm-rotate'                 => esc_html__( 'Rotate', 'dsm-supreme-modules-for-divi' ),
					'dsm-float'                  => esc_html__( 'Float', 'dsm-supreme-modules-for-divi' ),
					'dsm-sink'                   => esc_html__( 'Sink', 'dsm-supreme-modules-for-divi' ),
					'dsm-bob'                    => esc_html__( 'Bob', 'dsm-supreme-modules-for-divi' ),
					'dsm-hang'                   => esc_html__( 'Hang', 'dsm-supreme-modules-for-divi' ),
					'dsm-skew'                   => esc_html__( 'Skew', 'dsm-supreme-modules-for-divi' ),
					'dsm-skew-forward'           => esc_html__( 'Skew Forward', 'dsm-supreme-modules-for-divi' ),
					'dsm-skew-backward'          => esc_html__( 'Skew Backward', 'dsm-supreme-modules-for-divi' ),
					'dsm-wobble-vertical'        => esc_html__( 'Wobble Vertical', 'dsm-supreme-modules-for-divi' ),
					'dsm-wobble-horizontal'      => esc_html__( 'Wobble Horizontal', 'dsm-supreme-modules-for-divi' ),
					'dsm-wobble-to-bottom-right' => esc_html__( 'Wobble to Bottom Right', 'dsm-supreme-modules-for-divi' ),
					'dsm-wobble-to-top-right'    => esc_html__( 'Wobble to Top Right', 'dsm-supreme-modules-for-divi' ),
					'dsm-wobble-top'             => esc_html__( 'Wobble Top', 'dsm-supreme-modules-for-divi' ),
					'dsm-wobble-bottom'          => esc_html__( 'Wobble Bottom', 'dsm-supreme-modules-for-divi' ),
					'dsm-wobble-skew'            => esc_html__( 'Wobble Skew', 'dsm-supreme-modules-for-divi' ),
					'dsm-buzz'                   => esc_html__( 'Buzz', 'dsm-supreme-modules-for-divi' ),
					'dsm-buzz-out'               => esc_html__( 'Buzz Out', 'dsm-supreme-modules-for-divi' ),
					'dsm-forward'                => esc_html__( 'Forward', 'dsm-supreme-modules-for-divi' ),
					'dsm-backward'               => esc_html__( 'Backward', 'dsm-supreme-modules-for-divi' ),
				),
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'animation',
				'default_on_front' => 'dsm-none',
			),
			'button_one_icon_hover_animation'    => array(
				'label'            => esc_html__( 'Button Icon Hover #1 Animation', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'dsm-icon-none'         => esc_html__( 'None', 'dsm-supreme-modules-for-divi' ),
					'dsm-icon-back'         => esc_html__( 'Back', 'dsm-supreme-modules-for-divi' ),
					'dsm-icon-forward'      => esc_html__( 'Forward', 'dsm-supreme-modules-for-divi' ),
					'dsm-icon-down'         => esc_html__( 'Down', 'dsm-supreme-modules-for-divi' ),
					'dsm-icon-up'           => esc_html__( 'Up', 'dsm-supreme-modules-for-divi' ),
					'dsm-icon-spin'         => esc_html__( 'Spin', 'dsm-supreme-modules-for-divi' ),
					'dsm-icon-float-away'   => esc_html__( 'Float Away', 'dsm-supreme-modules-for-divi' ),
					'dsm-icon-sink-away'    => esc_html__( 'Sink Away', 'dsm-supreme-modules-for-divi' ),
					'dsm-icon-grow'         => esc_html__( 'Grow', 'dsm-supreme-modules-for-divi' ),
					'dsm-icon-shrink'       => esc_html__( 'Shrink', 'dsm-supreme-modules-for-divi' ),
					'dsm-icon-pulse'        => esc_html__( 'Pulse', 'dsm-supreme-modules-for-divi' ),
					'dsm-icon-pulse-grow'   => esc_html__( 'Pulse Grow', 'dsm-supreme-modules-for-divi' ),
					'dsm-icon-pulse-shrink' => esc_html__( 'Pulse Shrink', 'dsm-supreme-modules-for-divi' ),
					'dsm-icon-push'         => esc_html__( 'Push', 'dsm-supreme-modules-for-divi' ),
					'dsm-icon-pop'          => esc_html__( 'Pop', 'dsm-supreme-modules-for-divi' ),
					'dsm-icon-bounce'       => esc_html__( 'Bounce', 'dsm-supreme-modules-for-divi' ),
				),
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'animation',
				'default_on_front' => 'dsm-icon-none',
				'show_if'          => array(
					'button_one_on_hover' => 'off',
				),
			),
			'button_two_hover_animation'         => array(
				'label'            => esc_html__( 'Button Hover #2 Animation', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'dsm-none'                   => esc_html__( 'None', 'dsm-supreme-modules-for-divi' ),
					'dsm-grow'                   => esc_html__( 'Grow', 'dsm-supreme-modules-for-divi' ),
					'dsm-grow-rotate'            => esc_html__( 'Grow Rotate', 'dsm-supreme-modules-for-divi' ),
					'dsm-shrink'                 => esc_html__( 'Shrink', 'dsm-supreme-modules-for-divi' ),
					'dsm-pulse'                  => esc_html__( 'Pulse', 'dsm-supreme-modules-for-divi' ),
					'dsm-pulse-grow'             => esc_html__( 'Pulse Grow', 'dsm-supreme-modules-for-divi' ),
					'dsm-pulse-shrink'           => esc_html__( 'Pulse Shrink', 'dsm-supreme-modules-for-divi' ),
					'dsm-push'                   => esc_html__( 'Push', 'dsm-supreme-modules-for-divi' ),
					'dsm-pop'                    => esc_html__( 'Pop', 'dsm-supreme-modules-for-divi' ),
					'dsm-bounce-in'              => esc_html__( 'Bounce In', 'dsm-supreme-modules-for-divi' ),
					'dsm-bounce-out'             => esc_html__( 'Bounce Out', 'dsm-supreme-modules-for-divi' ),
					'dsm-rotate'                 => esc_html__( 'Rotate', 'dsm-supreme-modules-for-divi' ),
					'dsm-float'                  => esc_html__( 'Float', 'dsm-supreme-modules-for-divi' ),
					'dsm-sink'                   => esc_html__( 'Sink', 'dsm-supreme-modules-for-divi' ),
					'dsm-bob'                    => esc_html__( 'Bob', 'dsm-supreme-modules-for-divi' ),
					'dsm-hang'                   => esc_html__( 'Hang', 'dsm-supreme-modules-for-divi' ),
					'dsm-skew'                   => esc_html__( 'Skew', 'dsm-supreme-modules-for-divi' ),
					'dsm-skew-forward'           => esc_html__( 'Skew Forward', 'dsm-supreme-modules-for-divi' ),
					'dsm-skew-backward'          => esc_html__( 'Skew Backward', 'dsm-supreme-modules-for-divi' ),
					'dsm-wobble-vertical'        => esc_html__( 'Wobble Vertical', 'dsm-supreme-modules-for-divi' ),
					'dsm-wobble-horizontal'      => esc_html__( 'Wobble Horizontal', 'dsm-supreme-modules-for-divi' ),
					'dsm-wobble-to-bottom-right' => esc_html__( 'Wobble to Bottom Right', 'dsm-supreme-modules-for-divi' ),
					'dsm-wobble-to-top-right'    => esc_html__( 'Wobble to Top Right', 'dsm-supreme-modules-for-divi' ),
					'dsm-wobble-top'             => esc_html__( 'Wobble Top', 'dsm-supreme-modules-for-divi' ),
					'dsm-wobble-bottom'          => esc_html__( 'Wobble Bottom', 'dsm-supreme-modules-for-divi' ),
					'dsm-wobble-skew'            => esc_html__( 'Wobble Skew', 'dsm-supreme-modules-for-divi' ),
					'dsm-buzz'                   => esc_html__( 'Buzz', 'dsm-supreme-modules-for-divi' ),
					'dsm-buzz-out'               => esc_html__( 'Buzz Out', 'dsm-supreme-modules-for-divi' ),
					'dsm-forward'                => esc_html__( 'Forward', 'dsm-supreme-modules-for-divi' ),
					'dsm-backward'               => esc_html__( 'Backward', 'dsm-supreme-modules-for-divi' ),
				),
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'animation',
				'default_on_front' => 'dsm-none',
			),
			'button_two_icon_hover_animation'    => array(
				'label'            => esc_html__( 'Button Icon Hover #2 Animation', 'dsm-supreme-modules-for-divi' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'dsm-icon-none'         => esc_html__( 'None', 'dsm-supreme-modules-for-divi' ),
					'dsm-icon-back'         => esc_html__( 'Back', 'dsm-supreme-modules-for-divi' ),
					'dsm-icon-forward'      => esc_html__( 'Forward', 'dsm-supreme-modules-for-divi' ),
					'dsm-icon-down'         => esc_html__( 'Down', 'dsm-supreme-modules-for-divi' ),
					'dsm-icon-up'           => esc_html__( 'Up', 'dsm-supreme-modules-for-divi' ),
					'dsm-icon-spin'         => esc_html__( 'Spin', 'dsm-supreme-modules-for-divi' ),
					'dsm-icon-float-away'   => esc_html__( 'Float Away', 'dsm-supreme-modules-for-divi' ),
					'dsm-icon-sink-away'    => esc_html__( 'Sink Away', 'dsm-supreme-modules-for-divi' ),
					'dsm-icon-grow'         => esc_html__( 'Grow', 'dsm-supreme-modules-for-divi' ),
					'dsm-icon-shrink'       => esc_html__( 'Shrink', 'dsm-supreme-modules-for-divi' ),
					'dsm-icon-pulse'        => esc_html__( 'Pulse', 'dsm-supreme-modules-for-divi' ),
					'dsm-icon-pulse-grow'   => esc_html__( 'Pulse Grow', 'dsm-supreme-modules-for-divi' ),
					'dsm-icon-pulse-shrink' => esc_html__( 'Pulse Shrink', 'dsm-supreme-modules-for-divi' ),
					'dsm-icon-push'         => esc_html__( 'Push', 'dsm-supreme-modules-for-divi' ),
					'dsm-icon-pop'          => esc_html__( 'Pop', 'dsm-supreme-modules-for-divi' ),
					'dsm-icon-bounce'       => esc_html__( 'Bounce', 'dsm-supreme-modules-for-divi' ),
				),
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'animation',
				'default_on_front' => 'dsm-icon-none',
				'show_if'          => array(
					'button_two_on_hover' => 'off',
				),
			),
		);

		return $fields;
	}

	/**
	 * Get button alignment.
	 *
	 * @since 3.23 Add responsive support by adding device parameter.
	 *
	 * @param  string $device Current device name.
	 * @return string         Alignment value, rtl or not.
	 */
	public function get_button_alignment( $device = 'desktop' ) {
		$suffix           = 'desktop' !== $device ? "_{$device}" : '';
		$text_orientation = isset( $this->props[ "button_alignment{$suffix}" ] ) ? $this->props[ "button_alignment{$suffix}" ] : '';

		return et_pb_get_alignment( $text_orientation );
	}

	public function get_transition_fields_css_props() {
		return array();
	}

	function render( $attrs, $content, $render_slug ) {
		$button_one_text                        = $this->props['button_one_text'];
		$button_one_url                         = $this->props['button_one_url'];
		$button_one_url_type                    = $this->props['button_one_url_type'];
		$button_one_download_file               = $this->props['button_one_download_file'];
		$button_one_email_address               = $this->props['button_one_email_address'];
		$button_one_email_cc                    = $this->props['button_one_email_cc'];
		$button_one_email_bcc                   = $this->props['button_one_email_bcc'];
		$button_one_email_subject               = rawurlencode( $this->props['button_one_email_subject'] );
		$button_one_email_msg                   = rawurlencode( $this->props['button_one_email_msg'] );
		$button_one_phone_number                = $this->props['button_one_phone_number'];
		$button_one_sms_number                  = $this->props['button_one_sms_number'];
		$button_one_sms_body_text               = $this->props['button_one_sms_body_text'];
		$button_one_fb_messenger                = $this->props['button_one_fb_messenger'];
		$button_one_skype                       = $this->props['button_one_skype'];
		$button_one_skype_action_type           = $this->props['button_one_skype_action_type'];
		$button_one_whatsapp_number             = $this->props['button_one_whatsapp_number'];
		$button_one_whatsapp_msg                = rawurlencode( $this->props['button_one_whatsapp_msg'] );
		$button_one_telegram                    = $this->props['button_one_telegram'];
		$button_one_telegram_msg                = rawurlencode( $this->props['button_one_telegram_msg'] );
		$button_one_video_popup                 = $this->props['button_one_video_popup'];
		$button_one_image_popup                 = $this->props['button_one_image_popup'];
		$button_one_image_src                   = $this->props['button_one_image_src'];
		$hover                                  = et_pb_hover_options();
		$button_one_lightbox_max_width_values   = et_pb_responsive_options()->get_property_values( $this->props, 'button_one_lightbox_max_width' );
		$button_one_lightbox_close_color_values = et_pb_responsive_options()->get_property_values( $this->props, 'button_one_lightbox_close_color' );
		$button_one_tooltip                     = $this->props['button_one_tooltip'];
		$button_one_tooltip_content             = $this->props['button_one_tooltip_content'];
		$button_one_tooltip_placement           = $this->props['button_one_tooltip_placement'];
		$button_one_tooltip_size                = $this->props['button_one_tooltip_size'];
		$button_one_tooltip_arrow               = $this->props['button_one_tooltip_arrow'];
		$button_one_tooltip_animation           = $this->props['button_one_tooltip_animation'];
		// $button_one_tooltip_distance = floatval($this->props['button_one_tooltip_distance']);
		$button_one_tooltip_bg_color            = $this->props['button_one_tooltip_bg_color'];
		$button_one_rel                         = $this->props['button_one_rel'];
		$button_one_on_hover                    = $this->props['button_one_on_hover'];
		$button_one_icon_hover_animation        = $this->props['button_one_icon_hover_animation'];
		$button_two_text                        = $this->props['button_two_text'];
		$button_two_url                         = $this->props['button_two_url'];
		$button_two_url_type                    = $this->props['button_two_url_type'];
		$button_two_download_file               = $this->props['button_two_download_file'];
		$button_two_email_address               = $this->props['button_two_email_address'];
		$button_two_email_cc                    = $this->props['button_two_email_cc'];
		$button_two_email_bcc                   = $this->props['button_two_email_bcc'];
		$button_two_email_subject               = rawurlencode( $this->props['button_two_email_subject'] );
		$button_two_email_msg                   = rawurlencode( $this->props['button_two_email_msg'] );
		$button_two_phone_number                = $this->props['button_two_phone_number'];
		$button_two_sms_number                  = $this->props['button_two_sms_number'];
		$button_two_sms_body_text               = $this->props['button_two_sms_body_text'];
		$button_two_fb_messenger                = $this->props['button_two_fb_messenger'];
		$button_two_skype                       = $this->props['button_two_skype'];
		$button_two_skype_action_type           = $this->props['button_two_skype_action_type'];
		$button_two_whatsapp_number             = $this->props['button_two_whatsapp_number'];
		$button_two_whatsapp_msg                = rawurlencode( $this->props['button_two_whatsapp_msg'] );
		$button_two_telegram                    = $this->props['button_two_telegram'];
		$button_two_telegram_msg                = rawurlencode( $this->props['button_two_telegram_msg'] );
		$button_two_video_popup                 = $this->props['button_two_video_popup'];
		$button_two_image_popup                 = $this->props['button_two_image_popup'];
		$button_two_image_src                   = $this->props['button_two_image_src'];
		$button_two_lightbox_max_width_values   = et_pb_responsive_options()->get_property_values( $this->props, 'button_two_lightbox_max_width' );
		$button_two_lightbox_close_color_values = et_pb_responsive_options()->get_property_values( $this->props, 'button_two_lightbox_close_color' );
		$button_two_tooltip                     = $this->props['button_two_tooltip'];
		$button_two_tooltip_content             = $this->props['button_two_tooltip_content'];
		$button_two_tooltip_placement           = $this->props['button_two_tooltip_placement'];
		$button_two_tooltip_size                = $this->props['button_two_tooltip_size'];
		$button_two_tooltip_arrow               = $this->props['button_two_tooltip_arrow'];
		$button_two_tooltip_animation           = $this->props['button_two_tooltip_animation'];
		// $button_two_tooltip_distance = floatval($this->props['button_two_tooltip_distance']);
		$button_two_tooltip_bg_color        = $this->props['button_two_tooltip_bg_color'];
		$button_two_rel                     = $this->props['button_two_rel'];
		$button_two_on_hover                = $this->props['button_two_on_hover'];
		$button_two_icon_hover_animation    = $this->props['button_two_icon_hover_animation'];
		$background_layout                  = $this->props['background_layout'];
		$background_layout_hover            = et_pb_hover_options()->get_value( 'background_layout', $this->props, 'light' );
		$background_layout_hover_enabled    = et_pb_hover_options()->is_enabled( 'background_layout', $this->props );
		$background_layout_values           = et_pb_responsive_options()->get_property_values( $this->props, 'background_layout' );
		$background_layout_tablet           = isset( $background_layout_values['tablet'] ) ? $background_layout_values['tablet'] : '';
		$background_layout_phone            = isset( $background_layout_values['phone'] ) ? $background_layout_values['phone'] : '';
		$button_one_url_new_window          = $this->props['button_one_url_new_window'];
		$button_two_url_new_window          = $this->props['button_two_url_new_window'];
		$custom_icon_1                      = $this->props['button_one_icon'];
		$button_custom_1                    = $this->props['custom_button_one'];
		$custom_icon_2                      = $this->props['button_two_icon'];
		$button_custom_2                    = $this->props['custom_button_two'];
		$button_alignment                   = $this->get_button_alignment();
		$is_button_aligment_responsive      = et_pb_responsive_options()->is_responsive_enabled( $this->props, 'button_alignment' );
		$button_alignment_tablet            = $is_button_aligment_responsive ? $this->get_button_alignment( 'tablet' ) : '';
		$button_alignment_phone             = $is_button_aligment_responsive ? $this->get_button_alignment( 'phone' ) : '';
		$separator_text                     = $this->props['separator_text'];
		$separator_gap                      = $this->props['separator_gap'];
		$separator_gap_tablet               = $this->props['separator_gap_tablet'];
		$separator_gap_phone                = $this->props['separator_gap_phone'];
		$separator_gap_last_edited          = $this->props['separator_gap_last_edited'];
		$button_one_hover_animation         = $this->props['button_one_hover_animation'];
		$button_two_hover_animation         = $this->props['button_two_hover_animation'];
		$fullwidth_separator_text_on_mobile = $this->props['fullwidth_separator_text_on_mobile'];
		$remove_separator_text_on_mobile    = $this->props['remove_separator_text_on_mobile'];

		$button_one_url = trim( $button_one_url );
		$button_two_url = trim( $button_two_url );
		$button_one_id  = $this->props['button_one_id'];
		$button_one_css = $this->props['button_one_css'];
		$button_two_id  = $this->props['button_two_id'];
		$button_two_css = $this->props['button_two_css'];

		// Button Alignment.
		$button_alignments = array();
		if ( ! empty( $button_alignment ) ) {
			array_push( $button_alignments, sprintf( 'et_pb_button_alignment_%1$s', esc_attr( $button_alignment ) ) );
		}

		if ( ! empty( $button_alignment_tablet ) ) {
			array_push( $button_alignments, sprintf( 'et_pb_button_alignment_tablet_%1$s', esc_attr( $button_alignment_tablet ) ) );
		}

		if ( ! empty( $button_alignment_phone ) ) {
			array_push( $button_alignments, sprintf( 'et_pb_button_alignment_phone_%1$s', esc_attr( $button_alignment_phone ) ) );
		}

		$button_alignment_classes = join( ' ', $button_alignments );

		$separator_gap_responsive_active = et_pb_get_responsive_status( $separator_gap_last_edited );

		$separator_gap_values = array(
			'desktop' => $separator_gap,
			'tablet'  => $separator_gap_responsive_active ? $separator_gap_tablet : '',
			'phone'   => $separator_gap_responsive_active ? $separator_gap_phone : '',
		);

		et_pb_responsive_options()->generate_responsive_css( $separator_gap_values, '%%order_class%% .dsm-button-separator-text', 'margin-left', $render_slug );
		et_pb_responsive_options()->generate_responsive_css( $separator_gap_values, '%%order_class%% .dsm-button-separator-text', 'margin-right', $render_slug );

		if ( 'none' !== $this->props['button_one_lightbox_max_width'] ) {
			et_pb_responsive_options()->generate_responsive_css( $button_one_lightbox_max_width_values, '%%order_class%%.dsm_button_one_lightbox.dsm-lightbox-custom .mfp-content', 'max-width', $render_slug, '', 'max-width' );
		}

		if ( 'rgba(255,255,255,0.2)' !== $this->props['button_one_lightbox_close_color'] ) {
			et_pb_responsive_options()->generate_responsive_css( $button_one_lightbox_close_color_values, '%%order_class%%.dsm_button_one_lightbox.dsm-lightbox-custom .mfp-close', 'color', $render_slug, '', 'color' );
		}

		if ( $hover->is_enabled( 'button_one_lightbox_close_color', $this->props ) && $hover->get_value( 'button_one_lightbox_close_color', $this->props ) ) {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%%.dsm_button_one_lightbox.dsm-lightbox-custom .mfp-close:hover',
					'declaration' => sprintf(
						'color: %1$s !important;',
						esc_html( $hover->get_value( 'button_one_lightbox_close_color', $this->props ) )
					),
				)
			);
		}

		if ( 'none' !== $this->props['button_two_lightbox_max_width'] ) {
			et_pb_responsive_options()->generate_responsive_css( $button_two_lightbox_max_width_values, '%%order_class%%.dsm_button_two_lightbox.dsm-lightbox-custom .mfp-content', 'max-width', $render_slug, '', 'max-width' );
		}

		if ( 'rgba(255,255,255,0.2)' !== $this->props['button_two_lightbox_close_color'] ) {
			et_pb_responsive_options()->generate_responsive_css( $button_two_lightbox_close_color_values, '%%order_class%%.dsm_button_two_lightbox.dsm-lightbox-custom .mfp-close', 'color', $render_slug, '', 'color' );
		}

		if ( $hover->is_enabled( 'button_two_lightbox_close_color', $this->props ) && $hover->get_value( 'button_two_lightbox_close_color', $this->props ) ) {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%%.dsm_button_two_lightbox.dsm-lightbox-custom .mfp-close:hover',
					'declaration' => sprintf(
						'color: %1$s !important;',
						esc_html( $hover->get_value( 'button_two_lightbox_close_color', $this->props ) )
					),
				)
			);
		}

		if ( '' !== $button_one_tooltip_bg_color ) {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%% .dsm_button_one.dsm-tooltip:after',
					'declaration' => sprintf(
						'background-color: %1$s;',
						esc_html( $button_one_tooltip_bg_color )
					),
				)
			);
			if ( 'top' == $button_one_tooltip_placement ) {
				ET_Builder_Element::set_style(
					$render_slug,
					array(
						'selector'    => '%%order_class%% .dsm_button_one.dsm-tooltip:before',
						'declaration' => sprintf(
							'border-top-color: %1$s;',
							esc_html( $button_one_tooltip_bg_color )
						),
					)
				);
			} elseif ( 'bottom' == $button_one_tooltip_placement ) {
				ET_Builder_Element::set_style(
					$render_slug,
					array(
						'selector'    => '%%order_class%% .dsm_button_one.dsm-tooltip:before',
						'declaration' => sprintf(
							'border-bottom-color: %1$s;',
							esc_html( $button_one_tooltip_bg_color )
						),
					)
				);
			} elseif ( 'left' == $button_one_tooltip_placement ) {
				ET_Builder_Element::set_style(
					$render_slug,
					array(
						'selector'    => '%%order_class%% .dsm_button_one.dsm-tooltip:before',
						'declaration' => sprintf(
							'border-left-color: %1$s;',
							esc_html( $button_one_tooltip_bg_color )
						),
					)
				);
			} elseif ( 'right' == $button_one_tooltip_placement ) {
				ET_Builder_Element::set_style(
					$render_slug,
					array(
						'selector'    => '%%order_class%% .dsm_button_one.dsm-tooltip:before',
						'declaration' => sprintf(
							'border-right-color: %1$s;',
							esc_html( $button_one_tooltip_bg_color )
						),
					)
				);
			}
		}
		// Nothing to output if neither Button Text nor Button URL defined
		if ( '' !== $button_two_tooltip_bg_color ) {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%% .dsm_button_two.dsm-tooltip:after',
					'declaration' => sprintf(
						'background-color: %1$s;',
						esc_html( $button_two_tooltip_bg_color )
					),
				)
			);
			if ( 'top' == $button_two_tooltip_placement ) {
				ET_Builder_Element::set_style(
					$render_slug,
					array(
						'selector'    => '%%order_class%% .dsm_button_two.dsm-tooltip:before',
						'declaration' => sprintf(
							'border-top-color: %1$s !important;',
							esc_html( $button_two_tooltip_bg_color )
						),
					)
				);
			} elseif ( 'bottom' == $button_two_tooltip_placement ) {
				ET_Builder_Element::set_style(
					$render_slug,
					array(
						'selector'    => '%%order_class%% .dsm_button_two.dsm-tooltip:before',
						'declaration' => sprintf(
							'border-bottom-color: %1$s !important;',
							esc_html( $button_two_tooltip_bg_color )
						),
					)
				);
			} elseif ( 'left' == $button_two_tooltip_placement ) {
				ET_Builder_Element::set_style(
					$render_slug,
					array(
						'selector'    => '%%order_class%% .dsm_button_two.dsm-tooltip:before',
						'declaration' => sprintf(
							'border-left-color: %1$s !important;',
							esc_html( $button_two_tooltip_bg_color )
						),
					)
				);
			} elseif ( 'right' == $button_two_tooltip_placement ) {
				ET_Builder_Element::set_style(
					$render_slug,
					array(
						'selector'    => '%%order_class%% .dsm_button_two.dsm-tooltip:before',
						'declaration' => sprintf(
							'border-right-color: %1$s !important;',
							esc_html( $button_two_tooltip_bg_color )
						),
					)
				);
			}
		}

		$add_class  = '';
		$add_class .= " et_pb_bg_layout_{$background_layout}";
		if ( ! empty( $background_layout_tablet ) ) {
			$add_class .= " et_pb_bg_layout_{$background_layout_tablet}_tablet";
		}
		if ( ! empty( $background_layout_phone ) ) {
			$add_class .= " et_pb_bg_layout_{$background_layout_phone}_phone";
		}

		$link_one_url = '';

		if ( 'url' === $button_one_url_type ) {
			$link_one_url .= sprintf(
				'%1$s',
				'off' !== $button_one_image_popup ? esc_url( $button_one_image_src ) : esc_url( $button_one_url )
			);
		} elseif ( 'download' === $button_one_url_type ) {
			$link_one_url .= sprintf(
				'%1$s',
				esc_url( $button_one_download_file )
			);
		} elseif ( 'email' === $button_one_url_type ) {
			$link_one_url .= sprintf(
				'mailto:%1$s?%2$s%3$s%4$s%5$s',
				'' !== $button_one_email_address ? esc_attr( $button_one_email_address ) : '',
				'' !== $button_one_email_cc ? esc_attr( "&cc=$button_one_email_cc" ) : '',
				'' !== $button_one_email_bcc ? esc_attr( "&bcc=$button_one_email_bcc" ) : '',
				'' !== $button_one_email_subject ? esc_attr( '&subject=' . $button_one_email_subject ) : '',
				'' !== $button_one_email_msg ? esc_attr( '&body=' . $button_one_email_msg ) : ''
			);
		} elseif ( 'phone' === $button_one_url_type ) {
			$link_one_url .= sprintf(
				'tel:%1$s',
				'' !== $button_one_phone_number ? esc_attr( $button_one_phone_number ) : ''
			);
		} elseif ( 'sms' === $button_one_url_type ) {
			$link_one_url .= sprintf(
				'sms:%1$s%2$s',
				'' !== $button_one_sms_number ? esc_attr( $button_one_sms_number ) : '',
				'' !== $button_one_sms_body_text ? '?&body=' . rawurlencode( $button_one_sms_body_text ) : ''
			);
		} elseif ( 'fb_messenger' === $button_one_url_type ) {
			$link_one_url .= sprintf(
				'https://m.me/%1$s',
				'' !== $button_one_fb_messenger ? esc_attr( $button_one_fb_messenger ) : ''
			);
		} elseif ( 'skype' === $button_one_url_type ) {
			$link_one_url .= sprintf(
				'skype:%1$s%2$s',
				'' !== $button_one_skype ? esc_attr( $button_one_skype ) : '',
				esc_attr( '?' . $button_one_skype_action_type )
			);
		} elseif ( 'whatsapp' === $button_one_url_type ) {
			$link_one_url .= sprintf(
				'https://wa.me/%1$s%2$s',
				'' !== $button_one_whatsapp_number ? esc_attr( $button_one_whatsapp_number ) : '',
				'' !== $button_one_whatsapp_msg ? esc_attr( '?text=' . $button_one_whatsapp_msg ) : ''
			);
		} elseif ( 'telegram' === $button_one_url_type ) {
			$link_one_url .= sprintf(
				'https://t.me/%1$s%2$s',
				'' !== $button_one_telegram ? esc_attr( $button_one_telegram ) : '',
				'' !== $button_one_telegram_msg ? esc_attr( '?start=' . $button_one_telegram_msg ) : ''
			);
		}

		$link_two_url = '';

		if ( 'url' === $button_two_url_type ) {
			$link_two_url .= sprintf(
				'%1$s',
				'off' !== $button_two_image_popup ? esc_url( $button_two_image_src ) : esc_url( $button_two_url )
			);
		} elseif ( 'download' === $button_two_url_type ) {
			$link_two_url .= sprintf(
				'%1$s',
				esc_url( $button_two_download_file )
			);
		} elseif ( 'email' === $button_two_url_type ) {
			$link_two_url .= sprintf(
				'mailto:%1$s?%2$s%3$s%4$s%5$s',
				'' !== $button_two_email_address ? esc_attr( $button_two_email_address ) : '',
				'' !== $button_two_email_cc ? esc_attr( "&cc=$button_two_email_cc" ) : '',
				'' !== $button_two_email_bcc ? esc_attr( "&bcc=$button_two_email_bcc" ) : '',
				'' !== $button_two_email_subject ? esc_attr( '&subject=' . $button_two_email_subject ) : '',
				'' !== $button_two_email_msg ? esc_attr( '&body=' . $button_two_email_msg ) : ''
			);
		} elseif ( 'phone' === $button_two_url_type ) {
			$link_two_url .= sprintf(
				'tel:%1$s',
				'' !== $button_two_phone_number ? esc_attr( $button_two_phone_number ) : ''
			);
		} elseif ( 'sms' === $button_two_url_type ) {
			$link_two_url .= sprintf(
				'sms:%1$s%2$s',
				'' !== $button_two_sms_number ? esc_attr( $button_two_sms_number ) : '',
				'' !== $button_two_sms_body_text ? '?&body=' . rawurlencode( $button_two_sms_body_text ) : ''
			);
		} elseif ( 'fb_messenger' === $button_two_url_type ) {
			$link_two_url .= sprintf(
				'https://m.me/%1$s',
				'' !== $button_two_fb_messenger ? esc_attr( $button_two_fb_messenger ) : ''
			);
		} elseif ( 'skype' === $button_two_url_type ) {
			$link_two_url .= sprintf(
				'skype:%1$s%2$s',
				'' !== $button_two_skype ? esc_attr( $button_two_skype ) : '',
				esc_attr( '?' . $button_two_skype_action_type )
			);
		} elseif ( 'whatsapp' === $button_two_url_type ) {
			$link_two_url .= sprintf(
				'https://wa.me/%1$s%2$s',
				'' !== $button_two_whatsapp_number ? esc_attr( $button_two_whatsapp_number ) : '',
				'' !== $button_two_whatsapp_msg ? esc_attr( '?text=' . $button_two_whatsapp_msg ) : ''
			);
		} elseif ( 'telegram' === $button_two_url_type ) {
			$link_two_url .= sprintf(
				'https://t.me/%1$s%2$s',
				'' !== $button_two_telegram ? esc_attr( $button_two_telegram ) : '',
				'' !== $button_two_telegram_msg ? esc_attr( '?start=' . $button_two_telegram_msg ) : ''
			);
		}

		$button_output = '';

		if ( '' !== $button_one_text ) {
			if ( 'on' === $button_one_tooltip ) {
				$button_output .= sprintf(
					'<div class="dsm_button_one dsm-tooltip%2$s dsm-tooltip-%3$s"%1$s>',
					( 'off' !== $button_one_tooltip ? sprintf(
						' data-dsm-tooltip="%1$s" data-dsm-tooltip-placement="%3$s" data-dsm-tooltip-size="%2$s"',
						esc_attr( $button_one_tooltip_content ),
						esc_attr( $button_one_tooltip_size ),
						esc_attr( $button_one_tooltip_placement )
					) : '' ),
					'off' !== $button_one_tooltip_arrow ? '' : ' dsm-tooltip-hide-arrow',
					esc_attr( $button_one_tooltip_animation )
				);
			}
			$button_output .= sprintf(
				'<a%15$s class="et_pb_button et_pb_button_one%5$s%8$s%9$s%10$s%12$s %7$s %14$s" %6$s href="%1$s"%3$s%4$s data-dsm-lightbox-id="dsm_button_one_lightbox %11$s"%13$s>%2$s</a>',
				$link_one_url,
				esc_html( $button_one_text ),
				( 'on' === $button_one_url_new_window ? ' target="_blank"' : '' ),
				'' !== $custom_icon_1 && 'on' === $button_custom_1 ? sprintf(
					' data-icon="%1$s"',
					esc_attr( et_pb_process_font_icon( $custom_icon_1 ) )
				) : '',
				'' !== $custom_icon_1 && 'on' === $button_custom_1 ? ' et_pb_custom_button_icon' : '',
				$this->get_rel_attributes( $button_one_rel ),
				esc_attr( $button_one_hover_animation ),
				'off' !== $button_one_video_popup ? ' dsm-video-lightbox' : '',
				'off' !== $button_one_image_popup ? ' dsm-image-lightbox' : '',
				'off' === $button_one_on_hover && 'dsm-icon-none' !== $button_one_icon_hover_animation ? esc_attr( " {$button_one_icon_hover_animation}" ) : '',
				ET_Builder_Element::get_module_order_class( $render_slug ),
				$add_class,
				( 'download' === $button_one_url_type ? ' download' : '' ),
				isset( $button_one_css ) && '' !== $button_one_css ? esc_attr( $button_one_css ) : '',
				isset( $button_one_id ) && '' !== $button_one_id ? sprintf( ' id="%1$s"', esc_attr( $button_one_id ) ) : ''
			);
			if ( 'on' === $button_one_tooltip ) {
				$button_output .= '</div>';
			}
		}

		if ( '' !== $separator_text ) {
			$button_output .= '<span class="dsm-button-separator-text">' . et_core_esc_previously( $separator_text ) . '</span>';
		}

		if ( '' !== $button_two_text ) {
			if ( 'on' === $button_two_tooltip ) {
				$button_output .= sprintf(
					'<div class="dsm_button_two dsm-tooltip%2$s dsm-tooltip-%3$s"%1$s>',
					( 'off' !== $button_two_tooltip ? sprintf(
						' data-dsm-tooltip="%1$s" data-dsm-tooltip-placement="%3$s" data-dsm-tooltip-size="%2$s"',
						esc_attr( $button_two_tooltip_content ),
						esc_attr( $button_two_tooltip_size ),
						esc_attr( $button_two_tooltip_placement )
					) : '' ),
					'off' !== $button_two_tooltip_arrow ? '' : ' dsm-tooltip-hide-arrow',
					esc_attr( $button_two_tooltip_animation )
				);
			}
			$button_output .= sprintf(
				'<a%15$s class="et_pb_button et_pb_button_two%5$s%8$s%9$s%10$s%12$s %7$s %14$s" %6$s href="%1$s"%3$s%4$s data-dsm-lightbox-id="dsm_button_two_lightbox %11$s"%13$s>%2$s</a>',
				$link_two_url,
				esc_html( $button_two_text ),
				( 'on' === $button_two_url_new_window ? ' target="_blank"' : '' ),
				'' !== $custom_icon_2 && 'on' === $button_custom_2 ? sprintf(
					' data-icon="%1$s"',
					esc_attr( et_pb_process_font_icon( $custom_icon_2 ) )
				) : '',
				'' !== $custom_icon_2 && 'on' === $button_custom_2 ? ' et_pb_custom_button_icon' : '',
				$this->get_rel_attributes( $button_two_rel ),
				esc_attr( $button_two_hover_animation ),
				'off' !== $button_two_video_popup ? ' dsm-video-lightbox' : '',
				'off' !== $button_two_image_popup ? ' dsm-image-lightbox' : '',
				'off' === $button_two_on_hover && 'dsm-icon-none' !== $button_two_icon_hover_animation ? esc_attr( " {$button_two_icon_hover_animation}" ) : '',
				ET_Builder_Element::get_module_order_class( $render_slug ),
				$add_class,
				( 'download' === $button_two_url_type ? ' download' : '' ),
				isset( $button_two_css ) && '' !== $button_two_css ? esc_attr( $button_two_css ) : '',
				isset( $button_two_id ) && '' !== $button_two_id ? sprintf( ' id="%1$s"', esc_attr( $button_two_id ) ) : ''
			);
			if ( 'on' === $button_two_tooltip ) {
				$button_output .= '</div>';
			}
		}

		$data_background_layout       = '';
		$data_background_layout_hover = '';
		if ( $background_layout_hover_enabled ) {
			$data_background_layout       = sprintf(
				' data-background-layout="%1$s"',
				esc_attr( $background_layout )
			);
			$data_background_layout_hover = sprintf(
				' data-background-layout-hover="%1$s"',
				esc_attr( $background_layout_hover )
			);
		}

		// Module classnames
		$this->add_classname( "et_pb_bg_layout_{$background_layout}" );
		if ( ! empty( $background_layout_tablet ) ) {
			$this->add_classname( "et_pb_bg_layout_{$background_layout_tablet}_tablet" );
		}
		if ( ! empty( $background_layout_phone ) ) {
			$this->add_classname( "et_pb_bg_layout_{$background_layout_phone}_phone" );
		}

		if ( 'on' === $button_one_image_popup || 'on' === $button_two_image_popup ) {
			if ( ! wp_script_is( 'dsm-magnific-popup-image', 'enqueued' ) ) {
				wp_enqueue_script( 'dsm-magnific-popup-image' );
			}
		}

		if ( 'on' === $button_one_video_popup || 'on' === $button_two_video_popup ) {
			if ( ! wp_script_is( 'dsm-magnific-popup-video', 'enqueued' ) ) {
				wp_enqueue_script( 'dsm-magnific-popup-video' );
			}
		}

		if ( isset( get_option( 'dsm_settings_misc' )['dsm_dynamic_assets'] ) && ! empty( get_option( 'dsm_settings_misc' )['dsm_dynamic_assets'] ) && 'on' === get_option( 'dsm_settings_misc' )['dsm_dynamic_assets'] ) {
			if ( isset( get_option( 'dsm_settings_misc' )['dsm_dynamic_assets_compatibility'] ) && ! empty( get_option( 'dsm_settings_misc' )['dsm_dynamic_assets'] ) && 'on' === get_option( 'dsm_settings_misc' )['dsm_dynamic_assets_compatibility'] ) {
				add_filter( 'et_global_assets_list', array( $this, 'dsm_load_magnific_popup_assets' ), 10, 3 );
				add_filter( 'et_late_global_assets_list', array( $this, 'dsm_load_magnific_popup_assets' ), 10, 3 );
				wp_enqueue_style( 'dsm-button-hover', plugin_dir_url( __DIR__ ) . 'Buttons/hover.css', array(), DSM_VERSION, 'all' );
				wp_enqueue_style( 'dsm-button', plugin_dir_url( __DIR__ ) . 'Buttons/style.css', array(), DSM_VERSION, 'all' );
			} else {
				add_filter( 'et_global_assets_list', array( $this, 'dsm_load_required_divi_assets' ), 10, 3 );
				add_filter( 'et_late_global_assets_list', array( $this, 'dsm_load_required_divi_assets' ), 10, 3 );
			}
		}
		// Render module output.
		$output = sprintf(
			'<div class="et_pb_button_module_wrappers dsm_button_%3$s_wrapper %2$s et_pb_module%4$s%5$s%8$s"%6$s%7$s>
				%1$s
			</div>',
			$button_output,
			esc_attr( $button_alignment_classes ),
			$this->render_count(),
			( '' !== $separator_text && 'off' !== $remove_separator_text_on_mobile ? ' dsm-button-separator-remove' : '' ),
			( '' !== $separator_text && 'off' !== $fullwidth_separator_text_on_mobile ? ' dsm-button-separator-fullwidth' : '' ),
			et_core_esc_previously( $data_background_layout ),
			et_core_esc_previously( $data_background_layout_hover ),
			( '' !== $separator_text ? ' dsm-button-seperator' : '' )
		);

		return $output;
	}

	/**
	 * Force load global styles.
	 *
	 * @param array $assets_list Current global assets on the list.
	 *
	 * @return array
	 */
	public function dsm_load_magnific_popup_assets( $assets_list, $assets_args, $instance ) {
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

		return $assets_list;
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

		// Buttons.
		if ( ! isset( $assets_list['dsm_button'] ) ) {
			$assets_list['dsm_button'] = array(
				'css' => plugin_dir_url( __DIR__ ) . 'Buttons/style.css',
			);
		}
		if ( ! isset( $assets_list['dsm_button_hover'] ) ) {
			$assets_list['dsm_button_hover'] = array(
				'css' => plugin_dir_url( __DIR__ ) . 'Buttons/hover.css',
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

new DSM_Button();
