// menu toggle
$(function () {
  $(".menu-toggle").on("click", function () {
    var $toggle = $(this);

    $toggle.toggleClass("active").siblings(".menu-sub").slideToggle();

    $toggle.siblings(".menu-mega").children(".menu-sub").slideToggle();

    $toggle.parent().siblings(".menu-item-group").children(".menu-sub").slideUp();

    $toggle.parent().siblings(".menu-item-group").children(".menu-mega").children(".menu-sub").slideUp();

    $toggle.parent().siblings(".menu-item-group").children(".menu-toggle").removeClass("active");
  });

  $(".menu-item-group > .menu-link, .menu-item-mega > .menu-link").on("click", function (e) {
    if ($(window).width() < 1200 || !mobileAndTabletCheck()) return;

    e.preventDefault();
  });
});

// navbar mobile toggle
$(function () {
  var $body = $("html, body");
  var $navbar = $(".js-navbar");
  var $navbarToggle = $(".js-navbar-toggle");

  $navbarToggle.on("click", function () {
    $navbarToggle.toggleClass("active");
    $navbar.toggleClass("is-show");
    $body.toggleClass("overflow-hidden");
  });
});

$(function () {
  var $moveTop = $(".btn-movetop");
  var $window = $(window);
  var $body = $("html");

  if (!$moveTop.length) return;

  $window.on("scroll", function () {
    if ($window.scrollTop() > 150) {
      $moveTop.addClass("show");

      return;
    }

    $moveTop.removeClass("show");
  });

  $moveTop.on("click", function () {
    window.scrollTo({
      top: 0,
      left: 0,
      behavior: "smooth"
    });
  });
});

// swiper template
function addSwiper(selector, options = {}) {
  return Array.from(document.querySelectorAll(selector), function (item) {
    var $sliderContainer = $(item),
        $sliderEl = $sliderContainer.find(selector + "__container");

    if (options.navigation) {
      $sliderContainer.addClass("has-nav");
      options.navigation = {
        prevEl: $sliderContainer.find(selector + "__prev"),
        nextEl: $sliderContainer.find(selector + "__next")
      };
    }

    if (options.pagination) {
      $sliderContainer.addClass("has-pagination");
      options.pagination = {
        el: $sliderContainer.find(selector + "__pagination"),
        clickable: true
      };
    }

    return new Swiper($sliderEl, options);
  });
}

$(function () {
  addSwiper('.partner-slider', {
    slidesPerView: 3,
    slidesPerColumn: 3,
    slidesPerColumnFill: 'row',
    pagination: true,
    spaceBetween: 8,
    breakpoints: {
      576: {
        slidesPerView: 3
      },
      768: {
        slidesPerView: 4,
        spaceBetween: 16
      },
      992: {
        slidesPerView: 5,
        spaceBetween: 16
      },
      1200: {
        slidesPerView: 5,
        spaceBetween: 20
      }
    }
  });
});

$(function () {
  addSwiper('.review-slider', {
    spaceBetween: 20,
    pagination: true,
    navigation: true,
    loop: true,
    autoplay: {
      delay: 4000,
      disableOnInteraction: false
    }
  });
});

$(function () {
  addSwiper('.service-slider', {
    loop: true,
    pagination: true,
    navigation: true,
    slidesPerView: 2,
    spaceBetween: 16,
    speed: 600,
    autoplay: {
      delay: 4000,
      disableOnInteraction: false
    },
    breakpoints: {
      992: {
        slidesPerView: 3
      },
      1200: {
        slidesPerView: 3,
        spaceBetween: 60
      }
    }
  });
});

$(function () {
  addSwiper('.research-slider', {
    loop: true,
    pagination: true,
    navigation: true,
    slidesPerView: 2,
    spaceBetween: 16,
    breakpoints: {
      576: {
        slidesPerView: 3
      },
      768: {
        slidesPerView: 4
      },
      992: {
        slidesPerView: 5
      },
      1200: {
        slidesPerView: 5,
        spaceBetween: 20
      }
    }
  });
});

// file input
$(function () {
  $(".js-file-input").on("change", function () {
    var fileName = $(this).val().split(/\\|\//).pop();

    $(this).closest(".js-file").find(".js-file-text").text(fileName);

    var target = $(this).data("target");
    if (target) {
      readURL(this, target);
    }
  });

  function readURL(input, target) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();

      reader.onload = function (e) {
        $(target).show();
        $(target).attr("src", e.target.result);
      };

      reader.readAsDataURL(input.files[0]);
    }
  }
});

$(function () {

  const $window = $(window);

  const $search = $('.search');

  $('.btn-search').on('click', function (e) {

    e.stopPropagation();

    $search.fadeToggle();

    $search.find('input').focus();
  });

  $search.on('click', function (e) {

    e.stopPropagation();
  });

  $('html, body').on('click', function () {

    if ($window.width() < 1200) return;

    $search.hide();
  });
});

$(function () {

  const $window = $(window);

  const $header = $('.header');

  $window.on('scroll', function () {

    if ($window.scrollTop() > 10) {

      $header.addClass('is-fixed');
    } else {

      $header.removeClass('is-fixed');
    }
  });
});

$(function () {

  floating();
});

// floating

function floating() {

  $(".floating").each(function () {

    var $floating = $(this),
        width = $floating.width(),
        offsetLeft = $floating.offset().left,
        offsetTop = $floating.offset().top;

    $floating.data("offsetLeft", offsetLeft).data("offsetTop", offsetTop).css({

      width: width

    });
  });

  if ($(window).width() < 992) {

    return;
  }

  $(window).on("scroll resize", function () {

    const $container = $('.floating-container');

    if (!$container.length) return false;

    const paddingLeft = Number($container.css('padding-left').replace(/\D/g, '') || 15);

    const offsetLeft = $container.offset().left + paddingLeft;

    const offsetTop = $container.offset().top;

    $(".floating").each(function () {

      var $floating = $(this),
          height = $floating.outerHeight(),
          outerHeight = $floating.outerHeight(true),
          $container = $floating.closest(".floating-container"),
          dataTop = $floating.data("top"),
          top = dataTop !== undefined ? parseInt(dataTop) : 70,
          containerHeight = $container.outerHeight(),
          containerOffsetTop = $container.offset().top,
          scrollTop = $(window).scrollTop();

      if (outerHeight + offsetTop == containerHeight + containerOffsetTop) {

        return;
      } else if (scrollTop + top <= offsetTop) {

        $(this).css({

          position: "static"

        });
      } else if (scrollTop + height + top > containerHeight + containerOffsetTop) {

        $(this).css({

          position: "absolute",

          zIndex: 2,

          top: "auto",

          bottom: 0,

          left: paddingLeft

        });
      } else {

        $(this).css({

          position: "fixed",

          zIndex: 2,

          top: top,

          left: offsetLeft,

          bottom: "auto"

        });
      }
    });
  });
}

$(function () {

  $('[data-popup-url]').fancybox({

    afterShow: function (instance) {

      const url = instance['$trigger'].data('popup-url');

      if (url) {

        instance.current.$image.wrap(`<a href="${url}" target="_blank"></a>`);
      }

      console.log('trigger', instance, instance['$trigger']);
    }

  });
});

$(function () {

  $(window).on('resize', function () {

    if ($(window).width() >= 1200) {

      $('html, body').removeClass('overflow-hidden');
    }
  });
});
