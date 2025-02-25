<?php
/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use sgpb\AdminHelper;
use sgpb\SubscriptionPopup;

// Check if file URL is provided
if (empty($fileURL)) {
    // Handle the case where the file URL is not provided
	echo "ERROR-File URL is missing.";
    wp_die();
}
// Extract file extension from the URL
$fileExtension = pathinfo($fileURL, PATHINFO_EXTENSION);

// Check if the file has a CSV extension
if (strtolower($fileExtension) !== 'csv') {
    // Handle the case where the file is not a CSV file   
	echo "ERROR-The provided file is not a CSV file.";
    wp_die();
}

// Download file content from the URL
$fileContent = AdminHelper::getFileFromURL($fileURL);

// Check if file content is empty or invalid
if (empty($fileContent)) {
    // Handle the case where the file content is empty or invalid  
	echo "ERROR-Failed to retrieve valid file content from the URL.";
    wp_die();
}

//Decrypt the data when reading it back from the CSV
$fileContent = AdminHelper::decrypt_data( $fileContent );

if( $fileContent == false )
{
	//try old method of read csv data 
	$fileContent = AdminHelper::getFileFromURL($fileURL);
}

// Parse CSV file content into an array
$csvFileArray = array_map('str_getcsv', explode("\n", $fileContent));

if( is_array( $csvFileArray ) && count( $csvFileArray ) < 2)
{
	$error_message_import = '<p>ERROR-Failed to parse CSV file content. Please make sure that you put exactly the same token for both old and new sites at <a href="'.esc_url( admin_url( 'edit.php?post_type=popupbuilder&page=sgpbSettings' ) ).'" target="_blank">HERE</a>.</p>';
	echo  wp_kses($error_message_import, AdminHelper::allowed_html_tags());
    wp_die();
}
// Check if the CSV parsing was successful
if ($csvFileArray === false || count($csvFileArray) === 0) {
    // Handle the case where CSV parsing failed or resulted in an empty array   
	echo "ERROR-Failed to parse CSV file content.";
    wp_die();
}

$ourFieldsArgs = array(
	'class' => 'js-sg-select2 sgpb-our-fields-keys select__select'
);

$formData =  array('' => 'Select Field') + AdminHelper::getSubscriptionColumnsById($formId);
?>

<div id="importSubscribersSecondStep">
	<h1 id="importSubscriberHeader"><?php esc_html_e('Match Your Fields', 'popup-builder'); ?></h1>
	<div id="importSubscriberBody">
		<div class="formItem sgpb-justify-content-around">
			<div class="formItem__title">
				<?php esc_html_e('Available fields', 'popup-builder'); ?>
			</div>
			<div class="formItem__title">
				<?php esc_html_e('Our list fields', 'popup-builder'); ?>
			</div>
		</div>
		<?php foreach($csvFileArray[0] as $index => $current): ?>
			<?php if (empty($current) || $current == 'popup'): ?>
				<?php continue; ?>
			<?php endif; ?>
			<div class="formItem sgpb-justify-content-between">
				<div class="subFormItem__title">
					<?php echo esc_html($current); ?>
				</div>
				<div>
					<?php
					$ourFieldsArgs['data-index'] = $index;
					echo wp_kses(AdminHelper::createSelectBox($formData, '', $ourFieldsArgs), AdminHelper::allowed_html_tags());
					?>
				</div>
			</div>
		<?php endforeach;?>
		<input type="hidden" class="sgpb-to-import-popup-id" value="<?php echo esc_attr($formId)?>">
		<input type="hidden" class="sgpb-imported-file-url" value="<?php echo esc_attr($fileURL)?>">
	</div>

	<div id="importSubscriberFooter">
		<input type="button" value="<?php esc_html_e('Save', 'popup-builder'); ?>" class="sgpb-btn sgpb-btn-blue sgpb-save-subscriber" data-ajaxnonce="popupBuilderAjaxNonce">
	</div>

</div>

