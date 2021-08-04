(function ($) {
    "use strict";

    new Swiper('.features-swiper-container', {
        slidesPerView: 1,
        spaceBetween: 0,
        loop: false,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.features-swiper-pagination',
            clickable: true,

        },
    });

    new Swiper('.latest-webinars-swiper', {
        slidesPerView: 1,
        spaceBetween: 16,
        loop: false,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.latest-webinars-swiper-pagination',
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

    new Swiper('.best-sales-webinars-swiper', {
        slidesPerView: 1,
        spaceBetween: 16,
        loop: false,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.best-sales-webinars-swiper-pagination',
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

    new Swiper('.best-rates-webinars-swiper', {
        slidesPerView: 1,
        spaceBetween: 16,
        loop: false,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.best-rates-webinars-swiper-pagination',
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

    new Swiper('.has-discount-webinars-swiper', {
        slidesPerView: 1,
        spaceBetween: 16,
        loop: false,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.has-discount-webinars-swiper-pagination',
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

    new Swiper('.free-webinars-swiper', {
        slidesPerView: 1,
        spaceBetween: 16,
        loop: false,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.free-webinars-swiper-pagination',
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

    new Swiper('.testimonials-swiper', {
        slidesPerView: 1,
        spaceBetween: 16,
        loop: false,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.testimonials-swiper-pagination',
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

    new Swiper('.subscribes-swiper', {
        slidesPerView: 1,
        spaceBetween: 16,
        loop: false,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.subscribes-swiper-pagination',
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

    new Swiper('.organization-swiper-container', {
        slidesPerView: 1,
        spaceBetween: 16,
        loop: false,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.organization-swiper-pagination',
            clickable: true,
        },
        breakpoints: {
            991: {
                slidesPerView: 4,
            },

            660: {
                slidesPerView: 2,
            },
        }
    });

    $('.instructors-swiper-container').owlCarousel({
        loop: true,
        center: true,
        items: 3,
        margin: 0,
        autoplay: true,
        dots: true,
        autoplayTimeout: 5000,
        smartSpeed: 450,
        responsive: {
            0: {
                items: 1
            },
            768: {
                items: 2
            },
            1170: {
                items: 4
            }
        }
    });

    $(document).ready(function () {
        for (var i = 1; i <= 6; i++) {
            new Parallax(document.getElementById('parallax' + i), {
                relativeInput: true
            });
        }
    });
})(jQuery);
