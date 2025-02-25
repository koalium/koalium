import { Button } from '@wordpress/components';
import { __, isRTL } from '@wordpress/i18n';
import { helpFilled, Icon } from '@wordpress/icons';
import { useActivityStore } from '@shared/state/activity';
import classNames from 'classnames';
import { useGlobalSyncStore } from '@help-center/state/globals-sync';

export const PostEditor = () => {
	const { setVisibility } = useGlobalSyncStore();
	const { incrementActivity } = useActivityStore();
	return (
		<Button
			className="is-compact ml-1 inline-flex gap-1"
			data-test="help-center-editor-page-button"
			onClick={() => {
				setVisibility('open');
				incrementActivity('hc-editor-page-button');
			}}
			variant="primary">
			{__('Help', 'extendify-local')}
			<Icon
				icon={helpFilled}
				width={18}
				height={18}
				className={classNames('fill-design-text', {
					'scale-x-[-1]': isRTL(),
				})}
			/>
		</Button>
	);
};
