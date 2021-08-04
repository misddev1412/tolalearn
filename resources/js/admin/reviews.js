(function ($) {
    "use strict";

    $('body').on('click', '.js-show-review-details', function (e) {
        e.preventDefault();

        const $this = $(this);
        const parent = $this.closest('td');
        const items = ['content_quality', 'instructor_skills', 'purchase_worth', 'support_quality'];
        const $modal = $('#reviewRateDetail');

        for (let item of items) {
            const value = parent.find('.js-' + item).val();
            $modal.find('.js-' + item).text(value);
        }

        $modal.modal('show');
    });

    $('body').on('click', '.js-show-description', function (e) {
        e.preventDefault();

        const message = $(this).parent().find('input[type="hidden"]').val();

        const $modal = $('#contactMessage');
        $modal.find('.modal-body').html(message);

        $modal.modal('show');
    })
})(jQuery);
