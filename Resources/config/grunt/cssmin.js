module.exports = function (grunt, options) {
    return {
        cms_less: {
            files: [
                {
                    expand: true,
                    cwd: 'src/Ekyna/Bundle/CmsBundle/Resources/public/tmp/css',
                    src: ['*.css'],
                    dest: 'src/Ekyna/Bundle/CmsBundle/Resources/public/css',
                    ext: '.css'
                }
            ]
        }
    }
};
