/// <reference path="../../../../../../typings/requirejs/require.d.ts" />
/// <reference path="../../../../../../typings/jquery/jquery.d.ts" />

import * as c from './_editor/Controls';
import * as v from './_editor/Viewport';

//noinspection JSFileReferences
require(['jquery'], ($:JQueryStatic) => {
    'use strict';


    var controls = new c.Editor.ControlsView({
        model: new c.Editor.ControlsModel(null, {
            viewports: [
                {width: 320, height: 568, icon: 'mobile', title: 'Mobile phone (320x568)'},
                {width: 768, height: 1024, icon: 'tablet', title: 'Tablet (768x1024)'},
                {width: 1280, height: 800, icon: 'laptop', title: 'Laptop (1280x800)'},
                {width: 1920, height: 1080, icon: 'desktop', title: 'Desktop (1920x1080)'},
                {width: 0, height: 0, icon: 'arrows-alt', title: 'Auto size'},
            ]
        })
    });

    $('[data-controls-placeholder]').replaceWith(controls.render().$el);


    var viewport = new v.Editor.ViewportView({
        model: new v.Editor.ViewportModel({
            url: '/app_dev.php/'
        })
    });

    $('[data-viewport-placeholder]').replaceWith(viewport.render().$el);

    //viewport.resize({width: 320, height: 512});

    /*$('#editor-control-viewport').on('click', (e) => {
        var $button = $(e.target).closest('button'),
            width = $button.data('width'),
            height = $button.data('height');

        viewport.resize({
            width: width,
            height: height
        });
    });*/

});
