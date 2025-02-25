export const safeParseJson = (json) => {
	try {
		return JSON.parse(json);
	} catch (e) {
		return {};
	}
};
