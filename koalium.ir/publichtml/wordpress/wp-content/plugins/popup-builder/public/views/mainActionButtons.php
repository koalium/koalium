<?php
/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use sgpb\AdminHelper;
?>
<div class="sgpb sgpb-header">
    <h1 class="sgpb-header-h1 sgpb-margin-bottom-30"><?php esc_html_e( 'Popups', 'popup-builder' ) ?></h1>
    <div class="sgpb-margin-bottom-50 sgpb-display-flex sgpb-justify-content-between">
        <div>
            <a class="page-title-action sgpb-display-inline-block sgpb-btn sgpb-btn--rounded sgpb-btn-blue--outline sgpb-padding-x-30" href="<?php echo esc_url(AdminHelper::getPopupTypesPageURL()); ?>">
		        <?php esc_html_e( 'Add New', 'popup-builder' ); ?>
            </a>
            <a class="page-title-action sgpb-display-inline-block sgpb-btn sgpb-btn--rounded sgpb-btn-blue--outline sgpb-padding-x-30" href="<?php echo esc_url(AdminHelper::getPopupExportURL()); ?>">
		        <?php esc_html_e( 'Export', 'popup-builder' ); ?>
            </a>
            <a class="page-title-action sgpb-display-inline-block sgpb-btn sgpb-btn--rounded sgpb-btn-blue--outline sgpb-padding-x-30"
               id="sgpbImportSettings"
               href="javascript:void(0)">
		        <?php esc_html_e( 'Import', 'popup-builder' ); ?>
            </a>
        </div>
        <div style="text-align: right" id="sgpbPostSearch">
            <div class="sgpb--group">
                <input type="text" id="sgpbSearchInPosts" placeholder="Search Popup" class="sgpb-input">
                <input type="submit" value="GO!" id="sgpbSearchInPostsSubmit" class="sgpb-btn sgpb-btn-blue">
            </div>
        </div>
    </div>
</div>
<style>
    #wpbody-content > div.wrap > h1,
    .notice,
    #wpbody-content > div.wrap > a {
        display: none !important;
    }
	.notice_sgpb
	{
		 display: block !important;
	}
</style>
