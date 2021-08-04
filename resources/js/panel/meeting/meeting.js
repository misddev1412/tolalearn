(function ($) {
    "use strict";

    function substringText($element, am_pm) {
        let val = $element.val();

        const time = val.substring(0, val.length - 2);

        $element.val(time + am_pm);

        return time;
    }

    // toTimepicker and fromTimepicker are defined globally in blade

    function handleToTime() {
        toTimepicker = $('.to-clockpicker').clockpicker({
            placement: 'bottom',
            align: 'left',
            'default': '10:00AM',
            autoclose: true,
            twelvehour: true,
            afterDone: () => {
                handleFromTome();
                fromTimepicker.clockpicker('show');
                toTimepicker.clockpicker('remove');
                const to_time = $('.to-clockpicker input');
                const am_pm = $('.to-time .js-am-pm').text();

                $('.to-time').removeClass('pulsate').html(substringText(to_time, am_pm) + ' <span class="js-am-pm font-20">' + am_pm + '</span>');

                $('#timeTwelveSwitch').prop('disabled', true);
            },
        });
    }

    handleToTime();

    function handleFromTome() {
        fromTimepicker = $('.from-clockpicker').clockpicker({
            placement: 'bottom',
            align: 'left',
            autoclose: true,
            'default': '09:00AM',
            twelvehour: true,
            afterDone: () => {
                handleToTime();
                fromTimepicker.clockpicker('remove');
                toTimepicker.clockpicker('show');
                const from_time = $('.from-clockpicker input');
                const am_pm = $('.from-time .js-am-pm').text();

                $('.from-time').removeClass('pulsate').html(substringText(from_time, am_pm) + ' <span class="js-am-pm font-20">' + am_pm + '</span>');
                $('.to-time').addClass('pulsate');
            },
        });
        fromTimepicker.clockpicker('show');
    }


    $('body').on('change', '#timeTwelveSwitch', function (e) {
        e.preventDefault();
        let type = 'AM';
        let replace = 'PM';

        if (this.checked) {
            type = 'PM';
            replace = 'AM';
        }

        const $fromText = $('.from-time.pulsate').find('.js-am-pm');
        const $toText = $('.to-time.pulsate').find('.js-am-pm');

        if ($fromText.length) {
            $fromText.text(type);
            const $from = $('.from-clockpicker input');
            let from_time = $from.val();
            from_time = from_time.replace(replace, type);
            $from.val(from_time);
        }

        if ($toText.length) {
            $toText.text(type);
            const $to = $('.to-clockpicker input');
            let to_time = $to.val();
            to_time = to_time.replace(replace, type);
            $to.val(to_time);
        }
    });

    $('body').on('click', '.add-time', function (e) {
        e.preventDefault();
        const day = $(this).closest('tr').attr('data-day');

        var add_time_html = '<div class="add-time-sheet row flex-column-reverse flex-lg-row align-items-center justify-content-center justify-content-lg-between">\n' +
            '        <div class="clock-box col-12 col-lg-4 d-block position-relative d-flex align-items-center justify-content-center justify-content-lg-start">\n' +
            '            <div class="from-clockpicker">\n' +
            '                <input type="hidden" class="form-control " value="AM">\n' +
            '            </div>\n' +
            '            <div class="to-clockpicker">\n' +
            '                <input type="hidden" class="form-control " value="AM">\n' +
            '            </div>\n' +
            '        </div>\n' +
            '   <div class="col-12 col-lg-8">' +
            '     <div class="row">' +
            '        <div class="col-12 col-lg-4 mb-20 mb-lg-0 d-flex align-items-center justify-content-center custom-control custom-switch on-off-switch pl-0 py-0 py-lg-50">\n' +
            '            <label style="margin-right: 60px">AM</label>\n' +
            '            <input type="checkbox" class="custom-control-input" id="timeTwelveSwitch">\n' +
            '            <label class="custom-control-label" for="timeTwelveSwitch">PM</label>\n' +
            '        </div>\n' +
            '\n' +
            '        <div class="col-12 col-lg-8 d-flex flex-column align-items-center justify-content-center py-0 py-lg-50">\n' +
            '            <div class="font-48 text-primary from-time pulsate">03:00 <span class="js-am-pm font-16">AM</span></div>\n' +
            '            <div class="font-weight-500 text-dark-blue">To</div>\n' +
            '            <div class="font-48 text-primary to-time">04:00 <span class="js-am-pm font-16">AM</span></div>\n' +
            '        </div>\n' +
            '    </div>' +
            '   </div>' +
            '  </div>' +
            '<div class="mt-30 d-flex align-items-center justify-content-end">\n' +
            '    <button type="button" data-day="' + day + '" id="saveTime" class="btn btn-sm btn-primary">' + saveLang + '</button>\n' +
            '    <button type="button" class="btn btn-sm btn-danger ml-10 close-swl">' + closeLang + '</button>\n' +
            '</div>';

        Swal.fire({
            html: add_time_html,
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            width: '48rem',
            onOpen: () => {
                setTimeout(() => {
                    handleFromTome();
                }, 300)
            },
            onClose: () => {
                fromTimepicker.clockpicker('remove');
                toTimepicker.clockpicker('remove');
            }
        });
    });

    $('body').on('click', '#saveTime', function (e) {
        e.preventDefault();
        const $this = $(this);
        const from_time = $('.from-clockpicker input').val();
        const to_time = $('.to-clockpicker input').val();
        const day = $this.attr('data-day');

        $this.addClass('loadingbar primary').prop('disabled', true);

        const data = {
            day: day,
            time: from_time + '-' + to_time,
        };

        $.post('/panel/meetings/saveTime', data, function (result) {
            if (result && result.code == 200) {
                Swal.fire({
                    title: deleteAlertSuccess,
                    text: successSavedTime,
                    showConfirmButton: false,
                    icon: 'success',
                });
                setTimeout(() => {
                    window.location.reload();
                }, 1000)
            }
        }).fail(() => {
            Swal.fire({
                title: errorSavingTime,
                text: noteToTimeMustGreater,
                icon: 'error',
            });

            $this.removeClass('loadingbar primary').prop('disabled', false);
        }).always(() => {
            fromTimepicker.clockpicker('remove');
            toTimepicker.clockpicker('remove');
        });
    });


    function deleteTimeModal(time_id) {
        var html = '<div class="">\n' +
            '    <p class="">' + deleteAlertHint + '</p>\n' +
            '    <div class="mt-30 d-flex align-items-center justify-content-center">\n' +
            '        <button type="button" id="deleteTime" data-time-id="' + time_id + '" class="btn btn-sm btn-primary">' + deleteAlertConfirm + '</button>\n' +
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
        });
    }

    $('body').on('click', '.remove-time', function (e) {
        e.preventDefault();
        const $this = $(this);
        const time_id = $this.attr('data-time-id');

        deleteTimeModal(time_id);
    });

    $('body').on('click', '#deleteTime', function (e) {
        e.preventDefault();
        const $this = $(this);

        let time_id = $this.attr('data-time-id');
        time_id = time_id.split(',');

        handleRemoveTime(time_id);

        Swal.close();

        for (let id of time_id) {
            $('.remove-time[data-time-id="' + id + '"]').parent().remove();
        }
    });

    function handleRemoveTime(time_id) {
        const data = {
            time_id: time_id,
        };

        $.post('/panel/meetings/deleteTime', data, function (result) {
            $.toast({
                heading: deleteAlertSuccess,
                text: successDeleteTime,
                bgColor: '#43d477',
                textColor: 'white',
                hideAfter: 5000,
                position: 'bottom-right',
                icon: 'success'
            });
        }).fail(() => {
            $.toast({
                heading: deleteAlertFail,
                text: errorDeleteTime,
                bgColor: '#f63c3c',
                textColor: 'white',
                hideAfter: 5000,
                position: 'bottom-right',
                icon: 'error'
            });
        });
    }

    $('body').on('click', '.clear-all', function (e) {
        e.preventDefault();
        const parent = $(this).closest('tr');

        const timeIds = parent.find('.selected-time .remove-time').map(function () {
            return this.dataset.timeId;
        }).get();

        deleteTimeModal(timeIds.join(','));
    });

    $('body').on('change', '#temporaryDisableMeetingsSwitch', function (e) {
        e.preventDefault();
        const $this = $(this);

        loadingSwl();

        let disable = false;
        if (this.checked) {
            disable = true;
        }

        const data = {
            disable: disable
        };

        $.post('/panel/meetings/temporaryDisableMeetings', data, function (result) {
            if (result && result.code == 200) {
                Swal.fire({
                    text: requestSuccess,
                    showConfirmButton: false,
                    icon: 'success',
                });

                setTimeout(() => {
                    Swal.close();
                }, 2000)
            }
        }).fail(() => {
            Swal.fire({
                text: requestFailed,
                icon: 'error',
            });

            $this.removeClass('loadingbar primary').prop('disabled', false);
        })

    });

    $('body').on('click', '#meetingSettingFormSubmit', function (e) {
        e.preventDefault();

        const $this = $(this);
        const $form = $this.closest('form');
        const action = $form.attr('action');
        let data = serializeObjectByTag($form);

        $this.addClass('loadingbar primary').prop('disabled', true);

        $.post(action, data, function (result) {
            if (result && result.code === 200) {
                Swal.fire({
                    icon: 'success',
                    html: '<h3 class="font-20 text-center text-dark-blue py-25">' + saveMeetingSuccessLang + '</h3>',
                    showConfirmButton: false,
                    width: '25rem',
                });

                setTimeout(() => {
                    window.location.reload();
                }, 500)
            }
        }).fail(err => {
            $this.removeClass('loadingbar primary').prop('disabled', false);

            var errors = err.responseJSON;
            if (errors && errors.errors) {
                Object.keys(errors.errors).forEach((key) => {
                    const error = errors.errors[key];
                    let element = $form.find('[name="' + key + '"]');
                    element.addClass('is-invalid');
                    element.parent().find('.invalid-feedback').text(error[0]);
                });
            }
        })
    })
})(jQuery);
