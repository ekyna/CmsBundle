module.exports = function (grunt, options) {
    return {
        cms_lib: {
            files: [
                {
                    src: 'bower_components/backbone/backbone-min.js',
                    dest: 'src/Ekyna/Bundle/CmsBundle/Resources/public/lib/backbone.js'
                },
                {
                    src: 'bower_components/underscore/underscore-min.js',
                    dest: 'src/Ekyna/Bundle/CmsBundle/Resources/public/lib/underscore.js'
                },
                {
                    src: 'bower_components/handlebars/handlebars.amd.min.js',
                    dest: 'src/Ekyna/Bundle/CmsBundle/Resources/public/lib/handlebars.js'
                }
            ]
        },
        cms_editor_css: {
            src: 'src/Ekyna/Bundle/CmsBundle/Resources/public/tmp/css/editor.css',
            dest: 'src/Ekyna/Bundle/CmsBundle/Resources/public/css/_editor.css'
        },
        cms_js: {
            files: [
                {
                    expand: true,
                    cwd: 'src/Ekyna/Bundle/CmsBundle/Resources/private/js',
                    src: ['**'],
                    dest: 'src/Ekyna/Bundle/CmsBundle/Resources/public/js'
                }
            ]
        }
    }
};
