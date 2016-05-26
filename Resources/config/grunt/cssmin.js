module.exports = function (grunt, options) {
    return {
        cms: {
            files: {
                'src/Ekyna/Bundle/CmsBundle/Resources/public/css/editor.css': [
                    'src/Ekyna/Bundle/CmsBundle/Resources/private/css/editor.css'
                ],
                'src/Ekyna/Bundle/CmsBundle/Resources/public/css/_editor.css': [
                    'src/Ekyna/Bundle/CmsBundle/Resources/private/css/_editor.css'
                ]
            }
        }
    }
};
