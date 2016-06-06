module.exports = function (grunt, options) {
    return {
        cms_less: {
            files: ['src/Ekyna/Bundle/CmsBundle/Resources/private/less/**/*.less'],
            tasks: ['less:cms', 'copy:cms_less', 'clean:cms_less'],
            options: {
                spawn: false
            }
        },
        cms_js: {
            files: ['src/Ekyna/Bundle/CmsBundle/Resources/private/js/**/*.js'],
            tasks: ['copy:cms_js'],
            options: {
                spawn: false
            }
        },
        cms_ts: {
            files: ['src/Ekyna/Bundle/CmsBundle/Resources/private/ts/**/*.ts'],
            tasks: ['ts:cms', 'copy:cms_ts', 'clean:cms_ts'],
            options: {
                spawn: false
            }
        }
    }
};
