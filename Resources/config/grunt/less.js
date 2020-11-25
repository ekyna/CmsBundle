module.exports = function (grunt, options) {
    // @see https://github.com/gruntjs/grunt-contrib-less
    return {
        cms: {
            files: {
                'src/Ekyna/Bundle/CmsBundle/Resources/public/tmp/css/editor.css':
                    'src/Ekyna/Bundle/CmsBundle/Resources/private/less/editor.less',
                'src/Ekyna/Bundle/CmsBundle/Resources/public/tmp/css/editor-document.css':
                    'src/Ekyna/Bundle/CmsBundle/Resources/private/less/editor-document.less',
                'src/Ekyna/Bundle/CmsBundle/Resources/public/tmp/css/editor-icons.css':
                    'src/Ekyna/Bundle/CmsBundle/Resources/private/less/editor-icons.less',
                'src/Ekyna/Bundle/CmsBundle/Resources/public/tmp/css/bootstrap-content.css':
                    'src/Ekyna/Bundle/CmsBundle/Resources/private/less/bootstrap-content.less',
                'src/Ekyna/Bundle/CmsBundle/Resources/public/tmp/css/slide-show.css':
                    'src/Ekyna/Bundle/CmsBundle/Resources/private/less/slide-show.less'
            }
        }
    }
};
