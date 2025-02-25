import { subscribe } from '@wordpress/data';
import { preFetchImages as preFetchUnsplashImages } from '@shared/lib/unsplash';
import '@draft/app.css';

const isOnLaunch = () => {
	const query = new URLSearchParams(window.location.search);
	return query.get('page') === 'extendify-launch';
};

(() => {
	// Disable the page editor welcome guide always (they can manually open it)
	const key = `WP_PREFERENCES_USER_${window.extSharedData.userId}`;
	const existing = window.localStorage.getItem(key) || '{}';

	window.localStorage.setItem(
		key,
		JSON.stringify({
			...JSON.parse(existing),
			'core/edit-post': {
				...(JSON.parse(existing)?.['core/edit-post'] ?? {}),
				welcomeGuide: false,
			},
		}),
	);

	if (isOnLaunch()) return;

	preFetchUnsplashImages();

	// TODO: If this PR is released in WP (6.7?), then we can use the localstorage
	// approach that we use above for the welcome guide
	// https://github.com/WordPress/gutenberg/pull/65026

	// If the pattern modal shows up within 3 seconds, close it
	const modalClass = '.editor-start-page-options__modal-content';
	const modalCloseButton = '.components-modal__header > .components-button';

	// Add CSS to hide the modal initially (avoid content paint flash)
	const style = document.createElement('style');
	style.innerHTML =
		'.components-modal__screen-overlay { display: none!important }';
	document.head.appendChild(style);

	const unsub = subscribe(() => {
		const modal = document.querySelector(modalClass);
		if (!modal) return;
		modal.style.display = ''; // Temp show to click it
		document.querySelector(modalCloseButton)?.click();
	});

	setTimeout(() => {
		// Remove the CSS rule always
		document.head.removeChild(style);
		unsub();
	}, 3000);
})();
