export const deepMerge = (target, ...sources) => {
	return sources.reduce((acc, source) => {
		if (!isObject(acc) || !isObject(source)) {
			return null;
		}

		const newTarget = { ...acc };

		for (const key in source) {
			if (isObject(source[key]) && key in newTarget) {
				newTarget[key] = deepMerge(newTarget[key], source[key]);
			} else {
				newTarget[key] = source[key];
			}
		}

		return newTarget;
	}, target);
};

export const isObject = (value) => {
	return typeof value === 'object' && !Array.isArray(value) && value !== null;
};

export const sleep = (ms) => new Promise((resolve) => setTimeout(resolve, ms));
