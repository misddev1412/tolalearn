(function ($) {
    "use strict";

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

    function randomString() {
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";

        for (var i = 0; i < 5; i++)
            text += possible.charAt(Math.floor(Math.random() * possible.length));

        return text;
    }

    /**
     * close swl
     * */
    $('body').on('click', '.close-swl', function (e) {
        e.preventDefault();
        Swal.close();
    });


    $('#summernote').summernote({
        tabsize: 2,
        height: 400,
        placeholder: $('#summernote').attr('placeholder'),
        dialogsInBody: true,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['color', ['color']],
            ['para', ['paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview']],
        ],
    });


    $('body').on('click', '#saveAndPublish', function (e) {
        e.preventDefault();
        $('#forDraft').val('publish');
        $('#webinarForm').trigger('submit');
    });

    $('body').on('click', '#saveAsDraft', function (e) {
        e.preventDefault();
        $('#forDraft').val(1);
        $('#webinarForm').trigger('submit');
    });

    $('body').on('click', '#saveReject', function (e) {
        e.preventDefault();
        $('#forDraft').val('reject');
        $('#webinarForm').trigger('submit');
    });

    $('#partnerInstructorSwitch').on('change.bootstrapSwitch', function (e) {
        let isChecked = e.target.checked;

        if (isChecked) {
            $('#partnerInstructorInput').removeClass('d-none');
            resetSelect2();
        } else {
            $('#partnerInstructorInput').addClass('d-none');
        }
    });

    $('body').on('change', '#categories', function (e) {
        e.preventDefault();
        let category_id = this.value;
        $.get('/admin/filters/get-by-category-id/' + category_id, function (result) {

            if (result && typeof result.filters !== "undefined" && result.filters.length) {
                let html = '';
                Object.keys(result.filters).forEach(key => {
                    let filter = result.filters[key];
                    let options = [];

                    if (filter.options.length) {
                        options = filter.options;
                    }

                    html += '<div class="col-12 col-md-3">\n' +
                        '<div class="webinar-category-filters">\n' +
                        '<strong class="category-filter-title d-block">' + filter.title + '</strong>\n' +
                        '<div class="py-10"></div>\n' +
                        '\n';

                    if (options.length) {
                        Object.keys(options).forEach(index => {
                            let option = options[index];

                            html += '<div class="form-group mt-20 d-flex align-items-center justify-content-between">\n' +
                                '<label class="" for="filterOption' + option.id + '">' + option.title + '</label>\n' +
                                '<div class="custom-control custom-checkbox">\n' +
                                '<input type="checkbox" name="filters[]" value="' + option.id + '" class="custom-control-input" id="filterOption' + option.id + '">\n' +
                                '<label class="custom-control-label" for="filterOption' + option.id + '"></label>\n' +
                                '</div>\n' +
                                '</div>\n';
                        })
                    }

                    html += '</div></div>';
                });

                $('#categoriesFiltersContainer').removeClass('d-none');
                $('#categoriesFiltersCard').html(html);
            } else {
                $('#categoriesFiltersContainer').addClass('d-none');
                $('#categoriesFiltersCard').html('');
            }
        })
    });

    /*
    *
    * */
    function handleWebinarItemForm(form, $this) {
        let data = form.serializeObject();
        let action = form.attr('action');

        $this.addClass('loadingbar gray').prop('disabled', true);
        form.find('input').removeClass('is-invalid');
        form.find('textarea').removeClass('is-invalid');

        $.post(action, data, function (result) {
            if (result && result.code === 200) {
                Swal.fire({
                    icon: 'success',
                    html: '<h3 class="font-20 text-center text-dark-blue py-25">' + saveSuccessLang + '</h3>',
                    showConfirmButton: false,
                    width: '25rem',
                });

                setTimeout(() => {
                    window.location.reload();
                }, 500)
            }
        }).fail(err => {
            $this.removeClass('loadingbar gray').prop('disabled', false);
            var errors = err.responseJSON;

            if (errors && errors.status === 'zoom_jwt_token_invalid') {
                Swal.fire({
                    icon: 'error',
                    html: '<h3 class="font-20 text-center text-dark-blue py-25">' + zoomJwtTokenInvalid + '</h3>',
                    showConfirmButton: false,
                    width: '25rem',
                });
            }


            if (errors && errors.errors) {
                Object.keys(errors.errors).forEach((key) => {
                    const error = errors.errors[key];
                    let element = form.find('[name="' + key + '"]');

                    if (key === 'zoom-not-complete-alert') {
                        form.find('.js-zoom-not-complete-alert').removeClass('d-none');
                    } else {
                        element.addClass('is-invalid');
                        element.parent().find('.invalid-feedback').text(error[0]);
                    }
                });
            }
        })
    }

    /**
     * add ticket
     * */
    $('body').on('click', '#webinarAddTicket', function (e) {
        e.preventDefault();
        let add_ticket_modal = '<div id="addTicketModal">';
        add_ticket_modal += $('#webinarTicketModal').html();
        add_ticket_modal += '</div>';

        Swal.fire({
            html: add_ticket_modal,
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            width: '48rem',
        });

        resetDatePickers();
    });

    $('body').on('click', '#saveTicket', function (e) {
        e.preventDefault();
        const $this = $(this);
        let form = $('#addTicketModal form');
        handleWebinarItemForm(form, $this);
    });

    /**
     * add webinar sessions
     * */
    $('body').on('click', '#webinarAddSession', function (e) {
        e.preventDefault();
        const nameId = randomString();

        const modal = $('#webinarSessionModal');
        modal.find('input').prop('disabled', false);

        let add_session_modal = '<div id="addSessionModal">';
        add_session_modal += modal.html();
        add_session_modal += '</div>';
        add_session_modal = add_session_modal.replaceAll('record', nameId);


        Swal.fire({
            html: add_session_modal,
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            width: '48rem',
        });

        resetDatePickers();
    });

    $('body').on('change', '.js-api-input', function (e) {
        e.preventDefault();

        const sessionForm = $(this).closest('.session-form');
        const value = this.value;

        sessionForm.find('.js-zoom-not-complete-alert').addClass('d-none');

        if (value === 'big_blue_button') {
            sessionForm.find('.js-local-link').addClass('d-none');
            sessionForm.find('.js-api-secret').removeClass('d-none');
            sessionForm.find('.js-moderator-secret').removeClass('d-none');
        } else if (value === 'zoom') {
            sessionForm.find('.js-local-link').addClass('d-none');
            sessionForm.find('.js-api-secret').addClass('d-none');
            sessionForm.find('.js-moderator-secret').addClass('d-none');
        } else {
            sessionForm.find('.js-local-link').removeClass('d-none');
            sessionForm.find('.js-api-secret').removeClass('d-none');
            sessionForm.find('.js-moderator-secret').addClass('d-none');
        }
    })

    $('body').on('click', '#saveSession', function (e) {
        e.preventDefault();
        const $this = $(this);
        let form = $('#addSessionModal form');
        handleWebinarItemForm(form, $this);
    });

    /**
     * add webinar files
     * */
    $('body').on('click', '#webinarAddFile', function (e) {
        e.preventDefault();
        const nameId = randomString();

        let add_file_modal = '<div id="addFilesModal">';
        add_file_modal += $('#webinarFileModal').html();
        add_file_modal += '</div>';
        add_file_modal = add_file_modal.replaceAll('str_', '');
        add_file_modal = add_file_modal.replaceAll('record', nameId);

        Swal.fire({
            html: add_file_modal,
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            width: '48rem',
        });
    });

    $('body').on('click', '#saveFile', function (e) {
        e.preventDefault();
        const $this = $(this);
        let form = $('#addFilesModal form');
        handleWebinarItemForm(form, $this);
    });

    /**
     * add webinar prerequisites
     * */
    $('body').on('click', '#webinarAddPrerequisites', function (e) {
        e.preventDefault();
        let add_prerequisites_modal = '<div id="addPrerequisitesModal">';
        add_prerequisites_modal += $('#webinarPrerequisitesModal').html();
        add_prerequisites_modal += '</div>';
        add_prerequisites_modal = add_prerequisites_modal.replaceAll('prerequisites-select', 'prerequisites-select2');
        add_prerequisites_modal = add_prerequisites_modal.replaceAll('str_', '');

        Swal.fire({
            html: add_prerequisites_modal,
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            width: '48rem',
            onOpen: () => {
                $('.prerequisites-select2').select2({
                    placeholder: $(this).data('placeholder'),
                    minimumInputLength: 3,
                    allowClear: true,
                    ajax: {
                        url: '/admin/webinars/search',
                        dataType: 'json',
                        type: "POST",
                        quietMillis: 50,
                        data: function (params) {
                            var queryParameters = {
                                term: params.term,
                                webinar_id: $(this).data('webinar-id')
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
            },
        });
    });

    $('body').on('click', '#savePrerequisites', function (e) {
        e.preventDefault();
        const $this = $(this);
        let form = $('#addPrerequisitesModal form');
        handleWebinarItemForm(form, $this);
    });

    /**
     * add webinar FAQ
     * */
    $('body').on('click', '#webinarAddFAQ', function (e) {
        e.preventDefault();
        let add_faq_modal = '<div id="addFAQsModal">';
        add_faq_modal += $('#webinarFaqModal').html();
        add_faq_modal += '</div>';

        Swal.fire({
            html: add_faq_modal,
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            width: '48rem',
        });
    });

    $('body').on('click', '#saveFAQ', function (e) {
        e.preventDefault();
        const $this = $(this);
        let form = $('#addFAQsModal form');
        handleWebinarItemForm(form, $this);
    });


    /**
     * add webinar Quiz
     * */
    $('body').on('click', '#webinarAddQuiz', function (e) {
        e.preventDefault();
        let add_quiz_modal = '<div id="addQuizModal">';
        add_quiz_modal += $('#quizzesModal').html();
        add_quiz_modal += '</div>';
        add_quiz_modal = add_quiz_modal.replaceAll('quiz-select2', 'quiz-select22');

        Swal.fire({
            html: add_quiz_modal,
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            width: '30rem',
            onOpen: () => {
                $(".quiz-select22").select2({
                    placeholder: $(this).data('placeholder'),
                    allowClear: true,
                    width: '100%',
                });
            }
        });
    });

    $('body').on('click', '#saveQuiz', function (e) {
        e.preventDefault();
        const $this = $(this);
        let form = $('#addQuizModal form');
        handleWebinarItemForm(form, $this);
    });

    /*
    * edit ticket
    * */
    $('body').on('click', '.edit-ticket', function (e) {
        e.preventDefault();
        const $this = $(this);
        const ticket_id = $this.attr('data-ticket-id');
        const webinar_id = $this.attr('data-webinar-id');

        loadingSwl();

        const edit_data = {
            item_id: webinar_id
        };

        $.post('/admin/tickets/' + ticket_id + '/edit', edit_data, function (result) {
            if (result && result.ticket) {
                const ticket = result.ticket;

                let edit_ticket_modal = '<div id="addTicketModal">';
                edit_ticket_modal += $('#webinarTicketModal').html();
                edit_ticket_modal += '</div>';
                edit_ticket_modal = edit_ticket_modal.replaceAll('/admin/tickets/store', '/admin/tickets/' + ticket_id + '/update');

                Swal.fire({
                    html: edit_ticket_modal,
                    showCancelButton: false,
                    showConfirmButton: false,
                    customClass: {
                        content: 'p-0 text-left',
                    },
                    width: '48rem',
                    onOpen: () => {
                        $('.date-range-picker').daterangepicker({
                            locale: {format: 'YYYY-MM-DD'},
                            drops: 'down',
                            opens: 'right',
                            startDate: moment(ticket.start_date * 1000).toDate(),
                            endDate: moment(ticket.end_date * 1000).toDate(),
                        });
                        delete ticket.start_date;
                        delete ticket.end_date;

                        Object.keys(ticket).forEach(key => {
                            $('#addTicketModal').find('[name="' + key + '"]').val(ticket[key]);
                        })
                    }
                });
            }
        });
    });

    /*
    * edit session
    * */
    $('body').on('click', '.edit-session', function (e) {
        e.preventDefault();
        const $this = $(this);
        const session_id = $this.attr('data-session-id');
        const webinar_id = $this.attr('data-webinar-id');

        loadingSwl();

        const edit_data = {
            item_id: webinar_id
        };

        $.post('/admin/sessions/' + session_id + '/edit', edit_data, function (result) {
            if (result && result.session) {
                const session = result.session;

                let edit_session_modal = '<div id="addSessionModal">';
                edit_session_modal += $('#webinarSessionModal').html();
                edit_session_modal += '</div>';
                edit_session_modal = edit_session_modal.replaceAll('/admin/sessions/store', '/admin/sessions/' + session_id + '/update');
                const nameId = randomString();
                edit_session_modal = edit_session_modal.replaceAll('record', nameId);

                Swal.fire({
                    html: edit_session_modal,
                    showCancelButton: false,
                    showConfirmButton: false,
                    customClass: {
                        content: 'p-0 text-left',
                    },
                    width: '48rem',
                    onOpen: () => {
                        var $modal = $('#addSessionModal');
                        $('.datetimepicker').daterangepicker({
                            locale: {format: 'YYYY-MM-DD HH:mm'},
                            singleDatePicker: true,
                            timePicker: true,
                            timePicker24Hour: true,
                            startDate: moment(session.date * 1000).toDate(),
                        });
                        delete session.date;

                        Object.keys(session).forEach(key => {
                            if (key === 'session_api') {
                                var apiInput = $modal.find('.js-api-input[value="' + session[key] + '"]');
                                apiInput.prop('checked', true);

                                if (session[key] !== 'local') {
                                    $modal.find('.js-api-input').prop('disabled', true);
                                    $modal.find('.js-ajax-api_secret').prop('disabled', true);
                                    $modal.find('.js-ajax-date').prop('disabled', true);
                                    $modal.find('.js-ajax-duration').prop('disabled', true);
                                    $modal.find('.js-ajax-link').prop('disabled', true);
                                }

                                if (session[key] === 'big_blue_button') {
                                    $modal.find('.js-moderator-secret').removeClass('d-none');
                                    $modal.find('.js-ajax-moderator_secret').prop('disabled', true);
                                }

                                if (session[key] === 'zoom') {
                                    $modal.find('.js-local-link').addClass('d-none');
                                    $modal.find('.js-api-secret').addClass('d-none');
                                    $modal.find('.js-moderator-secret').addClass('d-none');
                                }

                            } else {
                                $modal.find('[name="' + key + '"]').val(session[key]);
                            }
                        });
                    }
                });
            }
        });
    });

    /*
    * edit files
    * */
    $('body').on('click', '.edit-file', function (e) {
        e.preventDefault();
        const $this = $(this);
        const file_id = $this.attr('data-file-id');
        const webinar_id = $this.attr('data-webinar-id');

        loadingSwl();

        const edit_data = {
            item_id: webinar_id
        };

        $.post('/admin/files/' + file_id + '/edit', edit_data, function (result) {
            if (result && result.file) {
                const file = result.file;

                let edit_file_modal = '<div id="addFilesModal">';
                edit_file_modal += $('#webinarFileModal').html();
                edit_file_modal += '</div>';
                edit_file_modal = edit_file_modal.replaceAll('/admin/files/store', '/admin/files/' + file_id + '/update');
                edit_file_modal = edit_file_modal.replaceAll('str_', '');

                Swal.fire({
                    html: edit_file_modal,
                    showCancelButton: false,
                    showConfirmButton: false,
                    customClass: {
                        content: 'p-0 text-left',
                    },
                    width: '48rem',
                    onOpen: () => {
                        var $modal = $('#addFilesModal');

                        Object.keys(file).forEach(key => {
                            if (key === 'storage') {
                                if (file[key] === 'online') {
                                    $modal.find('.local-input').addClass('d-none');
                                    $modal.find('.online-inputs').removeClass('d-none');
                                    $modal.find('.js-downloadable-file').addClass('d-none');
                                    $modal.find('.js-downloadable-file input').prop('checked', false);
                                } else {
                                    $modal.find('.local-input').removeClass('d-none');
                                    $modal.find('.online-inputs').addClass('d-none');
                                    $modal.find('.js-downloadable-file').removeClass('d-none');
                                    $modal.find('.js-downloadable-file input').prop('checked', true);
                                }
                            }

                            $modal.find('[name="' + key + '"]').val(file[key]);
                        });
                    }
                });
            }
        });
    });

    /*
    * edit prerequisites
    * */
    $('body').on('click', '.edit-prerequisite', function (e) {
        e.preventDefault();
        const $this = $(this);
        const prerequisite_id = $this.attr('data-prerequisite-id');
        const webinar_id = $this.attr('data-webinar-id');

        loadingSwl();

        const edit_data = {
            item_id: webinar_id
        };

        $.post('/admin/prerequisites/' + prerequisite_id + '/edit', edit_data, function (result) {
            if (result && result.prerequisite) {
                const prerequisite = result.prerequisite;

                let edit_prerequisite_modal = '<div id="addPrerequisitesModal">';
                edit_prerequisite_modal += $('#webinarPrerequisitesModal').html();
                edit_prerequisite_modal += '</div>';
                edit_prerequisite_modal = edit_prerequisite_modal.replaceAll('prerequisites-select', 'prerequisites-select2');
                edit_prerequisite_modal = edit_prerequisite_modal.replaceAll('/admin/prerequisites/store', '/admin/prerequisites/' + prerequisite_id + '/update');
                edit_prerequisite_modal = edit_prerequisite_modal.replaceAll('str_', '');

                Swal.fire({
                    html: edit_prerequisite_modal,
                    showCancelButton: false,
                    showConfirmButton: false,
                    customClass: {
                        content: 'p-0 text-left',
                    },
                    width: '48rem',
                    onOpen: () => {
                        $('.prerequisites-select2').append('<option selected="selected" value="' + prerequisite.webinar_id + '">' + prerequisite.webinar_title + '</option>');

                        if (prerequisite.required === 1) {
                            $('#addPrerequisitesModal').find('[name="required"]').prop('checked', true);
                        }

                        $('.prerequisites-select2').select2({
                            placeholder: $(this).data('placeholder'),
                            minimumInputLength: 3,
                            allowClear: true,
                            ajax: {
                                url: '/admin/webinars/search',
                                dataType: 'json',
                                type: "POST",
                                quietMillis: 50,
                                data: function (params) {
                                    var queryParameters = {
                                        term: params.term,
                                        webinar_id: $(this).data('webinar-id')
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
                    }
                });
            }
        });
    });

    /*
   * edit FAQ
   * */
    $('body').on('click', '.edit-faq', function (e) {
        e.preventDefault();
        const $this = $(this);
        const faq_id = $this.attr('data-faq-id');
        const webinar_id = $this.attr('data-webinar-id');

        loadingSwl();

        const edit_data = {
            item_id: webinar_id
        };

        $.post('/admin/faqs/' + faq_id + '/edit', edit_data, function (result) {
            if (result && result.faq) {
                const faq = result.faq;

                let edit_faq_modal = '<div id="addFAQsModal">';
                edit_faq_modal += $('#webinarFaqModal').html();
                edit_faq_modal += '</div>';
                edit_faq_modal = edit_faq_modal.replaceAll('/admin/faqs/store', '/admin/faqs/' + faq_id + '/update');

                Swal.fire({
                    html: edit_faq_modal,
                    showCancelButton: false,
                    showConfirmButton: false,
                    customClass: {
                        content: 'p-0 text-left',
                    },
                    width: '48rem',
                    onOpen: () => {
                        Object.keys(faq).forEach(key => {
                            $('#addFAQsModal').find('[name="' + key + '"]').val(faq[key]);
                        })
                    }
                });
            }
        });
    });

    /*
   * edit FAQ
   * */
    $('body').on('click', '.edit-webinar-quiz', function (e) {
        e.preventDefault();
        const $this = $(this);
        const webinar_quiz_id = $this.attr('data-webinar-quiz-id');
        const webinar_id = $this.attr('data-webinar-id');

        loadingSwl();

        const edit_data = {
            item_id: webinar_id
        };

        $.post('/admin/webinar-quiz/' + webinar_quiz_id + '/edit', edit_data, function (result) {
            if (result && result.webinarQuiz) {
                const webinar_quiz = result.webinarQuiz;

                let edit_webinar_quiz_modal = '<div id="addQuizModal">';
                edit_webinar_quiz_modal += $('#quizzesModal').html();
                edit_webinar_quiz_modal += '</div>';
                edit_webinar_quiz_modal = edit_webinar_quiz_modal.replaceAll('/admin/webinar-quiz/store', '/admin/webinar-quiz/' + webinar_quiz_id + '/update');
                edit_webinar_quiz_modal = edit_webinar_quiz_modal.replaceAll('quiz-select2', 'quiz-select22');

                Swal.fire({
                    html: edit_webinar_quiz_modal,
                    showCancelButton: false,
                    showConfirmButton: false,
                    customClass: {
                        content: 'p-0 text-left',
                    },
                    width: '30rem',
                    onOpen: () => {
                        $('.quiz-select22').append('<option selected="selected" value="' + webinar_quiz.id + '">' + webinar_quiz.title + '</option>');
                        $(".quiz-select22").select2({
                            placeholder: $(this).data('placeholder'),
                            allowClear: true,
                            width: '100%',
                        });
                    }
                });
            }
        });
    });

    $('body').on('change', '.js-file-storage', function (e) {
        e.preventDefault();

        const value = this.value;
        const formGroup = $(this).closest('form');

        if (value === 'online') {
            formGroup.find('.local-input').addClass('d-none');
            formGroup.find('.local-input input').val('');
            formGroup.find('.online-inputs').removeClass('d-none');
            formGroup.find('.js-downloadable-file').addClass('d-none');
            formGroup.find('.js-downloadable-file input').prop('checked', false);
        } else {
            formGroup.find('.local-input input').val('');
            formGroup.find('.local-input').removeClass('d-none');
            formGroup.find('.online-inputs').addClass('d-none');
            formGroup.find('.js-downloadable-file').removeClass('d-none');
            formGroup.find('.js-downloadable-file input').prop('checked', true);
        }
    });


    /**
     * add webinar textLessons
     * */
    $('body').on('click', '#webinarAddTestLesson', function (e) {
        e.preventDefault();
        const nameId = randomString();

        let addTextLesson = '<div id="addTestLessonModal">';
        addTextLesson += $('#webinarTestLessonModal').html();
        addTextLesson += '</div>';
        addTextLesson = addTextLesson.replaceAll('record', nameId);
        addTextLesson = addTextLesson.replaceAll('attachments-select2', 'attachments-select2-' + nameId);

        Swal.fire({
            html: addTextLesson,
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            width: '48rem',
            onOpen: () => {
                $('.attachments-select2-' + nameId).select2({
                    multiple: true,
                    width: '100%',
                });

                var $modal = $('#addTestLessonModal');
                var summernoteTarget = $modal.find('.js-content-summernote');
                if (summernoteTarget.length) {
                    summernoteTarget.summernote({
                        tabsize: 2,
                        height: 400,
                        callbacks: {
                            onChange: function (contents, $editable) {
                                $modal.find('.js-hidden-content-summernote').val(contents);
                            }
                        }
                    });
                }
            }
        });
    });

    $('body').on('click', '#saveTestLesson', function (e) {
        e.preventDefault();
        const $this = $(this);
        let form = $('#addTestLessonModal form');
        handleWebinarItemForm(form, $this);
    });

    $('body').on('click', '.edit-test-lesson', function (e) {
        e.preventDefault();
        const $this = $(this);
        const id = $this.attr('data-text-id');
        const webinar_id = $this.attr('data-webinar-id');

        loadingSwl();

        const edit_data = {
            item_id: webinar_id
        };

        $.post('/admin/test-lesson/' + id + '/edit', edit_data, function (result) {
            if (result && result.testLesson) {
                const testLesson = result.testLesson;
                const nameId = randomString();

                let addTextLesson = '<div id="addTestLessonModal">';
                addTextLesson += $('#webinarTestLessonModal').html();
                addTextLesson += '</div>';
                addTextLesson = addTextLesson.replaceAll('record', nameId);
                addTextLesson = addTextLesson.replaceAll('/admin/test-lesson/store', '/admin/test-lesson/' + id + '/update');
                addTextLesson = addTextLesson.replaceAll('str_', '');
                addTextLesson = addTextLesson.replaceAll('attachments-select2', 'attachments-select2-' + nameId);

                Swal.fire({
                    html: addTextLesson,
                    showCancelButton: false,
                    showConfirmButton: false,
                    customClass: {
                        content: 'p-0 text-left',
                    },
                    width: '48rem',
                    onOpen: () => {
                        var $modal = $('#addTestLessonModal');
                        let attachments = [];

                        Object.keys(testLesson).forEach(key => {
                            if (key === 'attachments') {
                                if (testLesson[key] && testLesson[key].length) {
                                    Object.keys(testLesson[key]).forEach(k => {
                                        var file = testLesson[key][k];

                                        if (file) {
                                            attachments.push(file.file_id);
                                        }
                                    });
                                }
                            } else {
                                $modal.find('[name="' + key + '"]').val(testLesson[key]);
                            }
                        });

                        $('.attachments-select2-' + nameId).select2({
                            multiple: true,
                            width: '100%',
                        });

                        $('.attachments-select2-' + nameId).val(attachments).change();

                        var summernoteTarget = $modal.find('.js-content-summernote');
                        if (summernoteTarget.length) {
                            summernoteTarget.val(testLesson['content']);

                            summernoteTarget.summernote({
                                tabsize: 2,
                                height: 400,
                                callbacks: {
                                    onChange: function (contents, $editable) {
                                        $modal.find('.js-hidden-content-summernote').val(contents);
                                    }
                                }
                            });
                        }
                    }
                });
            }
        });
    });

    $('body').on('change', 'select[name="type"]', function () {
        const value = this.value;
        const webinarItem = ['capacity', 'start_date'];

        let show = true;

        if (value !== 'webinar') {
            show = false;
        }

        for (let item of webinarItem) {
            if (show) {
                $('.js-' + item).removeClass('d-none');
            } else {
                $('.js-' + item).addClass('d-none');
            }
        }
    });
})(jQuery);
