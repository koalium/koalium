/**
 * Initialize the formbricks survey.
 * 
 * @see https://github.com/formbricks/setup-examples/tree/main/html
 */

function initFormbricks() {
 	window?.tsdk_formbricks?.init?.({
		environmentId: "clza2w309000x2hkas1nydy4s",
		apiHost: "https://app.formbricks.com",
		...(window?.wpcf7rSurveyData ?? {}),
	});
}

function handleSurveyData() {
	// Skip formbricks init event for other cf7 tabs.
	if ( document.querySelector('#redirect-panel-tab') ) {
		document
		.querySelector( '#contact-form-editor-tabs li#redirect-panel-tab' )
		.addEventListener( 'click', initFormbricks );
		return;
	}

	initFormbricks();
}
window.addEventListener('themeisle:survey:loaded', handleSurveyData);
