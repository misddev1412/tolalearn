(function ($) {
    "use strict";

    $('body').on('click', '.contact-info', function (e) {
        e.preventDefault();
        const $this = $(this);
        const user_id = $this.attr('data-user-id');
        const user_type = $this.attr('data-user-type');

        loadingSwl();

        const data = {
            user_id: user_id
        };
        $.post('/panel/users/contact-info', data, function (result) {
            if (result && result.code === 200) {
                const modal_title = (user_type === 'instructor') ? instructor_contact_information_lang : student_contact_information_lang;
                var html = '<div class="contact-info-modal">\n' +
                    '        <h2 class="section-title after-line">'+ modal_title +'</h2>\n' +
                    '        <div class="mt-25 d-flex flex-column align-items-center justify-content-center">\n' +
                    '            <div class="contact-avatar">\n' +
                    '                <img src="'+ result.avatar +'" class="img-cover" alt="">\n' +
                    '            </div>\n' +
                    '            <div class="mt-15 w-75 text-center">\n' +
                    '                <h3 class="font-16 font-weight-bold text-dark-blue">'+ result.name +'</h3>\n' +
                    '                <div class="d-flex align-items-center justify-content-between mt-15">\n' +
                    '                    <span>'+ email_lang +':</span>\n' +
                    '                    <span>'+ result.email +'</span>\n' +
                    '                </div>\n' +
                    '                <div class="d-flex align-items-center justify-content-between mt-15">\n' +
                    '                    <span>'+ phone_lang +':</span>\n' +
                    '                    <span>'+ result.phone +'</span>\n' +
                    '                </div>\n' +
                    '            </div>\n' +
                    '        </div>\n' +
                    '       <div class="mt-30 d-flex align-items-center justify-content-end">\n' +
                    '            <button type="button" class="btn btn-sm btn-danger ml-10 close-swl">'+ close_lang +'</button>\n' +
                    '        </div>'+
                    '    </div>';

                Swal.fire({
                    html: html,
                    showCancelButton: false,
                    showConfirmButton: false,
                    customClass: {
                        content: 'p-0 text-left',
                    },
                    width: '40rem',
                });
            }
        })
    });
})(jQuery);
