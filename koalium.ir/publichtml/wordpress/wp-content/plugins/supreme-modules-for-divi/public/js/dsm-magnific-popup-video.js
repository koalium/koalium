jQuery(function ($) {
  if ($("a.dsm-video-lightbox").length) {
    $("a.dsm-video-lightbox").magnificPopup({
      type: 'iframe',
      removalDelay: 500,
      iframe: {
        markup: '<div class="mfp-iframe-scaler dsm-video-popup">' +
          '<div class="mfp-close"></div>' +
          '<iframe class="mfp-iframe" frameborder="0" allowfullscreen allow="autoplay"></iframe>' +
          '</div>',
        patterns: {
          youtube: {
            index: 'youtube.com/',
            id: 'v=',
            src: '//www.youtube.com/embed/%id%?autoplay=1&rel=0'
          },
          youtu_be: {
            index: 'youtu.be',
            id: '/',
            src: '//www.youtube.com/embed/%id%?autoplay=1&rel=0'
          },
          vimeo: {
            index: 'vimeo.com/',
            id: '/',
            src: '//player.vimeo.com/video/%id%?autoplay=1'
          },
          dailymotion: {
            index: 'dailymotion.com',
            id: function (url) {
              var m = url.match(/^.+dailymotion.com\/(video|hub)\/([^_]+)[^#]*(#video=([^_&]+))?/);
              if (m !== null) {
                if (m[4] !== undefined) {
                  return m[4];
                }
                return m[2];
              }
              return null;
            },
            src: 'https://www.dailymotion.com/embed/video/%id%'
          }
        },
        srcAction: 'iframe_src',
      },
      mainClass: 'dsm-video-popup-wrap mfp-fade',
      callbacks: {
        open: function () {
          var mp = $.magnificPopup.instance,
            t = $(mp.currItem.el[0]);
          this.container.addClass(t.data('dsm-lightbox-id') + ' dsm-lightbox-custom');
        }
      }
    });
  }
});