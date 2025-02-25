function SGPBGutenbergBlock()
{

}

SGPBGutenbergBlock.prototype.init = function()
{
	var localizedParams = SGPB_GUTENBERG_PARAMS;

	var __ = wp.i18n;
	var createElement     = wp.element.createElement;
	var registerBlockType = wp.blocks.registerBlockType;
	var InspectorControls = wp.editor.InspectorControls;
	var _wp$components    = wp.components,
		SelectControl     = _wp$components.SelectControl,
		TextareaControl   = _wp$components.TextareaControl,
		ToggleControl     = _wp$components.ToggleControl,
		PanelBody         = _wp$components.PanelBody,
		ServerSideRender  = _wp$components.ServerSideRender,
		Placeholder       = _wp$components.Placeholder;

	registerBlockType('popupbuilder/popups', {
		title: localizedParams.title,
		description: localizedParams.description,
		keywords: ['popup', 'popup-builder'],
		category: 'widgets',
		icon: 'welcome-widgets-menus',
		attributes: {
			popupId: {
				type: 'number'
			},
			popupEvent: {
				type: 'string'
			}
		},
		edit(props) {
			const {
				attributes: {
					popupId = '',
					displayTitle = false,
					displayDesc = false,
					popupEvent = ''
				},
				setAttributes
			} = props;
			const formOptions = SGPB_GUTENBERG_PARAMS.allPopups.map(value => (
					{
						value: value.id,
						label: value.title
					}
				)
			);
			const eventsOptions = SGPB_GUTENBERG_PARAMS.allEvents.map(value => (
					{
						value: value.value,
						label: value.title
					}
				)
			);
			let jsx;

			formOptions.unshift({
				value: '',
				label: SGPB_GUTENBERG_PARAMS.i18n.form_select
			});

			function selectPopup(value) {
				setAttributes({
					popupId: value
				});
			}

			function selectEvent(value) {
				setAttributes({
					popupEvent: value
				});
			}

			function setContent(value) {
				setAttributes({
					content: value
				});
			}

			function toggleDisplayTitle(value) {
				setAttributes({
					displayTitle: value
				});
			}

			function toggleDisplayDesc(value) {
				setAttributes({
					displayDesc: value
				});
			}

			jsx = [
				<InspectorControls key="popuopbuilder-gutenberg-form-selector-inspector-controls">
					<PanelBody title={'popup builder title'}>
						<SelectControl
							label = {SGPB_GUTENBERG_PARAMS.i18n.form_selected}
							value = {popupId}
							options = {formOptions}
							onChange = {selectPopup}
						/>
						<SelectControl
							label = {SGPB_GUTENBERG_PARAMS.i18n.form_selected}
							value = {popupId}
							options = {eventsOptions}
							onChange = {selectEvent}
						/>
						<ToggleControl
							label = {SGPB_GUTENBERG_PARAMS.i18n.show_title}
							checked = {displayTitle}
							onChange = {toggleDisplayTitle}
						/>
						<ToggleControl
							label = {SGPB_GUTENBERG_PARAMS.i18n.show_description}
							checked = {displayDesc}
							onChange = {toggleDisplayDesc}
						/>
					</PanelBody>
				</InspectorControls>
			];

			if (popupId && popupEvent) {
				var clickText = '';
				if (popupEvent == 'click') {
					clickText = 'click me';
				}
				return '[sg_popup id="'+popupId+'" event="'+popupEvent+'"]'+clickText+'[/sg_popup]';
			}
			else {
				jsx.push(
					<Placeholder
					key="sgpb-gutenberg-form-selector-wrap"
					className="sgpb-gutenberg-form-selector-wrapper">
						<img class={SGPB_GUTENBERG_PARAMS.logo_classname} src={ SGPB_GUTENBERG_PARAMS.logo_url }/>
						<SelectControl
							key = "sgpb-gutenberg-form-selector-select-control"
							value = {popupId}
							options = {formOptions}
							onChange = {selectPopup}
								/>
								<SelectControl
							key = "sgpb-gutenberg-form-selector-select-control"
							value = {popupEvent}
							options = {eventsOptions}
							onChange = {selectEvent}
								/>
					</Placeholder>
				);
			}

			return jsx;
		},
		save(props) {
			var clickText = '';
			if (props.attributes.popupEvent == 'click') {
				clickText = SGPB_GUTENBERG_PARAMS.clickText;
			}

			return '[sg_popup id="'+props.attributes.popupId+'" event="'+props.attributes.popupEvent+'"]'+clickText+'[/sg_popup]';
		}
	});
};

jQuery(document).ready(function () {
	if (typeof wp != 'undefined' && typeof wp.element != 'undefined' && typeof wp.blocks != 'undefined' && typeof wp.editor != 'undefined' && typeof wp.components != 'undefined') {
		var block = new SGPBGutenbergBlock();
		block.init();
	}
});
