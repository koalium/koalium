import { useEffect, useLayoutEffect, useState } from '@wordpress/element';
import { __, isRTL } from '@wordpress/i18n';
import { NavigationButton } from '@launch/components/NavigationButton';
import {
	PagesSelect,
	state as pagesSelectState,
} from '@launch/pages/PagesSelect';
import { useGlobalStore } from '@launch/state/Global';
import { usePagesStore } from '@launch/state/Pages';
import { useUserSelectionStore } from '@launch/state/user-selections';
import { RightCaret, LeftCaret } from '@launch/svg';

const PagesPageData = {
	component: PagesSelect,
	state: pagesSelectState,
};

export const PageControl = () => {
	const {
		currentPageIndex,
		addPage,
		removePage,
		pages,
		getPageState,
		previousPage,
		replaceHistory,
	} = usePagesStore();
	const { siteStructure } = useUserSelectionStore();

	useLayoutEffect(() => {
		// If we later add more structures, consider having predefined paths
		if (siteStructure === 'multi-page') {
			addPage('page-select', PagesPageData, 'layout');
			return;
		}
		removePage('page-select');
	}, [siteStructure, addPage, removePage]);

	useEffect(() => {
		const replaceStateHistory = () => {
			history.state === null && replaceHistory(currentPageIndex);
		};
		window.addEventListener('load', replaceStateHistory);

		const popstate = () => {
			const page = currentPageIndex - 1;
			if (page === -1) history.go(-1);
			previousPage();
		};
		window.addEventListener('popstate', popstate);
		return () => {
			window.removeEventListener('popstate', popstate);
		};
	}, [previousPage, currentPageIndex, replaceHistory]);

	const pagesList = Array.from(pages.entries());
	// Some pages act as a notice or loading message and move on their own
	if (!getPageState(pagesList[currentPageIndex][0])?.useNav) return null;

	return (
		<div className="z-10 w-full flex-none border-t border-gray-100 bg-white px-6 py-5 shadow-surface md:px-12 md:py-6">
			<div className="flex justify-between">
				<span className="flex-1 self-start">
					<PrevButton />
				</span>
				<span className="hidden grow items-center justify-center md:flex">
					<Steps />
				</span>
				<span className="flex flex-1 justify-end">
					<NextButton />
				</span>
			</div>
		</div>
	);
};

const Steps = () => {
	const { currentPageIndex, pages, getPageState } = usePagesStore();
	const totalPages = usePagesStore((state) => state.count());
	const pagesList = Array.from(pages.entries());

	return (
		<div
			className="flex"
			role="progressbar"
			aria-valuenow={currentPageIndex}
			aria-valuemin="0"
			aria-valuetext={pagesList[currentPageIndex][1].state.getState().title}
			aria-valuemax={totalPages - 1}>
			{pagesList.map(([page], index) => {
				const bgColor =
					index < currentPageIndex ? 'bg-design-main' : 'bg-gray-200';
				if (!getPageState(page)?.useNav) return null;
				return (
					<div key={page} className="flex items-center">
						{(index !== currentPageIndex && (
							<div className={`${bgColor} h-2.5 w-2.5 rounded-full`} />
						)) || (
							<div className="flex h-4 w-4 items-center justify-center rounded-full bg-design-main">
								<div className="h-1.5 w-1.5 rounded-full bg-white/80" />
							</div>
						)}
						{index < totalPages - 1 && (
							<div className={`${bgColor} h-0.5 w-16`} />
						)}
					</div>
				);
			})}
		</div>
	);
};

const PrevButton = () => {
	const { previousPage, currentPageIndex } = usePagesStore();
	const onFirstPage = currentPageIndex === 0;

	if (onFirstPage) {
		return (
			<NavigationButton
				onClick={() =>
					(window.location.href = `${window.extSharedData.adminUrl}admin.php?page=extendify-assist`)
				}
				className="border-gray-200 bg-white text-design-main hover:bg-gray-50 focus:bg-gray-50">
				<>
					{isRTL() ? (
						<RightCaret className="mt-px h-5 w-5" />
					) : (
						<LeftCaret className="mt-px h-5 w-5" />
					)}
					<span>{__('Exit Launch', 'extendify-local')}</span>
				</>
			</NavigationButton>
		);
	}

	return (
		<NavigationButton
			onClick={previousPage}
			data-test="back-button"
			className="border-gray-200 bg-white text-design-main hover:bg-gray-50 focus:bg-gray-50">
			<>
				{isRTL() ? (
					<RightCaret className="mt-px h-5 w-5" />
				) : (
					<LeftCaret className="mt-px h-5 w-5" />
				)}
				<span>{__('Back', 'extendify-local')}</span>
			</>
		</NavigationButton>
	);
};

const NextButton = () => {
	const { nextPage, currentPageIndex, pages } = usePagesStore();
	const totalPages = usePagesStore((state) => state.count());
	const onLastPage = currentPageIndex === totalPages - 1;
	const currentPageKey = Array.from(pages.keys())[currentPageIndex];
	const pageState = pages.get(currentPageKey).state;
	const [canProgress, setCanProgress] = useState(false);
	const [canSkip, setCanSkip] = useState(false);

	const nextPageOrComplete = () => {
		if (onLastPage) {
			useGlobalStore.setState({ generating: true });
		} else {
			nextPage();
		}
	};

	useEffect(() => {
		const { ready, canSkip } = pageState?.getState() || {};
		setCanSkip(canSkip ?? false);
		setCanProgress(ready ?? false);
		return pageState.subscribe((s) => {
			setCanSkip(s.canSkip);
			setCanProgress(s.ready);
		});
	}, [pageState, currentPageIndex]);

	return canSkip ? (
		<NavigationButton
			onClick={() => nextPageOrComplete()}
			data-test="back-button"
			className="mr-2 border-gray-200 bg-white text-design-main hover:bg-gray-50 focus:bg-gray-50">
			<>
				{__('Skip', 'extendify-local')}
				{isRTL() ? (
					<LeftCaret className="mt-px h-5 w-5" />
				) : (
					<RightCaret className="mt-px h-5 w-5" />
				)}
			</>
		</NavigationButton>
	) : (
		<NavigationButton
			onClick={nextPageOrComplete}
			disabled={!canProgress}
			className="border-design-main bg-design-main text-design-text"
			data-test="next-button">
			<>
				{__('Next', 'extendify-local')}
				{isRTL() ? (
					<LeftCaret className="mt-px h-5 w-5" />
				) : (
					<RightCaret className="mt-px h-5 w-5" />
				)}
			</>
		</NavigationButton>
	);
};
