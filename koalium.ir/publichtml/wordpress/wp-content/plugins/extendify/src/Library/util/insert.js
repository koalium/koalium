import { dispatch, select } from '@wordpress/data';
import { useGlobalsStore } from '@library/state/global';
import { addGlobalCSS, requiredCSSVars } from '@library/util/css';

export const insertBlocks = async (blocks) => {
	const { insertBlocks, replaceBlock } = dispatch('core/block-editor');
	const {
		getSelectedBlock,
		getBlockHierarchyRootClientId,
		getBlockIndex,
		getGlobalBlockCount,
	} = select('core/block-editor');

	const { clientId, name, attributes } = getSelectedBlock() || {};
	const rootClientId = clientId ? getBlockHierarchyRootClientId(clientId) : '';
	const insertPointIndex =
		(rootClientId ? getBlockIndex(rootClientId) : getGlobalBlockCount()) + 1;

	// If there are spacing vars in state, we need to add them to the dom
	const { missingCSSVars } = useGlobalsStore.getState();
	missingCSSVars.forEach((key) => {
		// Add variables to the dom
		document?.documentElement?.style?.setProperty(key, requiredCSSVars[key]);
		// Editor might be nested in an iframe too
		document
			.querySelector('iframe[name="editor-canvas"]')
			?.contentDocument?.documentElement?.style?.setProperty(
				key,
				requiredCSSVars[key],
			);
	});

	// We also need to add them to global styles too
	if (missingCSSVars.length) addGlobalCSS(missingCSSVars);

	if (name === 'core/paragraph' && attributes?.content === '') {
		return await replaceBlock(clientId, blocks);
	}
	return await insertBlocks(blocks, insertPointIndex);
};
