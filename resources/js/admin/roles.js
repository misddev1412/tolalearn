(function ($) {
    "use strict";

    $('body').on('change', '#isAdmin', function () {
        if (this.checked) {
            $('#sections').removeClass('d-none');
        } else {
            $('#sections').addClass('d-none');
        }
    });

    $('.section-parent').on('change', function (e) {
        let $this = $(this);
        let parent = $this.parent().closest('.section-box');
        let isChecked = e.target.checked;

        if (isChecked) {
            parent.find('input[type="checkbox"].section-child').prop('checked', true);
        } else {
            parent.find('input[type="checkbox"].section-child').prop('checked', false);
        }
    });

    $('.section-child').on('change', function (e) {
        let $this = $(this);
        let parent = $(this).parent().closest('.section-box');
        let setChecked = false;
        let allChild = parent.find('input[type="checkbox"].section-child');

        allChild.each(function (index, child) {
            if ($(child).is(':checked')) {
                setChecked = true;
            }
        });

        let parentInput = parent.find('input[type="checkbox"].section-parent');
        parentInput.prop('checked', setChecked);
    });
})(jQuery);
