(function(doc, $, router) {
    $(doc).ready(function() {

        var $flashesContainer = $('#cms-flashes');

        var initXrh = $.ajax({
            url: router.generate('ekyna_cms_init'),
            data: {
                flashes: $flashesContainer.length > 0 ? 1 : 0,
                editor: $('.cms-editor-block').length > 0 ? 1 : 0
            },
            dataType: 'xml',
            type: 'POST'
        });

        initXrh.done(function (xml) {
            var $xml = $(xml);
            if ($flashesContainer.length > 0) {
                var flashes = $xml.find('flashes').text();
                $flashesContainer.html($(flashes));
            }
            var editor = $xml.find('editor').text();
            $('body').append($(editor));
        });
    });
})(document, jQuery, Routing);