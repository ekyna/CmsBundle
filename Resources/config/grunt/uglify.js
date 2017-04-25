module.exports = function (grunt, options) {
    return {
        cms_ts: {
            files: [{
                expand: true,
                cwd: 'src/Ekyna/Bundle/CmsBundle/Resources/public/tmp/js',
                src: '**/*.js',
                dest: 'src/Ekyna/Bundle/CmsBundle/Resources/public/js'
            }]
        },
        cms_js: {
            files: [{
                expand: true,
                cwd: 'src/Ekyna/Bundle/CmsBundle/Resources/private/js',
                src: ['*.js', '**/*.js'],
                dest: 'src/Ekyna/Bundle/CmsBundle/Resources/public/js'
            }]
        }
    }
};
