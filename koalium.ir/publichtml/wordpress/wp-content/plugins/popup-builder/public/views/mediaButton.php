<?php
	namespace sgpb;
	/* Exit if accessed directly */
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}
	$defaultData = \SGPBConfigDataHelper::defaultData();
	$excludePostId = 0;
	if (isset($_GET['post']) && !empty($_GET['post'])) {
		$excludePostId = sanitize_text_field( wp_unslash( $_GET['post'] ) );
	}
	$excludedPopups = array($excludePostId);
	$allPopups = AdminHelper::getPopupsIdAndTitle($excludedPopups);
	$allowed_html = AdminHelper::allowed_html_tags();
?>

<div class="sgpb-hide " style="display: none">
	<div id="sgpb-hidden-media-popup" class="sgpb-wrapper sgpb">
		<div class="row">
			<div class="col-sm-10">
				<label><?php esc_html_e('Insert the [shortcode]', 'popup-builder')?></label>
			</div>
			<div class="col-sm-2">
				<img class="sgpb-add-subscriber-popup-close-btn sgpb-close-media-popup-js" src="<?php echo esc_attr(SG_POPUP_IMG_URL.'subscribers_close.png'); ?>" width='15px'>
			</div>
		</div>
		<div class="sgpb-insert-popup-title-border"></div>
		<div class="row">
			<div class="col-sm-4">
				<label><?php esc_html_e('Select popup', 'popup-builder')?></label>:
			</div>
			<div class="col-sm-8">
				<?php echo wp_kses(AdminHelper::createSelectBox($allPopups, '', array('class'=>'sgpb-insert-popup')), $allowed_html); ?>
			</div>
		</div>
		<?php if (get_post_type() != SG_POPUP_POST_TYPE): ?>
			<div class="row sgpb-static-padding-top">
				<div class="col-sm-4">
					<label><?php esc_html_e('Select event', 'popup-builder')?></label>:
				</div>
				<div class="col-sm-8">
					<?php echo wp_kses(AdminHelper::createSelectBox($defaultData['popupInsertEventTypes'], '', array('class'=>'sgpb-insert-popup-event')), $allowed_html); ?>
				</div>
			</div>
		<?php endif;?>
		<div class="row sgpb-static-padding-top ">
			<div class="col-sm-3">
				<input type="button" class="sgpb-btn sgpb-btn-blue sgpb-insert-popup-js sgpb-insert-popup-btns" value="<?php esc_html_e('Insert', 'popup-builder')?>">
			</div>
			<div class="col-sm-3">
				<input type="button" class="sgpb-btn sgpb-btn-gray-light sgpb-close-media-popup-js sgpb-insert-popup-btns" value="<?php esc_html_e('Cancel', 'popup-builder')?>">
			</div>
		</div>
	</div>
</div>

<style type="text/css">
	.post-type-popupbuilder .select2-container.select2-container--open {
		z-index: 99999;
	}
</style>
