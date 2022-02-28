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
                rootDir: 'src/Ekyna/Bundle/CmsBundle/Resources/private/ts',
                //verbose: true,
                lib: ['dom', 'es2015', 'esnext'],
                target: 'es5',
                module: 'amd',
                moduleResolution: 'classic',
                noImplicitAny: false,
                removeComments: true,
                preserveConstEnums: true,
                sourceMap: false
            }
        }
    }
};
