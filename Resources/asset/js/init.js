(function(win, $, router) {
    $(document).ready(function() {
        if ($('.cms-editor-block').length > 0) {
            $.ajax({
                url: Routing.generate('ekyna_cms_editor_init'),
                type: 'GET',
                dataType: 'html'
            })
            .done(function(html) {
                $(html).appendTo('body');
            });
        }
    });
})(window, jQuery, Routing);