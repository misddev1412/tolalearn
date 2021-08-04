(function ($) {
    "use strict";

    // *******************
    // create
    // *****************

    $('body').on('click', '#add_multiple_question', function (e) {
        e.preventDefault();
        var multipleQuestionModal = $('#multipleQuestionModal');
        var clone = multipleQuestionModal.clone();
        var id = 'correctAnswerSwitch' + randomString();
        clone.find('label.js-switch').attr('for', id);
        clone.find('input.js-switch').attr('id', id);

        const random_id = randomString();
        clone.find('.panel-file-manager').attr('data-input', random_id);
        clone.find('.lfm-input').attr('id', random_id);

        clone.find('.main-answer-row').removeClass('main-answer-row').addClass('main-answer-box');

        Swal.fire({
            html: clone.html(),
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            width: '48rem',
        });
    });

    $('body').on('click', '.add-answer-btn', function (e) {
        e.preventDefault();
        var mainRow = $('.add-answer-container .main-answer-box');

        var copy = mainRow.clone();
        copy.removeClass('main-answer-box');
        copy.find('.answer-remove').removeClass('d-none');

        const id = 'correctAnswerSwitch' + randomString();
        copy.find('label.js-switch').attr('for', id);
        copy.find('input.js-switch').attr('id', id);

        const random_id = randomString();
        copy.find('.panel-file-manager').attr('data-input', random_id);
        copy.find('.lfm-input').attr('id', random_id);

        copy.find('input[type="checkbox"]').prop('checked', false);

        var copyHtml = copy.prop('innerHTML');
        const nameId = randomString();
        copyHtml = copyHtml.replace(/\[record\]/g, '[' + nameId + ']');
        copyHtml = copyHtml.replace(/\[\d+\]/g, '[' + nameId + ']');
        copy.html(copyHtml);
        copy.find('input[type="checkbox"]').prop('checked', false);
        copy.find('input[type="text"]').val('');
        mainRow.parent().append(copy);
    });

    $('body').on('click', '.answer-remove', function (e) {
        e.preventDefault();
        $(this).closest('.add-answer-card').remove();
    });

    function randomString() {
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";

        for (var i = 0; i < 5; i++)
            text += possible.charAt(Math.floor(Math.random() * possible.length));

        return text;
    }


    $('body').on('click', '#add_descriptive_question', function (e) {
        e.preventDefault();
        var multipleQuestionModal = $('#descriptiveQuestionModal');
        var clone = multipleQuestionModal.clone();

        Swal.fire({
            html: clone.html(),
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            width: '48rem',
        });
    });

    $('body').on('change', '.js-switch', function () {
        const $this = $(this);
        const parent = $this.closest('.js-switch-parent');

        if (this.checked) {
            $('.js-switch').each(function () {
                const switcher = $(this);
                const switcher_parent = switcher.closest('.js-switch-parent');
                const switcher_input = switcher_parent.find('input[type="checkbox"]');
                switcher_input.prop('checked', false);
            });

            $this.prop('checked', true);
        }
    });

    $('body').on('click', '.save-question', function (e) {
        e.preventDefault();
        const $this = $(this);
        let form = $this.closest('form');
        let data = form.serializeObject();
        let action = form.attr('action');

        $this.addClass('loadingbar primary').prop('disabled', true);
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
            $this.removeClass('loadingbar primary').prop('disabled', false);
            var errors = err.responseJSON;
            if (errors && errors.errors) {
                Object.keys(errors.errors).forEach((key) => {
                    const error = errors.errors[key];
                    let element = form.find('[name="' + key + '"]');
                    element.addClass('is-invalid');
                    element.parent().find('.invalid-feedback').text(error[0]);
                });
            }
        })
    });


    // *******************
    // edit
    // *****************

    $('body').on('click', '.edit_question', function (e) {
        e.preventDefault();
        const $this = $(this);
        const question_id = $this.attr('data-question-id');

        loadingSwl();

        $.get('/admin/quizzes-questions/' + question_id + '/edit', function (result) {
            if (result && result.html) {
                let $html = '<div id="editQuestion">' + result.html + '</div>';
                Swal.fire({
                    html: $html,
                    showCancelButton: false,
                    showConfirmButton: false,
                    customClass: {
                        content: 'p-0 text-left',
                    },
                    width: '48rem',
                    onOpen: () => {
                        const editModal = $('#editQuestion');
                        editModal.find('.main-answer-row').removeClass('main-answer-row').addClass('main-answer-box');

                        const random_id = randomString();
                        editModal.find('.panel-file-manager').first().attr('data-input', random_id);
                        editModal.find('.lfm-input').first().attr('id', random_id);

                        const id = 'correctAnswerSwitch' + randomString();
                        editModal.find('label.js-switch').first().attr('for', id);
                        editModal.find('input.js-switch').first().attr('id', id);

                        feather.replace();
                    }
                });
            }
        })
    });

})(jQuery);
