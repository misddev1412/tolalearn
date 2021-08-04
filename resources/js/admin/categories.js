(function ($) {
    "use strict";

    $('body').on('change', '#hasSubCategory', function (e) {
        if (this.checked) {
            $('#subCategories').removeClass('d-none');
        } else {
            $('#subCategories').addClass('d-none');
        }
    });


    $('body').on('click', '.add-btn', function (e) {
        e.preventDefault();
        var mainRow = $('.main-row');

        var copy = mainRow.clone();
        copy.removeClass('main-row');
        copy.removeClass('d-none');
        var copyHtml = copy.prop('innerHTML');
        copyHtml = copyHtml.replace(/\[record\]/g, '[' + randomString() + ']');
        copy.html(copyHtml);
        $('.draggable-lists').append(copy);
    });

    $('body').on('click', '.remove-btn', function (e) {
        e.preventDefault();
        $(this).closest('.form-group').remove();
    });

    function randomString() {
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";

        for (var i = 0; i < 16; i++)
            text += possible.charAt(Math.floor(Math.random() * possible.length));

        return text;
    }

    function setSortable(target) {
        if (target.length) {
            target.sortable({
                group: 'no-drop',
                handle: '.move-icon',
                axis: "y",
                update: function (e, ui) {
                    var sortData = target.sortable('toArray', {attribute: 'data-id'});
                    var table = e.target.getAttribute('data-order-table');
                }
            });
        }
    }

    var target = $('.draggable-lists');
    setSortable(target);

    $(".colorpickerinput").colorpicker({
        format: 'hex',
        component: '.input-group-append',
    });
})(jQuery);
