require(['require', 'jquery', 'ekyna-cms/user', 'bootstrap'], function(require, $, User) {

    User.init();

    // Forms
    var $forms = $('.form-body');
    if ($forms.size() > 0) {
        require(['ekyna-form'], function(Form) {
            $forms.each(function(i, f) {
                Form.create(f);
            });
        });
    }
});
