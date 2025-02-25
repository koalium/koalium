<?php
namespace sgpb;
/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use sgpb\PopupBuilderActivePackage;

$defaultData = \SGPBConfigDataHelper::defaultData();
$required = '';
if ($popupTypeObj->getOptionValue('sgpb-schedule-status')) {
	$required = 'required';
}
$conditionsCanBeUsed = PopupBuilderActivePackage::canUseSection('popupOtherConditionsSection');
?>
<div class="sgpb-wrapper sgpb-position-relative" onclick="window.open('<?php echo esc_url(SG_POPUP_SCHEDULING_URL);?>', '_blank')">
	<div class="formItem sgpb-padding-20 sgpb-option-disable">
		<div>
			<div class="formItem">
				<span class="formItem__title sgpb-margin-right-20"><?php esc_html_e('Schedule', 'popup-builder'); ?>:</span>
				<div class="sgpb-onOffSwitch">
					<input type="checkbox" id="schedule-status" class="sgpb-onOffSwitch-checkbox" disabled name="sgpb-schedule-status"  <?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-schedule-status')); ?>>
					<label class="sgpb-onOffSwitch__label" for="schedule-status">
						<span class="sgpb-onOffSwitch-inner"></span>
						<span class="sgpb-onOffSwitch-switch"></span>
					</label>
				</div>
				<div class="question-mark">B</div>
			</div>
			<div class="formItem">
				<span class="formItem__title sgpb-margin-right-20"><?php esc_html_e('Show popup in date range', 'popup-builder'); ?>:</span>
				<div class="sgpb-onOffSwitch">
					<input type="checkbox" name="sgpb-popup-timer-status" id="sgpb-popup-timer-status" disabled class="sgpb-onOffSwitch-checkbox" <?php echo esc_attr($popupTypeObj->getOptionValue('sgpb-popup-timer-status'));?>>
					<label class="sgpb-onOffSwitch__label" for="sgpb-popup-timer-status">
						<span class="sgpb-onOffSwitch-inner"></span>
						<span class="sgpb-onOffSwitch-switch"></span>
					</label>
				</div>
				<div class="question-mark">B</div>
			</div>
			<?php if (!$conditionsCanBeUsed): ?>
				<div class="sgpb-unlock-options">
					<div class="sgpb-unlock-options__icon">
						<img src="<?php echo esc_url(SG_POPUP_PUBLIC_URL.'icons/time-is-money.svg');?>" alt="Time icon" width="45" height="45" />
					</div>
					<span class="sgpb-unlock-options__title"><?php esc_html_e('Unlock Option', 'popup-builder'); ?></span>
				</div>
			<?php endif; ?>
		</div>
	</div>

</div>

<style type="text/css">
	#options-otherConditionsMetaBoxView .sgpb-option-disable {
		max-width: 650px;
	}
	#options-otherConditionsMetaBoxView .sgpb-unlock-options {
		position: absolute;
		top: 8%;
		right: 48%;
	}
	.rtl #options-otherConditionsMetaBoxView .sgpb-unlock-options {
		position: absolute;
		top: 8%;
		left: 48%;
		right: unset;
	}
</style>
