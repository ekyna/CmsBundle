/// <reference path="../../../../../../typings/requirejs/require.d.ts" />
/// <reference path="../../../../../../typings/jquery/jquery.d.ts" />

import Viewport = require('./_editor/Viewport')

//noinspection JSFileReferences
require(['jquery', 'backbone'], ($:JQueryStatic) => {
    'use strict';

    var viewport = new Viewport($('#editor-viewport'));
    //viewport.resize();

    $('#editor-control-viewport').on('click', (e) => {
        var $button = $(e.target).closest('button'),
            width = $button.data('width'),
            height = $button.data('height');

        viewport.resize({
            width: width,
            height: height
        });
    });

});
