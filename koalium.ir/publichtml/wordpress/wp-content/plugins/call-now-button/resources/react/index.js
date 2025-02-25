import domReady from '@wordpress/dom-ready';
import { createRoot } from '@wordpress/element';
import InitialRouting from "./components/routing/InitialRouting";

domReady(() => {
    createRoot(document.getElementById('call-now-button-app'))
        .render(<InitialRouting />);
});
