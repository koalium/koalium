import { createRoot } from '@wordpress/element';
import { registerPlugin } from '@wordpress/plugins';
import { MainButton } from '@library/components/MainButton';
import { Modal } from '@library/components/Modal';
import './library.css';

registerPlugin('extendify-library', {
	render: () => {
		if (typeof createRoot !== 'function') return;
		const id = 'extendify-library-btn';
		const className = 'extendify-library';
		const page = '.edit-post-header-toolbar';
		const fse = '.edit-site-header-edit-mode__start';
		if (!document.querySelector(page) && !document.querySelector(fse)) {
			return;
		}
		requestAnimationFrame(() => {
			if (document.getElementById(id)) return;
			const btnWrap = document.createElement('div');
			const btn = Object.assign(btnWrap, { id, className });
			document.querySelector(page)?.append(btn);
			document.querySelector(fse)?.append(btn);
			createRoot(btn).render(<MainButton />);

			const mdl = 'extendify-library-modal';
			if (document.getElementById(mdl)) return;
			const modalWrap = document.createElement('div');
			const modal = Object.assign(modalWrap, { id: mdl, className });
			document.body.append(modal);
			createRoot(modal).render(<Modal />);
		});
	},
});
