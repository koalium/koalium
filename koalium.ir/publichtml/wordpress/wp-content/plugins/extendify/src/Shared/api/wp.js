import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';

export const getPlugin = async (slug) => {
	const response = await apiFetch({
		path: addQueryArgs('/wp/v2/plugins', { search: slug }),
	});

	let plugin = response?.[0];

	if (!plugin) throw new Error('Plugin not found');

	return plugin;
};

export const installPlugin = async (slug) => {
	return await apiFetch({
		path: '/wp/v2/plugins',
		method: 'POST',
		data: {
			slug,
		},
	});
};

export const activatePlugin = async (slug) => {
	const plugin = await getPlugin(slug);

	return await apiFetch({
		path: `/wp/v2/plugins/${plugin.plugin}`,
		method: 'POST',
		data: {
			status: 'active',
		},
	});
};
