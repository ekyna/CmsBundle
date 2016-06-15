module.exports = function (grunt, options) {
    return {
        cms: {
            files: [
                {
                    src: 'src/Ekyna/Bundle/CmsBundle/Resources/private/ts/**/*.ts',
                    dest: 'src/Ekyna/Bundle/CmsBundle/Resources/public/tmp/js'
                }
            ],
            options: {
                fast: 'never',
                module: 'amd',
                rootDir: 'src/Ekyna/Bundle/CmsBundle/Resources/private/ts',
                noImplicitAny: false,
                removeComments: true,
                preserveConstEnums: true,
                sourceMap: false
            }
        }
    }
};
