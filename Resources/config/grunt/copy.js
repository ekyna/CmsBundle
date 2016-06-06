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
                }/*,
                {
                    src: 'bower_components/handlebars/handlebars.amd.min.js',
                    dest: 'src/Ekyna/Bundle/CmsBundle/Resources/public/lib/handlebars.js'
                }*/
            ]
        },
        cms_less: { // For watch:cms_less
            files: [
                {
                    expand: true,
                    cwd: 'src/Ekyna/Bundle/CmsBundle/Resources/public/tmp/css',
                    src: ['**'],
                    dest: 'src/Ekyna/Bundle/CmsBundle/Resources/public/css'
                }
            ]
        },
        cms_js: { // For watch:cms_js
            files: [
                {
                    expand: true,
                    cwd: 'src/Ekyna/Bundle/CmsBundle/Resources/private/js',
                    src: ['*.js'],
                    dest: 'src/Ekyna/Bundle/CmsBundle/Resources/public/js'
                }
            ]
        },
        cms_ts: { // For watch:cms_ts
            files: [
                {
                    expand: true,
                    cwd: 'src/Ekyna/Bundle/CmsBundle/Resources/public/tmp/js',
                    src: ['**'],
                    dest: 'src/Ekyna/Bundle/CmsBundle/Resources/public/js'
                }
            ]
        }
    }
};
