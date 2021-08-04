(function ($) {
    "use strict";

    $('#localItems').flagStrap({
        inputName: "country",
        buttonSize: "btn-sm",
        buttonType: "btn-default",
        scrollable: true,
        labelMargin: "5px",
        scrollableHeight: "350px",
    });

    $('.language-select').find("select").change(function () {
        var $form = $(this).closest('form');
        var val = $(this).val();

        if (val && val !== '') {
            $form.find('input[name="locale"]').val(val);
            $form.trigger('submit');
        }
    });
})(jQuery);
