(function ($) {
    "use strict";

    $('body').on('click', '.sidebarNavToggle', function (e) {
        e.preventDefault();
        var sidebar = $('#panelSidebar');

        if (sidebar.hasClass('nav-show')) {
            sidebar.removeClass('nav-show');
        } else {
            sidebar.addClass('nav-show');
        }
    });

    // **************************
    // file manager conf

    $('body').on('click', '.panel-file-manager', function (e) {
        e.preventDefault();
        $(this).filemanager('file', {prefix: '/laravel-filemanager'})
    });

    // ********************************************
    // ********************************************
    // date & time piker
    window.resetDatePickers = () => {
        if (jQuery().daterangepicker) {
            $('.date-range-picker').daterangepicker({
                locale: {format: 'YYYY-MM-DD'},
                drops: 'down',
                opens: 'right'
            });

            $('.datetimepicker').daterangepicker({
                locale: {format: 'YYYY-MM-DD HH:mm'},
                singleDatePicker: true,
                timePicker: true,
                timePicker24Hour: true,
            });

            $('.datepicker').daterangepicker({
                locale: {format: 'YYYY-MM-DD'},
                singleDatePicker: true,
                timePicker: false,
            });
        }
    };
    resetDatePickers();

    var datefilter = $('.datefilter');
    if (datefilter.length) {
        datefilter.daterangepicker({
            singleDatePicker: true,
            timePicker: false,
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear'
            }
        });
    }

    datefilter.on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD'));
    });

    datefilter.on('cancel.daterangepicker', function (ev, picker) {
        $(this).val('');
    });

    // Timepicker
    if (jQuery().timepicker) {
        $(".setTimepicker").each(function (key, item) {
            $(item).timepicker({
                icons: {
                    up: 'chevron-up-icon',
                    down: 'chevron-down-icon'
                },
                showMeridian: false,
            });
        })
    }

    // ********************************************
    // ********************************************
    // inputtags
    if (jQuery().tagsinput) {
        var input_tags = $('.inputtags');
        input_tags.tagsinput({
            tagClass: 'badge badge-primary py-5',
            maxTags: (input_tags.data('max-tag') ? input_tags.data('max-tag') : 10),
        });
    }

    window.panelSearchWebinarSelect2 = () => {
        $('.panel-search-webinar-select2').select2({
            minimumInputLength: 3,
            ajax: {
                url: '/panel/webinars/search',
                dataType: 'json',
                type: "POST",
                quietMillis: 50,
                data: function (params) {
                    var queryParameters = {
                        term: params.term,
                        webinar_id: $(this).data('webinar-id'),
                        option: $(this).data('option')
                    }
                    return queryParameters;
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.title,
                                id: item.id
                            }
                        })
                    };
                }
            }
        });
    };
    if ($('.panel-search-webinar-select2').length) {
        panelSearchWebinarSelect2();
    }

    window.panelSearchUserSelect2 = () => {
        $('.panel-search-user-select2').select2({
            placeholder: $(this).data('placeholder'),
            minimumInputLength: 3,
            allowClear: true,
            ajax: {
                url: '/panel/users/search',
                dataType: 'json',
                type: "POST",
                quietMillis: 50,
                data: function (params) {
                    return {
                        term: params.term,
                        option: $('.panel-search-user-select2').attr('data-search-option'),
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.full_name,
                                id: item.id
                            }
                        })
                    };
                }
            }
        });
    };
    if ($('.panel-search-user-select2').length) {
        panelSearchUserSelect2();
    }

    $('body').on('click', '.js-copy', function (e) {
        e.preventDefault();

        const $this = $(this);
        const inputName = $this.attr('data-input');
        const copyText = $this.attr('data-copy-text');
        const doneText = $this.attr('data-done-text');

        const input = $this.closest('.form-group').find('input[name="' + inputName + '"]');

        input.removeAttr('disabled');
        input.focus();
        input.select();
        document.execCommand("copy");

        $this.attr('data-original-title', doneText)
            .tooltip('show');
        $this.attr('data-original-title', copyText);
    });

    $('body').on('change','.js-panel-list-switch-filter',function (e) {
        $(this).closest('form').trigger('submit');
    });

})(jQuery);
