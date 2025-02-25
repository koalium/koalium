import { store as blockEditorStore } from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import { useCallback } from '@wordpress/element';

export const useSelectedText = () => {
	const { getSelectedBlockClientIds, getBlocksByClientId } = useSelect(
		(select) => select(blockEditorStore),
		[],
	);

	const selectedBlockId = getSelectedBlockClientIds();

	const getSelectedContent = useCallback(() => {
		const selectedBlocks = getBlocksByClientId(selectedBlockId);
		return selectedBlocks
			.map(({ attributes }) => attributes.content)
			.join('\n\n');
	}, [getBlocksByClientId, selectedBlockId]);

	return {
		selectedText: getSelectedContent(),
	};
};
