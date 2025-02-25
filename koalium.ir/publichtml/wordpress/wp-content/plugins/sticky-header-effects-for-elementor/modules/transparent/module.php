<?php
namespace SheHeader\Modules\Transparent;

use Elementor;
use Elementor\Plugin;
use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use SheHeader\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Module_Base {

	public function __construct() {
		parent::__construct();

		$this->add_actions();
	}

	public function get_name() {
		return 'transparent';
	}

	public function register_controls( Controls_Stack $element ) {
		$element->start_controls_section(
			'section_sticky_header_effect',
			[
				'label' => __( 'Sticky Header Effects', 'she-header' ),
				'tab' => Controls_Manager::TAB_ADVANCED,
			]
		);

		$element->add_control(
			'transparent',
			[
				'label' => __( 'Enable', 'she-header' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'On', 'she-header' ),
				'label_off' => __( 'Off', 'bew-header' ),
				'return_value' => 'yes',
				'default' => '',
				'frontend_available' => true,
				'prefix_class'  => 'she-header-'
			]
		);

		// $element->add_control(
		// 	'donate_notice',
		// 	[
		// 		'type' => Controls_Manager::ALERT,
		// 		'alert_type' => 'alert',
		// 		'heading' => esc_html__( 'Donations', 'she-header' ),
		// 		'content' => esc_html__( 'If you have enjoyed using the plugin please consider ' , 'she-header' ) . ' <br><a href="https://www.paypal.me/StickyHeaderEffects">' . esc_html__( 'DONATING HERE', 'she-header' ) . '</a>' . ' <br><br>' . esc_html__( 'Instead of realeasing a paid Pro verion I will be adding a few Pro features to this free version. However, I will only be providing minimal support from now on.', 'she-header' ) ,
		// 		'condition' => [
		// 			'transparent!' => '',
		// 		],
		// 	]
		// );
		
		$element->add_control(
			'upgrade_notice',
			[
				'type' => Controls_Manager::NOTICE,
				'notice_type' => 'info',
				'dismissible' => true,
				'heading' => esc_html__( 'New FREE Pro Features', 'she-header' ),
				'content' => esc_html__( 'Disable Fully Transparent Background, Background Type, Custom Menu Toggle Button, Bottom Shadow, Blur Background settings' , 'she-header' ) ,
				'condition' => [
					'transparent!' => '',
				],
			]
		);

		$element->add_control(
			'sticky_header_notice',
			[
				'raw' => __( 'IMPORTANT: This plugin does NOT control the sticky position of the header. Please use the above Motion Effects tab sticky options to make the header sticky', 'she-header' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				'condition' => [
					'transparent!' => '',
				],
			]
		);

		$element->add_control(
			'transparent_on',
			[
				'label' => __( 'Enable On', 'she-header' ),
				'type' => Controls_Manager::SELECT2,
				'multiple' => true,
				'label_block' => 'true',
				'default' => [ 'desktop', 'tablet', 'mobile' ],
				'options' => [
					'desktop' => __( 'Desktop', 'she-header' ),
					'tablet' => __( 'Tablet', 'she-header' ),
					'mobile' => __( 'Mobile', 'she-header' ),
				],
				'condition' => [
					'transparent!' => ''
				],
				'render_type' => 'none',
				'frontend_available' => true,
			]
		);

		$element->add_responsive_control(
			'scroll_distance',
			[
				'label' => __( 'Scroll Distance (px)', 'she-header' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 60,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'size_units' => [ 'px'],
				'description' => __( 'Choose the scroll distance to enable Sticky Header Effects', 'she-header' ),
				'frontend_available' => true,
				'condition' => [
					'transparent!' => '',
				],
			]
		);

		$element->add_control(
			'settings_notice',
			[
				'raw' => __( 'Remember: The settings below will not be applied until the page is scrolled the distance set above', 'she-header' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor',
				'condition' => [
					'transparent!' => '',
					],
			]
		);

		$element->add_control(
			'popover-toggle',
			[
				'label' => esc_html__( 'After Sticky', 'she-header' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'label_off' => esc_html__( 'Default', 'she-header' ),
				'label_on' => esc_html__( 'Custom', 'she-header' ),
				'return_value' => 'yes',
				'condition' => [
					'transparent!' => '',
				],
			]
		);

		$element->start_popover();
		$element->add_control(
			'she_sticky_header',
			array(
				'label'     => __( 'After Sticky', 'she-header' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'after',
			)
		);
		$element->add_responsive_control(
			'she_offset_top',
			[
				'label' => esc_html__( 'Offset', 'she-header' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'frontend_available' => true,
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'condition' => [
					'transparent!' => '',
				],
			]
		);
		// $element->add_responsive_control(
		// 	'she_width',
		// 	[
		// 		'label' => esc_html__( 'Width', 'she-header' ),
		// 		'type' => Controls_Manager::SLIDER,
		// 		'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
		// 		'range' => [
		// 			'px' => [
		// 				'min' => 0,
		// 				'max' => 500,
		// 				'step' => 5,
		// 			],
		// 			'%' => [
		// 				'min' => 0,
		// 				'max' => 100,
		// 			],
		// 		],
		// 		'frontend_available' => true,
		// 		'default' => [
		// 			'unit' => '%',
		// 			'size' => 100,
		// 		],
		// 		'condition' => [
		// 			'transparent!' => '',
		// 		],
		// 	]
		// );
		$element->add_responsive_control(
			'she_padding',
			[
				'label' => esc_html__( 'Padding', 'she-header' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'default' => [
					'top' => 0,
					'right' => '',
					'bottom' => 0,
					'left' => '',
					'unit' => 'px',
					'isLinked' => true,
				],
				'frontend_available' => true,
				'condition' => [
					'transparent!' => '',
				],
			]
		);
		$element->add_control(
			'after_sticky_header_notice',
			[
				'raw' => __( 'Adjust properties once Status will be changed to Sticky.', 'she-header' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				'condition' => [
					'transparent!' => '',
				],
			]
		);
		$element->end_popover();

		$element->add_control(
			'transparent_header_show',
			[
				'label' => __('Transparent Header', 'she-header'),
				'type' => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'label_on' => __('On', 'she-header'),
				'label_off' => __('Off', 'bew-header'),
				'return_value' => 'yes',
				'default' => '',
				'frontend_available' => true,
				'prefix_class'  => 'she-header-transparent-',
				'condition' => [
					'transparent!' => '',
				],
				'selectors' => [
					'.she-header-transparent-yes' => 'background-color: rgba(0,0,0,0) !important;',
					'.she-header-transparent-yes' => 'position:absolute;',
				],
				'description' => __('Sets the header position to "absolute" so negative margins are not needed', 'she-header'),
			]
		);

		$element->add_control(
			'transparent_note',
			[
				'raw' => __('This will make the header overlap the main page so extra spacing at the top of sections may be needed<br><br>
				Does NOT work on/override elementor sticky settings', 'she-header'),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-control-notice elementor-control-notice-type-info',
				'condition' => [
					'transparent!' => '',
					'transparent_header_show' => 'yes',
				],
			]
		);

		$element->add_control(
			'background_show',
			[
				'label' => __( 'Background Color', 'she-header' ),
				'type' => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'label_on' => __( 'On', 'she-header' ),
				'label_off' => __( 'Off', 'bew-header' ),
				'return_value' => 'yes',
				'default' => '',
				'frontend_available' => true,
				'condition' => [
					'transparent!' => '',
				],
				'description' => __( 'Choose what color to change the background to after scrolling', 'she-header' ),
			]
		);
		
		$element->add_control(
			'background_type',
			[
				'label' => __('Background Type', 'she-header'),
				'type' => Controls_Manager::CHOOSE,
				'condition' => [
					'background_show' => 'yes',
					'transparent!' => '',
				],
				'label_block' => false,
				'render_type' => 'ui',
				'options' => [
					'classic' => [
						'title' => __('Classic', 'she-header'),
						'icon' => 'eicon-paint-brush',
					],
					'gradient' => [
						'title' => __('Gradient', 'she-header'),
						'icon' => 'eicon-barcode',
					],
				],
				'default' => 'classic'
			]
		);

		$element->add_control(
			'background',
			[
				'label' => __( 'Color', 'she-header' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
				    'background_show' => 'yes',
					'transparent!' => '',
				],
				'render_type' => 'none',
				'frontend_available' => true,
			]
		);
		
		$element->add_control(
			'gradient_transition_notice',
			[
				'raw' => __( 'Please note that gradients will not be transitioned', 'she-header' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor',
				'condition' => [
					'background_type' => ['gradient'],
					'transparent!' => '',
				],
			 ]
			);
		
		$element->add_control(
			'color_stop',
			[
				'label' => __('Location', 'she-header'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['%'],
				'default' => [
					'unit' => '%',
					'size' => 0,
				],
				'render_type' => 'ui',
				'condition' => [
					'background_show' => 'yes',
					'transparent!' => '',
					'background_type' => ['gradient'],
				],
				'of_type' => 'gradient',
			]
		);

		$element->add_control(
			'color_b',
			[
				'label' => __('Second Color', 'she-header'),
				'type' => Controls_Manager::COLOR,
				'default' => '#f2295b',
				'render_type' => 'ui',
				'condition' => [
					'background_show' => 'yes',
					'transparent!' => '',
					'background_type' => ['gradient'],
				],
				'of_type' => 'gradient',
			]
		);

		$element->add_control(
			'color_b_stop',
			[
				'label' => __('Location', 'she-header'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['%'],
				'default' => [
					'unit' => '%',
					'size' => 100,
				],
				'render_type' => 'ui',
				'condition' => [
					'background_show' => 'yes',
					'transparent!' => '',
					'background_type' => ['gradient'],
				],
				'of_type' => 'gradient',
			]
		);

		$element->add_control(
			'gradient_type',
			[
				'label' => __('Type', 'she-header'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'linear' => __('Linear', 'she-header'),
					'radial' => __('Radial', 'she-header'),
				],
				'default' => 'linear',
				'render_type' => 'ui',
				'condition' => [
					'background_show' => 'yes',
					'transparent!' => '',
					'background_type' => ['gradient'],
				],
				'of_type' => 'gradient',
			]
		);

		$element->add_control(
			'gradient_angle',
			[
				'label' => __('Angle', 'she-header'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['deg'],
				'default' => [
					'unit' => 'deg',
					'size' => 180,
				],
				'range' => [
					'deg' => [
						'step' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}}.she-header' => 'background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{background.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}})',
				],
				'condition' => [
					'background_show' => 'yes',
					'transparent!' => '',
					'background_type' => ['gradient'],
					'gradient_type' => 'linear',
				],
				'of_type' => 'gradient',
			]
		);

		$element->add_control(
			'gradient_position',
			[
				'label' => __('Position', 'she-header'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'center center' => __('Center Center', 'she-header'),
					'center left' => __('Center Left', 'she-header'),
					'center right' => __('Center Right', 'she-header'),
					'top center' => __('Top Center', 'she-header'),
					'top left' => __('Top Left', 'she-header'),
					'top right' => __('Top Right', 'she-header'),
					'bottom center' => __('Bottom Center', 'she-header'),
					'bottom left' => __('Bottom Left', 'she-header'),
					'bottom right' => __('Bottom Right', 'she-header'),
				],
				'default' => 'center center',
				'selectors' => [
					'{{WRAPPER}}.she-header' => 'background-color: transparent; background-image: radial-gradient(at {{VALUE}}, {{background.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}})',
				],
				'condition' => [
					'background_show' => 'yes',
					'transparent!' => '',
					'background_type' => ['gradient'],
					'gradient_type' => 'radial',
				],
				'of_type' => 'gradient',
			]
		);
		
		$element->add_control(
		'mobile_menu_toggle_animation',
		[
			'label' => __('Custom Menu Toggle Button', 'she-header'),
			'type' => Controls_Manager::SWITCHER,
			'separator' => 'before',
			'label_on' => __('On', 'she-header'),
			'label_off' => __('Off', 'she-header'),
			'return_value' => 'yes',
			'default' => '',
			'frontend_available' => true,
			'condition' => [
				'transparent!' => '',
			],
			'description' => __('Customize and add animation to the Elementor menu icon<br>*Only supports Elementor nav menu', 'she-header'),
			'selectors' => [
				'.she-header-yes .elementor-menu-toggle:before,
					.she-header-yes .elementor-menu-toggle:after,
					.she-header-yes .elementor-menu-toggle i:after' => 'content: "";
					position: absolute;
					background: currentColor;
					top: 50%;
					left: 50%;',

				'.she-header-yes .elementor-menu-toggle' => 'position: relative;
					transition: color 0.4s ease-in-out, background-color 0.4s ease-in-out;',

				'.she-header-yes .elementor-menu-toggle.elementor-active:before' => 'transform: translate(-50%,-50%) rotate(-45deg);',

				'.she-header-yes .elementor-menu-toggle.elementor-active:after' => 'transform: translate(-50%,-50%) rotate(45deg);',

				'.she-header-yes .elementor-menu-toggle i:after' => 'transform: translate(-50%,-50%);',

				'.she-header-yes .elementor-menu-toggle i:before,
					.she-header-yes .elementor-menu-toggle.elementor-active i:after' => 'opacity: 0;',
			],
		]
	);

	$element->add_control(
		'mobile_menu_toggle_note',
		[
			'raw' => __('This option ONLY works on the WordPress menu widget hamburger icon.<br><br>Tip: Set the nav menu toggle button size first. All of these settings are scaled off of that size', 'she-header'),
			'type' => Controls_Manager::RAW_HTML,
			'content_classes' => 'elementor-control-notice elementor-control-notice-type-info',
			'condition' => [
				'mobile_menu_toggle_animation' => 'yes',
				'transparent!' => '',
			],
		]
	);

	$element->add_control(
		'mobile_menu_toggle_type',
		[
			'label' => __('Toggle Button Type', 'she-header'),
			'type' => Controls_Manager::CHOOSE,
			'default' => 'lines',
			'options' => [
				'lines' => [
					'title' => __('Lines', 'she-header'),
					'icon' => 'eicon-menu-bar',

				],
				'dots' => [
					'title' => __('Dots/Squares', 'she-header'),
					'icon' => 'eicon-ellipsis-v',
				],
			],
			'condition' => [
				'mobile_menu_toggle_animation' => 'yes',
				'transparent!' => '',
			],
		]
	);

	$element->add_control(
		'lines_note',
		[
			'raw' => __('<b>Lines</b><br>Separately adjustable line weight and width', 'she-header'),
			'type' => Controls_Manager::RAW_HTML,
			'content_classes' => 'elementor-descriptor',
			'condition' => [
				'mobile_menu_toggle_animation' => 'yes',
				'transparent!' => '',
			],
		]
	);

	$element->add_control(
		'dots_note',
		[
			'raw' => __('<b>Dots/Squares</b><br>Line weight and width are equal with an adjustable width for the active(close button) lines', 'she-header'),
			'type' => Controls_Manager::RAW_HTML,
			'content_classes' => 'elementor-descriptor',
			'condition' => [
				'mobile_menu_toggle_animation' => 'yes',
				'transparent!' => '',
			],
		]
	);

	$element->add_control(
		'mobile_menu_toggle_only_two',
		[
			'label' => __('Only 2', 'she-header'),
			'type' => Controls_Manager::SWITCHER,
			'label_on' => __('On', 'she-header'),
			'label_off' => __('Off', 'she-header'),
			'return_value' => 'yes',
			'default' => '',
			'frontend_available' => true,
			'condition' => [
				'mobile_menu_toggle_animation' => 'yes',
				'transparent!' => '',
			],
			'description' => __('If 3 is too many, try only 2', 'she-header'),
			'selectors' => [
				'.she-header-yes .elementor-menu-toggle i:after' => 'display: none;',
			],
		]
	);

	$element->add_control(
		'mobile_menu_toggle_weight',
		[
			'label' => __('Weight', 'she-header'),
			'type' => Controls_Manager::SLIDER,
			'size_units' => ['em', 'rem', '%', 'px'],
			'range' => [
				'em' => [
					'min' => 0,
					'max' => 1,
					'step' => 0.01,
				],
				'rem' => [
					'min' => 0,
					'max' => 2,
					'step' => 0.01,
				],
				'%' => [
					'min' => 0,
					'max' => 33,
					'step' => 0.1,
				],
				'px' => [
					'min' => 0,
					'max' => 50,
					'step' => 1,
				],
			],
			'default' => [
				'size' => 0.12,
				'unit' => 'em',
			],
			'condition' => [
				'mobile_menu_toggle_animation' => 'yes',
				'transparent!' => '',
			],
			'frontend_available' => true,
			'selectors' => [
				'.she-header-yes .elementor-menu-toggle:before,
					.she-header-yes .elementor-menu-toggle:after,
					.she-header-yes .elementor-menu-toggle i:after' => '
					height: {{SIZE}}{{UNIT}};',
			],
		]
	);

	$element->add_control(
		'mobile_menu_toggle_width',
		[
			'label' => __('Width', 'she-header'),
			'type' => Controls_Manager::SLIDER,
			'size_units' => ['em', 'rem', '%', 'px'],
			'range' => [
				'em' => [
					'min' => 0,
					'max' => 2,
					'step' => 0.01,
				],
				'rem' => [
					'min' => 0,
					'max' => 5,
					'step' => 0.1,
				],
				'%' => [
					'min' => 0,
					'max' => 100,
					'step' => 1,
				],
				'px' => [
					'min' => 0,
					'max' => 100,
					'step' => 1,
				],
			],
			'default' => [
				'size' => 1,
				'unit' => 'em',
			],
			'condition' => [
				'mobile_menu_toggle_animation' => 'yes',
				'mobile_menu_toggle_type' => ['lines'],
				'transparent!' => '',
			],
			'frontend_available' => true,
			'selectors' => [
				'.she-header-yes .elementor-menu-toggle:before,
					.she-header-yes .elementor-menu-toggle:after,
					.she-header-yes .elementor-menu-toggle i:after' => '
					width: {{SIZE}}{{UNIT}};',
			],
		]
	);

	$element->add_control(
		'mobile_menu_toggle_active_width',
		[
			'label' => __('Active Width(Close Button)', 'she-header'),
			'type' => Controls_Manager::SLIDER,
			'size_units' => ['em', 'rem', '%', 'px'],
			'range' => [
				'em' => [
					'min' => 0,
					'max' => 2,
					'step' => 0.01,
				],
				'rem' => [
					'min' => 0,
					'max' => 5,
					'step' => 0.1,
				],
				'%' => [
					'min' => 0,
					'max' => 100,
					'step' => 1,
				],
				'px' => [
					'min' => 0,
					'max' => 100,
					'step' => 1,
				],
			],
			'default' => [
				'size' => 1,
				'unit' => 'em',
			],
			'condition' => [
				'mobile_menu_toggle_animation' => 'yes',
				'mobile_menu_toggle_type' => ['dots'],
				'transparent!' => '',
			],
			'frontend_available' => true,
			'selectors' => [
				'.she-header-yes .elementor-menu-toggle:before,
					.she-header-yes .elementor-menu-toggle:after,
					.she-header-yes .elementor-menu-toggle i:after' => '
					height: {{mobile_menu_toggle_weight.SIZE}}{{mobile_menu_toggle_weight.UNIT}};
					width: {{mobile_menu_toggle_weight.SIZE}}{{mobile_menu_toggle_weight.UNIT}};
					transition: transform 0.3s cubic-bezier(0.28, 0.55, 0.385, 1.65) 0.3s, width 0.3s cubic-bezier(0.28, 0.55, 0.385, 1.65) !important;',

				'.she-header-yes .elementor-menu-toggle.elementor-active:before,
					.she-header-yes .elementor-menu-toggle.elementor-active:after,
					.she-header-yes .elementor-menu-toggle.elementor-active i:after' => 'width: {{SIZE}}{{UNIT}} !important;
					transition: transform 0.3s cubic-bezier(0.28, 0.55, 0.385, 1.65), width 0.3s cubic-bezier(0.28, 0.55, 0.385, 1.65) 0.3s !important;',
			],
		]
	);

	$element->add_control(
		'mobile_menu_toggle_gap',
		[
			'label' => __('Gap', 'she-header'),
			'type' => Controls_Manager::SLIDER,
			'size_units' => ['em', 'rem', '%', 'px'],
			'range' => [
				'em' => [
					'min' => 0,
					'max' => 1,
					'step' => 0.01,
				],
				'rem' => [
					'min' => 0,
					'max' => 2,
					'step' => 0.01,
				],
				'%' => [
					'min' => 0,
					'max' => 300,
					'step' => 1,
				],
				'px' => [
					'min' => 0,
					'max' => 50,
					'step' => 1,
				],
			],
			'default' => [
				'size' => 0.12,
				'unit' => 'em',
			],
			'condition' => [
				'mobile_menu_toggle_animation' => 'yes',
				'transparent!' => '',
			],
			'frontend_available' => true,
			'selectors' => [
				'.she-header-yes .elementor-menu-toggle:before,
					.she-header-yes .elementor-menu-toggle:after,
					.she-header-yes .elementor-menu-toggle i:after' => '
					transform: translate(-50%,calc(-50% + {{SIZE}}{{UNIT}} * 2));',
				'.she-header-yes .elementor-menu-toggle:after' => 'transform: translate(-50%,calc(-50% - {{SIZE}}{{UNIT}} * 2));',
			],
		]
	);

	$element->add_control(
		'mobile_menu_toggle_radius',
		[
			'label' => __('Radius', 'she-header'),
			'type' => Controls_Manager::SLIDER,
			'size_units' => ['em', 'rem', '%', 'px'],
			'range' => [
				'em' => [
					'min' => 0,
					'max' => 1,
					'step' => 0.01,
				],
				'rem' => [
					'min' => 0,
					'max' => 2,
					'step' => 0.01,
				],
				'%' => [
					'min' => 0,
					'max' => 100,
					'step' => 1,
				],
				'px' => [
					'min' => 0,
					'max' => 100,
					'step' => 1,
				],
			],
			'default' => [
				'size' => 0.12,
				'unit' => 'em',
			],
			'condition' => [
				'mobile_menu_toggle_animation' => 'yes',
				'transparent!' => '',
			],
			'frontend_available' => true,
			'selectors' => [
				'.she-header-yes .elementor-menu-toggle:before,
					.she-header-yes .elementor-menu-toggle:after,
					.she-header-yes .elementor-menu-toggle i:after' => '
					border-radius: {{SIZE}}{{UNIT}};',
			],
		]
	);

		$element->add_control(
			'bottom_border',
			[
				'label' => __( 'Bottom Border', 'she-header' ),
				'type' => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'label_on' => __( 'On', 'she-header' ),
				'label_off' => __( 'Off', 'she-header' ),
				'return_value' => 'yes',
				'default' => '',
				'frontend_available' => true,
				'condition' => [
					'transparent!' => '',
				],
				'description' => __( 'Choose bottom border size and color', 'she-header' ),
			]
		);


		$element->add_control(
			'custom_bottom_border_color',
			[
				'label' => __( 'Color', 'she-header' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
				    'bottom_border' => 'yes',
					'transparent!' => '',
				],
				'render_type' => 'none',
				'frontend_available' => true,
			]
		);

		$element->add_responsive_control(
			'custom_bottom_border_width',
			[
				'label' => __( 'Bottom Border Thickness (px)', 'she-header' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ 'px'],
				'description' => __( 'Note: A border size(even 0px) must be set on the header for the transition to work both ways', 'she-header' ),
				'condition' => [
				    'bottom_border' => 'yes',
					'transparent!' => '',
				],
				'frontend_available' => true,
			]
		);
		
		$element->add_control(
			'bottom_shadow',
			[
				'label' => __('Bottom Shadow', 'she-header'),
				'type' => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'label_on' => __('On', 'she-header'),
				'label_off' => __('Off', 'she-header'),
				'return_value' => 'yes',
				'default' => '',
				'frontend_available' => true,
				'condition' => [
					'transparent!' => '',
				],
				'description' => __('Choose bottom shadow options after scrolling', 'she-header'),
				'selectors' => [
					'body:not(.elementor-editor-active) .she-header-yes' => 'box-shadow: 0 0 0 0 rgb(0 0 0 / 0%); clip-path: inset(0 0 -100vh 0);',
					'body:not(.elementor-editor-active) .she-header-yes.she-header' => 'box-shadow: 0 {{bottom_shadow_vertical.SIZE}}{{bottom_shadow_vertical.UNIT}} {{bottom_shadow_blur.SIZE}}{{bottom_shadow_blur.UNIT}} {{bottom_shadow_spread.SIZE}}{{bottom_shadow_spread.UNIT}} {{bottom_shadow_color.VALUE}}; clip-path: inset(0 0 -100vh 0);',
				],
			]
		);

		$element->add_control(
			'bottom_shadow_color',
			[
				'label' => __('Color', 'she-header'),
				'type' => Controls_Manager::COLOR,
				'default' => 'rgba(0, 0, 0, 0.15)',
				'condition' => [
					'bottom_shadow' => 'yes',
					'transparent!' => '',
				],
				'render_type' => 'none',
				'frontend_available' => true,
			]
		);

		$element->add_control(
			'bottom_shadow_vertical',
			[
				'label' => __('Vertical', 'she-header'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => ['px'],
				'condition' => [
					'bottom_shadow' => 'yes',
					'transparent!' => '',
				],
				'frontend_available' => true,
			]
		);

		$element->add_control(
			'bottom_shadow_blur',
			[
				'label' => __('Blur', 'she-header'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 30,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => ['px'],
				'condition' => [
					'bottom_shadow' => 'yes',
					'transparent!' => '',
				],
				'frontend_available' => true,
			]
		);

		$element->add_control(
			'bottom_shadow_spread',
			[
				'label' => __('Spread', 'she-header'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => ['px'],
				'condition' => [
					'bottom_shadow' => 'yes',
					'transparent!' => '',
				],
				'frontend_available' => true,
			]
		);

		$element->add_control(
			'shrink_header',
			[
				'label' => __( 'Shrink Header', 'she-header' ),
				'type' => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'label_on' => __( 'On', 'she-header' ),
				'label_off' => __( 'Off', 'she-header' ),
				'return_value' => 'yes',
				'default' => '',
				'frontend_available' => true,
				'description' => __( 'Choose header height after scrolling', 'she-header' ),
				'condition' => [
					'transparent!' => '',
				],
			]
		);

		$element->add_responsive_control(
			'custom_height_header',
			[
				'label' => __( 'Header Height (px)', 'she-header' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 70,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'size_units' => [ 'px'],
				'description' => __( 'Remember: The header cannot shrink smaller than the elements inside of it', 'she-header' ),
				'condition' => [
				   'shrink_header' => 'yes',
					'transparent!' => '',
				],
				'frontend_available' => true,
			]
		);

$element->add_control(
			'shrink_header_logo',
			[
				'label' => __('Shrink Logo', 'she-header'),
				'type' => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'label_on' => __('On', 'she-header'),
				'label_off' => __('Off', 'she-header'),
				'return_value' => 'yes',
				'default' => '',
				'frontend_available' => true,
				'description' => __('Resize logo after scrolling', 'she-header'),
				'condition' => [
					'transparent!' => '',
				],
			]
		);

		$element->add_responsive_control(
			'custom_height_header_logo',
			[
				'label' => __( 'Logo Height (%)', 'she-header' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 100,
				],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ '%'],
				'condition' => [
				    'shrink_header_logo' => 'yes',
					'transparent!' => '',
				],
				'frontend_available' => true,
				// 'description' => __('<b>Depricated:</b> Responsive sizes only load on page refresh, not window resize. Please migrate to using the settings below', 'she-header'),
			]
		);

// ---------------------------------- LOGO COLOR TOGGLE

     $element->add_control(
		'change_logo_color',
		[
			'label' => __( 'Change Logo Color', 'she-header' ),
			'type' => Controls_Manager::SWITCHER,
			'separator' => 'before',
			'label_on' => __( 'On', 'she-header' ),
			'label_off' => __( 'Off', 'she-header' ),
			'return_value' => 'yes',
			'default' => '',
			'frontend_available' => true,
			'description' => __( 'Change the logo image color before or after the user reaches the scroll distance set above', 'she-header' ),
			'condition' => [
				'transparent!' => '',
			],
		]
	);

// ---------------------------------- LOGO COLOR NOTICE

$element->add_control(
'logo_color_notice',
[
	'raw' => __( 'Please select <b>only 1 option</b> for each tab', 'she-header' ),
	'type' => Controls_Manager::RAW_HTML,
	'content_classes' => 'elementor-descriptor',
	'condition' => [
		'change_logo_color' => 'yes',
		'transparent!' => '',
	],
 ]
);

// ---------------------------------- LOGO COLOR TABS

  $element->start_controls_tabs(
		 'logo_color_tabs'
	 );

// ---------------------------------- LOGO BEFORE TAB

  $element->start_controls_tab(
		 'before_tab',
		 [
	'label' => __( 'Before scrolling', 'she-header' ),
		 'condition' => [
				'change_logo_color' => 'yes',
				'transparent!' => '',
			],
		]
	);

// ---------------------------------- LOGO WHITE BEFORE

	$element->add_control(
		'logo_color_white_before',
		[
			'label' => __( 'White Logo', 'she-header' ),
			'type' => Controls_Manager::SWITCHER,
			'label_on' => __( 'On', 'she-header' ),
			'label_off' => __( 'Off', 'she-header' ),
			'return_value' => 'yes',
			'default' => '',
			'frontend_available' => true,
			'description' => __( 'Change the logo to white', 'she-header' ),
			'prefix_class'  => 'she-header-change-logo-color-',
			'condition' => [
				'change_logo_color' => 'yes',
				'transparent!' => '',
			],
			'selectors' => [
				'{{WRAPPER}}.she-header-yes:not(.she-header) .elementor-widget-theme-site-logo:not(.elementor-widget-n-menu .elementor-widget-theme-site-logo), 
				{{WRAPPER}}.she-header-yes:not(.she-header) .elementor-widget-image:not(.elementor-widget-n-menu .elementor-widget-image), 
				{{WRAPPER}}.she-header-yes:not(.she-header) .logo' => '-webkit-filter: brightness(0) invert(1); filter: brightness(0) invert(1);',
				'{{WRAPPER}}.she-header-yes:not(.she-header) .elementor-widget-n-menu .elementor-widget-image, 
				{{WRAPPER}}.she-header-yes:not(.she-header) .not-logo' => '-webkit-filter: none; filter: none;'
			],
		]
	);

// ---------------------------------- LOGO BLACK BEFORE

 $element->add_control(
		'logo_color_black_before',
		[
			'label' => __( 'Black Logo', 'she-header' ),
			'type' => Controls_Manager::SWITCHER,
			'label_on' => __( 'On', 'she-header' ),
			'label_off' => __( 'Off', 'she-header' ),
			'return_value' => 'yes',
			'default' => '',
			'frontend_available' => true,
			'description' => __( 'Change the logo to black', 'she-header' ),
			'prefix_class'  => 'she-header-change-logo-color-',
			'condition' => [
				'change_logo_color' => 'yes',
				'transparent!' => '',
			],
			'selectors' => [
				'{{WRAPPER}}.she-header-yes:not(.she-header) .elementor-widget-theme-site-logo,
				{{WRAPPER}}.she-header-yes:not(.she-header) .elementor-widget-image,
				{{WRAPPER}}.she-header-yes:not(.she-header) .logo' => '-webkit-filter: brightness(0) invert(0); filter: brightness(0) invert(0);',
				'{{WRAPPER}}.she-header-yes:not(.she-header) .elementor-widget-n-menu .elementor-widget-image, 
				{{WRAPPER}}.she-header-yes:not(.she-header) .not-logo' => '-webkit-filter: none; filter: none;'
			],
		]
	);

  $element->end_controls_tab();

// ---------------------------------- LOGO AFTER TAB

  $element->start_controls_tab(
		 'after_tab',
		 [
	'label' => __( 'After Scrolling', 'she-header' ),
		 'condition' => [
				'change_logo_color' => 'yes',
				'transparent!' => '',
			],
		]
	);

// ---------------------------------- LOGO WHITE AFTER

	$element->add_control(
		'logo_color_white_after',
		[
			'label' => __( 'White Logo', 'she-header' ),
			'type' => Controls_Manager::SWITCHER,
			'label_on' => __( 'On', 'she-header' ),
			'label_off' => __( 'Off', 'she-header' ),
			'return_value' => 'yes',
			'default' => '',
			'frontend_available' => true,
			'description' => __( 'Change the logo to white', 'she-header' ),
			'prefix_class'  => 'she-header-change-logo-color-',
			'condition' => [
				'change_logo_color' => 'yes',
				'transparent!' => '',
			],
			'selectors' => [
				'{{WRAPPER}}.she-header .elementor-widget-theme-site-logo,
				{{WRAPPER}}.she-header .elementor-widget-image,
				{{WRAPPER}}.she-header .logo' => '-webkit-filter: brightness(0) invert(1); filter: brightness(0) invert(1);',
				'{{WRAPPER}}.she-header .elementor-widget-n-menu .elementor-widget-image, 
				{{WRAPPER}}.she-header .not-logo' => '-webkit-filter: none; filter: none;'
			],
		]
	);

// ---------------------------------- LOGO BLACK AFTER

 $element->add_control(
		'logo_color_black_after',
		[
			'label' => __( 'Black Logo', 'she-header' ),
			'type' => Controls_Manager::SWITCHER,
			'label_on' => __( 'On', 'she-header' ),
			'label_off' => __( 'Off', 'she-header' ),
			'return_value' => 'yes',
			'default' => '',
			'frontend_available' => true,
			'description' => __( 'Change the logo to black', 'she-header' ),
			'prefix_class'  => 'she-header-change-logo-color-',
			'condition' => [
				'change_logo_color' => 'yes',
				'transparent!' => '',
			],
			'selectors' => [
				'{{WRAPPER}}.she-header .elementor-widget-theme-site-logo,
				{{WRAPPER}}.she-header .elementor-widget-image,
				{{WRAPPER}}.she-header .logo' => '-webkit-filter: brightness(0) invert(0); filter: brightness(0) invert(0);',
				'{{WRAPPER}}.she-header .elementor-widget-n-menu .elementor-widget-image, 
				{{WRAPPER}}.she-header .not-logo' => '-webkit-filter: none; filter: none;'
			],
		]
	);

// ---------------------------------- LOGO FULL COLOR AFTER

 $element->add_control(
		'logo_color_full_after',
		[
			'label' => __( 'Full Color Logo', 'she-header' ),
			'type' => Controls_Manager::SWITCHER,
			'label_on' => __( 'On', 'she-header' ),
			'label_off' => __( 'Off', 'she-header' ),
			'return_value' => 'yes',
			'default' => '',
			'frontend_available' => true,
			'description' => __( 'Removes all filters to allow a full color logo image', 'she-header' ),
			'prefix_class'  => 'she-header-change-logo-color-',
			'condition' => [
				'change_logo_color' => 'yes',
				'transparent!' => '',
			],
			'selectors' => [
				'{{WRAPPER}}.she-header .elementor-widget-theme-site-logo,
				{{WRAPPER}}.she-header .elementor-widget-image,
				{{WRAPPER}}.she-header .logo' => '-webkit-filter: brightness(1) invert(0); filter: brightness(1) invert(0);',
				'{{WRAPPER}}.she-header .elementor-widget-n-menu .elementor-widget-image, 
				{{WRAPPER}}.she-header .not-logo' => '-webkit-filter: none; filter: none;'
			],
		]
	);

	$element->end_controls_tab();
	$element->end_controls_tabs();
	
		$element->add_control(
			'blur_bg',
			[
				'label' => __('Blur Background', 'she-header'),
				'type' => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'label_on' => __('On', 'she-header'),
				'label_off' => __('Off', 'she-header'),
				'return_value' => 'yes',
				'default' => '',
				'frontend_available' => true,
				'condition' => [

					'transparent!' => '',
				],
				'description' => __('Add a modern blur effect to a semi-transparent header background color after scrolling', 'she-header'),
				'selectors' => [
					'{{WRAPPER}}.she-header' => 'backdrop-filter: blur(20px) saturate(180%); -webkit-backdrop-filter: blur(20px) saturate(180%);',
					'{{WRAPPER}}.she-header' => 'backdrop-filter: blur({{blur_bg_blur_amount.SIZE}}{{blur_bg_blur_amount.UNIT}}) saturate({{blur_bg_saturate_amount.SIZE}}) !important; -webkit-backdrop-filter: blur({{blur_bg_blur_amount.SIZE}}{{blur_bg_blur_amount.UNIT}}) saturate({{blur_bg_saturate_amount.SIZE}}) !important;',
				],
			]
		);
		
		$element->add_control(
			'blur_bg_note',
			[
				'raw' => __('Tip: Low saturation works best with no background color<br><br>DEFAULTS: Blur 20px, Saturation 1.80', 'she-header'),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor',
				'condition' => [
					'transparent!' => '',
					'blur_bg' => 'yes',
				],
			]
		);

		$element->add_control(
			'blur_bg_blur_amount',
			[
				'label' => __('Blur Amount (Px)', 'she-header'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
					'size' => 20,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'size_units' => ['px'],
				'condition' => [
					'transparent!' => '',
					'blur_bg' => 'yes',
				],
				'description' => __('', 'she-header'),
				'frontend_available' => true,
			]
		);

		$element->add_control(
			'blur_bg_saturate_amount',
			[
				'label' => __('Saturation Amount', 'she-header'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 1.8,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 10,
						'step' => 0.1,
					],
				],
				'condition' => [
					'transparent!' => '',
					'blur_bg' => 'yes',
				],
				'description' => __('', 'she-header'),
				'frontend_available' => true,
			]
		);

		$element->add_control(
			'hide_header',
			[
				'label' => __( 'Hide header on scroll down', 'she-header' ),
				'type' => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'label_on' => __( 'On', 'she-header' ),
				'label_off' => __( 'Off', 'she-header' ),
				'return_value' => 'yes',
				'default' => '',
				'frontend_available' => true,
				'description' => __( 'Hides the header if scrolling down, and shows header if scrolling up', 'she-header' ),
				'prefix_class'  => 'she-header-hide-on-scroll-',
				'condition' => [
					'transparent!' => '',
				],
			]
		);

		$element->add_control(
			'hide_header_notice',
			[
				'raw' => __('WARNING: This might break section/container entrance animations', 'she-header'),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition' => [
					'hide_header' => 'yes',
					'transparent!' => '',
				],
			]
		);

		$element->add_responsive_control(
			'scroll_distance_hide_header',
			[
				'label' => __( 'Scroll Distance (px)', 'she-header' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 500,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'size_units' => [ 'px'],
				'description' => __( 'Choose the scroll distance to start hiding the header', 'she-header' ),
				'frontend_available' => true,
				'condition' => [
					'hide_header' => 'yes',
					'transparent!' => '',
				],
			]
		);

		$element->end_controls_section();
	}

	private function add_actions()
	{
		if (!function_exists('is_plugin_active')) {

			include_once(ABSPATH . 'wp-admin/includes/plugin.php');
		}

		// add She on sections
		if (is_plugin_active('elementor/elementor.php')) {
			add_action('elementor/element/section/section_effects/after_section_end', [$this, 'register_controls']);
		}

		add_action('elementor/frontend/after_enqueue_styles', [$this, 'enqueue_styles']);
		if (Elementor\Plugin::instance()->editor->is_edit_mode()) {
		} else {
			add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
		}

		// add She on containers
		if (is_plugin_active('elementor/elementor.php')) {
			add_action('elementor/element/container/section_effects/after_section_end', [$this, 'register_controls']);
		}
	}

	public function enqueue_styles() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style(
			'she-header-style',
			SHE_HEADER_ASSETS_URL  . 'css/she-header-style' . '.css',
			[],
			SHE_HEADER_VERSION
		);

	}

	public function enqueue_scripts() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script(
			'she-header',
			SHE_HEADER_URL . 'assets/js/she-header.js',
			[
				'jquery',
			],
			SHE_HEADER_VERSION,
			false
		);
	}


}