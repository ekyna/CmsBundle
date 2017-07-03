require(['require', 'jquery', 'ekyna-cms/cms', 'bootstrap', 'bootstrap/hover-dropdown'], function(require, $, Cms) {

    Cms.init();

    $('.dropdown-toggle').dropdownHover();

    // Forms
    var $forms = $('form');
    if ($forms.size() > 0) {
        require(['ekyna-form'], function(Form) {
            $forms.each(function(i, f) {
                var form = Form.create(f);
                form.init();
            });
        });
    }
});
