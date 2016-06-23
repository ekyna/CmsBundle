/// <reference path="../../../../../../../typings/index.d.ts" />

import * as $ from 'jquery';
import * as Router from 'routing';
import * as Modal from 'ekyna-modal';
import Dispatcher from './dispatcher';

import {MainToolbar, MainToolbarView, ViewportButtonConfig} from './controls';
import {ViewportModel, ViewportView} from './viewport';
import {DocumentManager, DocumentData, BaseManager, PluginManager, PluginRegistryConfig} from './document-manager';
import {Button, Select, SelectChoiceConfig} from "./ui";

interface EditorConfig {
    hostname: string
    path: string
    locales: Array<{name:string, title:string}>
    viewports: Array<ViewportButtonConfig>
    plugins: PluginRegistryConfig
}

class Editor {
    config:EditorConfig;
    documentManager:DocumentManager;
    mainToolbar:MainToolbarView;
    viewport:ViewportView;

    private onLocaleSelectChangeHandler:(select:Select) => void;
    private onPageSelectChangeHandler:(select:Select) => void;
    private onDocumentDataHandler:(data:DocumentData) => void;
    private onNewPageClickHandler:(button:Button) => void;
    private onEditPageClickHandler:(button:Button) => void;

    constructor(config:EditorConfig) {
        this.config = config;

        this.onLocaleSelectChangeHandler = (s:Select) => this.onLocaleSelectChange(s);
        this.onPageSelectChangeHandler = (s:Select) => this.onPageSelectChange(s);
        this.onDocumentDataHandler = (d:DocumentData) => this.onDocumentData(d);
        this.onNewPageClickHandler = (b:Button) => this.onNewPageClick(b);
        this.onEditPageClickHandler = (b:Button) => this.onEditPageClick(b);
    }

    init() {
        // Plugin manager
        PluginManager.load(config.plugins);


        // Document manager
        this.documentManager = new DocumentManager(config.hostname);
        this.documentManager.initialize();


        // Main toolbar
        this.mainToolbar = new MainToolbarView({
            model: new MainToolbar({
                id: 'editor-control-bar',
                classes: ['horizontal']
            }, {
                viewports: config.viewports,
                locales: config.locales
            })
        });
        $('[data-controls-placeholder]').replaceWith(this.mainToolbar.$el);
        this.mainToolbar.render();


        // Viewport
        this.viewport = new ViewportView({
            model: new ViewportModel()
        });
        $('[data-viewport-placeholder]').replaceWith(this.viewport.render().$el);


        // Init event handlers
        this.initHandlers();


        // Init viewport iframe
        this.viewport.initIFrame(config.path);
    }

    setBusy(e?:Event):void {
        if (e && e.defaultPrevented) {
            return;
        }
        (<Button>this.mainToolbar.model.getControl('default', 'reload'))
            .activate().startSpinning();
    }

    unsetBusy():void {
        (<Button>this.mainToolbar.model.getControl('default', 'reload'))
            .deactivate().stopSpinning();
    }

    private initHandlers() {
        // Main toolbar
        Dispatcher.on('controls.power.click', this.documentManager.powerClickHandler);
        Dispatcher.on('controls.reload.click', this.viewport.onControlsReloadClickHandler);
        Dispatcher.on('controls.viewport.click', this.viewport.onControlsViewportClickHandler);
        Dispatcher.on('controls.locale.change', this.onLocaleSelectChangeHandler);
        Dispatcher.on('controls.page.change', this.onPageSelectChangeHandler);
        Dispatcher.on('controls.new_page.click', this.onNewPageClickHandler);
        Dispatcher.on('controls.edit_page.click', this.onEditPageClickHandler);

        // Viewport
        Dispatcher.on('viewport_iframe.unload', this.documentManager.viewportUnloadHandler);
        Dispatcher.on('viewport_iframe.unload', (e:Event) => this.setBusy(e));
        Dispatcher.on('viewport_iframe.load', this.documentManager.viewportLoadHandler);
        Dispatcher.on('viewport_iframe.load', () => this.unsetBusy());

        // Document manager
        Dispatcher.on('document_manager.navigate', this.viewport.onDocumentManagerNavigateHandler);
        Dispatcher.on('document_manager.document_data', this.onDocumentDataHandler);

        Dispatcher.on('ui.control.render', () => {
            this.mainToolbar.postRender();
        });
    }

    private loadPagesList(locale:string):JQueryXHR {
        // TODO use resource controller
        var pageSelect = this.mainToolbar.getPageSelect(),
            pagesListXhr = $.ajax({
                url: Router.generate('ekyna_cms_editor_pages_list', {'document_locale': locale}),
                method: 'GET',
                dataType: 'json'
            });

        pagesListXhr.done(function (response) {
            pageSelect.setChoices(response);
        });

        pagesListXhr.fail(function () {
            throw 'Failed to load page list.';
        });

        return pagesListXhr;
    }

    private onLocaleSelectChange(select:Select) {
        var pageSelect:Select = this.mainToolbar.getPageSelect(),
            pageId:string = pageSelect.getActiveChoice().value;

        this.loadPagesList(select.getActiveChoice().value)
            .then(() => this.updateDocumentControls(pageId));
    }

    private onPageSelectChange(select:Select):void {
        this.viewport.load(select.getActiveChoice().data.path);
    }

    private onDocumentData(data:DocumentData):void {
        var localeSelect = this.mainToolbar.getLocaleSelect(),
            pageSelect = this.mainToolbar.getPageSelect(),
            currentLocale = localeSelect.get('value');

        localeSelect.select(data.locale);
        if (data.locale != currentLocale || pageSelect.get('choices').length == 0) {
            this.loadPagesList(data.locale)
                .then(() => this.updateDocumentControls(data.id, false));
        } else if (data.id) {
            this.updateDocumentControls(data.id, false)
        }
        // TODO dynamic path page selection
    }

    private onNewPageClick(button:Button):void {
        var modal:Ekyna.Modal = new Modal(),
            parentPageId:string = this.mainToolbar.getPageSelect().getValue();

        // TODO use resource controller
        modal.load({
            url: Router.generate('ekyna_cms_page_admin_new_child', {'pageId': parentPageId}),
            method: 'GET'
        });

        $(modal).on('ekyna.modal.response', (e:Ekyna.ModalResponseEvent) => {
            if (e.contentType == 'json') {
                e.preventDefault();

                var data:{id:string} = e.content;
                this.loadPagesList(this.mainToolbar.getLocaleSelect().getValue())
                    .then(() => this.updateDocumentControls(data.id));
            }
        });
    }

    private onEditPageClick(button:Button):void {
        var modal:Ekyna.Modal = new Modal(),
            pageId:string = this.mainToolbar.getPageSelect().getValue();

        modal.load({
            url: Router.generate('ekyna_cms_page_admin_edit', {'pageId': pageId}),
            method: 'GET'
        });

        $(modal).on('ekyna.modal.response', (e:Ekyna.ModalResponseEvent) => {
            if (e.contentType == 'json') {
                e.preventDefault();

                var data:{id:string} = e.content;
                this.loadPagesList(this.mainToolbar.getLocaleSelect().getValue())
                    .then(() => this.updateDocumentControls(data.id));
            }
        });
    }

    private updateDocumentControls(pageId:string, reload?:boolean):void {
        this.mainToolbar.getEditPageButton().disable();
        this.mainToolbar.getNewPageButton().disable();

        var pageSelect:Select = this.mainToolbar.getPageSelect();
        reload = !!reload || pageSelect.select(pageId);

        var pageChoice:SelectChoiceConfig = pageSelect.getActiveChoice();
        if (pageChoice) {
            if (reload) {
                this.viewport.load(pageChoice.data.path);
            }
            this.mainToolbar.getEditPageButton().enable();
            if (!pageChoice.data.locked) {
                this.mainToolbar.getNewPageButton().enable();
                return;
            }
        }
    }
}

var config = (<EditorConfig>$('.cms-editor').data('config'));
var editor:Editor = new Editor(config);
editor.init();

export = editor;
