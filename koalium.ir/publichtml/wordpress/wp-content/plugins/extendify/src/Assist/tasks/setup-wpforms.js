import { __ } from '@wordpress/i18n';

const { themeSlug } = window.extSharedData;
const { launchCompleted } = window.extAssistData;

export default {
	slug: 'setup-wpforms',
	title: __('Set up WPForms', 'extendify-local'),
	description: __(
		'Set up the WPForms plugin to add a contact form on your site.',
		'extendify-local',
	),
	link: '?page=wpforms-getting-started',
	buttonLabels: {
		notCompleted: __('Set up', 'extendify-local'),
		completed: __('Revisit', 'extendify-local'),
	},
	type: 'internalLink',
	dependencies: { goals: [], plugins: ['wpforms-lite'] },
	show: ({ plugins, goals, activePlugins, userGoals }) => {
		// They need either extendable or launch completed
		if (themeSlug !== 'extendable' && !launchCompleted) return false;
		if (!plugins.length && !goals.length) return true;

		return activePlugins
			.concat(userGoals)
			.some((item) => plugins.concat(goals).includes(item));
	},
	backgroundImage:
		'https://assets.extendify.com/assist-tasks/bg-for-forms.webp',
};
