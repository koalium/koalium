<?php
class SGPBConfigDataHelper
{
	public static $customPostType;
	public static $allCustomPosts = array();

	public static function getPostTypeData($args = array())
	{
		$query = self::getQueryDataByArgs($args);

		$posts = array();
		foreach ($query->posts as $post) {
			$posts[$post->ID] = $post->post_title;
		}

		return $posts;
	}

	public static function getQueryDataByArgs($args = array())
	{
		$defaultArgs = array(
			'offset'           => '',
			'orderby'          => 'date',
			'order'            => 'DESC',
			'post_status'      => 'publish',
			'suppress_filters' => false,
			'post_type'        => 'post',
			'posts_per_page'   => 1000
		);
		$args = wp_parse_args($args, $defaultArgs);		
		$query = new WP_Query($args);

		return $query;
	}

	/**
	 * this method is used for to get all other post types
	 * that may created by another plugins or theme or website owner!
	 *
	 * example: download from EDD, product from Woocommerce!
	 */
	public static function getAllCustomPosts()
	{
		$args = array(
			'public' => true,
			'_builtin' => false
		);

		$allCustomPosts = get_post_types($args);

		if (isset($allCustomPosts[SG_POPUP_POST_TYPE])) {
			unset($allCustomPosts[SG_POPUP_POST_TYPE]);
		}
		self::$allCustomPosts = $allCustomPosts;
		return $allCustomPosts; // TODO check for usages and remove this line
	}

	public static function addFilters()
	{
		self::addPostTypeToFilters();
	}

	private static function addPostTypeToFilters()
	{
		self::getAllCustomPosts();
		add_filter('sgPopupTargetParams', array(__CLASS__, 'addPopupTargetParams'), 1, 1);
		add_filter('sgPopupTargetData', array(__CLASS__, 'addPopupTargetData'), 1, 1);
		add_filter('sgPopupTargetTypes', array(__CLASS__, 'addPopupTargetTypes'), 1, 1);
		add_filter('sgPopupTargetAttrs', array(__CLASS__, 'addPopupTargetAttrs'), 1, 1);
		add_filter('sgPopupPageTemplates', array(__CLASS__, 'addPopupPageTemplates'), 1, 1);
		add_filter('sgPopupTargetPostType', array(__CLASS__, 'getAllCustomPostTypes'), 1, 1);
		add_filter('sgPopupTargetPageType', array(__CLASS__, 'getPageTypes'), 1, 1);
	}

	public static function addPopupTargetParams($targetParams)
	{
		$allCustomPostTypes = self::$allCustomPosts;
		// for conditions, to exclude other post types, tags etc.
		if (isset($targetParams['select_role'])) {
			return $targetParams;
		}

		foreach ($allCustomPostTypes as $customPostType) {
			$targetParams[$customPostType] = array(
				$customPostType.'_all' => 'All '.ucfirst($customPostType).'s',
				$customPostType.'_archive' => 'Archives '.ucfirst($customPostType).'s',
				$customPostType.'_selected' => 'Select '.ucfirst($customPostType).'s',
				$customPostType.'_categories' => 'Select '.ucfirst($customPostType).' categories'
			);
		}

		return $targetParams;
	}

	public static function addPopupTargetData($targetData)
	{
		$allCustomPostTypes = self::$allCustomPosts;

		foreach ($allCustomPostTypes as $customPostType) {
			$targetData[$customPostType.'_all'] = null;
			$targetData[$customPostType.'_selected'] = '';
			$targetData[$customPostType.'_categories'] = self::getCustomPostCategories($customPostType);
		}

		return $targetData;
	}

	public static function getCustomPostCategories($postTypeName)
	{
		$taxonomyObjects = get_object_taxonomies($postTypeName);
		if ($postTypeName == 'product') {
			$taxonomyObjects = array('product_cat');
		}
		$categories = self::getPostsAllCategories($postTypeName, $taxonomyObjects);

		return $categories;
	}

	public static function addPopupTargetTypes($targetTypes)
	{
		$allCustomPostTypes = self::$allCustomPosts;

		foreach ($allCustomPostTypes as $customPostType) {
			$targetTypes[$customPostType.'_selected'] = 'select';
			$targetTypes[$customPostType.'_categories'] = 'select';
		}

		return $targetTypes;
	}

	public static function addPopupTargetAttrs($targetAttrs)
	{
		$allCustomPostTypes = self::$allCustomPosts;

		foreach ($allCustomPostTypes as $customPostType) {
			$targetAttrs[$customPostType.'_selected']['htmlAttrs'] = array('class' => 'js-sg-select2 js-select-ajax', 'data-select-class' => 'js-select-ajax', 'data-select-type' => 'ajax', 'data-value-param' => $customPostType, 'multiple' => 'multiple');
			$targetAttrs[$customPostType.'_selected']['infoAttrs'] = array('label' => __('Select ', 'popup-builder').$customPostType);

			$targetAttrs[$customPostType.'_categories']['htmlAttrs'] = array('class' => 'js-sg-select2 js-select-ajax', 'data-select-class' => 'js-select-ajax', 'isNotPostType' => true, 'data-value-param' => $customPostType, 'multiple' => 'multiple');
			$targetAttrs[$customPostType.'_categories']['infoAttrs'] = array('label' => __('Select ', 'popup-builder').$customPostType.' categories');
		}

		return $targetAttrs;
	}

	public static function addPopupPageTemplates($templates)
	{
		$pageTemplates = self::getPageTemplates();

		$pageTemplates += $templates;

		return $pageTemplates;
	}

	public static function getAllCustomPostTypes()
	{
		$args = array(
			'public' => true,
			'_builtin' => false
		);

		$allCustomPosts = get_post_types($args);
		if (!empty($allCustomPosts[SG_POPUP_POST_TYPE])) {
			unset($allCustomPosts[SG_POPUP_POST_TYPE]);
		}

		return $allCustomPosts;
	}

	public static function getPostsAllCategories($postType = 'post', $taxonomies = array(), $search_text = '')
	{
		$cats =  get_terms(
			array(
				'taxonomy' => $taxonomies,
				'hide_empty' => false,
				'type'      => $postType,
				'orderby'   => 'name',
				'order'     => 'ASC',
				'number'    => 200,
				'offset'    => 0,
				'name__like'    => $search_text
			)
		);

		$supportedTaxonomies = array('category');
		if (!empty($taxonomies)) {
			$supportedTaxonomies = $taxonomies;
		}

		$catsParams = array();
		foreach ($cats as $cat) {
			if (isset($cat->taxonomy)) {
				if (!in_array($cat->taxonomy, $supportedTaxonomies)) {
					continue;
				}
			}
			$id = $cat->term_id;
			$name = $cat->name;
			$catsParams[$id] = $name;
		}

		return $catsParams;
	}

	public static function getPageTypes()
	{
		$postTypes = array();

		$postTypes['is_home_page'] = __('Home Page', 'popup-builder');
		$postTypes['is_home'] = __('Posts Page', 'popup-builder');
		$postTypes['is_search'] = __('Search Pages', 'popup-builder');
		$postTypes['is_404'] = __('404 Pages', 'popup-builder');
		if (function_exists('is_shop')) {
			$postTypes['is_shop'] = __('Shop Page', 'popup-builder');
		}
		if (function_exists('is_archive')) {
			$postTypes['is_archive'] = __('Archive Page', 'popup-builder');
		}

		return $postTypes;
	}

	public static function getPageTemplates()
	{
		$pageTemplates = array(
			'page.php' => __('Default Template', 'popup-builder')
		);

		$templates = wp_get_theme()->get_page_templates();
		if (empty($templates)) {
			return $pageTemplates;
		}

		foreach ($templates as $key => $value) {
			$pageTemplates[$key] = $value;
		}

		return $pageTemplates;
	}

	public static function getAllTags($search_text = '')
	{
		$allTags = array();
		$tags = get_tags(array(
			'hide_empty' => false,
			'name__like' => $search_text
		));

		foreach ($tags as $tag) {
			$allTags[$tag->slug] = $tag->name;
		}

		return $allTags;
	}
	public static function getTagsByIds($ids = [])
	{
		$allTags = array();
		$tags = get_tags(array(
			'hide_empty' => false,
			'include' => $ids
		));
		foreach ($tags as $tag) {
			$allTags[$tag->slug] = $tag->name;
		}
		return $allTags;
	}
	public static function getTagsBySlug($ids = [])
	{
		$allTags = array();
		$tags = get_tags(array(
			'hide_empty' => false,
			'slug' => $ids
		));
		foreach ($tags as $tag) {
			$allTags[$tag->slug] = $tag->name;
		}
		return $allTags;
	}

	public static function getTermsByIds($ids = array())
	{
		$allTags = array();
		$terms = get_terms(array(
			'hide_empty' => false,
			'include' => $ids
		));
		foreach ($terms as $term) {
			$allTags[$term->term_id] = $term->name;
		}
		return $allTags;
	}

	public static function defaultData()
	{
		$data = array();

		$data['contentClickOptions'] = array(
			'template' => array(
				'fieldWrapperAttr' => array(
					'class' => 'col-md-7 sgpb-choice-option-wrapper'
				),
				'labelAttr' => array(
					'class' => 'formItem__title'
				),
				'groupWrapperAttr' => array(
					'class' => 'subFormItem sgpb-choice-wrapper formItem'
				)
			),
			'buttonPosition' => 'right',
			'nextNewLine' => true,
			'fields' => array(
				array(
					'attr' => array(
						'type' => 'radio',
						'name' => 'sgpb-content-click-behavior',
						'value' => 'close'
					),
					'label' => array(
						'name' => __('Close Popup', 'popup-builder').':'
					)
				),
				array(
					'attr' => array(
						'type' => 'radio',
						'name' => 'sgpb-content-click-behavior',
						'data-attr-href' => 'content-click-redirect',
						'value' => 'redirect'
					),
					'label' => array(
						'name' => __('Redirect', 'popup-builder').':'
					)
				),
				array(
					'attr' => array(
						'type' => 'radio',
						'name' => 'sgpb-content-click-behavior',
						'data-attr-href' => 'content-copy-to-clipboard',
						'value' => 'copy'
					),
					'label' => array(
						'name' => __('Copy to clipboard', 'popup-builder').':'
					)
				)
			)
		);

		$data['customEditorContent'] = array(
			'js' => array(
				'helperText' => array(
					'ShouldOpen' => '<b>Opening events:</b><br><br><b>#1</b> Add the code you want to run <b>before</b> the popup opening. This will be a condition for opening the popup, that is processed and defined before the popup opening. If the return value is <b>"true"</b> then the popup will open, if the value is <b>"false"</b> the popup won\'t open.',
					'WillOpen' => '<b>#2</b> Add the code you want to run <b>before</b> the popup opens. This will be the code that will work in the process of opening the popup. <b>true/false</b> conditions will not work in this phase.',
					'DidOpen' => '<b>#3</b> Add the code you want to run <b>after</b> the popup opens. This code will work when the popup is already open on the page.',
					'ShouldClose' => '<b>Closing events:</b><br><br><b>#1</b> Add the code that will be fired <b>before</b> the popup closes. This will be a condition for the popup closing. If the return value is <b>"true"</b> then the popup will close, if the value is <b>"false"</b> the popup won\'t close.',
					'WillClose' => '<b>#2</b> Add the code you want to run <b>before</b> the popup closes.  This will be the code that will work in the process of closing the popup. <b>true/false</b> conditions will not work in this phase.',
					'DidClose' => '<b>#3</b> Add the code you want to run <b>after</b> the popup closes. This code will work when the popup is already closed on the page.'
				),
				'description' => array(
					'<span class="formItem__text">
	                    '.__('If you need the popup id number in the custom code, you may use the following variable to get the ID:', 'popup-builder').'
	                    <b>popupId</b>
	                </span>'
				)
			),
			'css' => array(
				// we need this oldDefaultValue for the backward compatibility
				'oldDefaultValue' => array(
					'/*popup content wrapper*/'."\n".
					'.sgpb-content-popupId {'."\n\n".'}'."\n\n".

					'/*overlay*/'."\n".
					'.sgpb-popup-overlay-popupId {'."\n\n".'}'."\n\n".

					'/*popup wrapper*/'."\n".
					'.sgpb-popup-builder-content-popupId {'."\n\n".'}'."\n\n"
				),
				'helperText' => array(
					'<br>/*popup content wrapper*/',
					'.sgpb-content-popupId',
					'<br>/*overlay*/',
					'.sgpb-popup-overlay-popupId',
					'<br>/*popup wrapper*/',
					'.sgpb-popup-builder-content-popupId'
				),
				'description' => array(
					'<span class="formItem__text">
	                    '.__('If you need the popup id number in the custom code, you may use the following variable to get the ID:', 'popup-builder').'
	                    <b>popupId</b>
	                </span>'
				)
			)
		);

		$data['htmlCustomButtonArgs'] = array(
			'template' => array(
				'fieldWrapperAttr' => array(
					'class' => 'col-md-6 sgpb-choice-option-wrapper'
				),
				'labelAttr' => array(
					'class' => 'col-md-6 sgpb-choice-option-wrapper sgpb-sub-option-label'
				),
				'groupWrapperAttr' => array(
					'class' => 'row form-group sgpb-choice-wrapper'
				)
			),
			'buttonPosition' => 'right',
			'nextNewLine' => true,
			'fields' => array(
				array(
					'attr' => array(
						'type' => 'radio',
						'name' => 'sgpb-custom-button',
						'class' => 'custom-button-copy-to-clipboard',
						'data-attr-href' => 'sgpb-custom-button-copy',
						'value' => 'copyToClipBoard'
					),
					'label' => array(
						'name' => __('Copy to clipboard', 'popup-builder').':'
					)
				),
				array(
					'attr' => array(
						'type' => 'radio',
						'name' => 'sgpb-custom-button',
						'class' => 'custom-button-copy-to-clipboard',
						'data-attr-href' => 'sgpb-custom-button-redirect-to-URL',
						'value' => 'redirectToURL'
					),
					'label' => array(
						'name' => __('Redirect to URL', 'popup-builder').':'
					)
				),
				array(
					'attr' => array(
						'type' => 'radio',
						'name' => 'sgpb-custom-button',
						'class' => 'subs-success-open-popup',
						'data-attr-href' => 'sgpb-custom-button-open-popup',
						'value' => 'openPopup'
					),
					'label' => array(
						'name' => __('Open popup', 'popup-builder').':'
					)
				),
				array(
					'attr' => array(
						'type' => 'radio',
						'name' => 'sgpb-custom-button',
						'class' => 'sgpb-custom-button-hide-popup',
						'value' => 'hidePopup'
					),
					'label' => array(
						'name' => __('Hide popup', 'popup-builder').':'
					)
				)
			)
		);

		$data['popupDimensions'] = array(
			'template' => array(
				'fieldWrapperAttr' => array(
					'class' => 'col-md-7 sgpb-choice-option-wrapper'
				),
				'labelAttr' => array(
					'class' => 'formItem__title'
				),
				'groupWrapperAttr' => array(
					'class' => 'subFormItem sgpb-choice-wrapper sgpb-display-flex sgpb-align-item-center formItem'
				)
			),
			'buttonPosition' => 'right',
			'nextNewLine' => true,
			'fields' => array(
				array(
					'attr' => array(
						'type' => 'radio',
						'name' => 'sgpb-popup-dimension-mode',
						'class' => 'test class',
						'data-attr-href' => 'responsive-dimension-wrapper',
						'value' => 'responsiveMode'
					),
					'label' => array(
						'name' => __('Responsive mode', 'popup-builder').':',
						'info' => __('The sizes of the popup will be counted automatically, according to the content size of the popup. You can select the size in percentages, with this mode, to specify the size on the screen', 'popup-builder').'.'
					)
				),
				array(
					'attr' => array(
						'type' => 'radio',
						'name' => 'sgpb-popup-dimension-mode',
						'class' => 'test class',
						'data-attr-href' => 'custom-dimension-wrapper',
						'value' => 'customMode'
					),
					'label' => array(
						'name' => __('Custom mode', 'popup-builder').':',
						'info' => __('Add your own custom dimensions for the popup to get the exact sizing for your popup', 'popup-builder').'.'
					)
				)
			)
		);

		$data['theme'] = array(
			array(
				'value' => 'sgpb-theme-1',
				'data-attributes' => array(
					'class' => 'js-sgpb-popup-themes sgpb-popup-theme-1',
					'data-popup-theme-number' => 1
				)
			),
			array(
				'value' => 'sgpb-theme-2',
				'data-attributes' => array(
					'class' => 'js-sgpb-popup-themes sgpb-popup-theme-2',
					'data-popup-theme-number' => 2
				)
			),
			array(
				'value' => 'sgpb-theme-3',
				'data-attributes' => array(
					'class' => 'js-sgpb-popup-themes sgpb-popup-theme-3',
					'data-popup-theme-number' => 3
				)
			),
			array(
				'value' => 'sgpb-theme-4',
				'data-attributes' => array(
					'class' => 'js-sgpb-popup-themes sgpb-popup-theme-4',
					'data-popup-theme-number' => 4
				)
			),
			array(
				'value' => 'sgpb-theme-5',
				'data-attributes' => array(
					'class' => 'js-sgpb-popup-themes sgpb-popup-theme-5',
					'data-popup-theme-number' => 5
				)
			),
			array(
				'value' => 'sgpb-theme-6',
				'data-attributes' => array(
					'class' => 'js-sgpb-popup-themes sgpb-popup-theme-6',
					'data-popup-theme-number' => 6
				)
			)
		);

		$data['responsiveDimensions'] = array(
			'auto' =>  __('Auto', 'popup-builder'),
			'10' => '10%',
			'20' => '20%',
			'30' => '30%',
			'40' => '40%',
			'50' => '50%',
			'60' => '60%',
			'70' => '70%',
			'80' => '80%',
			'90' => '90%',
			'100' => '100%',
			'fullScreen' => __('Full screen', 'popup-builder')
		);

		$data['freeConditionsAdvancedTargeting'] = array(
			'devices' => __('Devices', 'popup-builder'),
			'user-status' => __('User Status', 'popup-builder'),
			'after-x' => __('After x pages visit', 'popup-builder'),
			'user-role' => __('User Role', 'popup-builder'),
			'detect-by-url' => __('Referral URL detection', 'popup-builder'),
			'cookie-detection' => __('Cookie Detection', 'popup-builder'),
			'operation-system' => __('Operating System', 'popup-builder'),
			'browser-detection' => __('Web Browser', 'popup-builder'),
			'query-string' => __('URL Query String', 'popup-builder')
		);

		$data['freeConditionsGeoTargeting'] = array(
			'countries' => __('Countries', 'popup-builder'),
			'cities' => __('Cities', 'popup-builder'),
			'states' => __('States', 'popup-builder'),
			'visitor-ip' => __('Visitor IP', 'popup-builder')
		);

		$data['closeButtonPositions'] = array(
			'topLeft' => __('top-left', 'popup-builder'),
			'topRight' => __('top-right', 'popup-builder'),
			'bottomLeft' => __('bottom-left', 'popup-builder'),
			'bottomRight' => __('bottom-right', 'popup-builder')
		);

		$data['closeButtonPositionsFirstTheme'] = array(
			'bottomLeft' => __('bottom-left', 'popup-builder'),
			'bottomRight' => __('bottom-right', 'popup-builder')
		);

		$data['pxPercent'] = array(
			'px' => 'px',
			'%' => '%'
		);

		$data['countdownFormat'] = array(
			SG_COUNTDOWN_COUNTER_SECONDS_SHOW => 'DD:HH:MM:SS',
			SG_COUNTDOWN_COUNTER_SECONDS_HIDE => 'DD:HH:MM'
		);

		$data['countdownTimezone'] = self::getPopupTimeZone();

		$data['countdownLanguage'] = array(
			'English'    => 'English',
			'German'     => 'Deutsche',
			'Spanish'    => 'Español',
			'Arabic'     => 'عربى',
			'Italian'    => 'Italiano',
			'Dutch'      => 'Dutch',
			'Norwegian'  => 'Norsk',
			'Portuguese' => 'Português',
			'Russian'    => 'Русский',
			'Swedish'    => 'Svenska',
			'Czech'      => 'Čeština',
			'Chinese'    => '中文'
		);

		$data['weekDaysArray'] = array(
			'Mon' => __('Monday', 'popup-builder'),
			'Tue' => __('Tuesday', 'popup-builder'),
			'Wed' => __('Wednesday', 'popup-builder'),
			'Thu' => __('Thursday', 'popup-builder'),
			'Fri' => __('Friday', 'popup-builder'),
			'Sat' => __('Saturday', 'popup-builder'),
			'Sun' => __('Sunday', 'popup-builder')
		);

		$data['messageResize'] = array(
			'both' => __('Both', 'popup-builder'),
			'horizontal' => __('Horizontal', 'popup-builder'),
			'vertical' => __('Vertical', 'popup-builder'),
			'none' => __('None', 'popup-builder'),
			'inherit' => __('Inherit', 'popup-builder')
		);

		$data['socialShareOptions'] = array(
			'template' => array(
				'fieldWrapperAttr' => array(
					'class' => 'col-md-7 sgpb-choice-option-wrapper'
				),
				'labelAttr' => array(
					'class' => 'col-md-5 sgpb-choice-option-wrapper'
				),
				'groupWrapperAttr' => array(
					'class' => 'row form-group sgpb-choice-wrapper'
				)
			),
			'buttonPosition' => 'right',
			'nextNewLine' => true,
			'fields' => array(
				array(
					'attr' => array(
						'type' => 'radio',
						'name' => 'sgpb-social-share-url-type',
						'class' => 'sgpb-share-url-type',
						'data-attr-href' => '',
						'value' => 'activeUrl'
					),
					'label' => array(
						'name' => __('Use active URL', 'popup-builder').':'
					)
				),
				array(
					'attr' => array(
						'type' => 'radio',
						'name' => 'sgpb-social-share-url-type',
						'class' => 'sgpb-share-url-type',
						'data-attr-href' => 'sgpb-social-share-url-wrapper',
						'value' => 'shareUrl'
					),
					'label' => array(
						'name' => __('Share URL', 'popup-builder').':'
					)
				)
			)
		);

		$data['countdownDateFormat'] = array(
			'template' => array(
				'fieldWrapperAttr' => array(
					'class' => 'col-md-5 sgpb-choice-option-wrapper'
				),
				'labelAttr' => array(
					'class' => 'col-md-5 sgpb-choice-option-wrapper sgpb-sub-option-label'
				),
				'groupWrapperAttr' => array(
					'class' => 'row form-group sgpb-choice-wrapper'
				)
			),
			'buttonPosition' => 'right',
			'nextNewLine' => true,
			'fields' => array(
				array(
					'attr' => array(
						'type' => 'radio',
						'name' => 'sgpb-countdown-date-format',
						'class' => 'sgpb-countdown-date-format-from-date',
						'data-attr-href' => 'sgpb-countdown-date-format-from-date',
						'value' => 'date'
					),
					'label' => array(
						'name' => __('Due Date', 'popup-builder').':'
					)
				),
				array(
					'attr' => array(
						'type' => 'radio',
						'name' => 'sgpb-countdown-date-format',
						'class' => 'sgpb-countdown-date-format-from-date',
						'data-attr-href' => 'sgpb-countdown-date-format-from-input',
						'value' => 'input'
					),
					'label' => array(
						'name' => __('Timer', 'popup-builder').':'
					)
				)
			)
		);

		$data['socialShareTheme'] = array(
			array(
				'value' => 'flat',
				'data-attributes' => array(
					'class' => 'js-social-share-theme sgpb-social-popup-flat',
					'data-popup-theme-number' => 1
				)
			),
			array(
				'value' => 'classic',
				'data-attributes' => array(
					'class' => 'js-social-share-theme sgpb-social-popup-classic',
					'data-popup-theme-number' => 1
				)
			),
			array(
				'value' => 'minima',
				'data-attributes' => array(
					'class' => 'js-social-share-theme sgpb-social-popup-minima',
					'data-popup-theme-number' => 1
				)
			),
			array(
				'value' => 'plain',
				'data-attributes' => array(
					'class' => 'js-social-share-theme sgpb-social-popup-plain',
					'data-popup-theme-number' => 1
				)
			)
		);

		$data['socialThemeSizes'] = array(
			'8' => '8',
			'10' => '10',
			'12' => '12',
			'14' => '14',
			'16' => '16',
			'18' => '18',
			'20' => '20',
			'24' => '24'
		);

		$data['socialThemeShereCount'] = array(
			'true' => __('True', 'popup-builder'),
			'false' => __('False', 'popup-builder'),
			'inside' => __('Inside', 'popup-builder')
		);

		$data['popupInsertEventTypes'] = array(
			'inherit' => __('Inherit', 'popup-builder'),
			'onLoad' => __('On load', 'popup-builder'),
			'click' => __('On click', 'popup-builder'),
			'hover' => __('On hover', 'popup-builder')
		);

		$data['subscriptionSuccessBehavior'] = array(
			'template' => array(
				'fieldWrapperAttr' => array(
					'class' => 'col-md-6 sgpb-choice-option-wrapper'
				),
				'labelAttr' => array(
					'class' => 'subFormItem__title sgpb-margin-right-10'
				),
				'groupWrapperAttr' => array(
					'class' => 'subFormItem sgpb-choice-wrapper'
				)
			),
			'buttonPosition' => 'right',
			'nextNewLine' => true,
			'fields' => array(
				array(
					'attr' => array(
						'type' => 'radio',
						'name' => 'sgpb-subs-success-behavior',
						'class' => 'subs-success-message',
						'data-attr-href' => 'subs-show-success-message',
						'value' => 'showMessage'
					),
					'label' => array(
						'name' => __('Success message', 'popup-builder').':'
					)
				),
				array(
					'attr' => array(
						'type' => 'radio',
						'name' => 'sgpb-subs-success-behavior',
						'class' => 'subs-redirect-to-URL',
						'data-attr-href' => 'subs-redirect-to-URL',
						'value' => 'redirectToURL'
					),
					'label' => array(
						'name' => __('Redirect to URL', 'popup-builder').':'
					)
				),
				array(
					'attr' => array(
						'type' => 'radio',
						'name' => 'sgpb-subs-success-behavior',
						'class' => 'subs-success-open-popup',
						'data-attr-href' => 'subs-open-popup',
						'value' => 'openPopup'
					),
					'label' => array(
						'name' => __('Open popup', 'popup-builder').':'
					)
				),
				array(
					'attr' => array(
						'type' => 'radio',
						'name' => 'sgpb-subs-success-behavior',
						'class' => 'subs-hide-popup',
						'value' => 'hidePopup'
					),
					'label' => array(
						'name' => __('Hide popup', 'popup-builder').':'
					)
				)
			)
		);

		$data['buttonsType'] = array(
			'standard' => __('Standard', 'popup-builder'),
			'box_count' => __('Box with count', 'popup-builder'),
			'button_count' => __('Button with count', 'popup-builder'),
			'button' => __('Button', 'popup-builder')
		);

		$data['backroundImageModes'] = array(
			'no-repeat' => __('None', 'popup-builder'),
			'cover' => __('Cover', 'popup-builder'),
			'contain' => __('Contain', 'popup-builder'),
			'repeat' => __('Repeat', 'popup-builder')
		);

		$data['openAnimationEfects'] = array(
			'No effect' => __('None', 'popup-builder'),
			'sgpb-flip' => __('Flip', 'popup-builder'),
			'sgpb-shake' => __('Shake', 'popup-builder'),
			'sgpb-wobble' => __('Wobble', 'popup-builder'),
			'sgpb-swing' => __('Swing', 'popup-builder'),
			'sgpb-flash' => __('Flash', 'popup-builder'),
			'sgpb-bounce' => __('Bounce', 'popup-builder'),
			'sgpb-bounceInRight' => __('BounceInRight', 'popup-builder'),
			'sgpb-bounceIn' => __('BounceIn', 'popup-builder'),
			'sgpb-pulse' => __('Pulse', 'popup-builder'),
			'sgpb-rubberBand' => __('RubberBand', 'popup-builder'),
			'sgpb-tada' => __('Tada', 'popup-builder'),
			'sgpb-slideInUp' => __('SlideInUp', 'popup-builder'),
			'sgpb-jello' => __('Jello', 'popup-builder'),
			'sgpb-rotateIn' => __('RotateIn', 'popup-builder'),
			'sgpb-fadeIn' => __('FadeIn', 'popup-builder')
		);

		$data['closeAnimationEfects'] = array(
			'No effect' => __('None', 'popup-builder'),
			'sgpb-flipInX' => __('Flip', 'popup-builder'),
			'sgpb-shake' => __('Shake', 'popup-builder'),
			'sgpb-wobble' => __('Wobble', 'popup-builder'),
			'sgpb-swing' => __('Swing', 'popup-builder'),
			'sgpb-flash' => __('Flash', 'popup-builder'),
			'sgpb-bounce' => __('Bounce', 'popup-builder'),
			'sgpb-bounceOutLeft' => __('BounceOutLeft', 'popup-builder'),
			'sgpb-bounceOut' => __('BounceOut', 'popup-builder'),
			'sgpb-pulse' => __('Pulse', 'popup-builder'),
			'sgpb-rubberBand' => __('RubberBand', 'popup-builder'),
			'sgpb-tada' => __('Tada', 'popup-builder'),
			'sgpb-slideOutUp' => __('SlideOutUp', 'popup-builder'),
			'sgpb-jello' => __('Jello', 'popup-builder'),
			'sgpb-rotateOut' => __('RotateOut', 'popup-builder'),
			'sgpb-fadeOut' => __('FadeOut', 'popup-builder')
		);

		$data['floatingButtonPositionsCorner'] = array(
			'top-left' => __('Top left', 'popup-builder'),
			'top-right' => __('Top right', 'popup-builder'),
			'bottom-left' => __('Bottom left', 'popup-builder'),
			'bottom-right' => __('Bottom right', 'popup-builder')
		);

		$data['floatingButtonPositionsBasic'] = array(
			'top-left' => __('Top left', 'popup-builder'),
			'top-right' => __('Top right', 'popup-builder'),
			'bottom-left' => __('Bottom left', 'popup-builder'),
			'bottom-right' => __('Bottom right', 'popup-builder'),
			'top-center' => __('Top center', 'popup-builder'),
			'bottom-center' => __('Bottom center', 'popup-builder'),
			'right-center' => __('Right center', 'popup-builder'),
			'left-center' => __('Left center', 'popup-builder')
		);

		$data['floatingButtonStyle'] = array(
			'corner' => __('Corner', 'popup-builder'),
			'basic' => __('Basic', 'popup-builder')
		);

		$data['userRoles'] = self::getAllUserRoles();

		return $data;
	}

	public static function getAllUserRoles()
	{
		$rulesArray = array();
		if (!function_exists('get_editable_roles')){
			return $rulesArray;
		}

		$roles = get_editable_roles();
		foreach ($roles as $roleName => $roleInfo) {
			if ($roleName == 'administrator') {
				continue;
			}
			$rulesArray[$roleName] = $roleName;
		}

		return $rulesArray;
	}

	public static function getClickActionOptions()
	{
		$settings = array(
			'defaultClickClassName' => __('Default', 'popup-builder'),
			'clickActionCustomClass' => __('Custom class', 'popup-builder')
		);

		return $settings;
	}

	public static function getHoverActionOptions()
	{
		$settings = array(
			'defaultHoverClassName' => __('Default', 'popup-builder'),
			'hoverActionCustomClass' => __('Custom class', 'popup-builder')
		);

		return $settings;
	}

	// proStartSilver
	public static function getPopupDefaultTimeZone()
	{
		$timeZone = get_option('timezone_string');
		if (!$timeZone) {
			$timeZone = SG_POPUP_DEFAULT_TIME_ZONE;
		}

		return $timeZone;
	}
	// proEndSilver

	// proStartGold
	public static function getPopupTimeZone()
	{
		return array(
			'Pacific/Midway' => '(GMT-11:00) Midway',
			'Pacific/Niue' => '(GMT-11:00) Niue',
			'Pacific/Pago_Pago' => '(GMT-11:00) Pago Pago',
			'Pacific/Honolulu' => '(GMT-10:00) Hawaii Time',
			'Pacific/Rarotonga' => '(GMT-10:00) Rarotonga',
			'Pacific/Tahiti' => '(GMT-10:00) Tahiti',
			'Pacific/Marquesas' => '(GMT-09:30) Marquesas',
			'America/Anchorage' => '(GMT-09:00) Alaska Time',
			'Pacific/Gambier' => '(GMT-09:00) Gambier',
			'America/Los_Angeles' => '(GMT-08:00) Pacific Time',
			'America/Tijuana' => '(GMT-08:00) Pacific Time - Tijuana',
			'America/Vancouver' => '(GMT-08:00) Pacific Time - Vancouver',
			'America/Whitehorse' => '(GMT-08:00) Pacific Time - Whitehorse',
			'Pacific/Pitcairn' => '(GMT-08:00) Pitcairn',
			'America/Dawson_Creek' => '(GMT-07:00) Mountain Time - Dawson Creek',
			'America/Denver' => '(GMT-07:00) Mountain Time',
			'America/Edmonton' => '(GMT-07:00) Mountain Time - Edmonton',
			'America/Hermosillo' => '(GMT-07:00) Mountain Time - Hermosillo',
			'America/Mazatlan' => '(GMT-07:00) Mountain Time - Chihuahua, Mazatlan',
			'America/Phoenix' => '(GMT-07:00) Mountain Time - Arizona',
			'America/Yellowknife' => '(GMT-07:00) Mountain Time - Yellowknife',
			'America/Belize' => '(GMT-06:00) Belize',
			'America/Chicago' => '(GMT-06:00) Central Time',
			'America/Costa_Rica' => '(GMT-06:00) Costa Rica',
			'America/El_Salvador' => '(GMT-06:00) El Salvador',
			'America/Guatemala' => '(GMT-06:00) Guatemala',
			'America/Managua' => '(GMT-06:00) Managua',
			'America/Mexico_City' => '(GMT-06:00) Central Time - Mexico City',
			'America/Regina' => '(GMT-06:00) Central Time - Regina',
			'America/Tegucigalpa' => '(GMT-06:00) Central Time - Tegucigalpa',
			'America/Winnipeg' => '(GMT-06:00) Central Time - Winnipeg',
			'Pacific/Easter' => '(GMT-06:00) Easter Island',
			'Pacific/Galapagos' => '(GMT-06:00) Galapagos',
			'America/Bogota' => '(GMT-05:00) Bogota',
			'America/Cayman' => '(GMT-05:00) Cayman',
			'America/Guayaquil' => '(GMT-05:00) Guayaquil',
			'America/Havana' => '(GMT-05:00) Havana',
			'America/Iqaluit' => '(GMT-05:00) Eastern Time - Iqaluit',
			'America/Jamaica' => '(GMT-05:00) Jamaica',
			'America/Lima' => '(GMT-05:00) Lima',
			'America/Montreal' => '(GMT-05:00) Eastern Time - Montreal',
			'America/Nassau' => '(GMT-05:00) Nassau',
			'America/New_York' => '(GMT-05:00) Eastern Time',
			'America/Panama' => '(GMT-05:00) Panama',
			'America/Port-au-Prince' => '(GMT-05:00) Port-au-Prince',
			'America/Rio_Branco' => '(GMT-05:00) Rio Branco',
			'America/Toronto' => '(GMT-05:00) Eastern Time - Toronto',
			'America/Caracas' => '(GMT-04:30) Caracas',
			'America/Antigua' => '(GMT-04:00) Antigua',
			'America/Asuncion' => '(GMT-04:00) Asuncion',
			'America/Barbados' => '(GMT-04:00) Barbados',
			'America/Boa_Vista' => '(GMT-04:00) Boa Vista',
			'America/Campo_Grande' => '(GMT-04:00) Campo Grande',
			'America/Cuiaba' => '(GMT-04:00) Cuiaba',
			'America/Curacao' => '(GMT-04:00) Curacao',
			'America/Grand_Turk' => '(GMT-04:00) Grand Turk',
			'America/Guyana' => '(GMT-04:00) Guyana',
			'America/Halifax' => '(GMT-04:00) Atlantic Time - Halifax',
			'America/La_Paz' => '(GMT-04:00) La Paz',
			'America/Manaus' => '(GMT-04:00) Manaus',
			'America/Martinique' => '(GMT-04:00) Martinique',
			'America/Port_of_Spain' => '(GMT-04:00) Port of Spain',
			'America/Porto_Velho' => '(GMT-04:00) Porto Velho',
			'America/Puerto_Rico' => '(GMT-04:00) Puerto Rico',
			'America/Santiago' => '(GMT-04:00) Santiago',
			'America/Santo_Domingo' => '(GMT-04:00) Santo Domingo',
			'America/Thule' => '(GMT-04:00) Thule',
			'Antarctica/Palmer' => '(GMT-04:00) Palmer',
			'Atlantic/Bermuda' => '(GMT-04:00) Bermuda',
			'America/St_Johns' => '(GMT-03:30) Newfoundland Time - St. Johns',
			'America/Araguaina' => '(GMT-03:00) Araguaina',
			'America/Argentina/Buenos_Aires' => '(GMT-03:00) Buenos Aires',
			'America/Bahia' => '(GMT-03:00) Salvador',
			'America/Belem' => '(GMT-03:00) Belem',
			'America/Cayenne' => '(GMT-03:00) Cayenne',
			'America/Fortaleza' => '(GMT-03:00) Fortaleza',
			'America/Godthab' => '(GMT-03:00) Godthab',
			'America/Maceio' => '(GMT-03:00) Maceio',
			'America/Miquelon' => '(GMT-03:00) Miquelon',
			'America/Montevideo' => '(GMT-03:00) Montevideo',
			'America/Paramaribo' => '(GMT-03:00) Paramaribo',
			'America/Recife' => '(GMT-03:00) Recife',
			'America/Sao_Paulo' => '(GMT-03:00) Sao Paulo',
			'Antarctica/Rothera' => '(GMT-03:00) Rothera',
			'Atlantic/Stanley' => '(GMT-03:00) Stanley',
			'America/Noronha' => '(GMT-02:00) Noronha',
			'Atlantic/South_Georgia' => '(GMT-02:00) South Georgia',
			'America/Scoresbysund' => '(GMT-01:00) Scoresbysund',
			'Atlantic/Azores' => '(GMT-01:00) Azores',
			'Atlantic/Cape_Verde' => '(GMT-01:00) Cape Verde',
			'Africa/Abidjan' => '(GMT+00:00) Abidjan',
			'Africa/Accra' => '(GMT+00:00) Accra',
			'Africa/Bissau' => '(GMT+00:00) Bissau',
			'Africa/Casablanca' => '(GMT+00:00) Casablanca',
			'Africa/El_Aaiun' => '(GMT+00:00) El Aaiun',
			'Africa/Monrovia' => '(GMT+00:00) Monrovia',
			'America/Danmarkshavn' => '(GMT+00:00) Danmarkshavn',
			'Atlantic/Canary' => '(GMT+00:00) Canary Islands',
			'Atlantic/Faroe' => '(GMT+00:00) Faeroe',
			'Atlantic/Reykjavik' => '(GMT+00:00) Reykjavik',
			'Etc/GMT' => '(GMT+00:00) GMT (no daylight saving)',
			'Europe/Dublin' => '(GMT+00:00) Dublin',
			'Europe/Lisbon' => '(GMT+00:00) Lisbon',
			'Europe/London' => '(GMT+00:00) London',
			'Africa/Algiers' => '(GMT+01:00) Algiers',
			'Africa/Ceuta' => '(GMT+01:00) Ceuta',
			'Africa/Lagos' => '(GMT+01:00) Lagos',
			'Africa/Ndjamena' => '(GMT+01:00) Ndjamena',
			'Africa/Tunis' => '(GMT+01:00) Tunis',
			'Africa/Windhoek' => '(GMT+01:00) Windhoek',
			'Europe/Amsterdam' => '(GMT+01:00) Amsterdam',
			'Europe/Andorra' => '(GMT+01:00) Andorra',
			'Europe/Belgrade' => '(GMT+01:00) Central European Time - Belgrade',
			'Europe/Berlin' => '(GMT+01:00) Berlin',
			'Europe/Brussels' => '(GMT+01:00) Brussels',
			'Europe/Budapest' => '(GMT+01:00) Budapest',
			'Europe/Copenhagen' => '(GMT+01:00) Copenhagen',
			'Europe/Gibraltar' => '(GMT+01:00) Gibraltar',
			'Europe/Luxembourg' => '(GMT+01:00) Luxembourg',
			'Europe/Madrid' => '(GMT+01:00) Madrid',
			'Europe/Malta' => '(GMT+01:00) Malta',
			'Europe/Monaco' => '(GMT+01:00) Monaco',
			'Europe/Oslo' => '(GMT+01:00) Oslo',
			'Europe/Paris' => '(GMT+01:00) Paris',
			'Europe/Prague' => '(GMT+01:00) Central European Time - Prague',
			'Europe/Rome' => '(GMT+01:00) Rome',
			'Europe/Stockholm' => '(GMT+01:00) Stockholm',
			'Europe/Tirane' => '(GMT+01:00) Tirane',
			'Europe/Vienna' => '(GMT+01:00) Vienna',
			'Europe/Warsaw' => '(GMT+01:00) Warsaw',
			'Europe/Zurich' => '(GMT+01:00) Zurich',
			'Africa/Cairo' => '(GMT+02:00) Cairo',
			'Africa/Johannesburg' => '(GMT+02:00) Johannesburg',
			'Africa/Maputo' => '(GMT+02:00) Maputo',
			'Africa/Tripoli' => '(GMT+02:00) Tripoli',
			'Asia/Amman' => '(GMT+02:00) Amman',
			'Asia/Beirut' => '(GMT+02:00) Beirut',
			'Asia/Damascus' => '(GMT+02:00) Damascus',
			'Asia/Gaza' => '(GMT+02:00) Gaza',
			'Asia/Jerusalem' => '(GMT+02:00) Jerusalem',
			'Asia/Nicosia' => '(GMT+02:00) Nicosia',
			'Europe/Athens' => '(GMT+02:00) Athens',
			'Europe/Bucharest' => '(GMT+02:00) Bucharest',
			'Europe/Chisinau' => '(GMT+02:00) Chisinau',
			'Europe/Helsinki' => '(GMT+02:00) Helsinki',
			'Europe/Istanbul' => '(GMT+02:00) Istanbul',
			'Europe/Kaliningrad' => '(GMT+02:00) Moscow-01 - Kaliningrad',
			'Europe/Kiev' => '(GMT+02:00) Kiev',
			'Europe/Riga' => '(GMT+02:00) Riga',
			'Europe/Sofia' => '(GMT+02:00) Sofia',
			'Europe/Tallinn' => '(GMT+02:00) Tallinn',
			'Europe/Vilnius' => '(GMT+02:00) Vilnius',
			'Africa/Addis_Ababa' => '(GMT+03:00) Addis Ababa',
			'Africa/Asmara' => '(GMT+03:00) Asmera',
			'Africa/Dar_es_Salaam' => '(GMT+03:00) Dar es Salaam',
			'Africa/Djibouti' => '(GMT+03:00) Djibouti',
			'Africa/Kampala' => '(GMT+03:00) Kampala',
			'Africa/Khartoum' => '(GMT+03:00) Khartoum',
			'Africa/Mogadishu' => '(GMT+03:00) Mogadishu',
			'Africa/Nairobi' => '(GMT+03:00) Nairobi',
			'Antarctica/Syowa' => '(GMT+03:00) Syowa',
			'Asia/Aden' => '(GMT+03:00) Aden',
			'Asia/Baghdad' => '(GMT+03:00) Baghdad',
			'Asia/Bahrain' => '(GMT+03:00) Bahrain',
			'Asia/Kuwait' => '(GMT+03:00) Kuwait',
			'Asia/Qatar' => '(GMT+03:00) Qatar',
			'Asia/Riyadh' => '(GMT+03:00) Riyadh',
			'Europe/Minsk' => '(GMT+03:00) Minsk',
			'Europe/Moscow' => '(GMT+03:00) Moscow+00',
			'Indian/Antananarivo' => '(GMT+03:00) Antananarivo',
			'Indian/Comoro' => '(GMT+03:00) Comoro',
			'Indian/Mayotte' => '(GMT+03:00) Mayotte',
			'Asia/Tehran' => '(GMT+03:30) Tehran',
			'Asia/Baku' => '(GMT+04:00) Baku',
			'Asia/Dubai' => '(GMT+04:00) Dubai',
			'Asia/Muscat' => '(GMT+04:00) Muscat',
			'Asia/Tbilisi' => '(GMT+04:00) Tbilisi',
			'Asia/Yerevan' => '(GMT+04:00) Yerevan',
			'Europe/Samara' => '(GMT+04:00) Moscow+00 - Samara',
			'Indian/Mahe' => '(GMT+04:00) Mahe',
			'Indian/Mauritius' => '(GMT+04:00) Mauritius',
			'Indian/Reunion' => '(GMT+04:00) Reunion',
			'Asia/Kabul' => '(GMT+04:30) Kabul',
			'Antarctica/Mawson' => '(GMT+05:00) Mawson',
			'Asia/Aqtau' => '(GMT+05:00) Aqtau',
			'Asia/Aqtobe' => '(GMT+05:00) Aqtobe',
			'Asia/Ashgabat' => '(GMT+05:00) Ashgabat',
			'Asia/Dushanbe' => '(GMT+05:00) Dushanbe',
			'Asia/Karachi' => '(GMT+05:00) Karachi',
			'Asia/Tashkent' => '(GMT+05:00) Tashkent',
			'Asia/Yekaterinburg' => '(GMT+05:00) Moscow+02 - Yekaterinburg',
			'Indian/Kerguelen' => '(GMT+05:00) Kerguelen',
			'Indian/Maldives' => '(GMT+05:00) Maldives',
			'Asia/Calcutta' => '(GMT+05:30) India Standard Time',
			'Asia/Colombo' => '(GMT+05:30) Colombo',
			'Asia/Katmandu' => '(GMT+05:45) Katmandu',
			'Antarctica/Vostok' => '(GMT+06:00) Vostok',
			'Asia/Almaty' => '(GMT+06:00) Almaty',
			'Asia/Bishkek' => '(GMT+06:00) Bishkek',
			'Asia/Dhaka' => '(GMT+06:00) Dhaka',
			'Asia/Omsk' => '(GMT+06:00) Moscow+03 - Omsk, Novosibirsk',
			'Asia/Thimphu' => '(GMT+06:00) Thimphu',
			'Indian/Chagos' => '(GMT+06:00) Chagos',
			'Asia/Rangoon' => '(GMT+06:30) Rangoon',
			'Indian/Cocos' => '(GMT+06:30) Cocos',
			'Antarctica/Davis' => '(GMT+07:00) Davis',
			'Asia/Bangkok' => '(GMT+07:00) Bangkok',
			'Asia/Hovd' => '(GMT+07:00) Hovd',
			'Asia/Jakarta' => '(GMT+07:00) Jakarta',
			'Asia/Krasnoyarsk' => '(GMT+07:00) Moscow+04 - Krasnoyarsk',
			'Asia/Saigon' => '(GMT+07:00) Hanoi',
			'Indian/Christmas' => '(GMT+07:00) Christmas',
			'Antarctica/Casey' => '(GMT+08:00) Casey',
			'Asia/Brunei' => '(GMT+08:00) Brunei',
			'Asia/Choibalsan' => '(GMT+08:00) Choibalsan',
			'Asia/Hong_Kong' => '(GMT+08:00) Hong Kong',
			'Asia/Irkutsk' => '(GMT+08:00) Moscow+05 - Irkutsk',
			'Asia/Kuala_Lumpur' => '(GMT+08:00) Kuala Lumpur',
			'Asia/Macau' => '(GMT+08:00) Macau',
			'Asia/Makassar' => '(GMT+08:00) Makassar',
			'Asia/Manila' => '(GMT+08:00) Manila',
			'Asia/Shanghai' => '(GMT+08:00) China Time - Beijing',
			'Asia/Singapore' => '(GMT+08:00) Singapore',
			'Asia/Taipei' => '(GMT+08:00) Taipei',
			'Asia/Ulaanbaatar' => '(GMT+08:00) Ulaanbaatar',
			'Australia/Perth' => '(GMT+08:00) Western Time - Perth',
			'Asia/Dili' => '(GMT+09:00) Dili',
			'Asia/Jayapura' => '(GMT+09:00) Jayapura',
			'Asia/Pyongyang' => '(GMT+09:00) Pyongyang',
			'Asia/Seoul' => '(GMT+09:00) Seoul',
			'Asia/Tokyo' => '(GMT+09:00) Tokyo',
			'Asia/Yakutsk' => '(GMT+09:00) Moscow+06 - Yakutsk',
			'Pacific/Palau' => '(GMT+09:00) Palau',
			'Australia/Adelaide' => '(GMT+09:30) Central Time - Adelaide',
			'Australia/Darwin' => '(GMT+09:30) Central Time - Darwin',
			'Antarctica/DumontDUrville' => '(GMT+10:00) Dumont D\'Urville',
			'Asia/Magadan' => '(GMT+10:00) Moscow+08 - Magadan',
			'Asia/Vladivostok' => '(GMT+10:00) Moscow+07 - Yuzhno-Sakhalinsk',
			'Australia/Brisbane' => '(GMT+10:00) Eastern Time - Brisbane',
			'Australia/Hobart' => '(GMT+10:00) Eastern Time - Hobart',
			'Australia/Sydney' => '(GMT+10:00) Eastern Time - Melbourne, Sydney',
			'Pacific/Chuuk' => '(GMT+10:00) Truk',
			'Pacific/Guam' => '(GMT+10:00) Guam',
			'Pacific/Port_Moresby' => '(GMT+10:00) Port Moresby',
			'Pacific/Saipan' => '(GMT+10:00) Saipan',
			'Pacific/Efate' => '(GMT+11:00) Efate',
			'Pacific/Guadalcanal' => '(GMT+11:00) Guadalcanal',
			'Pacific/Kosrae' => '(GMT+11:00) Kosrae',
			'Pacific/Noumea' => '(GMT+11:00) Noumea',
			'Pacific/Pohnpei' => '(GMT+11:00) Ponape',
			'Pacific/Norfolk' => '(GMT+11:30) Norfolk',
			'Asia/Kamchatka' => '(GMT+12:00) Moscow+08 - Petropavlovsk-Kamchatskiy',
			'Pacific/Auckland' => '(GMT+12:00) Auckland',
			'Pacific/Fiji' => '(GMT+12:00) Fiji',
			'Pacific/Funafuti' => '(GMT+12:00) Funafuti',
			'Pacific/Kwajalein' => '(GMT+12:00) Kwajalein',
			'Pacific/Majuro' => '(GMT+12:00) Majuro',
			'Pacific/Nauru' => '(GMT+12:00) Nauru',
			'Pacific/Tarawa' => '(GMT+12:00) Tarawa',
			'Pacific/Wake' => '(GMT+12:00) Wake',
			'Pacific/Wallis' => '(GMT+12:00) Wallis',
			'Pacific/Apia' => '(GMT+13:00) Apia',
			'Pacific/Enderbury' => '(GMT+13:00) Enderbury',
			'Pacific/Fakaofo' => '(GMT+13:00) Fakaofo',
			'Pacific/Tongatapu' => '(GMT+13:00) Tongatapu',
			'Pacific/Kiritimati' => '(GMT+14:00) Kiritimati'
		);
	}

	public static function getJsLocalizedData()
	{
		$translatedData = array(
			'imageSupportAlertMessage' => __('Only image files supported', 'popup-builder'),
			'pdfSupportAlertMessage' => __('Only pdf files supported', 'popup-builder'),
			'areYouSure' => __('Are you sure?', 'popup-builder'),
			'addButtonSpinner' => __('L', 'popup-builder'),
			'audioSupportAlertMessage' => __('Only audio files supported (e.g.: mp3, wav, m4a, ogg)', 'popup-builder'),
			'publishPopupBeforeElementor' => __('Please, publish the popup before starting to use Elementor with it!', 'popup-builder'),
			'publishPopupBeforeDivi' => __('Please, publish the popup before starting to use Divi Builder with it!', 'popup-builder'),
			'closeButtonAltText' =>  __('Close', 'popup-builder')
		);

		return $translatedData;
	}

	public static function getCurrentDateTime()
	{
		return gmdate('Y-m-d H:i', strtotime(' +1 day'));
	}

	public static function getDefaultTimezone()
	{
		$timezone = get_option('timezone_string');
		if (!$timezone) {
			$timezone = 'America/New_York';
		}

		return $timezone;
	}
}

// We will keep this class to make sure all Popup Extension Add-ons are still working fine.
// In the future, we will remove it when you update all code in Add-ons to use NEW class above.
class ConfigDataHelper
{
	public static $customPostType;
	public static $allCustomPosts = array();

	public static function getPostTypeData($args = array())
	{
		$query = self::getQueryDataByArgs($args);

		$posts = array();
		foreach ($query->posts as $post) {
			$posts[$post->ID] = $post->post_title;
		}

		return $posts;
	}

	public static function getQueryDataByArgs($args = array())
	{
		$defaultArgs = array(
			'offset'           => '',
			'orderby'          => 'date',
			'order'            => 'DESC',
			'post_status'      => 'publish',
			'suppress_filters' => false,
			'post_type'        => 'post',
			'posts_per_page'   => 1000
		);
		$args = wp_parse_args($args, $defaultArgs);
		$query = new WP_Query($args);

		return $query;
	}

	/**
	 * this method is used for to get all other post types
	 * that may created by another plugins or theme or website owner!
	 *
	 * example: download from EDD, product from Woocommerce!
	 */
	public static function getAllCustomPosts()
	{
		$args = array(
			'public' => true,
			'_builtin' => false
		);

		$allCustomPosts = get_post_types($args);

		if (isset($allCustomPosts[SG_POPUP_POST_TYPE])) {
			unset($allCustomPosts[SG_POPUP_POST_TYPE]);
		}
		self::$allCustomPosts = $allCustomPosts;
		return $allCustomPosts; // TODO check for usages and remove this line
	}

	public static function addFilters()
	{
		self::addPostTypeToFilters();
	}

	private static function addPostTypeToFilters()
	{
		self::getAllCustomPosts();
		add_filter('sgPopupTargetParams', array(__CLASS__, 'addPopupTargetParams'), 1, 1);
		add_filter('sgPopupTargetData', array(__CLASS__, 'addPopupTargetData'), 1, 1);
		add_filter('sgPopupTargetTypes', array(__CLASS__, 'addPopupTargetTypes'), 1, 1);
		add_filter('sgPopupTargetAttrs', array(__CLASS__, 'addPopupTargetAttrs'), 1, 1);
		add_filter('sgPopupPageTemplates', array(__CLASS__, 'addPopupPageTemplates'), 1, 1);
		add_filter('sgPopupTargetPostType', array(__CLASS__, 'getAllCustomPostTypes'), 1, 1);
		add_filter('sgPopupTargetPageType', array(__CLASS__, 'getPageTypes'), 1, 1);
	}

	public static function addPopupTargetParams($targetParams)
	{
		$allCustomPostTypes = self::$allCustomPosts;
		// for conditions, to exclude other post types, tags etc.
		if (isset($targetParams['select_role'])) {
			return $targetParams;
		}

		foreach ($allCustomPostTypes as $customPostType) {
			$targetParams[$customPostType] = array(
				$customPostType.'_all' => 'All '.ucfirst($customPostType).'s',
				$customPostType.'_archive' => 'Archives '.ucfirst($customPostType).'s',
				$customPostType.'_selected' => 'Select '.ucfirst($customPostType).'s',
				$customPostType.'_categories' => 'Select '.ucfirst($customPostType).' categories'
			);
		}

		return $targetParams;
	}

	public static function addPopupTargetData($targetData)
	{
		$allCustomPostTypes = self::$allCustomPosts;

		foreach ($allCustomPostTypes as $customPostType) {
			$targetData[$customPostType.'_all'] = null;
			$targetData[$customPostType.'_selected'] = '';
			$targetData[$customPostType.'_categories'] = self::getCustomPostCategories($customPostType);
		}

		return $targetData;
	}

	public static function getCustomPostCategories($postTypeName)
	{
		$taxonomyObjects = get_object_taxonomies($postTypeName);
		if ($postTypeName == 'product') {
			$taxonomyObjects = array('product_cat');
		}
		$categories = self::getPostsAllCategories($postTypeName, $taxonomyObjects);

		return $categories;
	}

	public static function addPopupTargetTypes($targetTypes)
	{
		$allCustomPostTypes = self::$allCustomPosts;

		foreach ($allCustomPostTypes as $customPostType) {
			$targetTypes[$customPostType.'_selected'] = 'select';
			$targetTypes[$customPostType.'_categories'] = 'select';
		}

		return $targetTypes;
	}

	public static function addPopupTargetAttrs($targetAttrs)
	{
		$allCustomPostTypes = self::$allCustomPosts;

		foreach ($allCustomPostTypes as $customPostType) {
			$targetAttrs[$customPostType.'_selected']['htmlAttrs'] = array('class' => 'js-sg-select2 js-select-ajax', 'data-select-class' => 'js-select-ajax', 'data-select-type' => 'ajax', 'data-value-param' => $customPostType, 'multiple' => 'multiple');
			$targetAttrs[$customPostType.'_selected']['infoAttrs'] = array('label' => __('Select ', 'popup-builder').$customPostType);

			$targetAttrs[$customPostType.'_categories']['htmlAttrs'] = array('class' => 'js-sg-select2 js-select-ajax', 'data-select-class' => 'js-select-ajax', 'isNotPostType' => true, 'data-value-param' => $customPostType, 'multiple' => 'multiple');
			$targetAttrs[$customPostType.'_categories']['infoAttrs'] = array('label' => __('Select ', 'popup-builder').$customPostType.' categories');
		}

		return $targetAttrs;
	}

	public static function addPopupPageTemplates($templates)
	{
		$pageTemplates = self::getPageTemplates();

		$pageTemplates += $templates;

		return $pageTemplates;
	}

	public static function getAllCustomPostTypes()
	{
		$args = array(
			'public' => true,
			'_builtin' => false
		);

		$allCustomPosts = get_post_types($args);
		if (!empty($allCustomPosts[SG_POPUP_POST_TYPE])) {
			unset($allCustomPosts[SG_POPUP_POST_TYPE]);
		}

		return $allCustomPosts;
	}

	public static function getPostsAllCategories($postType = 'post', $taxonomies = array(), $search_text = '')
	{
		$cats =  get_terms(
			array(
				'taxonomy' => $taxonomies,
				'hide_empty' => false,
				'type'      => $postType,
				'orderby'   => 'name',
				'order'     => 'ASC',
				'number'    => 200,
				'offset'    => 0,
				'name__like'    => $search_text
			)
		);

		$supportedTaxonomies = array('category');
		if (!empty($taxonomies)) {
			$supportedTaxonomies = $taxonomies;
		}

		$catsParams = array();
		foreach ($cats as $cat) {
			if (isset($cat->taxonomy)) {
				if (!in_array($cat->taxonomy, $supportedTaxonomies)) {
					continue;
				}
			}
			$id = $cat->term_id;
			$name = $cat->name;
			$catsParams[$id] = $name;
		}

		return $catsParams;
	}

	public static function getPageTypes()
	{
		$postTypes = array();

		$postTypes['is_home_page'] = __('Home Page', 'popup-builder');
		$postTypes['is_home'] = __('Posts Page', 'popup-builder');
		$postTypes['is_search'] = __('Search Pages', 'popup-builder');
		$postTypes['is_404'] = __('404 Pages', 'popup-builder');
		if (function_exists('is_shop')) {
			$postTypes['is_shop'] = __('Shop Page', 'popup-builder');
		}
		if (function_exists('is_archive')) {
			$postTypes['is_archive'] = __('Archive Page', 'popup-builder');
		}

		return $postTypes;
	}

	public static function getPageTemplates()
	{
		$pageTemplates = array(
			'page.php' => __('Default Template', 'popup-builder')
		);

		$templates = wp_get_theme()->get_page_templates();
		if (empty($templates)) {
			return $pageTemplates;
		}

		foreach ($templates as $key => $value) {
			$pageTemplates[$key] = $value;
		}

		return $pageTemplates;
	}

	public static function getAllTags($search_text = '')
	{
		$allTags = array();
		$tags = get_tags(array(
			'hide_empty' => false,
			'name__like' => $search_text
		));

		foreach ($tags as $tag) {
			$allTags[$tag->slug] = $tag->name;
		}

		return $allTags;
	}
	public static function getTagsByIds($ids = [])
	{
		$allTags = array();
		$tags = get_tags(array(
			'hide_empty' => false,
			'include' => $ids
		));
		foreach ($tags as $tag) {
			$allTags[$tag->slug] = $tag->name;
		}
		return $allTags;
	}
	public static function getTagsBySlug($ids = [])
	{
		$allTags = array();
		$tags = get_tags(array(
			'hide_empty' => false,
			'slug' => $ids
		));
		foreach ($tags as $tag) {
			$allTags[$tag->slug] = $tag->name;
		}
		return $allTags;
	}

	public static function getTermsByIds($ids = array())
	{
		$allTags = array();
		$terms = get_terms(array(
			'hide_empty' => false,
			'include' => $ids
		));
		foreach ($terms as $term) {
			$allTags[$term->term_id] = $term->name;
		}
		return $allTags;
	}

	public static function defaultData()
	{
		$data = array();

		$data['contentClickOptions'] = array(
			'template' => array(
				'fieldWrapperAttr' => array(
					'class' => 'col-md-7 sgpb-choice-option-wrapper'
				),
				'labelAttr' => array(
					'class' => 'formItem__title'
				),
				'groupWrapperAttr' => array(
					'class' => 'subFormItem sgpb-choice-wrapper formItem'
				)
			),
			'buttonPosition' => 'right',
			'nextNewLine' => true,
			'fields' => array(
				array(
					'attr' => array(
						'type' => 'radio',
						'name' => 'sgpb-content-click-behavior',
						'value' => 'close'
					),
					'label' => array(
						'name' => __('Close Popup', 'popup-builder').':'
					)
				),
				array(
					'attr' => array(
						'type' => 'radio',
						'name' => 'sgpb-content-click-behavior',
						'data-attr-href' => 'content-click-redirect',
						'value' => 'redirect'
					),
					'label' => array(
						'name' => __('Redirect', 'popup-builder').':'
					)
				),
				array(
					'attr' => array(
						'type' => 'radio',
						'name' => 'sgpb-content-click-behavior',
						'data-attr-href' => 'content-copy-to-clipboard',
						'value' => 'copy'
					),
					'label' => array(
						'name' => __('Copy to clipboard', 'popup-builder').':'
					)
				)
			)
		);

		$data['customEditorContent'] = array(
			'js' => array(
				'helperText' => array(
					'ShouldOpen' => '<b>Opening events:</b><br><br><b>#1</b> Add the code you want to run <b>before</b> the popup opening. This will be a condition for opening the popup, that is processed and defined before the popup opening. If the return value is <b>"true"</b> then the popup will open, if the value is <b>"false"</b> the popup won\'t open.',
					'WillOpen' => '<b>#2</b> Add the code you want to run <b>before</b> the popup opens. This will be the code that will work in the process of opening the popup. <b>true/false</b> conditions will not work in this phase.',
					'DidOpen' => '<b>#3</b> Add the code you want to run <b>after</b> the popup opens. This code will work when the popup is already open on the page.',
					'ShouldClose' => '<b>Closing events:</b><br><br><b>#1</b> Add the code that will be fired <b>before</b> the popup closes. This will be a condition for the popup closing. If the return value is <b>"true"</b> then the popup will close, if the value is <b>"false"</b> the popup won\'t close.',
					'WillClose' => '<b>#2</b> Add the code you want to run <b>before</b> the popup closes.  This will be the code that will work in the process of closing the popup. <b>true/false</b> conditions will not work in this phase.',
					'DidClose' => '<b>#3</b> Add the code you want to run <b>after</b> the popup closes. This code will work when the popup is already closed on the page.'
				),
				'description' => array(
					'<span class="formItem__text">
	                    '.__('If you need the popup id number in the custom code, you may use the following variable to get the ID:', 'popup-builder').'
	                    <b>popupId</b>
	                </span>'
				)
			),
			'css' => array(
				// we need this oldDefaultValue for the backward compatibility
				'oldDefaultValue' => array(
					'/*popup content wrapper*/'."\n".
					'.sgpb-content-popupId {'."\n\n".'}'."\n\n".

					'/*overlay*/'."\n".
					'.sgpb-popup-overlay-popupId {'."\n\n".'}'."\n\n".

					'/*popup wrapper*/'."\n".
					'.sgpb-popup-builder-content-popupId {'."\n\n".'}'."\n\n"
				),
				'helperText' => array(
					'<br>/*popup content wrapper*/',
					'.sgpb-content-popupId',
					'<br>/*overlay*/',
					'.sgpb-popup-overlay-popupId',
					'<br>/*popup wrapper*/',
					'.sgpb-popup-builder-content-popupId'
				),
				'description' => array(
					'<span class="formItem__text">
	                    '.__('If you need the popup id number in the custom code, you may use the following variable to get the ID:', 'popup-builder').'
	                    <b>popupId</b>
	                </span>'
				)
			)
		);

		$data['htmlCustomButtonArgs'] = array(
			'template' => array(
				'fieldWrapperAttr' => array(
					'class' => 'col-md-6 sgpb-choice-option-wrapper'
				),
				'labelAttr' => array(
					'class' => 'col-md-6 sgpb-choice-option-wrapper sgpb-sub-option-label'
				),
				'groupWrapperAttr' => array(
					'class' => 'row form-group sgpb-choice-wrapper'
				)
			),
			'buttonPosition' => 'right',
			'nextNewLine' => true,
			'fields' => array(
				array(
					'attr' => array(
						'type' => 'radio',
						'name' => 'sgpb-custom-button',
						'class' => 'custom-button-copy-to-clipboard',
						'data-attr-href' => 'sgpb-custom-button-copy',
						'value' => 'copyToClipBoard'
					),
					'label' => array(
						'name' => __('Copy to clipboard', 'popup-builder').':'
					)
				),
				array(
					'attr' => array(
						'type' => 'radio',
						'name' => 'sgpb-custom-button',
						'class' => 'custom-button-copy-to-clipboard',
						'data-attr-href' => 'sgpb-custom-button-redirect-to-URL',
						'value' => 'redirectToURL'
					),
					'label' => array(
						'name' => __('Redirect to URL', 'popup-builder').':'
					)
				),
				array(
					'attr' => array(
						'type' => 'radio',
						'name' => 'sgpb-custom-button',
						'class' => 'subs-success-open-popup',
						'data-attr-href' => 'sgpb-custom-button-open-popup',
						'value' => 'openPopup'
					),
					'label' => array(
						'name' => __('Open popup', 'popup-builder').':'
					)
				),
				array(
					'attr' => array(
						'type' => 'radio',
						'name' => 'sgpb-custom-button',
						'class' => 'sgpb-custom-button-hide-popup',
						'value' => 'hidePopup'
					),
					'label' => array(
						'name' => __('Hide popup', 'popup-builder').':'
					)
				)
			)
		);

		$data['popupDimensions'] = array(
			'template' => array(
				'fieldWrapperAttr' => array(
					'class' => 'col-md-7 sgpb-choice-option-wrapper'
				),
				'labelAttr' => array(
					'class' => 'formItem__title'
				),
				'groupWrapperAttr' => array(
					'class' => 'subFormItem sgpb-choice-wrapper sgpb-display-flex sgpb-align-item-center formItem'
				)
			),
			'buttonPosition' => 'right',
			'nextNewLine' => true,
			'fields' => array(
				array(
					'attr' => array(
						'type' => 'radio',
						'name' => 'sgpb-popup-dimension-mode',
						'class' => 'test class',
						'data-attr-href' => 'responsive-dimension-wrapper',
						'value' => 'responsiveMode'
					),
					'label' => array(
						'name' => __('Responsive mode', 'popup-builder').':',
						'info' => __('The sizes of the popup will be counted automatically, according to the content size of the popup. You can select the size in percentages, with this mode, to specify the size on the screen', 'popup-builder').'.'
					)
				),
				array(
					'attr' => array(
						'type' => 'radio',
						'name' => 'sgpb-popup-dimension-mode',
						'class' => 'test class',
						'data-attr-href' => 'custom-dimension-wrapper',
						'value' => 'customMode'
					),
					'label' => array(
						'name' => __('Custom mode', 'popup-builder').':',
						'info' => __('Add your own custom dimensions for the popup to get the exact sizing for your popup', 'popup-builder').'.'
					)
				)
			)
		);

		$data['theme'] = array(
			array(
				'value' => 'sgpb-theme-1',
				'data-attributes' => array(
					'class' => 'js-sgpb-popup-themes sgpb-popup-theme-1',
					'data-popup-theme-number' => 1
				)
			),
			array(
				'value' => 'sgpb-theme-2',
				'data-attributes' => array(
					'class' => 'js-sgpb-popup-themes sgpb-popup-theme-2',
					'data-popup-theme-number' => 2
				)
			),
			array(
				'value' => 'sgpb-theme-3',
				'data-attributes' => array(
					'class' => 'js-sgpb-popup-themes sgpb-popup-theme-3',
					'data-popup-theme-number' => 3
				)
			),
			array(
				'value' => 'sgpb-theme-4',
				'data-attributes' => array(
					'class' => 'js-sgpb-popup-themes sgpb-popup-theme-4',
					'data-popup-theme-number' => 4
				)
			),
			array(
				'value' => 'sgpb-theme-5',
				'data-attributes' => array(
					'class' => 'js-sgpb-popup-themes sgpb-popup-theme-5',
					'data-popup-theme-number' => 5
				)
			),
			array(
				'value' => 'sgpb-theme-6',
				'data-attributes' => array(
					'class' => 'js-sgpb-popup-themes sgpb-popup-theme-6',
					'data-popup-theme-number' => 6
				)
			)
		);

		$data['responsiveDimensions'] = array(
			'auto' =>  __('Auto', 'popup-builder'),
			'10' => '10%',
			'20' => '20%',
			'30' => '30%',
			'40' => '40%',
			'50' => '50%',
			'60' => '60%',
			'70' => '70%',
			'80' => '80%',
			'90' => '90%',
			'100' => '100%',
			'fullScreen' => __('Full screen', 'popup-builder')
		);

		$data['freeConditionsAdvancedTargeting'] = array(
			'devices' => __('Devices', 'popup-builder'),
			'user-status' => __('User Status', 'popup-builder'),
			'after-x' => __('After x pages visit', 'popup-builder'),
			'user-role' => __('User Role', 'popup-builder'),
			'detect-by-url' => __('Referral URL detection', 'popup-builder'),
			'cookie-detection' => __('Cookie Detection', 'popup-builder'),
			'operation-system' => __('Operating System', 'popup-builder'),
			'browser-detection' => __('Web Browser', 'popup-builder'),
			'query-string' => __('URL Query String', 'popup-builder')
		);

		$data['freeConditionsGeoTargeting'] = array(
			'countries' => __('Countries', 'popup-builder'),
			'cities' => __('Cities', 'popup-builder'),
			'states' => __('States', 'popup-builder'),
			'visitor-ip' => __('Visitor IP', 'popup-builder')
		);

		$data['closeButtonPositions'] = array(
			'topLeft' => __('top-left', 'popup-builder'),
			'topRight' => __('top-right', 'popup-builder'),
			'bottomLeft' => __('bottom-left', 'popup-builder'),
			'bottomRight' => __('bottom-right', 'popup-builder')
		);

		$data['closeButtonPositionsFirstTheme'] = array(
			'bottomLeft' => __('bottom-left', 'popup-builder'),
			'bottomRight' => __('bottom-right', 'popup-builder')
		);

		$data['pxPercent'] = array(
			'px' => 'px',
			'%' => '%'
		);

		$data['countdownFormat'] = array(
			SG_COUNTDOWN_COUNTER_SECONDS_SHOW => 'DD:HH:MM:SS',
			SG_COUNTDOWN_COUNTER_SECONDS_HIDE => 'DD:HH:MM'
		);

		$data['countdownTimezone'] = self::getPopupTimeZone();

		$data['countdownLanguage'] = array(
			'English'    => 'English',
			'German'     => 'Deutsche',
			'Spanish'    => 'Español',
			'Arabic'     => 'عربى',
			'Italian'    => 'Italiano',
			'Dutch'      => 'Dutch',
			'Norwegian'  => 'Norsk',
			'Portuguese' => 'Português',
			'Russian'    => 'Русский',
			'Swedish'    => 'Svenska',
			'Czech'      => 'Čeština',
			'Chinese'    => '中文'
		);

		$data['weekDaysArray'] = array(
			'Mon' => __('Monday', 'popup-builder'),
			'Tue' => __('Tuesday', 'popup-builder'),
			'Wed' => __('Wednesday', 'popup-builder'),
			'Thu' => __('Thursday', 'popup-builder'),
			'Fri' => __('Friday', 'popup-builder'),
			'Sat' => __('Saturday', 'popup-builder'),
			'Sun' => __('Sunday', 'popup-builder')
		);

		$data['messageResize'] = array(
			'both' => __('Both', 'popup-builder'),
			'horizontal' => __('Horizontal', 'popup-builder'),
			'vertical' => __('Vertical', 'popup-builder'),
			'none' => __('None', 'popup-builder'),
			'inherit' => __('Inherit', 'popup-builder')
		);

		$data['socialShareOptions'] = array(
			'template' => array(
				'fieldWrapperAttr' => array(
					'class' => 'col-md-7 sgpb-choice-option-wrapper'
				),
				'labelAttr' => array(
					'class' => 'col-md-5 sgpb-choice-option-wrapper'
				),
				'groupWrapperAttr' => array(
					'class' => 'row form-group sgpb-choice-wrapper'
				)
			),
			'buttonPosition' => 'right',
			'nextNewLine' => true,
			'fields' => array(
				array(
					'attr' => array(
						'type' => 'radio',
						'name' => 'sgpb-social-share-url-type',
						'class' => 'sgpb-share-url-type',
						'data-attr-href' => '',
						'value' => 'activeUrl'
					),
					'label' => array(
						'name' => __('Use active URL', 'popup-builder').':'
					)
				),
				array(
					'attr' => array(
						'type' => 'radio',
						'name' => 'sgpb-social-share-url-type',
						'class' => 'sgpb-share-url-type',
						'data-attr-href' => 'sgpb-social-share-url-wrapper',
						'value' => 'shareUrl'
					),
					'label' => array(
						'name' => __('Share URL', 'popup-builder').':'
					)
				)
			)
		);

		$data['countdownDateFormat'] = array(
			'template' => array(
				'fieldWrapperAttr' => array(
					'class' => 'col-md-5 sgpb-choice-option-wrapper'
				),
				'labelAttr' => array(
					'class' => 'col-md-5 sgpb-choice-option-wrapper sgpb-sub-option-label'
				),
				'groupWrapperAttr' => array(
					'class' => 'row form-group sgpb-choice-wrapper'
				)
			),
			'buttonPosition' => 'right',
			'nextNewLine' => true,
			'fields' => array(
				array(
					'attr' => array(
						'type' => 'radio',
						'name' => 'sgpb-countdown-date-format',
						'class' => 'sgpb-countdown-date-format-from-date',
						'data-attr-href' => 'sgpb-countdown-date-format-from-date',
						'value' => 'date'
					),
					'label' => array(
						'name' => __('Due Date', 'popup-builder').':'
					)
				),
				array(
					'attr' => array(
						'type' => 'radio',
						'name' => 'sgpb-countdown-date-format',
						'class' => 'sgpb-countdown-date-format-from-date',
						'data-attr-href' => 'sgpb-countdown-date-format-from-input',
						'value' => 'input'
					),
					'label' => array(
						'name' => __('Timer', 'popup-builder').':'
					)
				)
			)
		);

		$data['socialShareTheme'] = array(
			array(
				'value' => 'flat',
				'data-attributes' => array(
					'class' => 'js-social-share-theme sgpb-social-popup-flat',
					'data-popup-theme-number' => 1
				)
			),
			array(
				'value' => 'classic',
				'data-attributes' => array(
					'class' => 'js-social-share-theme sgpb-social-popup-classic',
					'data-popup-theme-number' => 1
				)
			),
			array(
				'value' => 'minima',
				'data-attributes' => array(
					'class' => 'js-social-share-theme sgpb-social-popup-minima',
					'data-popup-theme-number' => 1
				)
			),
			array(
				'value' => 'plain',
				'data-attributes' => array(
					'class' => 'js-social-share-theme sgpb-social-popup-plain',
					'data-popup-theme-number' => 1
				)
			)
		);

		$data['socialThemeSizes'] = array(
			'8' => '8',
			'10' => '10',
			'12' => '12',
			'14' => '14',
			'16' => '16',
			'18' => '18',
			'20' => '20',
			'24' => '24'
		);

		$data['socialThemeShereCount'] = array(
			'true' => __('True', 'popup-builder'),
			'false' => __('False', 'popup-builder'),
			'inside' => __('Inside', 'popup-builder')
		);

		$data['popupInsertEventTypes'] = array(
			'inherit' => __('Inherit', 'popup-builder'),
			'onLoad' => __('On load', 'popup-builder'),
			'click' => __('On click', 'popup-builder'),
			'hover' => __('On hover', 'popup-builder')
		);

		$data['subscriptionSuccessBehavior'] = array(
			'template' => array(
				'fieldWrapperAttr' => array(
					'class' => 'col-md-6 sgpb-choice-option-wrapper'
				),
				'labelAttr' => array(
					'class' => 'subFormItem__title sgpb-margin-right-10'
				),
				'groupWrapperAttr' => array(
					'class' => 'subFormItem sgpb-choice-wrapper'
				)
			),
			'buttonPosition' => 'right',
			'nextNewLine' => true,
			'fields' => array(
				array(
					'attr' => array(
						'type' => 'radio',
						'name' => 'sgpb-subs-success-behavior',
						'class' => 'subs-success-message',
						'data-attr-href' => 'subs-show-success-message',
						'value' => 'showMessage'
					),
					'label' => array(
						'name' => __('Success message', 'popup-builder').':'
					)
				),
				array(
					'attr' => array(
						'type' => 'radio',
						'name' => 'sgpb-subs-success-behavior',
						'class' => 'subs-redirect-to-URL',
						'data-attr-href' => 'subs-redirect-to-URL',
						'value' => 'redirectToURL'
					),
					'label' => array(
						'name' => __('Redirect to URL', 'popup-builder').':'
					)
				),
				array(
					'attr' => array(
						'type' => 'radio',
						'name' => 'sgpb-subs-success-behavior',
						'class' => 'subs-success-open-popup',
						'data-attr-href' => 'subs-open-popup',
						'value' => 'openPopup'
					),
					'label' => array(
						'name' => __('Open popup', 'popup-builder').':'
					)
				),
				array(
					'attr' => array(
						'type' => 'radio',
						'name' => 'sgpb-subs-success-behavior',
						'class' => 'subs-hide-popup',
						'value' => 'hidePopup'
					),
					'label' => array(
						'name' => __('Hide popup', 'popup-builder').':'
					)
				)
			)
		);

		$data['buttonsType'] = array(
			'standard' => __('Standard', 'popup-builder'),
			'box_count' => __('Box with count', 'popup-builder'),
			'button_count' => __('Button with count', 'popup-builder'),
			'button' => __('Button', 'popup-builder')
		);

		$data['backroundImageModes'] = array(
			'no-repeat' => __('None', 'popup-builder'),
			'cover' => __('Cover', 'popup-builder'),
			'contain' => __('Contain', 'popup-builder'),
			'repeat' => __('Repeat', 'popup-builder')
		);

		$data['openAnimationEfects'] = array(
			'No effect' => __('None', 'popup-builder'),
			'sgpb-flip' => __('Flip', 'popup-builder'),
			'sgpb-shake' => __('Shake', 'popup-builder'),
			'sgpb-wobble' => __('Wobble', 'popup-builder'),
			'sgpb-swing' => __('Swing', 'popup-builder'),
			'sgpb-flash' => __('Flash', 'popup-builder'),
			'sgpb-bounce' => __('Bounce', 'popup-builder'),
			'sgpb-bounceInRight' => __('BounceInRight', 'popup-builder'),
			'sgpb-bounceIn' => __('BounceIn', 'popup-builder'),
			'sgpb-pulse' => __('Pulse', 'popup-builder'),
			'sgpb-rubberBand' => __('RubberBand', 'popup-builder'),
			'sgpb-tada' => __('Tada', 'popup-builder'),
			'sgpb-slideInUp' => __('SlideInUp', 'popup-builder'),
			'sgpb-jello' => __('Jello', 'popup-builder'),
			'sgpb-rotateIn' => __('RotateIn', 'popup-builder'),
			'sgpb-fadeIn' => __('FadeIn', 'popup-builder')
		);

		$data['closeAnimationEfects'] = array(
			'No effect' => __('None', 'popup-builder'),
			'sgpb-flipInX' => __('Flip', 'popup-builder'),
			'sgpb-shake' => __('Shake', 'popup-builder'),
			'sgpb-wobble' => __('Wobble', 'popup-builder'),
			'sgpb-swing' => __('Swing', 'popup-builder'),
			'sgpb-flash' => __('Flash', 'popup-builder'),
			'sgpb-bounce' => __('Bounce', 'popup-builder'),
			'sgpb-bounceOutLeft' => __('BounceOutLeft', 'popup-builder'),
			'sgpb-bounceOut' => __('BounceOut', 'popup-builder'),
			'sgpb-pulse' => __('Pulse', 'popup-builder'),
			'sgpb-rubberBand' => __('RubberBand', 'popup-builder'),
			'sgpb-tada' => __('Tada', 'popup-builder'),
			'sgpb-slideOutUp' => __('SlideOutUp', 'popup-builder'),
			'sgpb-jello' => __('Jello', 'popup-builder'),
			'sgpb-rotateOut' => __('RotateOut', 'popup-builder'),
			'sgpb-fadeOut' => __('FadeOut', 'popup-builder')
		);

		$data['floatingButtonPositionsCorner'] = array(
			'top-left' => __('Top left', 'popup-builder'),
			'top-right' => __('Top right', 'popup-builder'),
			'bottom-left' => __('Bottom left', 'popup-builder'),
			'bottom-right' => __('Bottom right', 'popup-builder')
		);

		$data['floatingButtonPositionsBasic'] = array(
			'top-left' => __('Top left', 'popup-builder'),
			'top-right' => __('Top right', 'popup-builder'),
			'bottom-left' => __('Bottom left', 'popup-builder'),
			'bottom-right' => __('Bottom right', 'popup-builder'),
			'top-center' => __('Top center', 'popup-builder'),
			'bottom-center' => __('Bottom center', 'popup-builder'),
			'right-center' => __('Right center', 'popup-builder'),
			'left-center' => __('Left center', 'popup-builder')
		);

		$data['floatingButtonStyle'] = array(
			'corner' => __('Corner', 'popup-builder'),
			'basic' => __('Basic', 'popup-builder')
		);

		$data['userRoles'] = self::getAllUserRoles();

		return $data;
	}

	public static function getAllUserRoles()
	{
		$rulesArray = array();
		if (!function_exists('get_editable_roles')){
			return $rulesArray;
		}

		$roles = get_editable_roles();
		foreach ($roles as $roleName => $roleInfo) {
			if ($roleName == 'administrator') {
				continue;
			}
			$rulesArray[$roleName] = $roleName;
		}

		return $rulesArray;
	}

	public static function getClickActionOptions()
	{
		$settings = array(
			'defaultClickClassName' => __('Default', 'popup-builder'),
			'clickActionCustomClass' => __('Custom class', 'popup-builder')
		);

		return $settings;
	}

	public static function getHoverActionOptions()
	{
		$settings = array(
			'defaultHoverClassName' => __('Default', 'popup-builder'),
			'hoverActionCustomClass' => __('Custom class', 'popup-builder')
		);

		return $settings;
	}

	// proStartSilver
	public static function getPopupDefaultTimeZone()
	{
		$timeZone = get_option('timezone_string');
		if (!$timeZone) {
			$timeZone = SG_POPUP_DEFAULT_TIME_ZONE;
		}

		return $timeZone;
	}
	// proEndSilver

	// proStartGold
	public static function getPopupTimeZone()
	{
		return array(
			'Pacific/Midway' => '(GMT-11:00) Midway',
			'Pacific/Niue' => '(GMT-11:00) Niue',
			'Pacific/Pago_Pago' => '(GMT-11:00) Pago Pago',
			'Pacific/Honolulu' => '(GMT-10:00) Hawaii Time',
			'Pacific/Rarotonga' => '(GMT-10:00) Rarotonga',
			'Pacific/Tahiti' => '(GMT-10:00) Tahiti',
			'Pacific/Marquesas' => '(GMT-09:30) Marquesas',
			'America/Anchorage' => '(GMT-09:00) Alaska Time',
			'Pacific/Gambier' => '(GMT-09:00) Gambier',
			'America/Los_Angeles' => '(GMT-08:00) Pacific Time',
			'America/Tijuana' => '(GMT-08:00) Pacific Time - Tijuana',
			'America/Vancouver' => '(GMT-08:00) Pacific Time - Vancouver',
			'America/Whitehorse' => '(GMT-08:00) Pacific Time - Whitehorse',
			'Pacific/Pitcairn' => '(GMT-08:00) Pitcairn',
			'America/Dawson_Creek' => '(GMT-07:00) Mountain Time - Dawson Creek',
			'America/Denver' => '(GMT-07:00) Mountain Time',
			'America/Edmonton' => '(GMT-07:00) Mountain Time - Edmonton',
			'America/Hermosillo' => '(GMT-07:00) Mountain Time - Hermosillo',
			'America/Mazatlan' => '(GMT-07:00) Mountain Time - Chihuahua, Mazatlan',
			'America/Phoenix' => '(GMT-07:00) Mountain Time - Arizona',
			'America/Yellowknife' => '(GMT-07:00) Mountain Time - Yellowknife',
			'America/Belize' => '(GMT-06:00) Belize',
			'America/Chicago' => '(GMT-06:00) Central Time',
			'America/Costa_Rica' => '(GMT-06:00) Costa Rica',
			'America/El_Salvador' => '(GMT-06:00) El Salvador',
			'America/Guatemala' => '(GMT-06:00) Guatemala',
			'America/Managua' => '(GMT-06:00) Managua',
			'America/Mexico_City' => '(GMT-06:00) Central Time - Mexico City',
			'America/Regina' => '(GMT-06:00) Central Time - Regina',
			'America/Tegucigalpa' => '(GMT-06:00) Central Time - Tegucigalpa',
			'America/Winnipeg' => '(GMT-06:00) Central Time - Winnipeg',
			'Pacific/Easter' => '(GMT-06:00) Easter Island',
			'Pacific/Galapagos' => '(GMT-06:00) Galapagos',
			'America/Bogota' => '(GMT-05:00) Bogota',
			'America/Cayman' => '(GMT-05:00) Cayman',
			'America/Guayaquil' => '(GMT-05:00) Guayaquil',
			'America/Havana' => '(GMT-05:00) Havana',
			'America/Iqaluit' => '(GMT-05:00) Eastern Time - Iqaluit',
			'America/Jamaica' => '(GMT-05:00) Jamaica',
			'America/Lima' => '(GMT-05:00) Lima',
			'America/Montreal' => '(GMT-05:00) Eastern Time - Montreal',
			'America/Nassau' => '(GMT-05:00) Nassau',
			'America/New_York' => '(GMT-05:00) Eastern Time',
			'America/Panama' => '(GMT-05:00) Panama',
			'America/Port-au-Prince' => '(GMT-05:00) Port-au-Prince',
			'America/Rio_Branco' => '(GMT-05:00) Rio Branco',
			'America/Toronto' => '(GMT-05:00) Eastern Time - Toronto',
			'America/Caracas' => '(GMT-04:30) Caracas',
			'America/Antigua' => '(GMT-04:00) Antigua',
			'America/Asuncion' => '(GMT-04:00) Asuncion',
			'America/Barbados' => '(GMT-04:00) Barbados',
			'America/Boa_Vista' => '(GMT-04:00) Boa Vista',
			'America/Campo_Grande' => '(GMT-04:00) Campo Grande',
			'America/Cuiaba' => '(GMT-04:00) Cuiaba',
			'America/Curacao' => '(GMT-04:00) Curacao',
			'America/Grand_Turk' => '(GMT-04:00) Grand Turk',
			'America/Guyana' => '(GMT-04:00) Guyana',
			'America/Halifax' => '(GMT-04:00) Atlantic Time - Halifax',
			'America/La_Paz' => '(GMT-04:00) La Paz',
			'America/Manaus' => '(GMT-04:00) Manaus',
			'America/Martinique' => '(GMT-04:00) Martinique',
			'America/Port_of_Spain' => '(GMT-04:00) Port of Spain',
			'America/Porto_Velho' => '(GMT-04:00) Porto Velho',
			'America/Puerto_Rico' => '(GMT-04:00) Puerto Rico',
			'America/Santiago' => '(GMT-04:00) Santiago',
			'America/Santo_Domingo' => '(GMT-04:00) Santo Domingo',
			'America/Thule' => '(GMT-04:00) Thule',
			'Antarctica/Palmer' => '(GMT-04:00) Palmer',
			'Atlantic/Bermuda' => '(GMT-04:00) Bermuda',
			'America/St_Johns' => '(GMT-03:30) Newfoundland Time - St. Johns',
			'America/Araguaina' => '(GMT-03:00) Araguaina',
			'America/Argentina/Buenos_Aires' => '(GMT-03:00) Buenos Aires',
			'America/Bahia' => '(GMT-03:00) Salvador',
			'America/Belem' => '(GMT-03:00) Belem',
			'America/Cayenne' => '(GMT-03:00) Cayenne',
			'America/Fortaleza' => '(GMT-03:00) Fortaleza',
			'America/Godthab' => '(GMT-03:00) Godthab',
			'America/Maceio' => '(GMT-03:00) Maceio',
			'America/Miquelon' => '(GMT-03:00) Miquelon',
			'America/Montevideo' => '(GMT-03:00) Montevideo',
			'America/Paramaribo' => '(GMT-03:00) Paramaribo',
			'America/Recife' => '(GMT-03:00) Recife',
			'America/Sao_Paulo' => '(GMT-03:00) Sao Paulo',
			'Antarctica/Rothera' => '(GMT-03:00) Rothera',
			'Atlantic/Stanley' => '(GMT-03:00) Stanley',
			'America/Noronha' => '(GMT-02:00) Noronha',
			'Atlantic/South_Georgia' => '(GMT-02:00) South Georgia',
			'America/Scoresbysund' => '(GMT-01:00) Scoresbysund',
			'Atlantic/Azores' => '(GMT-01:00) Azores',
			'Atlantic/Cape_Verde' => '(GMT-01:00) Cape Verde',
			'Africa/Abidjan' => '(GMT+00:00) Abidjan',
			'Africa/Accra' => '(GMT+00:00) Accra',
			'Africa/Bissau' => '(GMT+00:00) Bissau',
			'Africa/Casablanca' => '(GMT+00:00) Casablanca',
			'Africa/El_Aaiun' => '(GMT+00:00) El Aaiun',
			'Africa/Monrovia' => '(GMT+00:00) Monrovia',
			'America/Danmarkshavn' => '(GMT+00:00) Danmarkshavn',
			'Atlantic/Canary' => '(GMT+00:00) Canary Islands',
			'Atlantic/Faroe' => '(GMT+00:00) Faeroe',
			'Atlantic/Reykjavik' => '(GMT+00:00) Reykjavik',
			'Etc/GMT' => '(GMT+00:00) GMT (no daylight saving)',
			'Europe/Dublin' => '(GMT+00:00) Dublin',
			'Europe/Lisbon' => '(GMT+00:00) Lisbon',
			'Europe/London' => '(GMT+00:00) London',
			'Africa/Algiers' => '(GMT+01:00) Algiers',
			'Africa/Ceuta' => '(GMT+01:00) Ceuta',
			'Africa/Lagos' => '(GMT+01:00) Lagos',
			'Africa/Ndjamena' => '(GMT+01:00) Ndjamena',
			'Africa/Tunis' => '(GMT+01:00) Tunis',
			'Africa/Windhoek' => '(GMT+01:00) Windhoek',
			'Europe/Amsterdam' => '(GMT+01:00) Amsterdam',
			'Europe/Andorra' => '(GMT+01:00) Andorra',
			'Europe/Belgrade' => '(GMT+01:00) Central European Time - Belgrade',
			'Europe/Berlin' => '(GMT+01:00) Berlin',
			'Europe/Brussels' => '(GMT+01:00) Brussels',
			'Europe/Budapest' => '(GMT+01:00) Budapest',
			'Europe/Copenhagen' => '(GMT+01:00) Copenhagen',
			'Europe/Gibraltar' => '(GMT+01:00) Gibraltar',
			'Europe/Luxembourg' => '(GMT+01:00) Luxembourg',
			'Europe/Madrid' => '(GMT+01:00) Madrid',
			'Europe/Malta' => '(GMT+01:00) Malta',
			'Europe/Monaco' => '(GMT+01:00) Monaco',
			'Europe/Oslo' => '(GMT+01:00) Oslo',
			'Europe/Paris' => '(GMT+01:00) Paris',
			'Europe/Prague' => '(GMT+01:00) Central European Time - Prague',
			'Europe/Rome' => '(GMT+01:00) Rome',
			'Europe/Stockholm' => '(GMT+01:00) Stockholm',
			'Europe/Tirane' => '(GMT+01:00) Tirane',
			'Europe/Vienna' => '(GMT+01:00) Vienna',
			'Europe/Warsaw' => '(GMT+01:00) Warsaw',
			'Europe/Zurich' => '(GMT+01:00) Zurich',
			'Africa/Cairo' => '(GMT+02:00) Cairo',
			'Africa/Johannesburg' => '(GMT+02:00) Johannesburg',
			'Africa/Maputo' => '(GMT+02:00) Maputo',
			'Africa/Tripoli' => '(GMT+02:00) Tripoli',
			'Asia/Amman' => '(GMT+02:00) Amman',
			'Asia/Beirut' => '(GMT+02:00) Beirut',
			'Asia/Damascus' => '(GMT+02:00) Damascus',
			'Asia/Gaza' => '(GMT+02:00) Gaza',
			'Asia/Jerusalem' => '(GMT+02:00) Jerusalem',
			'Asia/Nicosia' => '(GMT+02:00) Nicosia',
			'Europe/Athens' => '(GMT+02:00) Athens',
			'Europe/Bucharest' => '(GMT+02:00) Bucharest',
			'Europe/Chisinau' => '(GMT+02:00) Chisinau',
			'Europe/Helsinki' => '(GMT+02:00) Helsinki',
			'Europe/Istanbul' => '(GMT+02:00) Istanbul',
			'Europe/Kaliningrad' => '(GMT+02:00) Moscow-01 - Kaliningrad',
			'Europe/Kiev' => '(GMT+02:00) Kiev',
			'Europe/Riga' => '(GMT+02:00) Riga',
			'Europe/Sofia' => '(GMT+02:00) Sofia',
			'Europe/Tallinn' => '(GMT+02:00) Tallinn',
			'Europe/Vilnius' => '(GMT+02:00) Vilnius',
			'Africa/Addis_Ababa' => '(GMT+03:00) Addis Ababa',
			'Africa/Asmara' => '(GMT+03:00) Asmera',
			'Africa/Dar_es_Salaam' => '(GMT+03:00) Dar es Salaam',
			'Africa/Djibouti' => '(GMT+03:00) Djibouti',
			'Africa/Kampala' => '(GMT+03:00) Kampala',
			'Africa/Khartoum' => '(GMT+03:00) Khartoum',
			'Africa/Mogadishu' => '(GMT+03:00) Mogadishu',
			'Africa/Nairobi' => '(GMT+03:00) Nairobi',
			'Antarctica/Syowa' => '(GMT+03:00) Syowa',
			'Asia/Aden' => '(GMT+03:00) Aden',
			'Asia/Baghdad' => '(GMT+03:00) Baghdad',
			'Asia/Bahrain' => '(GMT+03:00) Bahrain',
			'Asia/Kuwait' => '(GMT+03:00) Kuwait',
			'Asia/Qatar' => '(GMT+03:00) Qatar',
			'Asia/Riyadh' => '(GMT+03:00) Riyadh',
			'Europe/Minsk' => '(GMT+03:00) Minsk',
			'Europe/Moscow' => '(GMT+03:00) Moscow+00',
			'Indian/Antananarivo' => '(GMT+03:00) Antananarivo',
			'Indian/Comoro' => '(GMT+03:00) Comoro',
			'Indian/Mayotte' => '(GMT+03:00) Mayotte',
			'Asia/Tehran' => '(GMT+03:30) Tehran',
			'Asia/Baku' => '(GMT+04:00) Baku',
			'Asia/Dubai' => '(GMT+04:00) Dubai',
			'Asia/Muscat' => '(GMT+04:00) Muscat',
			'Asia/Tbilisi' => '(GMT+04:00) Tbilisi',
			'Asia/Yerevan' => '(GMT+04:00) Yerevan',
			'Europe/Samara' => '(GMT+04:00) Moscow+00 - Samara',
			'Indian/Mahe' => '(GMT+04:00) Mahe',
			'Indian/Mauritius' => '(GMT+04:00) Mauritius',
			'Indian/Reunion' => '(GMT+04:00) Reunion',
			'Asia/Kabul' => '(GMT+04:30) Kabul',
			'Antarctica/Mawson' => '(GMT+05:00) Mawson',
			'Asia/Aqtau' => '(GMT+05:00) Aqtau',
			'Asia/Aqtobe' => '(GMT+05:00) Aqtobe',
			'Asia/Ashgabat' => '(GMT+05:00) Ashgabat',
			'Asia/Dushanbe' => '(GMT+05:00) Dushanbe',
			'Asia/Karachi' => '(GMT+05:00) Karachi',
			'Asia/Tashkent' => '(GMT+05:00) Tashkent',
			'Asia/Yekaterinburg' => '(GMT+05:00) Moscow+02 - Yekaterinburg',
			'Indian/Kerguelen' => '(GMT+05:00) Kerguelen',
			'Indian/Maldives' => '(GMT+05:00) Maldives',
			'Asia/Calcutta' => '(GMT+05:30) India Standard Time',
			'Asia/Colombo' => '(GMT+05:30) Colombo',
			'Asia/Katmandu' => '(GMT+05:45) Katmandu',
			'Antarctica/Vostok' => '(GMT+06:00) Vostok',
			'Asia/Almaty' => '(GMT+06:00) Almaty',
			'Asia/Bishkek' => '(GMT+06:00) Bishkek',
			'Asia/Dhaka' => '(GMT+06:00) Dhaka',
			'Asia/Omsk' => '(GMT+06:00) Moscow+03 - Omsk, Novosibirsk',
			'Asia/Thimphu' => '(GMT+06:00) Thimphu',
			'Indian/Chagos' => '(GMT+06:00) Chagos',
			'Asia/Rangoon' => '(GMT+06:30) Rangoon',
			'Indian/Cocos' => '(GMT+06:30) Cocos',
			'Antarctica/Davis' => '(GMT+07:00) Davis',
			'Asia/Bangkok' => '(GMT+07:00) Bangkok',
			'Asia/Hovd' => '(GMT+07:00) Hovd',
			'Asia/Jakarta' => '(GMT+07:00) Jakarta',
			'Asia/Krasnoyarsk' => '(GMT+07:00) Moscow+04 - Krasnoyarsk',
			'Asia/Saigon' => '(GMT+07:00) Hanoi',
			'Indian/Christmas' => '(GMT+07:00) Christmas',
			'Antarctica/Casey' => '(GMT+08:00) Casey',
			'Asia/Brunei' => '(GMT+08:00) Brunei',
			'Asia/Choibalsan' => '(GMT+08:00) Choibalsan',
			'Asia/Hong_Kong' => '(GMT+08:00) Hong Kong',
			'Asia/Irkutsk' => '(GMT+08:00) Moscow+05 - Irkutsk',
			'Asia/Kuala_Lumpur' => '(GMT+08:00) Kuala Lumpur',
			'Asia/Macau' => '(GMT+08:00) Macau',
			'Asia/Makassar' => '(GMT+08:00) Makassar',
			'Asia/Manila' => '(GMT+08:00) Manila',
			'Asia/Shanghai' => '(GMT+08:00) China Time - Beijing',
			'Asia/Singapore' => '(GMT+08:00) Singapore',
			'Asia/Taipei' => '(GMT+08:00) Taipei',
			'Asia/Ulaanbaatar' => '(GMT+08:00) Ulaanbaatar',
			'Australia/Perth' => '(GMT+08:00) Western Time - Perth',
			'Asia/Dili' => '(GMT+09:00) Dili',
			'Asia/Jayapura' => '(GMT+09:00) Jayapura',
			'Asia/Pyongyang' => '(GMT+09:00) Pyongyang',
			'Asia/Seoul' => '(GMT+09:00) Seoul',
			'Asia/Tokyo' => '(GMT+09:00) Tokyo',
			'Asia/Yakutsk' => '(GMT+09:00) Moscow+06 - Yakutsk',
			'Pacific/Palau' => '(GMT+09:00) Palau',
			'Australia/Adelaide' => '(GMT+09:30) Central Time - Adelaide',
			'Australia/Darwin' => '(GMT+09:30) Central Time - Darwin',
			'Antarctica/DumontDUrville' => '(GMT+10:00) Dumont D\'Urville',
			'Asia/Magadan' => '(GMT+10:00) Moscow+08 - Magadan',
			'Asia/Vladivostok' => '(GMT+10:00) Moscow+07 - Yuzhno-Sakhalinsk',
			'Australia/Brisbane' => '(GMT+10:00) Eastern Time - Brisbane',
			'Australia/Hobart' => '(GMT+10:00) Eastern Time - Hobart',
			'Australia/Sydney' => '(GMT+10:00) Eastern Time - Melbourne, Sydney',
			'Pacific/Chuuk' => '(GMT+10:00) Truk',
			'Pacific/Guam' => '(GMT+10:00) Guam',
			'Pacific/Port_Moresby' => '(GMT+10:00) Port Moresby',
			'Pacific/Saipan' => '(GMT+10:00) Saipan',
			'Pacific/Efate' => '(GMT+11:00) Efate',
			'Pacific/Guadalcanal' => '(GMT+11:00) Guadalcanal',
			'Pacific/Kosrae' => '(GMT+11:00) Kosrae',
			'Pacific/Noumea' => '(GMT+11:00) Noumea',
			'Pacific/Pohnpei' => '(GMT+11:00) Ponape',
			'Pacific/Norfolk' => '(GMT+11:30) Norfolk',
			'Asia/Kamchatka' => '(GMT+12:00) Moscow+08 - Petropavlovsk-Kamchatskiy',
			'Pacific/Auckland' => '(GMT+12:00) Auckland',
			'Pacific/Fiji' => '(GMT+12:00) Fiji',
			'Pacific/Funafuti' => '(GMT+12:00) Funafuti',
			'Pacific/Kwajalein' => '(GMT+12:00) Kwajalein',
			'Pacific/Majuro' => '(GMT+12:00) Majuro',
			'Pacific/Nauru' => '(GMT+12:00) Nauru',
			'Pacific/Tarawa' => '(GMT+12:00) Tarawa',
			'Pacific/Wake' => '(GMT+12:00) Wake',
			'Pacific/Wallis' => '(GMT+12:00) Wallis',
			'Pacific/Apia' => '(GMT+13:00) Apia',
			'Pacific/Enderbury' => '(GMT+13:00) Enderbury',
			'Pacific/Fakaofo' => '(GMT+13:00) Fakaofo',
			'Pacific/Tongatapu' => '(GMT+13:00) Tongatapu',
			'Pacific/Kiritimati' => '(GMT+14:00) Kiritimati'
		);
	}

	public static function getJsLocalizedData()
	{
		$translatedData = array(
			'imageSupportAlertMessage' => __('Only image files supported', 'popup-builder'),
			'pdfSupportAlertMessage' => __('Only pdf files supported', 'popup-builder'),
			'areYouSure' => __('Are you sure?', 'popup-builder'),
			'addButtonSpinner' => __('L', 'popup-builder'),
			'audioSupportAlertMessage' => __('Only audio files supported (e.g.: mp3, wav, m4a, ogg)', 'popup-builder'),
			'publishPopupBeforeElementor' => __('Please, publish the popup before starting to use Elementor with it!', 'popup-builder'),
			'publishPopupBeforeDivi' => __('Please, publish the popup before starting to use Divi Builder with it!', 'popup-builder'),
			'closeButtonAltText' =>  __('Close', 'popup-builder')
		);

		return $translatedData;
	}

	public static function getCurrentDateTime()
	{
		return gmdate('Y-m-d H:i', strtotime(' +1 day'));
	}

	public static function getDefaultTimezone()
	{
		$timezone = get_option('timezone_string');
		if (!$timezone) {
			$timezone = 'America/New_York';
		}

		return $timezone;
	}
}

