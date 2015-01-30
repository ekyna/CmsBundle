(function(doc, $, router) {
    $(doc).ready(function() {
        if ($('.cms-editor-block').length > 0) {
            var editorXhr = $.get(router.generate('ekyna_cms_editor_init'));
            editorXhr.done(function(html) {
                $(html).appendTo('body');
            });
        }
        // Load flashes if not handled by ESI
        var $flashes = $('#cms-flashes');
        if ($flashes.length > 0) {
            var flashXhr = $.get(router.generate('ekyna_cms_flashes'));
            flashXhr.done(function (html) {
                $flashes.html(html);
            })
        }
    });
})(document, jQuery, Routing);