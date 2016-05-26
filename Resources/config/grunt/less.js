module.exports = function (grunt, options) {
    // @see https://github.com/gruntjs/grunt-contrib-less
    return {
        cms: {
            files: {
                'src/Ekyna/Bundle/MediaBundle/Resources/public/tmp/css/editor.css':
                    'src/Ekyna/Bundle/CmsBundle/Resources/private/less/editor.less'
            }
        }
    }
};
