$(function ($) {
    "use strict";

    function handleShowDay(unix) {
        var day = new persianDate(unix).day();
        var startDayTime = new persianDate(unix).startOf('day').unix();
        var endDayTime = new persianDate(unix).endOf('day').unix();

        var showThisDay = false;

        // availableDays is defined globally in blade

        for (var index2 in availableDays) {
            var disabled_day = Number(availableDays[index2]);
            if (disabled_day === day) {
                showThisDay = true;
            }
        }

        return showThisDay;
    }

    $(".inline-reservation-calender").pDatepicker({
        inline: true,
        altField: '#inlineCalender',
        initialValue: true,
        calendarType: 'gregorian',
        initialValueType: true,
        autoClose: true,
        altFormat: 'DD MMMM YY',
        calendar: {
            gregorian: {
                locale: 'en'
            }
        },
        toolbox: {
            calendarSwitch: {
                enabled: false
            }
        },
        navigator: {
            scroll: {
                enabled: false
            },
            text: {
                btnNextText: '<',
                btnPrevText: ">"
            }
        },
        minDate: new persianDate().subtract('day', 0).valueOf(),
        checkDate: function (unix) {
            return handleShowDay(unix);

        },
        timePicker: {
            enabled: false,
        },
        onSelect: function (unix) {
            const timeStamp = new persianDate(unix).startOf('day').unix();
            $('#selectedDay').val(timeStamp);
            handleShowReservationTimes(timeStamp);
        }
    });

    function handleShowReservationTimes(timestamp) {
        const container = $('#PickTimeContainer');
        const body = $('#PickTimeBody');
        const availableTimes = $('#availableTimes');
        const loading = container.find('.loading-img');
        const user_id = container.attr('data-user-id');

        $('html, body').animate({
            scrollTop: (container.offset().top - (window.innerHeight / 2))
        }, 600);

        body.addClass('d-none');
        loading.removeClass('d-none');
        body.find('.selected_date span').text($('#inlineCalender').val());

        $.post('/users/' + user_id + '/availableTimes', {timestamp: timestamp}, function (result) {
            var html = '';
            if (result && typeof result.times !== "undefined") {
                Object.keys(result.times).forEach(key => {
                    const item = result.times[key];
                    html += '<div class="position-relative available-times ' + (item.can_reserve ? '' : 'disabled') + '">\n' +
                        '<input type="checkbox" name="time[]" id="availableTime' + item.id + '" value="' + item.id + '" ' + (item.can_reserve ? '' : 'disabled') + '>\n' +
                        '<label for="availableTime' + item.id + '">' + item.time + '</label>\n';
                    if (!item.can_reserve) {
                        html += '<span class="font-12 badge badge-danger text-white reserved-item">' + reservedLang + '</span>';
                    }
                    html += '</div>'
                });

                availableTimes.html(html);
            }
        }).always(() => {
            body.removeClass('d-none');
            loading.addClass('d-none');
        })
    }

    $('body').on('click', '#followToggle', function (e) {
        e.preventDefault();

        const $this = $(this);
        $this.addClass('loadingbar primary').prop('disabled', true);
        const user_id = $this.attr('data-user-id');

        const path = '/users/' + user_id + '/follow';

        $.get(path, function (result) {
            $this.removeClass('loadingbar primary').prop('disabled', false);
            if (result && result.code === 200) {
                if (result.follow) {
                    $this.removeClass('btn-primary').addClass('btn-danger');
                    $this.text(unFollowLang);
                } else {
                    $this.removeClass('btn-danger').addClass('btn-primary');
                    $this.text(followLang);
                }
            }
        })
    });

    $('body').on('click', '.js-refresh-captcha', function (e) {
        e.preventDefault();

        refreshCaptcha();
    });

    $('body').on('click', '.js-send-message', function (e) {
        e.preventDefault();

        Swal.fire({
            html: $('#sendMessageModal').html(),
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            onOpen: () => {
                refreshCaptcha();
            },
            width: '42rem',
        });
    });

    $('body').on('click', '.js-send-message-submit', function (e) {
        e.preventDefault();

        const $this = $(this);
        $this.addClass('loadingbar primary').prop('disabled', true);

        const $form = $this.closest('form');
        const action = $form.attr('action');

        const data = $form.serializeObject();

        $.post(action, data, function (result) {
            if (result && result.code === 200) {
                Swal.fire({
                    icon: 'success',
                    html: '<h3 class="font-20 text-center text-dark-blue">' + messageSuccessSentLang + '</h3>',
                    showConfirmButton: false,
                });

                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                Swal.fire({
                    icon: 'error',
                    html: '<h3 class="font-20 text-center text-dark-blue">' + result.message + '</h3>',
                    showConfirmButton: false,
                });
            }
        }).fail(err => {
            $this.removeClass('loadingbar primary').prop('disabled', false);
            var errors = err.responseJSON;

            refreshCaptcha();

            if (errors && errors.errors) {
                Object.keys(errors.errors).forEach((key) => {
                    const error = errors.errors[key];
                    let element = $form.find('[name="' + key + '"]');
                    element.addClass('is-invalid');
                    element.parent().find('.invalid-feedback').text(error[0]);
                });
            }
        })
    });
});
