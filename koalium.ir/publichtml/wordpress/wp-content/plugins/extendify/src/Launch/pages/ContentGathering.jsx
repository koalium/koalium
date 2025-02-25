import { useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { Title } from '@launch/components/Title';
import { VideoPlayer } from '@launch/components/VideoPlayer';
import { useHomeLayouts } from '@launch/hooks/useHomeLayouts';
import { useSiteImages } from '@launch/hooks/useSiteImages';
import { useSiteStrings } from '@launch/hooks/useSiteStrings';
import { PageLayout } from '@launch/layouts/PageLayout';
import { usePagesStore } from '@launch/state/Pages';
import { pageState } from '@launch/state/factory';
import { useUserSelectionStore } from '@launch/state/user-selections';

export const state = pageState('Content Gathering', () => ({
	ready: true,
	canSkip: false,
	useNav: false,
	onRemove: () => {},
}));

export const ContentGathering = () => {
	const { nextPage } = usePagesStore();
	const { setSiteStrings, setSiteImages } = useUserSelectionStore();
	const { siteStrings } = useSiteStrings();
	const { siteImages } = useSiteImages();
	const { homeLayouts } = useHomeLayouts();

	useEffect(() => {
		if (!siteStrings) return;
		setSiteStrings(siteStrings);
	}, [siteStrings, setSiteStrings]);

	useEffect(() => {
		if (!siteImages) return;
		setSiteImages(siteImages);
	}, [siteImages, setSiteImages]);

	useEffect(() => {
		if (!homeLayouts) return;
		let id = setTimeout(nextPage, 1000);
		return () => clearTimeout(id);
	}, [homeLayouts, nextPage]);

	return (
		<PageLayout>
			<div className="mx-auto grow overflow-y-auto px-4 py-8 md:p-12 md:px-6 3xl:p-16">
				<div className="mx-auto flex h-full flex-col justify-center">
					<VideoPlayer
						path="https://assets.extendify.com/launch/site-building.webm"
						className="mx-auto h-auto w-[200px] md:w-[400px]"
					/>

					<Title
						title={__('Designing Your Options', 'extendify-local')}
						description={__(
							'Please wait while we build some website options for you to select from.',
							'extendify-local',
						)}
					/>
				</div>
			</div>
		</PageLayout>
	);
};
