import { __ } from '@wordpress/i18n';
import { external, Icon } from '@wordpress/icons';

export const Support = ({ height }) => {
	if (!window.extHelpCenterData?.supportUrl) {
		return <div className={`bg-design-main ${height}`} />;
	}

	return (
		<div className="px-6 py-8">
			<a
				href={window.extHelpCenterData.supportUrl}
				target="_blank"
				rel="noopener noreferrer"
				className="flex items-center gap-4 rounded border border-solid border-gray-300 px-4 py-3 text-sm text-gray-800 no-underline">
				<span>
					{__(
						'For other questions, visit our support page.',
						'extendify-local',
					)}
				</span>
				<Icon icon={external} className="w-8 fill-current" />
			</a>
		</div>
	);
};
