(function ($) {
    "use strict";

    /* dropdown */
    // **
    // **
    $('.dropdown-toggle').dropdown();

    /**
     * close swl
     * */
    $('body').on('click', '.close-swl', function (e) {
        e.preventDefault();
        Swal.close();
    });

    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });

    // ********************************************
    // ********************************************
    // select 2
    window.resetSelect2 = () => {
        if (jQuery().select2) {
            $(".select2").select2({
                width: '100%',
            });
        }
    };
    resetSelect2();

    /*
    * loading Swl
    * */
    window.loadingSwl = () => {
        const loadingHtml = '<div class="d-flex align-items-center justify-content-center my-50 "><img src="/assets/default/img/loading.gif" width="80" height="80"></div>';
        Swal.fire({
            html: loadingHtml,
            showCancelButton: false,
            showConfirmButton: false,
            width: '30rem',
        });
    };

    //
    // delete sweet alert
    $('body').on('click', '.delete-action', function (e) {
        e.preventDefault();
        e.stopPropagation();
        const href = $(this).attr('href');

        var html = '<div class="">\n' +
            '    <p class="">' + deleteAlertHint + '</p>\n' +
            '    <div class="mt-30 d-flex align-items-center justify-content-center">\n' +
            '        <button type="button" id="swlDelete" data-href="' + href + '" class="btn btn-sm btn-primary">' + deleteAlertConfirm + '</button>\n' +
            '        <button type="button" class="btn btn-sm btn-danger ml-10 close-swl">' + deleteAlertCancel + '</button>\n' +
            '    </div>\n' +
            '</div>';

        Swal.fire({
            title: deleteAlertTitle,
            html: html,
            icon: 'warning',
            showConfirmButton: false,
            showCancelButton: false,
            allowOutsideClick: () => !Swal.isLoading(),
        })
    });

    $('body').on('click', '#swlDelete', function (e) {
        e.preventDefault();
        var $this = $(this);
        const href = $this.attr('data-href');

        $this.addClass('loadingbar primary').prop('disabled', true);

        $.get(href, function (result) {
            if (result && result.code === 200) {
                Swal.fire({
                    title: deleteAlertSuccess,
                    text: deleteAlertSuccessHint,
                    showConfirmButton: false,
                    icon: 'success',
                });
                setTimeout(() => {

                    if (typeof result.redirect_to !== "undefined" && result.redirect_to !== undefined && result.redirect_to !== null && result.redirect_to !== '') {
                        window.location.href = result.redirect_to;
                    } else {
                        window.location.reload();
                    }
                }, 1000)
            } else {
                Swal.fire({
                    title: deleteAlertFail,
                    text: deleteAlertFailHint,
                    icon: 'error',
                })
            }
        }).error(err => {
            Swal.fire({
                title: deleteAlertFail,
                text: deleteAlertFailHint,
                icon: 'error',
            })
        }).always(() => {
            $this.removeClass('loadingbar primary').prop('disabled', false);
        });
    })

    // ********************************************
    // ********************************************
    // form serialize to Object
    $.fn.serializeObject = function () {
        var o = {};
        var a = this.serializeArray();
        $.each(a, function () {
            if (o[this.name]) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };

    window.serializeObjectByTag = (tagId) => {
        var o = {};
        var a = tagId.find('input, textarea, select').serializeArray();
        $.each(a, function () {
            if (o[this.name]) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };

    $('.accordion-row').on('shown.bs.collapse', function () {
        $(this).find('.collapse-chevron-icon').toggleClass('feather-chevron-down feather-chevron-up');
    });
    $('.accordion-row').on('hidden.bs.collapse', function () {
        $(this).find('.collapse-chevron-icon').toggleClass('feather-chevron-down feather-chevron-up');
    });

    $('body').on('change', '#userLanguages', function (e) {
        $(this).closest('form').trigger('submit');
    });
    /* feather icons */
    // **
    // **
    feather.replace();

})(jQuery);

