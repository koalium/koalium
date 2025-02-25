import {
	render as renderDeprecated,
	unmountComponentAtNode,
	createRoot,
} from '@wordpress/element';

export const render = (component, node) => {
	if (typeof createRoot === 'function') {
		return createRoot(node).render(component);
	}
	// Old React api for rendering with unmount support
	renderDeprecated(component, node);
	return {
		unmount: () => {
			unmountComponentAtNode(node);
		},
	};
};
