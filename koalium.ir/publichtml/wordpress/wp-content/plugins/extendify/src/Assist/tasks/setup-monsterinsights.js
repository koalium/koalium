import { __ } from '@wordpress/i18n';

const { themeSlug } = window.extSharedData;
const { launchCompleted } = window.extAssistData;

export default {
	slug: 'setup-monsterinsights',
	title: __('Set up analytics', 'extendify-local'),
	description: __(
		'Set up the MonsterInsights plugin to enable Google Analytics in your website.',
		'extendify-local',
	),
	link: '?page=monsterinsights-onboarding#/',
	buttonLabels: {
		notCompleted: __('Set up', 'extendify-local'),
		completed: __('Revisit', 'extendify-local'),
	},
	type: 'internalLink',
	dependencies: { goals: [], plugins: ['google-analytics-for-wordpress'] },
	show: ({ plugins, goals, activePlugins, userGoals }) => {
		// They need either extendable or launch completed
		if (themeSlug !== 'extendable' && !launchCompleted) return false;
		if (!plugins.length && !goals.length) return true;

		return activePlugins
			.concat(userGoals)
			.some((item) => plugins.concat(goals).includes(item));
	},
	backgroundImage: 'https://assets.extendify.com/assist-tasks/hubspot.webp',
};
