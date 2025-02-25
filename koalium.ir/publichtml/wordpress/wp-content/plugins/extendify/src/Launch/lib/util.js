import { decodeEntities } from '@wordpress/html-entities';
import { pingServer } from '@launch/api/DataApi';

/** Removes any hash or qs values from URL - Airtable adds timestamps */
export const stripUrlParams = (url) => url?.[0]?.url?.split(/[?#]/)?.[0];

function cleanAndBuildUnsplashUrl(url) {
	const cleanUrl = url
		.replaceAll('\\u0026', '&')
		// Remove duplicate question marks in URL by replacing second '?' with '&'
		.replace(/(\?.*?)\?/, '$1&');
	let imageUrl = new URL(decodeEntities(cleanUrl));

	const size = 1440;
	const orientation = imageUrl.searchParams.get('orientation');

	if (orientation === 'portrait') {
		imageUrl.searchParams.set('h', size);
		imageUrl.searchParams.delete('w');
	} else if (orientation === 'landscape' || orientation === 'square') {
		const widthParam = imageUrl.searchParams.get('w');
		if (widthParam === null || widthParam === '') {
			imageUrl.searchParams.set('w', size);
		}
	}

	imageUrl.searchParams.delete('orientation');
	imageUrl.searchParams.delete('ixid');
	imageUrl.searchParams.delete('ixlib');
	imageUrl.searchParams.append('q', '1');
	imageUrl.searchParams.append('auto', 'format,compress');
	imageUrl.searchParams.append('fm', 'avif');
	return imageUrl.toString();
}

export const lowerImageQuality = (html) => {
	return html.replace(
		/https:\/\/images\.unsplash\.com\/[^"')]+/g,
		cleanAndBuildUnsplashUrl,
	);
};

/**
 * Will ping every 1s until we get a 200 response from the server.
 * This is used because we were dealing with a particular issue where
 * servers we're very resource limited and rate limiting was common.
 * */
export const waitFor200Response = async () => {
	try {
		// This will error if not 200
		await pingServer();
		return true;
	} catch (error) {
		//
	}
	await new Promise((resolve) => setTimeout(resolve, 1000));
	return waitFor200Response();
};

export const wasInstalled = (activePlugins, pluginSlug) =>
	activePlugins?.filter((p) => p.includes(pluginSlug))?.length;

export const hexTomatrixValues = (hex) => {
	// convert from hex
	const colorInt = parseInt(hex.replace('#', ''), 16);
	// convert to rgb
	// This shifts each primary color value to the right-most 8 bits
	// then applies a mask to get the value of that color
	const r = (colorInt >> 16) & 255;
	const g = (colorInt >> 8) & 255;
	const b = colorInt & 255;
	// normalize to 0-1
	return [
		Math.round((r / 255) * 10000) / 10000,
		Math.round((g / 255) * 10000) / 10000,
		Math.round((b / 255) * 10000) / 10000,
	];
};
