(function ($) {
    "use strict";

    $('body').on('click', '.request-payout', function (e) {
        e.preventDefault();

        Swal.fire({
            html: $('#requestPayoutModal').html(),
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            width: '40rem',
        });
    });

    $('body').on('click', '.js-submit-payout', function (e) {
        e.preventDefault();

        $(this).addClass('loadingbar primary').prop('disabled', true);

        $(this).closest('form').trigger('submit');
    });

})(jQuery);
