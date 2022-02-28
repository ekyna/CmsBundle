/// <reference path="../../../../../../../../assets/typings/index.d.ts" />

import * as $ from 'jquery';
import * as Router from 'routing';
import * as Modal from 'ekyna-modal';
import Dispatcher from './dispatcher';

import {MainToolbar, MainToolbarView, ViewportButtonConfig} from './controls';
import {ViewportModel, ViewportView} from './viewport';
import {DocumentManager, DocumentData, PluginManager, PluginRegistryConfig} from './document-manager';
import {Button, Select, SelectChoiceConfig} from "./ui";

interface EditorConfig {
    hostname: string
    css_path: string
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

    busy:boolean;
    $busy:JQuery;

    private onLocaleSelectChangeHandler:(select:Select) => void;
    private onPageSelectChangeHandler:(select:Select) => void;
    private onDocumentDataHandler:(data:DocumentData) => void;
    private onDocumentManagerReloadHandler:() => void;
    private onNewPageClickHandler:(button:Button) => void;
    private onEditPageClickHandler:(button:Button) => void;

    constructor() {
        this.onLocaleSelectChangeHandler = (s:Select) => this.onLocaleSelectChange(s);
        this.onPageSelectChangeHandler = (s:Select) => this.onPageSelectChange(s);
        this.onDocumentDataHandler = (d:DocumentData) => this.onDocumentData(d);
        this.onDocumentManagerReloadHandler = () => this.onDocumentManagerReload();
        this.onNewPageClickHandler = (b:Button) => this.onNewPageClick(b);
        this.onEditPageClickHandler = (b:Button) => this.onEditPageClick(b);

        this.busy = true;
    }

    init(config:EditorConfig) {
        this.config = config;

        // @ts-ignore
        $(document).ajaxError(function(jqXHR: JQuery.jqXHR) {
            if (403 === jqXHR.status) {
                alert('You have been disconnected. Please proceed to login.');
            }
        });

        // Plugin manager
        PluginManager.load(config.plugins);

        // Document manager
        this.documentManager = new DocumentManager({
            hostname: config.hostname,
            css_path: config.css_path
        });
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


        // Busy layer
        this.$busy = $('#cms-editor-busy');

        // Init event handlers
        this.initHandlers();

        // Init viewport iframe
        this.viewport.initIFrame(config.path);
    }

    setBusy(e?:Event):void {
        if ((e && e.defaultPrevented) || this.busy) {
            return;
        }

        this.busy = true;

        (<Button>this.mainToolbar.model.getControl('default', 'reload'))
            .activate().startSpinning();

        this.$busy.show();
    }

    unsetBusy():void {
        if (!this.busy) {
            return;
        }

        (<Button>this.mainToolbar.model.getControl('default', 'reload'))
            .deactivate().stopSpinning();

        this.$busy.hide();

        this.busy = false;
    }

    private initHandlers() {
        Dispatcher.on('editor.set_busy', () => this.setBusy());
        Dispatcher.on('editor.unset_busy', () => this.unsetBusy());

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
        Dispatcher.on('document_manager.reload', this.onDocumentManagerReloadHandler);
        Dispatcher.on('document_manager.navigate', this.viewport.onDocumentManagerNavigateHandler);
        Dispatcher.on('document_manager.document_data', this.onDocumentDataHandler);

        Dispatcher.on('ui.control.render', () => {
            this.mainToolbar.postRender();
        });
    }

    private loadPagesList(locale:string):JQueryXHR {
        // TODO use resource controller
        let pageSelect = this.mainToolbar.getPageSelect(),
            pagesListXhr = $.ajax({
                url: Router.generate('admin_ekyna_cms_editor_pages_list', {'document_locale': locale}),
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
        let pageSelect:Select = this.mainToolbar.getPageSelect(),
            pageId:string = pageSelect.getActiveChoice().value;

        this.loadPagesList(select.getActiveChoice().value)
            .then(() => this.updateDocumentControls(pageId));
    }

    private onPageSelectChange(select:Select):void {
        this.viewport.load(select.getActiveChoice().data.path);
    }

    private onDocumentData(data:DocumentData):void {
        let localeSelect = this.mainToolbar.getLocaleSelect(),
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

    private onDocumentManagerReload():void {
        this.viewport.reload();
    }

    //noinspection JSUnusedLocalSymbols
    private onNewPageClick(button:Button):void {
        let modal:Ekyna.Modal = new Modal(),
            parentPageId:string = this.mainToolbar.getPageSelect().getValue();

        modal.load({
            url: Router.generate('ekyna_cms_page_admin_new_child', {'pageId': parentPageId}),
            method: 'GET'
        });

        $(modal).on('ekyna.modal.response', (e:Ekyna.ModalResponseEvent) => {
            if (e.contentType == 'json') {
                e.preventDefault();

                let data:{id:string} = e.content;
                this.loadPagesList(this.mainToolbar.getLocaleSelect().getValue())
                    .then(() => this.updateDocumentControls(data.id));

                e.modal.close();
            }
        });
    }

    //noinspection JSUnusedLocalSymbols
    private onEditPageClick(button:Button):void {
        let modal:Ekyna.Modal = new Modal(),
            pageId:string = this.mainToolbar.getPageSelect().getValue();

        modal.load({
            url: Router.generate('ekyna_cms_page_admin_edit', {'pageId': pageId}),
            method: 'GET'
        });

        $(modal).on('ekyna.modal.response', (e:Ekyna.ModalResponseEvent) => {
            if (e.contentType == 'json') {
                e.preventDefault();

                let data:{id:string} = e.content;
                this.loadPagesList(this.mainToolbar.getLocaleSelect().getValue())
                    .then(() => this.updateDocumentControls(data.id));

                e.modal.close();
            }
        });
    }

    private updateDocumentControls(pageId:string, reload:boolean = true):SelectChoiceConfig {

        let newButton:Button = this.mainToolbar.getNewPageButton(),
            editButton:Button = this.mainToolbar.getEditPageButton(),
            pageSelect:Select = this.mainToolbar.getPageSelect(),
            pageChoice: SelectChoiceConfig = null;

        newButton.disable();
        editButton.disable();

        try {
            pageSelect.select(pageId);
            pageChoice = pageSelect.getActiveChoice();
        } catch(e) {
        }

        if (pageChoice) {
            editButton.enable();
            if (!pageChoice.data.locked) {
                newButton.enable();
            }
            if (reload) {
                this.viewport.load(pageChoice.data.path);
            }
        } else {
            newButton.disable();
            editButton.disable();
        }

        return pageChoice;
    }
}

export = Editor;
