/// <reference path="../../../../../../typings/backbone/backbone.d.ts" />

import $ = require('jquery');
import Backbone = require('backbone');
import _ = require('underscore');

import {ControlsModel, ControlsView} from './editor/Controls';
import {ViewportModel, ViewportView} from './editor/Viewport';
import {DocumentManager} from './editor/DocumentManager';

import Dispatcher from './editor/Dispatcher';

class Editor {
    private viewport:ViewportView;
    private mainBar:ControlsView;
    private documentManager:DocumentManager;

    initialize():Editor {

        // Main toolbar
        this.mainBar = new ControlsView({
            model: new ControlsModel(null, {
                viewports: [
                    {width: 320, height: 568, icon: 'mobile', title: 'Smartphone (320x568)', name: 'Smartphone'},
                    {width: 768, height: 1024, icon: 'tablet', title: 'Tablet (768x1024)', name: 'Tablet'},
                    {width: 1280, height: 800, icon: 'laptop', title: 'Laptop (1280x800)', name: 'Laptop'},
                    {width: 1920, height: 1080, icon: 'desktop', title: 'Desktop (1920x1080)', name: 'Desktop'},
                    {width: 0, height: 0, icon: 'arrows-alt', title: 'Adjust to screen', name: 'Adjust', active: true},
                ]
            })
        });
        $('[data-controls-placeholder]').replaceWith(this.mainBar.render().$el);

        //
        this.documentManager = new DocumentManager('sf.jessie2.dev');
        this.documentManager.initialize();


        this.viewport = new ViewportView({
            model: new ViewportModel()
        });
        $('[data-viewport-placeholder]').replaceWith(this.viewport.render().$el);
        this.viewport.initIFrame('http://sf.jessie2.dev/app_dev.php/');


        return this;
    }
}

export default new Editor().initialize();

