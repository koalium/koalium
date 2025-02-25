import { __ } from '@wordpress/i18n';

const { themeSlug } = window.extSharedData;
const { launchCompleted } = window.extAssistData;

export default {
	slug: 'setup-woocommerce-germanized-store',
	title: __('Set up WooCommerce Germanized plugin', 'extendify-local'),
	description: __(
		'Ensure your eCommerce store complies with local regulations.',
		'extendify-local',
	),
	link: 'admin.php?page=wc-gzd-setup',
	buttonLabels: {
		notCompleted: __('Set up', 'extendify-local'),
		completed: __('Revisit', 'extendify-local'),
	},
	type: 'internalLink',
	dependencies: {
		goals: ['products', 'services'],
		plugins: ['woocommerce-germanized'],
	},
	show: ({ plugins, goals, activePlugins, userGoals }) => {
		// They need either extendable or launch completed
		if (themeSlug !== 'extendable' && !launchCompleted) return false;
		if (!plugins.length && !goals.length) return true;

		return activePlugins
			.concat(userGoals)
			.some((item) => plugins.concat(goals).includes(item));
	},
	backgroundImage:
		'https://assets.extendify.com/assist-tasks/woocommerce-2.webp',
};
