module.exports = function (grunt, options) {
    return {
        cms: {
            files: [
                {
                    src: 'src/Ekyna/Bundle/CmsBundle/Resources/private/ts/**.ts',
                    dest: 'src/Ekyna/Bundle/CmsBundle/Resources/public/tmp/js'
                }
            ],
            options: {
                fast: 'never',
                module: 'amd',
                noImplicitAny: true,
                removeComments: true,
                preserveConstEnums: true,
                sourceMap: false
            }
        }
    }
};
