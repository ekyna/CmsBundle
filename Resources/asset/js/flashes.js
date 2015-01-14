(function(doc, $, router) {

    $(doc).ready(function() {

        $.get(router.generate('ekyna_cms_flashes'), function(html) {
            $('#flashes').html(html);
        });

    });

})(window.document, jQuery, Routing);
