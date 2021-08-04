(function ($) {
    "use strict";

    $('body').on('click', '.js-finish-meeting-reserve', function (e) {
        e.preventDefault();

        const reserve_id = $(this).attr('data-id');
        const action = '/panel/meetings/' + reserve_id + '/finish';

        var html = '<div class="">\n' +
            '    <p class="">' + finishReserveHint + '</p>\n' +
            '    <div class="mt-30 d-flex align-items-center justify-content-center">\n' +
            '        <button type="button" id="finishReserve" data-href="' + action + '" class="btn btn-sm btn-primary">' + finishReserveConfirm + '</button>\n' +
            '        <button type="button" class="btn btn-sm btn-danger ml-10 close-swl">' + finishReserveCancel + '</button>\n' +
            '    </div>\n' +
            '</div>';

        Swal.fire({
            title: finishReserveTitle,
            html: html,
            icon: 'warning',
            showConfirmButton: false,
            showCancelButton: false,
            allowOutsideClick: () => !Swal.isLoading(),
        });
    });

    $('body').on('click', '#finishReserve', function (e) {
        e.preventDefault();
        var $this = $(this);
        const href = $this.attr('data-href');

        $this.addClass('loadingbar primary').prop('disabled', true);

        $.get(href, function (result) {
            if (result && result.code === 200) {
                Swal.fire({
                    title: finishReserveSuccess,
                    text: finishReserveSuccessHint,
                    showConfirmButton: false,
                    icon: 'success',
                });
                setTimeout(() => {

                    if (typeof result.redirect_to !== "undefined" && result.redirect_to !== undefined && result.redirect_to !== null && result.redirect_to !== '') {
                        window.location.href = result.redirect_to;
                    } else {
                        window.location.reload();
                    }
                }, 1000);
            } else {
                Swal.fire({
                    title: finishReserveFail,
                    text: finishReserveFailHint,
                    icon: 'error',
                })
            }
        }).error(err => {
            Swal.fire({
                title: finishReserveFail,
                text: finishReserveFailHint,
                icon: 'error',
            })
        }).always(() => {
            $this.removeClass('loadingbar primary').prop('disabled', false);
        });
    })
})(jQuery);
