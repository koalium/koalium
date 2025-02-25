export const loadImage = (img) => {
	return new Promise((resolve, reject) => {
		img.onload = () => resolve(img);
		img.onerror = (e) => reject(e);
	});
};
