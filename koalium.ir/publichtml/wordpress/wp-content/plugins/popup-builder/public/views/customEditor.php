<?php
/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use sgpb\AdminHelper;
$popupId = !empty($_GET['post']) ? (int)sanitize_text_field( wp_unslash( $_GET['post'] ) ) : 0;
$editorModeJs = htmlentities('text/javascript');
$editorModeCss = htmlentities('text/css');

$defaultData = SGPBConfigDataHelper::defaultData();
$jsDefaultData = $defaultData['customEditorContent']['js'];
$cssDefaultData = $defaultData['customEditorContent']['css'];

$savedData = get_post_meta($popupId , 'sg_popup_scripts', true);
?>

<div class="sgpb-wrapper sgpb-customJsOrCss">
	<p class="sgpb-customJsOrCss_text"><?php esc_html_e('This section is for adding custom codes (CSS or JS) for the popup, it requires some coding knowledge', 'popup-builder');?>.</p>
	<p class="sgpb-customJsOrCss_text"><?php esc_html_e('You may use your custom codes for extra actions connected to the popup opening (before, after, etc.) in the fields below', 'popup-builder');?>.</p>
	<!-- editor buttons -->
	<div class="sgpb-tabs sgpb-margin-y-30">
		<span class="sgpb-tab-link sgpb-tab-active sgpb-tab-1" onclick="SGPBBackend.prototype.changeTab(1)">JS</span>
		<span class="sgpb-tab-link sgpb-tab-2" onclick="SGPBBackend.prototype.changeTab(2)">CSS</span>
	</div>

	<div class="sgpb-editor sgpb-tabs-content">
		<!-- JS editor content -->
		<div id="sgpb-editor-options-tab-content-wrapper-1" class="sgpb-editor-options-tab-content-wrapper sgpb-padding-right-40 sgpb-margin-y-20" style="display: block;">
			<?php
				foreach ($jsDefaultData['description'] as $text) { ?>
						<p><?php echo wp_kses($text, 'post'); ?></p>
				<?php }
			?>

			<?php foreach ($jsDefaultData['helperText'] as $key => $value) {?>
					<div class="formItem">
						<span class="formItem__title"><?php echo wp_kses($value, 'post'); ?></span>
						<?php						
						if (!empty($savedData['js']['sgpb-'.$key])) {
							if( AdminHelper::sgpbScanCustomJsStr( $savedData['js']['sgpb-'.$key] ) == true )
							{
								?>
								<span class="notice notice-warning">We have detected this snippet that is insecure and may compromise the security of your site. Please remove it and save your Popup data again.</span>
								<?php
							}	
						}
						?>						
						<textarea class="wp-editor-area formItem__textarea sgpb-margin-top-20"
									data-attr-event="<?php echo esc_attr($key);?>"
									placeholder=" #... type your code"
									mode="<?php echo esc_attr($editorModeJs); ?>"
									name="sgpb-<?php echo esc_attr($key); ?>"><?php
									if (!empty($savedData['js']['sgpb-'.$key])) {
										echo esc_html($savedData['js']['sgpb-'.$key]);
									}									
									?>
						</textarea>	
						<?php 
						//Ted-fix : we stopped the render custom js code into front-end page to fix HACKER attack
						
						/* 	
						if( AdminHelper::getOption('sgpb-disable-custom-js') )
						{
							?>
							<span class="notice notice-warning">We disabled this option for this version to remove hacker's attack code.</span>
							<textarea   class="wp-editor-area formItem__textarea sgpb-margin-top-20"
										data-attr-event="<?php echo esc_attr($key);?>"
										placeholder=" #... type your code"
										mode="<?php echo esc_attr($editorModeJs); ?>"
										name="sgpb-<?php echo esc_attr($key); ?>" readonly><?php
										if (!empty($savedData['js']['sgpb-'.$key])) {
											echo esc_html($savedData['js']['sgpb-'.$key]);
										}									
										?>
							</textarea>	
							<?php
						}
						else
						{
							?>
							<textarea   class="wp-editor-area formItem__textarea sgpb-margin-top-20"
										data-attr-event="<?php echo esc_attr($key);?>"
										placeholder=" #... type your code"
										mode="<?php echo esc_attr($editorModeJs); ?>"
										name="sgpb-<?php echo esc_attr($key); ?>"><?php
										if (!empty($savedData['js']['sgpb-'.$key])) {
											echo esc_html($savedData['js']['sgpb-'.$key]);
										}									
										?>
							</textarea>	
							<?php
						} */
						?>
					</div>
			<?php } ?>
		</div>

		<!-- CSS editor content -->
		<div id="sgpb-editor-options-tab-content-wrapper-2" class="sgpb-editor-options-tab-content-wrapper sgpb-padding-right-40 sgpb-margin-y-20" style="display: none;">
			<?php
				foreach ($cssDefaultData['description'] as $text) { ?>
					<div><?php echo wp_kses($text, 'post'); ?></div>
			<?php } ?>

			<?php foreach ($cssDefaultData['helperText'] as $key => $value) {?>
					<div class="formItem"><span class="formItem__title"><?php echo wp_kses($value, 'post'); ?></span></div>
			<?php } ?>

			<textarea class="wp-editor-area editor-content sgpb-editor-content-css formItem__textarea sgpb-margin-top-20"
				placeholder=" #... type your code"
				mode="<?php echo esc_attr($editorModeCss); ?>"
				name="sgpb-css-editor"><?php
				if (isset($savedData['css'])) {
					echo esc_html($savedData['css']);
				}?></textarea>
		</div>
	</div>
</div>
