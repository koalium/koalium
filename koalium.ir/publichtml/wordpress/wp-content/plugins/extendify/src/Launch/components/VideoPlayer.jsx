import { __ } from '@wordpress/i18n';

export const VideoPlayer = ({ path, className = null }) => {
	return (
		<div className={className}>
			<video id="video-player" playsInline muted autoPlay loop>
				<source src={path} type="video/webm" />
				{__('Your browser does not support the video tag.', 'extendify-local')}
			</video>
		</div>
	);
};
