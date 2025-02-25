import { __ } from '@wordpress/i18n';
import { AiImageGeneratorMarkup } from '@assist/tasks/images/AiImageGeneratorMarkup';

export default {
	slug: 'ai-image-editor',
	title: __('AI Image Generator', 'extendify-local'),
	sidebarTitle: __('Generate an image with AI', 'extendify-local'),
	description: __(
		'Generate AI images or search for images to bring a website to life.',
		'extendify-local',
	),
	buttonLabels: {
		completed: __('Revisit', 'extendify-local'),
		notCompleted: __('Start Generating with AI', 'extendify-local'),
	},
	link: 'post-new.php?post_type=page&ext-close&ext-add-image-block',
	type: 'html-text-button',
	dependencies: { goals: [], plugins: [] },
	show: () => !!window.extSharedData?.showDraft,
	backgroundImage: null,
	htmlBefore: () => (
		<AiImageGeneratorMarkup
			className="border-gray300 pointer-events-none relative hidden h-full min-h-56 w-full overflow-hidden rounded-t-lg border bg-gray-100 pt-5 lg:block"
			aria-hidden="true"
		/>
	),
};
