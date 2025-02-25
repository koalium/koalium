import { fetchImages } from '@shared/lib/unsplash';
import { useUnsplashCacheStore } from '@shared/state/unsplash-cache';
import useSWRImmutable from 'swr/immutable';

const searchImages = async (search) => {
	const cache = useUnsplashCacheStore.getState();
	if (search === 'unsplash' && !cache.isEmpty() && !cache.hasExpired()) {
		return cache.images;
	}
	return await fetchImages(search);
};

export const useUnsplashImages = (search) => {
	const { data, error } = useSWRImmutable(search || 'unsplash', searchImages);
	return { data, error, loading: !data && !error };
};
