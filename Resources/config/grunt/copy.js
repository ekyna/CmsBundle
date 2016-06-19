module.exports = function (grunt, options) {
    return {
        cms_img: {
            files: [
                {
                    expand: true,
                    cwd: 'src/Ekyna/Bundle/CmsBundle/Resources/private/img',
                    src: ['**'],
                    dest: 'src/Ekyna/Bundle/CmsBundle/Resources/public/img'
                }
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
        cms_ts: { // For watch:cms_ts
            files: [
                {
                    expand: true,
                    cwd: 'src/Ekyna/Bundle/CmsBundle/Resources/public/tmp/js',
                    src: ['**/*.js'],
                    dest: 'src/Ekyna/Bundle/CmsBundle/Resources/public/js'
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
        }
    }
};
