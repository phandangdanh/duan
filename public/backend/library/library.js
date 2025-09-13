(function ($) {
    "use strict";

    var HT = {};

    HT.select2 = function () {
        if ($.fn.select2) {
            $('.setupSelect2').select2();
        } else {
            console.warn('select2 library is not loaded!');
        }
    };

    $(document).ready(function () {
        HT.select2();
    });
})(jQuery);
