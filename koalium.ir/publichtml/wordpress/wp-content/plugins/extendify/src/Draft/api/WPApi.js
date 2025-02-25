import apiFetch from '@wordpress/api-fetch';
import { createBlock, insertBlock } from '@wordpress/blocks';
import { downloadPing } from '@draft/api/Data';
import { loadImage } from '@draft/lib/image';

export const importImage = async (imageUrl, metadata = {}) => {
	const image = new Image();
	image.src = imageUrl;
	image.crossOrigin = 'anonymous';
	await loadImage(image);

	const canvas = document.createElement('canvas');
	canvas.width = image.width;
	canvas.height = image.height;

	const ctx = canvas.getContext('2d');
	if (!ctx) return;
	ctx.drawImage(image, 0, 0);

	const blob = await new Promise((resolve) => {
		canvas.toBlob((blob) => {
			blob && resolve(blob);
		}, 'image/jpeg');
	});

	const formData = new FormData();
	formData.append('file', new File([blob], metadata.filename));
	formData.append('alt_text', metadata.alt ?? '');
	formData.append('caption', metadata.caption ?? '');
	formData.append('status', 'publish');

	return await apiFetch({
		path: 'wp/v2/media',
		method: 'POST',
		body: formData,
	});
};

export const importImageServer = async (src, metadata = {}) => {
	const formData = new FormData();
	formData.append('source', src);
	// Fallback doesn't suppport custom file_name
	formData.append('alt_text', metadata.alt ?? '');
	formData.append('caption', metadata.caption ?? '');

	return await apiFetch({
		path: '/extendify/v1/draft/upload-image',
		method: 'POST',
		body: formData,
	});
};

export const downloadImage = async (
	id,
	src,
	source,
	unsplashId,
	metadata = { alt: '', caption: '' },
) => {
	let image;
	await downloadPing(id, source, { unsplashId });
	try {
		image = await importImage(src, {
			alt: metadata.alt,
			filename: 'image.jpg',
			caption: metadata.caption,
		});
	} catch (_e) {
		image = await importImageServer(src, {
			alt: metadata.alt,
			filename: 'image.jpg',
			caption: metadata.caption,
		});
	}

	return image;
};

export const addImageToBlock = (
	selectedBlock,
	image,
	updateBlockAttributes,
) => {
	if (selectedBlock.name === 'core/image') {
		updateBlockAttributes(selectedBlock.clientId, {
			id: image.id,
			caption: image.caption.raw,
			url: image.source_url,
			alt: image.alt_text,
		});
	}

	if (selectedBlock.name === 'core/media-text') {
		updateBlockAttributes(selectedBlock.clientId, {
			mediaId: image.id,
			caption: image.caption.raw,
			mediaUrl: image.source_url,
			mediaAlt: image.alt_text,
			mediaType: 'image',
		});
	}

	if (selectedBlock.name === 'core/gallery') {
		const newBlock = createBlock('core/image', {
			id: image.id,
			caption: image.caption.raw,
			url: image.source_url,
			alt: image.alt_text,
		});

		insertBlock(newBlock, null, selectedBlock.clientId);
	}

	if (selectedBlock.name === 'core/cover') {
		updateBlockAttributes(selectedBlock.clientId, {
			id: image.id,
			url: image.source_url,
			alt: image.alt_text,
			backgroundType: 'image',
			dimRatio: 50,
			hasParallax: false,
			isDark: true,
			isRepeated: false,
			layout: {
				type: 'constrained',
			},
			tagName: 'div',
			useFeaturedImage: false,
		});
	}
};
