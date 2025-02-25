export const removeBlocks = (blocks, blocksToRemove) =>
	blocks
		.filter((block) => !blocksToRemove.includes(block.name))
		.map((block) => ({
			...block,
			innerBlocks: block.innerBlocks
				? removeBlocks(block.innerBlocks, blocksToRemove)
				: [],
		}));

export const addIdAttributeToBlock = (blockCode, id) =>
	blockCode.replace(
		/(<div\s[^>]*class="[^"]*\bwp-block-group\b[^"]*")/,
		`$1 id="${id}"`,
	);
