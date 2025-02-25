import useSWRImmutable from 'swr/immutable';
import { getGoals } from '@launch/api/DataApi';
import { useSiteProfile } from '@launch/hooks/useSiteProfile';
import { useUserSelectionStore } from '@launch/state/user-selections';

export const useGoals = () => {
	const { siteInformation } = useUserSelectionStore();
	const { loading, siteProfile } = useSiteProfile();
	const params = { key: 'goals', siteProfile, title: siteInformation?.title };
	const { data, error } = useSWRImmutable(loading ? null : params, getGoals);

	return { goals: data, error, loading: !data && !error };
};
