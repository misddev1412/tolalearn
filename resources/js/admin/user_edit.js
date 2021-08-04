(function ($) {
"use strict";

    $('body').on('change', '#banSwitch', function () {
        if (this.checked) {
            $('#banSection').removeClass('d-none');
        } else {
            $('#banSection').addClass('d-none');
        }
    });
})(jQuery);
