define(['jquery', 'select2'], function($) {
    "use strict";

    function formatTagOption(item) {
        if (!item.element) { return item.text; }
        var $opt = $(item.element);
        return $(
            '<span><span class="fa fa-' + $opt.data('icon') + '"></span> ' + item.text + '</span>'
        );
    }

    return {
        init: function($element) {
            $element.select2({
                templateResult: formatTagOption,
                templateSelection: formatTagOption
            });
        }
    };
});
