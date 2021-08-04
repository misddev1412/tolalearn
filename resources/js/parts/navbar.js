(function ($) {
    "use strict";

    $('body').on('click', '.xs-categories-toggle', function (e) {
        if (e.target !== this) return;

        if ($(this).hasClass('show-items')) {
            $(this).removeClass('show-items');
        } else {
            $(this).addClass('show-items');
        }
    });

    $('body').on('click', this, function (event) {
        if (!$(event.target).closest('.menu-category').length) {
            $(".xs-categories-toggle").removeClass('show-items');
        }
    });

    $('body').on('click', '.cat-dropdown-menu > li', function (e) {
        const $this = $(this);

        const hasOpen = $this.hasClass('show-sub-menu');

        $('.cat-dropdown-menu > li').each((key, item) => {
            $(item).removeClass('show-sub-menu')
        });

        if (hasOpen) {
            $this.removeClass('show-sub-menu');
        } else {
            $this.addClass('show-sub-menu');
        }
    });

    $('body').on('click', '#navbarClose', function (e) {
        $('#navbarContent').removeClass('show');
    });

    $('body').on('click', '#navbarToggle', function (e) {
        $('#navbarContent').addClass('show');
    });

    var navbar = document.getElementById("navbar");
    var navbarVacuum = document.getElementById("navbarVacuum");
    var sticky = navbar.offsetTop;

    function myFunction() {
        if (window.pageYOffset >= sticky) {
            navbarVacuum.style.height = navbar.offsetHeight + 'px';
            navbar.classList.add("sticky")
        } else {
            navbarVacuum.style.height = 0;
            navbar.classList.remove("sticky");
        }
    }

    window.onscroll = function () {
        myFunction()
    };

})(jQuery);
