(function(win, $, router) {
    $(document).ready(function() {
        if ($('.cms-editor-block').length > 0) {
            $.ajax({
                url: Routing.generate('ekyna_cms_editor_init'),
                type: 'GET',
                dataType: 'html'
            })
            .done(function(html) {
                console.log('done');
                $(html).appendTo('body');
            })
            .fail(function() {
                console.log('fail');
            });
        }
    });
})(window, jQuery, Routing);