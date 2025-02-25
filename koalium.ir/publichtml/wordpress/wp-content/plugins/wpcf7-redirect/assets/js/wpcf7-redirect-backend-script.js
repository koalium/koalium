var wpcf7_redirect_admin;

(function ($) {
	function Wpcf7_redirect_admin() {

		/**
		 * Initialize the class
		 * @return {[type]} [description]
		 */
		this.init = function () {
			this.setparams();
			//set hooks for handling the redirect settings tab
			this.admin_field_handlers();
			//set hooks
			this.register_action_hooks();
			//hide select options
			this.hide_select_options();
			//init drag and drop features
			this.init_draggable();
			this.renumber_rows();

			this.admin_validations = new Wpcf7_admin_validations($);

			this.init_select2();

			this.init_media_field();
			this.init_colorpickers();
			this.mark_default_select_fields();
		};

		/**
		 * Avoid alert while trying to leave page
		 */
		this.mark_default_select_fields = function () {
			$('.action-container select').each(function () {
				if ($(this).val() === 0 || !$(this).val()) {
					$(this).find('option:first-child').prop('selected', 'selected')
				}
			})
		}

		this.init_editors = function ($editor_action_wrap) {
			if ('undefined' !== typeof tinymce && 'undefined' !== typeof tinyMCEPreInit) {
				editor_id = $editor_action_wrap.find('textarea').prop('id');

				if (editor_id) {
					try {
						tinymce.init(editor_id, { selector: 'textarea' });
						tinymce.execCommand('mceAddEditor', false, editor_id);
						quicktags({ id: editor_id });

					} catch (err) {
						console.log(err);
					}
				}
			}
		}
		this.init_colorpickers = function () {
			$('input.colorpicker').addClass('rendered').wpColorPicker();
		}

		this.setparams = function () {
			/**
			 * Define jquery selectors
			 * @type {String}
			 */
			this.banner_selector = '.wpcfr-banner-holder';
			this.add_and_selector = '.add-condition';
			this.row_template_selector = '.row-template';
			this.remove_and_selector = '.qs-condition-actions .dashicons-minus';
			this.add_group_button_selector = '.wpcfr-add-group';
			this.rule_group_selector = '.wpcfr-rule-group';
			this.edit_block_title_selector = '.conditional-group-titles .dashicons-edit';
			this.cancel_block_title_selector = '.conditional-group-titles .dashicons-no';
			this.save_block_title_edit_selector = '.conditional-group-titles .dashicons-yes';
			this.tab_title_all_selector = '.block-title';
			this.tab_title_selector = '.block-title:not(.edit)';
			this.tab_title_active_selector = '.block-title.active';
			this.active_tab_selector = '.conditional-group-block.active';
			this.tab_inner_title = '.conditional-group-block-title';
			this.new_block_counter = 1;
			this.add_block_button_selector = '.wpcf7r-add-block';
			this.blocks_container_selector = '.conditional-group-blocks';
			this.remove_block_button_selector = '.remove-block';
			this.group_row_value_select_selector = '.group_row_value_select';
			this.group_select_field_selector = '.wpcf7r-fields';
			this.open_tab_selector = '#redirect-panel h3[data-tab-target]';
			this.show_action_selector = '.actions-list .edit a';
			this.move_action_to_trash_selector = '.actions-list .row-actions .trash a';
			this.dupicate_action_selector = '.actions-list .row-actions .duplicate a';
			this.move_lead_to_trash_selector = '.leads-list .trash a';
			this.add_new_action_selector = '.wpcf7-add-new-action';
			this.custom_checkbox_selector = '.wpcf7r-checkbox input';
			this.action_title_field = '.wpcf7-redirect-post-title-fields';
			this.migrate_from_cf7_api_selector = '.migrate-from-send-to-api';
			this.migrate_from_cf7_redirect_selector = '.migrate-from-redirection';
			this.json_textarea_selector = '.json-container';
			this.butify_button_selector = '.wpcf7-redirect-butify';
			this.add_repeater_field_selector = '.qs-repeater-action .dashicons-plus';
			this.remove_repeater_field_selector = '.qs-repeater-action .dashicons-minus';
			this.api_test_button_selector = '.wpcf7-redirect-test_button-fields';
			this.toggler_handler_selector = '.actions-list [data-toggle] input';
			this.select_toggler_selector = '[data-toggler-name]';
			this.select_action_selector = '[name="new-action-selector"]';
			this.mailchimp_get_lists = '.wpcf7-redirect-get_mailchimp_lists-fields';
			this.mailchimp_create_list = '.wpcf7-redirect-create_list-fields';
			this.mailchimp_list_selector = '.field-wrap-mailchimp_list_id select';
			this.tab_actions_selector = '[href="#redirect-panel"]';
			this.pro_banner_submit_btn_selector = '.btn-rp-submit';
			this.pro_banner_user_email_selecttor = '[name="rp_user_email"]';
			this.new_group_counter = 0;
			this.reset_all_button = '.cf7-redirect-reset';
			this.new_row_counter = 0;
			this.mail_tags_toggle = '.mail-tags-title';
			this.validate_salesforce_app_details = '.wpcf7-redirect-validate_connection-fields';
			this.debug_send_button_selector = '.send-debug-info';
			this.close_popup_button_selector = '.wpcfr-close-popup';

		}

		/**
		 * Initialize Select 2 fields
		 */
		this.init_select2 = function (e) {
			$('.select2-field select:not(.rendered)').each(function () {
				var options = {
					width: 'resolve'
				};
				$('.select2-field select:not(.rendered)').select2(options).addClass('rendered');
			});
		}

		/**
		 * Init wp media uploader
		 */
		this.init_media_field = function () {
			var $imgContainer, imgIdInput = '';

			if (typeof wp.media == 'undefined') {
				console.log('no media support');
				return;
			}

			file_frame = wp.media.frames.file_frame = wp.media({
				frame: 'post',
				state: 'insert',
				multiple: false
			});

			file_frame.on('insert', function (e) {
				// Get media attachment details from the frame state
				var attachment = file_frame.state().get('selection').first().toJSON();

				if ($imgContainer.hasClass('file-container')) {
					$imgContainer.find('.file-url').val(attachment.url);
				} else {
					$imgContainer.find('.popup-image').remove();
					// Send the attachment URL to our custom image input field.
					$imgContainer.prepend('<img src="' + attachment.url + '" alt="" style="max-width:100px;" class="popup-image"/>');
				}

				// Send the attachment id to our hidden input
				imgIdInput.val(attachment.url).change();
			});

			$(document.body).on('click', '.image-uploader-btn', function () {
				imgIdInput = $(this).parent().find('input[type=hidden]');
				$imgContainer = $(this).parent();
				file_frame.open();
			});

			$(document.body).on('click', '.image-remove-btn', function () {
				$imgIdInput = $(this).parent().find('input[type=hidden]');
				$imgContainer = $(this).parent();

				if ($imgContainer.hasClass('file-container')) {
					$imgContainer.find('.file-url').val('');
				} else {
					$imgContainer.find('img').remove();
				}

				$imgIdInput.val('');
			});
		}

		/**
		 * Beutify the user input (XML/JSON)
		 * @param  {[type]} e [description]
		 * @return {[type]}   [description]
		 */
		this.beutify_json_and_css = function (e) {
			e.preventDefault();
			$clicked_button = $(e.currentTarget);
			var $parent = $clicked_button.parents('.hidden-action');
			var record_type = $parent.find('.field-wrap-record_type select').val();
			this.remove_errors();
			var $textarea = $('textarea', $parent);
			var string = $textarea.val();
			try {
				if (record_type == 'json') {
					var json_object = jQuery.parseJSON(string);
					if (json_object) {
						string = JSON.stringify(json_object, null, "\t");
						if (string) {
							$textarea.val(string);
						}
					}
				} else if (record_type == 'xml') {
					var xml_object = jQuery.parseXML(string);
					if (xml_object) {
						var xmlString = (new XMLSerializer()).serializeToString(xml_object);
						$textarea.val(xmlString);
					}
				}
			} catch (err) {
				this.add_error($textarea, err);
			}
		}

		/**
		 * Init sortable elements
		 */
		this.init_draggable = function () {
			var _this = this;

			$('#the_list').sortable({
				'items': '.drag',
				'axis': 'y',
				'helper': fixHelper,
				'update': function (e, ui) {
					params = {
						'order': $('#the_list').sortable('serialize')
					};
					_this.make_ajax_call('wpcf7r_set_action_menu_order', params, 'after_ajax_call');

					var actionid = $(ui.item).data('actionid');

					$(ui.item).after($('.action-container[data-actionid=' + actionid + ']'));
					_this.renumber_rows();
				}
			});

			var fixHelper = function (e, ui) {
				ui.children().children().each(function () {
					$(this).width($(this).width());
				});
				return ui;
			};
		}

		/**
		 * Replace or add query parameter to url
		 */
		this.replace_query_var = function (uri, key, value) {
			var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
			var separator = uri.indexOf('?') !== -1 ? "&" : "?";
			if (uri.match(re)) {
				return uri.replace(re, '$1' + key + "=" + value + '$2');
			}
			else {
				return uri + separator + key + "=" + value;
			}
		}

		/**
		 * A callback used to rearange numbering after sort
		 * @return {[type]} [description]
		 */
		this.renumber_rows = function () {
			numbering = 1;
			$('#the_list tr .num:visible').each(function () {
				$(this).html(numbering);
				numbering++;
			});
		}

		/**
		 * Register all the event handlers
		 * @return {[type]} [description]
		 */
		this.register_action_hooks = function () {

			//add and rule
			$(document.body).on('click', this.add_and_selector, this.add_and_row.bind(this));
			//remove rule
			$(document.body).on('click', this.remove_and_selector, this.remove_and_row.bind(this));
			//add group
			$(document.body).on('click', this.add_group_button_selector, this.add_new_group.bind(this));
			//edit group block title
			$(document.body).on('click', this.edit_block_title_selector, this.edit_block_title.bind(this));
			//cancel group block title change
			$(document.body).on('click', this.cancel_block_title_selector, this.cancel_block_title_edit.bind(this));
			//save group block title change
			$(document.body).on('click', this.save_block_title_edit_selector, this.save_block_title_edit.bind(this));
			//change tab
			$(document.body).on('click', this.tab_title_selector, this.switch_tab.bind(this));
			//add a new rule block
			$(document.body).on('click', this.add_block_button_selector, this.add_new_block.bind(this));
			//remove block button
			$(document.body).on('click', this.remove_block_button_selector, this.remove_block.bind(this));
			//set select value
			$(document.body).on('change', this.group_row_value_select_selector, this.set_select_value.bind(this));
			//set select value
			$(document.body).on('change', this.group_select_field_selector, this.show_field_options.bind(this));
			//show/hide tabs
			$(document.body).on('click', this.open_tab_selector, this.show_hide_tab.bind(this));
			//show action
			$(document.body).on('click', this.show_action_selector, this.show_hide_action.bind(this));
			//move action to trash
			$(document.body).on('click', this.move_action_to_trash_selector, this.move_post_to_trash.bind(this));
			//move lead to trash
			$(document.body).on('click', this.move_lead_to_trash_selector, this.move_post_to_trash.bind(this));
			//after ajax call handler
			$(document.body).on('wpcf7r_after_ajax_call', this.after_ajax_call.bind(this));
			//add new action
			$(document.body).on('click', this.add_new_action_selector, this.add_new_action.bind(this));
			//checkbox change event
			$(document.body).on('change', this.custom_checkbox_selector, this.checkbox_changed.bind(this));
			//title change
			$(document.body).on('keyup', this.action_title_field, this.action_title_field_changed.bind(this));
			//migrate from wp7 api
			$(document.body).on('click', this.migrate_from_cf7_api_selector, this.migrate_from_cf7_api.bind(this));
			//migrate from wp7 redirect
			$(document.body).on('click', this.migrate_from_cf7_redirect_selector, this.migrate_from_cf7_api.bind(this));
			//butify json and xml
			$(document.body).on('click', this.butify_button_selector, this.beutify_json_and_css.bind(this));
			//add repeater field
			$(document.body).on('click', this.add_repeater_field_selector, this.add_repeating_field.bind(this));
			//remove repeater field
			$(document.body).on('click', this.remove_repeater_field_selector, this.remove_repeating_field.bind(this));
			//make API test
			$(document.body).on('click', this.api_test_button_selector, this.make_api_test_call.bind(this));
			//data toggler function
			$(document.body).on('change', this.toggler_handler_selector, this.data_toggler.bind(this));
			//display content according to select field
			$(document.body).on('change', this.select_toggler_selector, this.select_toggler.bind(this));
			//get mailchimp lists
			$(document.body).on('click', this.mailchimp_get_lists, this.mailchimp_get_lists_handler.bind(this));
			//create mailchimp list
			$(document.body).on('click', this.mailchimp_create_list, this.mailchimp_create_list_handler.bind(this));
			//change the selected list calback
			$(document.body).on('change', this.mailchimp_list_selector, this.mailchimp_list_select_handler.bind(this));
			//reset all plugin settings
			$(document.body).on('click', this.reset_all_button, this.reset_all_settings.bind(this));
			//toggle mail tags
			$(document.body).on('click', this.mail_tags_toggle, this.toggle_mail_tags.bind(this));
			//duplicate action
			$(document.body).on('click', this.dupicate_action_selector, this.duplicate_action.bind(this));
			//close general admin popups
			$(document.body).on('click' , this.close_popup_button_selector, this.close_popup.bind(this));
			//change selected action
			$(document.body).on('change', this.select_action_selector, this.select_action.bind(this));
		}

		/**
		 * close open popups
		 * @param {*} e
		 */
		this.close_popup = function(e){
			$('.wpcfr-popup-wrap').remove();
		}
		/**
		 * show or hide mail tags section
		 * @param {event} e
		 */
		this.toggle_mail_tags = function (e) {
			var $clicked_button = $(e.currentTarget);
			$clicked_button.next().slideToggle('fast');
		}

		/**
		 * Gets the available mailchimp lists
		 * @param  {[type]} e [description]
		 * @return {[type]}   [description]
		 */
		this.mailchimp_create_list_handler = function (e) {
			var $clicked_button = $(e.currentTarget);
			var $parent_action = $clicked_button.parents('.action-container').first();
			var list_name = prompt("Please enter list name");
			if (list_name != null) {
				this.mailchimp_get_lists_handler(e, list_name);
			}
		}

		/**
		 * Set the available mailchimp tags on the merge table
		 * @return {[type]} [description]
		 */
		this.mailchimp_list_select_handler = function (e) {
			var $changed_element = $(e.currentTarget);
			var $parent_action = $changed_element.parents('.action-container').first();
			var lists = $parent_action.find('.field-wrap-mailchimp_settings').data('lists');
			var selected_list = $parent_action.find(this.mailchimp_list_selector).val();
			lists = this.maybe_parse_json(lists);
			list_fields = lists[selected_list].list_fields;
			merge_tags_selects = $parent_action.find('.field-wrap-mailchimp_key select');
			$.each(merge_tags_selects, function (key, select) {
				$(select).html('');
				var o = '<option value="">Select field</option>';
				$(select).append(o);
				$.each(list_fields, function (k, v) {
					var o = '<option value="' + k + '">' + v + '</option>';
					$(select).append(o);
				});
			});
		}

		/**
		 * Try to parse the string
		 * OR return already json
		 * @param  {[type]} string [description]
		 * @return {[type]}        [description]
		 */
		this.maybe_parse_json = function (string) {
			try {
				a = JSON.parse(string);
			} catch (e) {
				a = string;
			}
			return a;
		}

		this.mailchimp_get_lists_handler = function (e, list_name) {
			var $clicked_button = $(e.currentTarget);
			var $parent_action = $clicked_button.parents('.action-container').first();
			this.remove_errors();

			if (!$('.wpcf7-redirect-mailchimp_api_key-fields').val()) {
				this.add_error('.wpcf7-redirect-mailchimp_api_key-fields', 'Add your key and save the form');
				return false;
			}

			this.show_action_loader($clicked_button);

			params = {
				'action_id': this.get_block_action_id($clicked_button),
				'mailchimp_api_key': $parent_action.find('.wpcf7-redirect-mailchimp_api_key-fields').val(),
				'list_name': list_name
			};

			this.make_ajax_call('wpcf7r_get_mailchimp_lists', params, 'after_ajax_call');
		}

		/**
		 * Show ajax loader on the open action tab
		 * @param  {[type]} $inner_element [description]
		 * @return {[type]}                [description]
		 */
		this.show_action_loader = function ($inner_element) {
			var $action_wrap = $inner_element.parents('.field-wrap-test_section').first();
			this.show_loader($inner_element.parents('.hidden-action').first());
		}

		/**
		 * Handle toggling display view according to select field.
		 * @param {*} e
		 */
		this.select_toggler = function (e) {
			var $select = $(e.currentTarget);

			var toggler_name = $select.data('toggler-name');
			var selected_value = $select.val();

			$('.' + toggler_name).hide();

			if (selected_value) {
				$('.' + toggler_name + '_' + selected_value).show();
			}
		}

		/**
		 * Handle action select change.
		 * @param {*} e
		 */
		this.select_action = function (e) {
			var $select = $(e.currentTarget);
			var action = $select.find(':selected').attr('data-action');
			if ('purchase' === action) {
				$('a.wpcf7-add-new-action').text( 'Get Addon' );
				return;
			}
			$('a.wpcf7-add-new-action').text( 'Add Action' );
		}

		this.data_toggler = function (e) {
			//prevent checkbox input from firing duplicated event but keep its original functionality
			var $clicked_button = $(e.currentTarget);
			var $parent_action = $clicked_button.parents('.action-container').first();
			var toggle_element = $clicked_button.parents('[data-toggle]').data('toggle');
			if (toggle_element) {
				$parent_action.find(toggle_element).slideToggle('fast');
			}
		}

		this.make_api_test_call = function (e) {
			e.preventDefault();
			var $clicked_button = $(e.currentTarget);
			var $action_wrap = $clicked_button.parents('.field-wrap-test_section').first();
			this.show_loader($clicked_button.parents('.hidden-action').first());

			params = {
				'action_id': $clicked_button.data('action_id'),
				'cf7_id': $clicked_button.data('cf7_id'),
				'rule_id': $clicked_button.data('ruleid'),
				'data': $action_wrap.find('input').serialize()
			};

			this.make_ajax_call('wpcf7r_make_api_test', params, 'after_ajax_call');
		}

		this.remove_repeating_field = function (e) {
			e.preventDefault();
			var $clicked_button = $(e.currentTarget);
			$clicked_button.parents('.qs-repeater-row').remove();
		}

		this.add_repeating_field = function (e) {
			e.preventDefault();
			var $clicked_button = $(e.currentTarget);
			var $parent_element = $clicked_button.parents('[data-repeater-template]');
			var next_row_count = $parent_element.find('[data-repeater-row-count]').last().data('repeater-row-count');
			next_row_count++;
			var template = $parent_element.data('repeater-template');
			template_html = this.replaceAll(template.template, 'new_row', next_row_count);
			$parent_element.append(template_html);
			$(document.body).trigger('added-repeating-row', [$parent_element]);
		}

		this.migrate_from_cf7_api = function (e) {
			e.preventDefault();
			$clicked_button = $(e.currentTarget);
			this.show_loader($clicked_button.parents('.actions-list'));
			params = {
				'post_id': $clicked_button.data('id'),
				'rule_id': $clicked_button.data('ruleid'),
				'action_type': $clicked_button.data('migration-type'),
			};

			this.make_ajax_call('wpcf7r_add_action', params, 'after_ajax_call');

			$clicked_button.fadeOut(function () {
				$(this).remove();
			});
		}

		this.action_title_field_changed = function (e) {
			e.preventDefault();
			$changed_title = $(e.currentTarget);
			action_id = this.get_block_action_id($changed_title);
			$('.primary[data-actionid="' + action_id + '"] .column-post-title').html($changed_title.val());
		}

		/**
		 * Catch checkbox change event
		 * @param  {[type]} e [description]
		 * @return {[type]}   [description]
		 */
		this.checkbox_changed = function (e) {
			e.preventDefault();
			$clicked_button = $(e.currentTarget);
			checkbox_on = $clicked_button.is(':checked');
			$parent_element = $clicked_button.parents('.hidden-action');
			$field_wrap = $clicked_button.parents('.field-wrap').first();
			if ($clicked_button.data('toggle-label')) {
				toggle_data = $clicked_button.data('toggle-label');
				jQuery.each(toggle_data, function (css_class, toggle) {
					if (checkbox_on) {
						string = toggle[0];
					} else {
						string = toggle[1];
					}
					$parent_element.find(css_class).html(string);
				});
			}
		}

		/**
		 * Add a new action handler
		 * @param  {[type]} e [description]
		 * @return {[type]}   [description]
		 */
		this.add_new_action = function (e) {
			e.preventDefault();
			$clicked_button = $(e.currentTarget);
			var $action_selector = $clicked_button.siblings('.new-action-selector');
			var action_type = $action_selector.val();
			this.remove_errors();
			if (!action_type) {
				this.add_error('.new-action-selector', 'Please choose an action');
				return false;
			}
			if ('purchase' === $action_selector.find(':selected').data('action')) {
				e.preventDefault();
				var url = action_type;
				window.open(url, '_blank');
			} else {
				this.show_loader($clicked_button.parents('.actions-list'));
				params = {
					'post_id': $clicked_button.data('id'),
					'rule_id': $clicked_button.data('ruleid'),
					'action_type': action_type,
				};
				$('.hidden-action').slideUp('fast');
				this.make_ajax_call('wpcf7r_add_action', params, 'after_ajax_call');
			}
		}

		this.remove_errors = function () {
			$('.error-message').removeClass('error-message');
			$('.error-label').remove();
		}

		this.add_error = function (selector, message) {
			$(selector).addClass('error-message').after('<span class="error-label">' + message + '</span>');
		}

		this.show_loader = function (selector) {
			$(selector).append('<div class="wpcf7r_loader"></div>');
			$('.wpcf7r_loader').addClass('active');
		}

		this.hide_loader = function () {
			$('.wpcf7r_loader').fadeOut(function () {
				$(this).remove();
			});
		}

		/**
		 * A callback after ajax actions
		 * @param  {[type]} e        [description]
		 * @param  {[type]} params   [description]
		 * @param  {[type]} response [description]
		 * @param  {[type]} action   [description]
		 * @return {[type]}          [description]
		 */
		this.after_ajax_call = function (e, params, response, action) {
			var _this = this;
			var $action_wrap;

			/**
			 * Handle action delete request
			 * @param  {[type]} [action== 'wpcf7r_delete_action'] [description]
			 * @return {[type]}           [description]
			 */
			if ('wpcf7r_delete_action' === action) {
				$(params).each(function (k, v) {
					$('[data-actionid="' + v.post_id + '"]').fadeOut(function () {
						$(this).remove();
						_this.renumber_rows();
					});
					$('[data-postid="' + v.post_id + '"]').fadeOut(function () {
						$(this).remove();
					});
				});
			}

			/**
			 * Handle action add request
			 * @param  {[type]} [action== 'wpcf7r_add_action'] [description]
			 * @return {[type]}           [description]
			 */
			if ('wpcf7r_add_action' === action || 'wpcf7r_duplicate_action' == action) {
				$('[data-wrapid=' + params.rule_id + '] #the_list:visible').append(response.action_row);

				$new_action_wrap = $('[data-wrapid=' + params.rule_id + '] #the_list > tr.action-container').last();

				_this.init_select2();
				_this.renumber_rows();
				_this.init_colorpickers();
				_this.init_editors($new_action_wrap);
			}

			if ('wpcf7r_reset_settings' === action) {
				window.location.reload();
			}

			/**
			 * Make an API test
			 * @param  {[type]} [action== 'wpcf7r_make_api_test'] [description]
			 * @return {[type]}           [description]
			 */
			if ('wpcf7r_make_api_test' === action) {
				$action_wrap = $('[data-actionid=' + params.action_id + '] .field-wrap-test_section');
				$('span.err').remove();
				if (typeof response.status != 'undefined' && response.status === 'failed') {
					$.each(response.invalid_fields, function (field_key, error) {
						$action_wrap.find('.' + field_key).append('<span class="err">' + error.reason + '</span>');
					});
				} else {
					$('body').append(response.html);
				}
			}

			/**
			 * After getting mailchimp lists
			 * @param  {[type]} [action== 'wpcf7r_get_mailchimp_lists'] [description]
			 * @return {[type]}           [description]
			 */
			if ('wpcf7r_get_mailchimp_lists' === action) {
				$action_wrap = $('[data-actionid=' + params.action_id + ']');
				$lists_select = $action_wrap.find('.field-wrap-mailchimp_list_id select');
				$api_key_input = $action_wrap.find('.field-wrap-mailchimp_api_key');
				$lists_select.html('');
				if (typeof response.error != 'undefined' && response.error) {
					this.add_error($api_key_input, response.error);
				} else {
					$action_wrap.find('.field-wrap-mailchimp_settings')
						.attr('data-lists', JSON.stringify(response.lists))
						.data('lists', JSON.stringify(response.lists));

					$.each(response.lists, function (k, v) {
						var o = '<option value="' + v.list_id + '">' + v.list_name + '</option>';
						$lists_select.append(o);
					});

					$lists_select.change();
				}
			}
			this.hide_loader();
		}

		this.duplicate_action = function (e) {
			e.preventDefault();
			$clicked_button = $(e.currentTarget);

			this.show_loader($clicked_button.parents('td'));

			params = {
				'post_id': $clicked_button.data('id'),
				'form_id': $('#post_ID').val(),
				'rule_id': $clicked_button.data('ruleid'),
			};

			this.make_ajax_call('wpcf7r_duplicate_action', params, 'after_ajax_call');
		}

		this.move_post_to_trash = function (e) {
			e.preventDefault();
			$clicked_button = $(e.currentTarget);
			this.show_loader($clicked_button.parents('td'));

			params = [{
				'post_id': $clicked_button.data('id')
			}];

			this.make_ajax_call('wpcf7r_delete_action', params, 'ater_ajax_delete');
		}

		this.show_hide_action = function (e) {
			e.preventDefault();
			$clicked_button = $(e.currentTarget);
			$hidden_action_to_show = $clicked_button.parents('tr').next().find('.hidden-action');
			$('.hidden-action').not($hidden_action_to_show).slideUp('fast');
			$hidden_action_to_show.slideToggle('fast');
		}

		this.show_hide_tab = function (e) {
			$clicked_tab = $(e.currentTarget);
			var target = $clicked_tab.data('tab-target');
			$clicked_tab.toggleClass('active');
			$('[data-tab=' + target + ']').slideToggle('fast');
		}

		this.hide_select_options = function (e) {
			$('.row-template .wpcf7r-fields').each(function () {
				$(this).trigger('change');
			});
		}

		this.show_field_options = function (e) {
			$changed_select = $(e.currentTarget);
			$row = $changed_select.parents('.row-template');

			if ($changed_select.val()) {
				$elem_to_show = $row.find('.group_row_value[data-rel=' + $changed_select.val() + ']');
			} else {
				$elem_to_show = "";
			}

			$row.find('.group_row_value').hide();

			if ($elem_to_show.length) {
				$elem_to_show.show();
				$row.find('.compare-options option').hide();
				$row.find('.compare-options option[data-comparetype=select]').show();
			} else {
				$row.find('.compare-options option').show();
				$row.find('.wpcf7-redirect-value').show();
			}
		}

		this.set_select_value = function (e) {
			$changed_select = $(e.currentTarget);
			$changed_select.siblings('.wpcf7-redirect-value').val($changed_select.val());
		}

		/**
		 * Removes a block of rules from the DOM
		 * @param  {[type]} e [description]
		 * @return {[type]}   [description]
		 */
		this.remove_block = function (e) {
			e.preventDefault();
			$clicked_button = $(e.currentTarget);
			$clicked_button_parent = $clicked_button.parents('.block-title').first();
			var tab_to_remove = $clicked_button_parent.data('rel');
			$clicked_button_parent.prev().click();
			$('.conditional-group-block[data-block-id=' + tab_to_remove + ']').remove();
			$('.block-title[data-rel=' + tab_to_remove + ']').remove();
		}

		/**
		 * Adds a new block to the DOM
		 * @param  {[type]} e [description]
		 * @return {[type]}   [description]
		 */
		this.add_new_block = function (e) {
			this.new_block_counter++;

			$clicked_button = $(e.currentTarget);

			action_id = this.get_block_action_id($clicked_button);
			html_block_template = wpcfr_template.block_html;
			block_title_html = wpcfr_template.block_title_html;
			html_block_template = this.replaceAll(html_block_template, 'new_block', 'block_' + this.new_block_counter);
			html_block_template = this.replaceAll(html_block_template, 'action_id', action_id);
			block_title_html = this.replaceAll(block_title_html, 'new_block', 'block_' + this.new_block_counter);
			block_title_html = this.replaceAll(block_title_html, 'action_id', action_id);

			$(this.tab_title_all_selector).last().after(block_title_html);
			$(this.blocks_container_selector).append(html_block_template);
			$(this.tab_title_all_selector).last().click();
		}

		/**
		 * Switch between tabs
		 * @param  {[type]} e [description]
		 * @return {[type]}   [description]
		 */
		this.switch_tab = function (e) {
			e.preventDefault();
			$clicked_button = $(e.currentTarget);
			var tab_to_show = $clicked_button.data('rel');
			var $tab_to_show = $('[data-block-id=' + tab_to_show + ']');
			$(this.active_tab_selector).removeClass('active');
			$(this.tab_title_active_selector).removeClass('active');
			$clicked_button.addClass('active');
			$tab_to_show.addClass('active');
		}

		/**
		 * Update block title upon save
		 * @param  {[type]} e [description]
		 * @return {[type]}   [description]
		 */
		this.save_block_title_edit = function (e) {
			e.preventDefault();
			$clicked_button = $(e.currentTarget);
			var tab_to_show = $clicked_button.data('rel');
			var $tab_to_show = $('[data-block-id=' + tab_to_show + ']');
			$clicked_button.siblings('input').attr('readonly', 'readonly');
			$clicked_button.parent().removeClass('edit');
			$tab_to_show.find(this.tab_inner_title).html($clicked_button.siblings('input').val());
		}

		/**
		 * Close the text field for editing
		 * @param  {[type]} e [description]
		 * @return {[type]}   [description]
		 */
		this.cancel_block_title_edit = function (e) {
			e.preventDefault();
			$clicked_button = $(e.currentTarget);
			var tab_to_show = $clicked_button.data('rel');
			var $tab_to_show = $('[data-block-id=' + tab_to_show + ']');
			$clicked_button.siblings('input').val($clicked_button.siblings('input').data('original')).attr('readonly', 'readonly');
			$clicked_button.parent().removeClass('edit');
			$tab_to_show.find(this.tab_inner_title).html($clicked_button.siblings('input').val());
		}

		/**
		 * Open block title for editing
		 * @param  {[type]} e [description]
		 * @return {[type]}   [description]
		 */
		this.edit_block_title = function (e) {
			e.preventDefault();
			$clicked_button = $(e.currentTarget);
			$clicked_button.parent().addClass('edit');
			$clicked_button.siblings('input').removeAttr('readonly');
		}

		/**
		 * Add a new group of fields (OR)
		 * @param  {[type]} e [description]
		 * @return {[type]}   [description]
		 */
		this.add_new_group = function (e) {
			e.preventDefault();

			$clicked_button = $(e.currentTarget);
			var block_id = 'block_1';
			var action_id = this.get_block_action_id($clicked_button);
			var $rule_group = $clicked_button.parents('.conditional-group-blocks').find('.wpcfr-rule-groups');
			this.new_group_counter = $rule_group.find('.wpcfr-rule-group').length;
			this.new_group_counter++;
			group_html = wpcfr_template.group_html;
			group_html = this.replaceAll(group_html, 'group-new_group', 'group-' + this.new_group_counter);
			group_html = this.replaceAll(group_html, 'new_group', 'group-' + this.new_group_counter);
			group_html = this.replaceAll(group_html, 'new_block', block_id);
			group_html = this.replaceAll(group_html, 'action_id', action_id);

			$rule_group.append(group_html);
		}

		/**
		 * Remove an and row from the dom
		 * @param  {[type]} e [description]
		 * @return {[type]}   [description]
		 */
		this.remove_and_row = function (e) {
			e.preventDefault();
			$clicked_button = $(e.currentTarget);
			if ($clicked_button.parents(this.rule_group_selector).find('.row-template').length == 1) {
				$clicked_button.parents(this.rule_group_selector).remove();
			} else {
				$clicked_button.parents(this.row_template_selector).remove();
			}
		}

		this.get_block_action_id = function ($inner_item) {
			return $inner_item.parents('[data-actionid]').data('actionid');
		}

		/**
		 * Add an and row to the dom
		 * @param  {[type]} e [description]
		 * @return {[type]}   [description]
		 */
		this.add_and_row = function (e) {
			e.preventDefault();

			$clicked_button = $(e.currentTarget);
			action_id = this.get_block_action_id($clicked_button);
			block_id = 'block_1';
			group_id = $clicked_button.parents('[data-group-id]').first().data('group-id');

			if (!this.new_row_counter) {
				$repeater_block = $clicked_button.parents('.repeater-table');
				this.new_row_counter = $repeater_block.find('.row-template').length;
			}

			this.new_row_counter++;

			$(wpcfr_template.row_html).find(this.add_and_selector).remove();

			row_html = wpcfr_template.row_html;
			row_html = this.replaceAll(row_html, 'new_block', block_id);
			row_html = this.replaceAll(row_html, 'new_group', group_id);
			row_html = this.replaceAll(row_html, 'new_row', 'row-' + this.new_row_counter);
			row_html = this.replaceAll(row_html, 'action_id', action_id);

			$clicked_button.parents('table').first().find('tbody').append(row_html);
		}

		/**
		 * Replace all instances of a string
		 * @param  {[type]} str     [description]
		 * @param  {[type]} find    [description]
		 * @param  {[type]} replace [description]
		 * @return {[type]}         [description]
		 */
		this.replaceAll = function (str, find, replace) {
			return str.replace(new RegExp(find, 'g'), replace);
		}
		this.admin_fields_init = function () {
			$('.field-wrap input[type=checkbox],.field-wrap select').each(function () {
				if ($(this).is(":checked")) {
					$(this).siblings('.field-notice-hidden').removeClass('field-notice-hidden').addClass('field-notice-show');
				}
			});
			$('.wpcf7-redirect-after-sent-script').each(function () {
				if ($(this).val()) {
					$(this).siblings('.field-warning-alert').removeClass('field-notice-hidden');
				}
			});
		}

		/**
		 * Show/hide fields according to user selections
		 * @return {[type]} [description]
		 */
		this.admin_field_handlers = function () {
			this.admin_fields_init();
			// field - open in a new tab
			$(document.body).on('change', '.field-wrap input[type=checkbox],.field-wrap select', function () {
				if ($(this).is(":checked")) {
					$(this).siblings('.field-notice-hidden').removeClass('field-notice-hidden').addClass('field-notice-show');
				} else {
					$(this).siblings('.field-notice-show').addClass('field-notice-hidden').removeClass('field-notice-show');
				}
			});
			// field - after sent script
			$(document.body).on('keyup', '.wpcf7-redirect-after-sent-script', function (event) {
				if ($(this).val().length != 0) {
					$(this).siblings('.field-warning-alert').removeClass('field-notice-hidden');
				} else {
					$(this).siblings('.field-warning-alert').addClass('field-notice-hidden');
				}
			});
			$(document.body).on('change', '.checkbox-radio-1', function () {
				var checked = $(this).is(':checked');
				$('.checkbox-radio-1').prop('checked', false);
				if (checked) {
					$(this).prop('checked', true);
				}
			});
		}

		this.reset_all_settings = function (e) {
			e.preventDefault();
			var action = 'wpcf7r_reset_settings';
			var params = [];
			// TODO: Translation
			if (confirm('Are you sure? this process will delete all of your plugin settings. There is no way back from this process!')) {
				this.make_ajax_call(action, params);
			}
		}

		/**
		 * Basic function to make admin ajax calls
		 * @param  {[type]} params [description]
		 * @return {[type]}        [description]
		 */
		this.make_ajax_call = function (action, params) {
			var _this = this;

			jQuery.ajax({
				type: "post",
				dataType: "json",
				url: ajaxurl,
				data: {
					action: action,
					data: params,
					wpcf7r_nonce: wpcf_get_nonce(),
				},
				success: function (response) {
					$(document.body).trigger('wpcf7r_after_ajax_call', [params, response, action]);
				}
			});
		}
		this.init();
	}

	$(document).ready(function () {
		//init the class functionality
		wpcf7_redirect_admin = new Wpcf7_redirect_admin();

		$(document.body).trigger('wpcf7r-loaded', wpcf7_redirect_admin);
	});
})(jQuery);

function wpcf_get_nonce() {
	return jQuery('[name=actions-nonce]').val() ? jQuery('[name=actions-nonce]').val() : jQuery('[name=_wpcf7nonce]').val();
}
