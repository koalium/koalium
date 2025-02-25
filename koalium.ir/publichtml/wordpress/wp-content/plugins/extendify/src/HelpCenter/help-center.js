import domReady from '@wordpress/dom-ready';
import { HelpCenter } from '@help-center/HelpCenter';
import { render, isOnLaunch } from '@help-center/lib/utils';
import './app.css';
import './buttons';

const isInsideIframe = () => window !== window.top;

domReady(() => {
	if (isOnLaunch() || isInsideIframe()) return;
	const id = 'extendify-help-center-main';
	if (document.getElementById(id)) return;
	const helpCenter = Object.assign(document.createElement('div'), {
		className: 'extendify-help-center',
		id,
	});
	document.body.append(helpCenter);
	render(<HelpCenter />, helpCenter);
});
