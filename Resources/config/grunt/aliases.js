module.exports = {
    'build:cms_css': [
        'less:cms',
        'cssmin:cms_less',
        'clean:cms_less'
    ],
    'build:cms_js': [
        'ts:cms',
        'uglify:cms_ts',
        'uglify:cms_js',
        'clean:cms_ts'
    ],
    'build:cms': [
        'clean:cms_pre',
        'build:cms_css',
        'build:cms_js',
        'clean:cms_post'
    ]
};
