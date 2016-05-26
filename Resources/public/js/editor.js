/// <reference path="../../../../../../typings/requirejs/require.d.ts" />
/// <reference path="../../../../../../typings/jquery/jquery.d.ts" />
define(["require", "exports", './_editor/Viewport'], function (require, exports, Viewport) {
    require(['jquery', 'backbone'], function ($) {
        'use strict';
        var viewport = new Viewport($('#editor-viewport'));
        $('#editor-control-viewport').on('click', function (e) {
            var $button = $(e.target).closest('button'), width = $button.data('width'), height = $button.data('height');
            viewport.resize({
                width: width,
                height: height
            });
        });
    });
});
