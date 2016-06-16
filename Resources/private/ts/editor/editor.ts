/// <reference path="../../../../../../../typings/backbone/backbone.d.ts" />

import $ = require('jquery');
import Dispatcher from './dispatcher';

import {MainToolbar, MainToolbarView} from './controls';
import {ViewportModel, ViewportView} from './viewport';
import {DocumentManager, PluginManager} from './document-manager';


// TODO require config
var config = {
    hostname: 'sf.jessie2.dev',
    path: '/app_dev.php/',
    viewports: [
        {width: 320, height: 568, icon: 'mobile', title: 'Smartphone (320x568)', name: 'Smartphone'},
        {width: 768, height: 1024, icon: 'tablet', title: 'Tablet (768x1024)', name: 'Tablet'},
        {width: 1280, height: 800, icon: 'laptop', title: 'Laptop (1280x800)', name: 'Laptop'},
        {width: 1920, height: 1080, icon: 'desktop', title: 'Desktop (1920x1080)', name: 'Desktop'},
        {width: 0, height: 0, icon: 'arrows-alt', title: 'Adjust to screen', name: 'Adjust', active: true}
    ],
    plugins: {
        block: [
            {name: 'ekyna_cms_tinymce', path: 'ekyna-cms/editor/plugin/block/tinymce-plugin'}
        ],
        container: [
            //{name: '', path:''}
        ]
    }
};


// Plugin manager
PluginManager.load(config.plugins);


// Document manager
var documentManager:DocumentManager = new DocumentManager(config.hostname);
documentManager.initialize();


// Main toolbar
var mainBar:MainToolbarView = new MainToolbarView({
    model: new MainToolbar({
        id: 'editor-control-bar',
        classes: ['horizontal']
    }, {
        viewports: config.viewports
    })
});
$('[data-controls-placeholder]').replaceWith(mainBar.render().$el);


// Viewport
var viewport:ViewportView = new ViewportView({
    model: new ViewportModel()
});
$('[data-viewport-placeholder]').replaceWith(viewport.render().$el);


// Events handlers
Dispatcher.on('document_manager.navigate', viewport.onDocumentManagerNavigateHandler);

Dispatcher.on('controls.power.click', documentManager.powerClickHandler);
Dispatcher.on('controls.reload.click', viewport.onControlsReloadClickHandler);
Dispatcher.on('controls.viewport.click', viewport.onControlsViewportClickHandler);

Dispatcher.on('viewport_iframe.unload', documentManager.viewportUnloadHandler);
Dispatcher.on('viewport_iframe.unload', (e:Event) => mainBar.setBusy(e));

Dispatcher.on('viewport_iframe.load', documentManager.viewportLoadHandler);
Dispatcher.on('viewport_iframe.load', () => mainBar.unsetBusy());


viewport.initIFrame(config.path);


