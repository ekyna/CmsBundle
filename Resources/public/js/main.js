require(['require', 'jquery', 'ekyna-cms/user', 'bootstrap', 'bootstrap/hover-dropdown'], function(require, $, User) {

    User.init();

    $('.dropdown-toggle').dropdownHover();

    // Forms
    var $forms = $('.form-body');
    if ($forms.size() > 0) {
        require(['ekyna-form'], function(Form) {
            $forms.each(function(i, f) {
                var form = Form.create(f);
                form.init();
            });
        });
    }
});
