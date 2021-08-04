(function ($) {
    "use strict";

    $('body').on('click', '#preview', function (e) {
        e.preventDefault();

        const form = $('#templateForm');
        const action = $(this).attr('data-action');

        form.attr('target', '_blank');
        form.attr('action', action);
        form.attr('method', 'get');
        form.trigger('submit');
    });

    $('body').on('click', '#submiter', function (e) {
        e.preventDefault();

        const form = $('#templateForm');
        const action = $(this).attr('data-action');

        form.removeAttr('target');
        form.attr('action', action);
        form.attr('method', 'post');
        form.trigger('submit');
    });
})(jQuery);
