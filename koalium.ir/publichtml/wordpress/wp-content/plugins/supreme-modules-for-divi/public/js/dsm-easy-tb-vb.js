jQuery(function ($) {
  // Make sure JS class is added.
  document.documentElement.className = "js";
  var et_is_mobile_device = navigator.userAgent.match(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/),
    adminBar = $("#wpadminbar").height(),
    threshold = dsm_easy_tb_js.threshold,
    first_section_bg = dsm_easy_tb_js.first_background_color,
    second_section_bg = dsm_easy_tb_js.second_background_color;
  if ($(".et-l--header").length) {
    if ($(".dsm_fixed_header").length) {
      if ($(".dsm_fixed_header_auto").length) {
        /*
        if ($("body").hasClass("admin-bar")) {
          $(".et-l--header").closest("body").attr('style', 'padding-top: 32px !important');
        }*/
      }
      //Scroll
      if ($(".dsm_fixed_header_scroll").length) {
        $(window).scroll(function () {
          // Toggle header class after threshold point.
          if ($(document).scrollTop() > threshold) {
            $(".dsm_fixed_header_scroll").addClass("dsm_fixed_header_scroll_active");
            $(".dsm_fixed_header_scroll").addClass("dsm_fixed_header_scrolled");
            if (first_section_bg !== '') {
              $(".dsm_fixed_header_scroll .et-l--header .et_pb_section:nth(0)").attr('style', 'background-color:' + first_section_bg + ';');
            }
            if (second_section_bg !== '') {
              $(".dsm_fixed_header_scroll .et-l--header .et_pb_section:nth(1)").attr('style', 'background-color:' + second_section_bg + ';');
            }
          } else {
            $(".dsm_fixed_header_scroll").removeClass("dsm_fixed_header_scroll_active");
            if (first_section_bg !== '') {
              $(".dsm_fixed_header_scroll .et-l--header .et_pb_section:nth(0)").css("background-color", "");
            }
            if (second_section_bg !== '') {
              $(".dsm_fixed_header_scroll .et-l--header .et_pb_section:nth(1)").css("background-color", "");
            }
          }
        });
      }
      //Shrink
      if ($(".dsm_fixed_header_shrink").length) {
        if (typeof $(".dsm_fixed_header_shrink .et-l--header .et_pb_menu__logo img") !== 'undefined' && $(".dsm_fixed_header_shrink .et-l--header .et_pb_menu__logo img").length || typeof $(".dsm_fixed_header_shrink .et-l--header img") !== 'undefined' && $(".dsm_fixed_header_shrink .et-l--header img").length) {
          if ($(".dsm_fixed_header_shrink .et-l--header .et_pb_menu__logo img").length) {
            var menu_logo = $(".dsm_fixed_header_shrink .et-l--header .et_pb_menu__logo img")[0].src;
          }
          if ($(".dsm_fixed_header_shrink .et-l--header img").length) {
            var header_image = $(".dsm_fixed_header_shrink .et-l--header img")[0].src;
          }
          var logo = $(".dsm_fixed_header_shrink .et-l--header .et_pb_menu__logo img").src !== undefined ? menu_logo : header_image;
          var beforeSrcSet = $(".dsm_fixed_header_shrink .et-l--header .et_pb_menu__logo img").attr("srcset");
        }
        shrink_logo = dsm_easy_tb_js.shrink_logo;
        // Run on page scroll.
        $(window).scroll(function () {
          // Toggle header class after threshold point.
          if ($(document).scrollTop() > threshold) {
            $(".dsm_fixed_header_shrink").addClass("dsm_fixed_header_shrink_active");
            $(".dsm_fixed_header_shrink").addClass("dsm_fixed_header_shrink_active_scrolled");
            if ($(".dsm_fixed_header_shrink").hasClass("dsm_fixed_header_shrink_logo")) {
              if ($(".dsm_fixed_header_shrink_logo .et-l--header .et_pb_menu__logo").length) {
                if (beforeSrcSet) {
                  $(".dsm_fixed_header_shrink_logo .et-l--header .et_pb_menu__logo img").attr("srcset", "");
                }
                $(".dsm_fixed_header_shrink_logo .et-l--header .et_pb_menu__logo img").attr("src", shrink_logo);
              } else {
                if (beforeSrcSet) {
                  $(".dsm_fixed_header_shrink_logo .et-l--header .et_pb_image img").attr("srcset", "");
                }
                $(".dsm_fixed_header_shrink_logo .et-l--header .et_pb_image img").attr("src", shrink_logo);
              }
            }
          } else {
            $(".dsm_fixed_header_shrink").removeClass("dsm_fixed_header_shrink_active");
            if ($(".dsm_fixed_header_shrink").hasClass("dsm_fixed_header_shrink_logo")) {
              if ($(".dsm_fixed_header_shrink_logo .et-l--header .et_pb_menu__logo").length) {
                if (beforeSrcSet) {
                  $(".dsm_fixed_header_shrink_logo .et-l--header .et_pb_menu__logo img").attr("srcset", beforeSrcSet);
                }
                $(".dsm_fixed_header_shrink_logo .et-l--header .et_pb_menu__logo img").attr("src", logo);
              } else {
                if (beforeSrcSet) {
                  $(".dsm_fixed_header_shrink_logo .et-l--header .et_pb_image img").attr("srcset", beforeSrcSet);
                }
                $(".dsm_fixed_header_shrink_logo .et-l--header .et_pb_image img").attr("src", logo);
              }
            }
          }

        });
      }
      //Run on resize.
      $(window).resize(function () {
        if (window.matchMedia("(max-width: 768px)").matches === true) {
          if ($("body").hasClass("admin-bar")) {
            $(".et-l--header").closest("body").attr('style', 'padding-top: 0;');
          }
        }
      });
    }
  }
});