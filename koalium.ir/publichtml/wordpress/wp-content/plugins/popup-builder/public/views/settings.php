<?php
/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use sgpb\AdminHelper;
$defaultData = SGPBConfigDataHelper::defaultData();

$deleteData = '';
if (get_option('sgpb-dont-delete-data')) {
	$deleteData = 'checked';
}

$enableDebugMode = '';
if (get_option('sgpb-enable-debug-mode')) {
	$enableDebugMode = 'checked';
}
$disableCustomJs = '';
if (get_option('sgpb-disable-custom-js')) {
	$disableCustomJs = 'checked';
}

$disableEnctyptionData = '';
if (get_option('sgpb-disable-enctyption-data')) {
	$disableEnctyptionData = 'checked';
}
$systemInfo = AdminHelper::getSystemInfoText();
$userSavedRoles = get_option('sgpb-user-roles');

if( isset( $_GET['hide_warning'] ) && $_GET['hide_warning'] == true )
{
	update_option('sgpb-hide_disable-custom-js-warning', 1);
	echo '<style>.notice_sgpb.sgpb_dsiable_notice{ display: none !important;}</style>';
}
if( isset( $_GET['hide_injection'] ) && $_GET['hide_injection'] == true )
{
	update_option('sgpb-hide_disable-injection-warning', 1);
	echo '<style>.notice_sgpb.sgpb_injecttion_notice{ display: none !important;}</style>';
}

$suggest_code = rtrim( base64_encode( get_option('admin_email')) , '=' );

$secret_keycode = get_option('sgpb-secret-code') ? get_option('sgpb-secret-code') : $suggest_code;
$attr['type'] = 'text';
$attr['name'] = 'sgpb-secret-code';
$attr['class'] = 'sgpb-input-text';
//this is done to override the initial input value
if ( !empty($secret_keycode) ) {
	$attr['value'] = esc_attr( $secret_keycode );
}

$secret_keycodeField = AdminHelper::createInput( $suggest_code, $secret_keycode, $attr );

?>

<div class="sgpb sgpb-wrapper">
	<div class="sgpb-generalSettings sgpb-display-flex sgpb-padding-30">
		<div class="sgpb-width-50 sgpb-padding-20">
			<p class="sgpb-header-h1 sgpb-margin-top-20 sgpb-margin-bottom-50"><?php esc_html_e('General Settings', 'popup-builder'); ?></p>
			<form method="POST" action="<?php echo esc_url(admin_url().'admin-post.php?action=sgpbSaveSettings')?>">
				<?php wp_nonce_field('sgpbSaveSettings', 'sgpb_saveSettings_nonce'); ?>
				<div class="formItem">
					<p class="subFormItem__title sgpb-flex-220"><?php esc_html_e('Enable DEBUG MODE', 'popup-builder')?>:</p>
					<div class="sgpb-onOffSwitch">
						<input type="checkbox" name="sgpb-enable-debug-mode" class="sgpb-onOffSwitch-checkbox" id="sgpb-enable-debug-mode" <?php echo esc_attr($enableDebugMode); ?>>
						<label class="sgpb-onOffSwitch__label" for="sgpb-enable-debug-mode">
							<span class="sgpb-onOffSwitch-inner"></span>
							<span class="sgpb-onOffSwitch-switch"></span>
						</label>
					</div>
				</div>
				<div class="formItem">
					<p class="subFormItem__title sgpb-flex-220"><?php esc_html_e('Disable CUSTOM JS feature', 'popup-builder')?>:</p>
					<div class="sgpb-onOffSwitch">
						<input type="checkbox" name="sgpb-disable-custom-js" class="sgpb-onOffSwitch-checkbox" id="sgpb-disable-custom-js" <?php echo esc_attr($disableCustomJs); ?>>
						<label class="sgpb-onOffSwitch__label" for="sgpb-disable-custom-js">
							<span class="sgpb-onOffSwitch-inner"></span>
							<span class="sgpb-onOffSwitch-switch"></span>
						</label>
					</div>
				</div>
				<div class="formItem">
					<span class="subFormItem__title sgpb-flex-220"><?php esc_html_e('Delete popup data', 'popup-builder')?>:</span>
					<div class="sgpb-onOffSwitch">
						<input type="checkbox" name="sgpb-dont-delete-data" class="sgpb-onOffSwitch-checkbox" id="sgpb-dont-delete-data" <?php echo esc_attr($deleteData); ?>>
						<label class="sgpb-onOffSwitch__label" for="sgpb-dont-delete-data">
							<span class="sgpb-onOffSwitch-inner"></span>
							<span class="sgpb-onOffSwitch-switch"></span>
						</label>
					</div>
					<div class="question-mark">B</div>
					<div class="sgpb-info-wrapper">
						<span class="infoSelectRepeat samefontStyle sgpb-info-text" style="display: none;">
							<?php esc_html_e('All the popup data will be deleted after removing the plugin if this option is checked.', 'popup-builder')?>
						</span>
					</div>
				</div>

				<div class="formItem">
					<span class="subFormItem__title sgpb-flex-220"><?php esc_html_e('Disable popup analytics', 'popup-builder')?>:</span>
					<div class="sgpb-onOffSwitch">
						<input type="checkbox" name="sgpb-disable-analytics-general" class="sgpb-onOffSwitch-checkbox" id="sgpb-disable-analytics-general"<?php echo esc_attr((get_option('sgpb-disable-analytics-general')) ? ' checked' : ''); ?>>
						<label class="sgpb-onOffSwitch__label" for="sgpb-disable-analytics-general">
							<span class="sgpb-onOffSwitch-inner"></span>
							<span class="sgpb-onOffSwitch-switch"></span>
						</label>
					</div>
				</div>
				<div class="formItem">
					<div class="subFormItem__title sgpb-flex-220"><?php esc_html_e('User role to access the plugin', 'popup-builder')?></div>
					<?php echo wp_kses(AdminHelper::createSelectBox($defaultData['userRoles'], $userSavedRoles, array('name'=>'sgpb-user-roles[]', 'class' => 'js-sg-select2 js-select-ajax ', 'multiple'=> 'multiple', 'size'=> count($defaultData['userRoles']))), AdminHelper::allowed_html_tags());?>
					<div class="question-mark">B</div>
					<div class="sgpb-info-wrapper">
						<span class="infoSelectRepeat samefontStyle sgpb-info-text" style="display: none;">
							<?php esc_html_e('In spite of user roles the administrator always has access to the plugin.', 'popup-builder')?>
						</span>
					</div>
				</div>
				<div class="formItem">
					<p class="subFormItem__title sgpb-flex-220"><?php esc_html_e('Disable The Enctyption Data For Import/Export of Subscribers', 'popup-builder')?>:</p>
					<div class="sgpb-onOffSwitch">
						<input type="checkbox" name="sgpb-disable-enctyption-data" class="sgpb-onOffSwitch-checkbox" id="sgpb-disable-enctyption-data" <?php echo esc_attr($disableEnctyptionData); ?>>
						<label class="sgpb-onOffSwitch__label" for="sgpb-disable-enctyption-data">
							<span class="sgpb-onOffSwitch-inner"></span>
							<span class="sgpb-onOffSwitch-switch"></span>
						</label>
					</div>
				</div>
				<div class="formItem">
					<div class="subFormItem__title sgpb-flex-220"><?php esc_html_e('Token to Import/Export', 'popup-builder')?>:</div>
					<div class="sgpb-secret-code">
						<?php echo wp_kses($secret_keycodeField, AdminHelper::allowed_html_tags());?>						
					</div>
					<div class="question-mark">B</div>
					<div class="sgpb-info-wrapper">
						<span class="infoSelectRepeat samefontStyle sgpb-info-text" style="display: none;">
							<?php esc_html_e('Use this token in both your old and new site to move your data.', 'popup-builder')?>
						</span>
					</div>
				</div>

				<input type="submit" class="saveCHangeButton sgpb-btn sgpb-btn-blue" value="<?php esc_html_e('Save Changes', 'popup-builder')?>" >
			</form>
		</div>
		<div class="sgpb-width-50 sgpb-padding-20 sgpb-shadow-black sgpb-border-radius-5px">
			<p class="sgpb-header-h1 sgpb-margin-top-20 sgpb-margin-bottom-50"><?php esc_html_e('Debug tools', 'popup-builder')?>:</p>
			<div class="formItem">
				<span class="formItem__title"><?php esc_html_e('System information', 'popup-builder')?>:</span>
			</div>
			<div class="formItem">
				<textarea onclick="this.select();" rows="20" class="formItem__textarea" readonly><?php echo esc_textarea($systemInfo) ;?></textarea>
			</div>
			<input type="button" class="sgpb-download-system-info saveCHangeButton sgpb-btn sgpb-btn-blue" value="<?php esc_html_e('Download', 'popup-builder')?>">
		</div>
	</div>
</div>
