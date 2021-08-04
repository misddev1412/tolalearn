/**
 *
 * You can write your JS code here, DO NOT touch the default style file
 * because it will make it harder for you to update.
 *
 */
(function ($) {
    "use strict";

    var datefilter = $('.datefilter');
    datefilter.daterangepicker({
        singleDatePicker: true,
        timePicker: false,
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear'
        }
    });

    datefilter.on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD'));
    });

    datefilter.on('cancel.daterangepicker', function (ev, picker) {
        $(this).val('');
    });


    $('body').on('click', '.admin-file-manager', function (e) {
        e.preventDefault();
        $(this).filemanager('file', {prefix: '/laravel-filemanager'})
    });

    $('body').on('click', '.admin-file-view', function (e) {
        e.preventDefault();
        var input = $(this).attr('data-input');

        var img_src = $('#' + input).val();

        $('#fileViewModal').find('img').attr('src', img_src);
        $('#fileViewModal').modal('show');
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
                locale: {format: 'YYYY-MM-DD H:mm'},
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

// Timepicker
    if (jQuery().timepicker) {
        $(".setTimepicker").each(function (key, item) {
            $(item).timepicker({
                icons: {
                    up: 'fa fa-chevron-up',
                    down: 'fa fa-chevron-down'
                },
                showMeridian: false,
            });
        })
    }

// ********************************************
// ********************************************
// select 2
    window.resetSelect2 = () => {
        if (jQuery().select2) {
            $(".select2").select2({
                placeholder: $(this).data('placeholder'),
                allowClear: true,
                width: '100%',
            });
        }
    };
    resetSelect2();
// ********************************************
// ********************************************
// inputtags
    if (jQuery().tagsinput) {
        var input_tags = $('.inputtags');
        input_tags.tagsinput({
            tagClass: 'badge badge-primary',
            maxTags: (input_tags.data('max-tag') ? input_tags.data('max-tag') : 10),
        });
    }

    $(document).ready(function () {

        $('.search-user-select2').select2({
            placeholder: $(this).data('placeholder'),
            minimumInputLength: 3,
            allowClear: true,
            ajax: {
                url: '/admin/users/search',
                dataType: 'json',
                type: "POST",
                quietMillis: 50,
                data: function (params) {
                    return {
                        term: params.term,
                        option: $(this).attr('data-search-option'),
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.name,
                                id: item.id
                            }
                        })
                    };
                }
            }
        });

        $('.search-webinar-select2').select2({
            placeholder: $(this).data('placeholder'),
            minimumInputLength: 3,
            allowClear: true,
            ajax: {
                url: '/admin/webinars/search',
                dataType: 'json',
                type: "POST",
                quietMillis: 50,
                data: function (params) {
                    return {
                        term: params.term,
                    };
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

        $('.search-blog-select2').select2({
            placeholder: $(this).data('placeholder'),
            minimumInputLength: 3,
            allowClear: true,
            ajax: {
                url: '/admin/blog/search',
                dataType: 'json',
                type: "POST",
                quietMillis: 50,
                data: function (params) {
                    return {
                        term: params.term,
                    };
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

        var datefilter = $('.datefilter');
        datefilter.daterangepicker({
            singleDatePicker: true,
            timePicker: false,
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear'
            }
        });

        datefilter.on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD'));
        });

        datefilter.on('cancel.daterangepicker', function (ev, picker) {
            $(this).val('');
        });

    });

    var lfm = function(options, cb) {
        var route_prefix = (options && options.prefix) ? options.prefix : '/laravel-filemanager';
        window.open(route_prefix + '?type=' + options.type || 'file', 'FileManager', 'width=900,height=600');
        window.SetUrl = cb;
    };

    // Define LFM summernote button
    var LFMButton = function(context) {
        var ui = $.summernote.ui;
        var button = ui.button({
            contents: '<i class="note-icon-picture"></i> ',
            tooltip: 'Insert image with filemanager',
            click: function() {

                lfm({type: 'file', prefix: '/laravel-filemanager'}, function(lfmItems, path) {
                    lfmItems.forEach(function (lfmItem) {
                        context.invoke('insertImage', lfmItem.url);
                    });
                });

            }
        });
        return button.render();
    };

    if (jQuery().summernote) {
        $(".summernote").summernote({
            dialogsInBody: true,
            tabsize: 2,
            height: 250,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']],
                ['popovers', ['lfm']],
            ],
            buttons: {
                lfm: LFMButton
            }
        });
    }
})(jQuery);
