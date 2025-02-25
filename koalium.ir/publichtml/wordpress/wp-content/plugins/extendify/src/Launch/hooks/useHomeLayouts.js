import useSWRImmutable from 'swr/immutable';
import { getHomeTemplates } from '@launch/api/DataApi';
import { useSiteImages } from '@launch/hooks/useSiteImages';
import { useSiteProfile } from '@launch/hooks/useSiteProfile';
import { useSiteStrings } from '@launch/hooks/useSiteStrings';
import { useSiteStyles } from '@launch/hooks/useSiteStyles';
import { useUserSelectionStore } from '@launch/state/user-selections';

export const useHomeLayouts = () => {
	const { siteType, siteStructure, goals } = useUserSelectionStore();
	const { siteStrings } = useSiteStrings();
	const { siteProfile } = useSiteProfile();
	const { siteImages } = useSiteImages();
	const { siteStyles } = useSiteStyles();

	const loading =
		!siteStructure ||
		!siteProfile ||
		!siteStrings ||
		!siteImages ||
		!siteStyles;

	const params = {
		key: 'home-layouts',
		siteType,
		siteStructure,
		siteProfile,
		siteStrings,
		siteImages,
		siteStyles,
		goals,
	};

	const { data, error } = useSWRImmutable(
		loading ? null : params,
		getHomeTemplates,
	);

	return { homeLayouts: data, error, loading: !data && !error };
};
