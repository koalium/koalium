import { render, createRoot } from '@wordpress/element';
import { LaunchPage } from '@launch/LaunchPage';
import './launch.css';

requestAnimationFrame(() => {
	const launch = document.getElementById('extendify-launch-page');
	if (!launch) return;
	if (typeof createRoot !== 'function') {
		render(<LaunchPage />, launch);
		return;
	}
	createRoot(launch).render(<LaunchPage />);
});
