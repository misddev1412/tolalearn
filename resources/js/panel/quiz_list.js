(function ($) {
    "use strict";

    $('body').on('change', '#activeQuizzesSwitch', function (e) {
        e.preventDefault();

        $(this).closest('form').trigger('submit');
    });

    $('body').on('change', '#onlyOpenQuizzesSwitch', function () {
        $(this).closest('form').trigger('submit');
    })

})(jQuery);
