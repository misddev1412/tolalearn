(function ($) {
    "use strict";

    new Swiper('.swiper-container', {
        slidesPerView: 1,
        spaceBetween: 16,
        loop: false,
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        breakpoints: {
            991: {
                slidesPerView: 3,
            },

            660: {
                slidesPerView: 2,
            },
        }
    });

    $('body').on('change', '#topFilters input,#topFilters select', function (e) {
        e.preventDefault();
        $('#filtersForm').trigger('submit');
    });
})(jQuery);
