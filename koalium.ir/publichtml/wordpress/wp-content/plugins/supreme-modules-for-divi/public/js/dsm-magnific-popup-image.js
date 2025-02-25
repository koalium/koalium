jQuery(function ($) {
  if ($("a.dsm-image-lightbox").length) {
    $("a.dsm-image-lightbox").each(function (index, value) {
      var ID = $(this).attr("data-dsm-lightbox-id"),
        checkCondition = $(this).hasClass('et_pb_button') ? false : true;
      $(this).magnificPopup({
        type: 'image',
        removalDelay: 500,
        mainClass: 'mfp-fade',
        zoom: {
          enabled: checkCondition,
          duration: 500,
          opener: function (element) {
            return element.find('img');
          }
        },
        callbacks: {
          open: function () {
            var mp = $.magnificPopup.instance,
              t = $(mp.currItem.el[0]);
            this.container.addClass(t.data('dsm-lightbox-id') + ' dsm-lightbox-custom');
          }
        }
      });
    });
  }
});