function SGPBPopupPro()
{

}

SGPBPopupPro.prototype.eventListener = function()
{
	sgAddEvent(window, 'sgpbDidOpen', function(e) {
		var args = e.detail;
		var popupId = parseInt(args.popupId);

		var iframeTag = jQuery('.sgpb-popup-builder-content-'+popupId+' .sgpb-iframe-'+popupId);
		if (iframeTag.length) {
			iframeTag.load(function() {
				var popupData = SGPBPopup.getPopupWindowDataById(popupId);
				/* remove spinner class when popup is open*/
				if (typeof popupData != 'undefined' && popupData.isOpen) {
					jQuery(this).removeClass('sgpb-iframe-spiner');
				}
			});
		}
	});

	sgAddEvent(window, 'sgpbDidClose', function(e) {
		var args = e.detail;
		var popupId = parseInt(args.popupId);

		var iframeTag = jQuery('.sgpb-popup-builder-content-'+popupId+' .sgpb-iframe-'+popupId);
		if (iframeTag.length) {
			iframeTag.each(function() {
				jQuery(this).addClass('sgpb-iframe-spiner');
			});
		}
	});
};


SGPBPopupPro.prototype.init = function()
{
	this.eventListener();
};

jQuery(document).ready(function() {
	var obj = new SGPBPopupPro();
	obj.init();
});
