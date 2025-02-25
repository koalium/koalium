// based on https://github.com/WordPress/wordpress-develop/blob/trunk/src/js/_enqueues/admin/plugin-install.js#L21
// Height is the same value as 'height' from get_modal_link() in CnbButtonView
window.cnb_tb_position = function() {
    const cnbHeight = 452
    const cnbWidth = 630
    const width = jQuery(window).width(),
        H = jQuery(window).height() - ((782 < width) ? cnbHeight : 20),
        W = (782 < width) ? cnbWidth : width - 20;

    tbWindow = jQuery( '#TB_window' );

    if ( tbWindow.length ) {
        tbWindow.width( W ).height( 'auto' );
        jQuery( '#TB_iframeContent' ).width( W ).height( H );
        jQuery( '#TB_ajaxContent' ).width(W - 30 ).height( 'auto' );

        tbWindow.css({
            'margin-left': '-' + parseInt( ( W / 2 ), 10 ) + 'px'
        });
        if ( typeof document.body.style.maxWidth !== 'undefined' ) {
            tbWindow.css({
                'top': '30px',
                'margin-top': '0'
            });
        }
    }

    return jQuery( 'a.thickbox' ).each( function() {
        var href = jQuery( this ).attr( 'href' );
        if ( ! href ) {
            return;
        }
        href = href.replace( /&width=[0-9]+/g, '' );
        href = href.replace( /&height=[0-9]+/g, '' );
        jQuery(this).attr( 'href', href + '&width=' + W + '&height=' + ( H ) );
    });
};

jQuery( window ).on( 'resize', function() {
    cnb_tb_position();
});

const observer = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
        if (mutation.addedNodes.length) {
            const tbWindow = mutation.addedNodes[0];
            if (tbWindow.id === 'TB_overlay') {
                cnb_tb_position();
            }
        }
    });
});

observer.observe(document.body, {
    childList: true
});