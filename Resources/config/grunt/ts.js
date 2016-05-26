module.exports = function (grunt, options) {
    return {
        cms: {
            files: [
                {
                    src: 'src/Ekyna/Bundle/CmsBundle/Resources/private/ts/**.ts',
                    dest: 'src/Ekyna/Bundle/CmsBundle/Resources/public/js'
                }
            ],
            tsconfig: 'src/Ekyna/Bundle/CmsBundle/tsconfig.json',
            options: {
                fast: 'never'
            }
        }
    }
};
