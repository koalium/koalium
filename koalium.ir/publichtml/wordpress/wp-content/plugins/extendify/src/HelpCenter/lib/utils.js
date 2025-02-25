import { render as renderDeprecated, createRoot } from '@wordpress/element';

export const render = (component, node) => {
	if (typeof createRoot !== 'function') {
		renderDeprecated(component, node);
		return;
	}
	createRoot(node).render(component);
};

export const isOnLaunch = () => {
	const q = new URLSearchParams(window.location.search);
	return ['page'].includes(q.get('extendify-launch'));
};
