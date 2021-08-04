(function ($) {
    "use strict";

    $('body').on('click', '.add-meeting-url', function (e) {
        e.preventDefault();
        const item_id = $(this).attr('data-item-id');
        const meeting_password = $('.js-meeting-password-' + item_id).val();
        const meeting_link = $('.js-meeting-link-' + item_id).val();


        const $modalHtml = $('#liveMeetingLinkModal');

        Swal.fire({
            html: '<div id="meetingLinkModal">' + $modalHtml.html() + '</div>',
            showCancelButton: false,
            showConfirmButton: false,
            customClass: {
                content: 'p-0 text-left',
            },
            width: '48rem',
            onOpen: () => {
                const editModal = $('#meetingLinkModal');

                editModal.find('input[name="item_id"]').val(item_id);
                editModal.find('input[name="password"]').val(meeting_password);
                editModal.find('input[name="link"]').val(meeting_link);
            }
        });
    });

    $('body').on('click', '.js-save-meeting-link', function (e) {
        e.preventDefault();

        const $this = $(this);
        const $form = $this.closest('form');
        const action = $form.attr('action');

        const data = $form.serializeObject();

        $this.addClass('loadingbar primary').prop('disabled', true);

        $.post(action, data, function (result) {
            if (result && result.code === 200) {
                Swal.fire({
                    icon: 'success',
                    html: '<h3 class="font-20 text-center text-dark-blue">' + linkSuccessAdd + '</h3>',
                    showConfirmButton: false,
                });

                setTimeout(() => {
                    window.location.reload();
                }, 2000);
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
        });
    });
})(jQuery);
