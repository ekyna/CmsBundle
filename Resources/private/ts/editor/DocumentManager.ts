/// <reference path="../../../../../../../typings/tsd.d.ts" />

import $ = require('jquery');

import Dispatcher from './Dispatcher';
import {ClickOrigin, Button, Toolbar, ToolbarView} from './Ui';

/**
 * DocumentManager
 */
export class DocumentManager {

    private $document:JQuery;
    private hostname:string;

    private enabled:boolean = false;
    private toolbar:ToolbarView<Toolbar>;

    private powerClickHandler:(button:Button) => void;
    private viewportLoadHandler:(doc:Document) => void;
    private viewportUnloadHandler:() => void;
    private documentClickHandler:(e:JQueryEventObject) => void;

    constructor(hostname:string) {
        this.hostname = hostname;
        this.$document = null;

        this.powerClickHandler = (button:Button) => this.onPowerClick(button);
        this.viewportLoadHandler = (doc:Document) => this.onViewportLoad(doc);
        this.viewportUnloadHandler = () => this.onViewportUnload();
        this.documentClickHandler = (e:JQueryEventObject) => this.onDocumentClick(e);
    }

    initialize() {
        Dispatcher.on('controls.power.click', this.powerClickHandler);
        Dispatcher.on('viewport_iframe.load', this.viewportLoadHandler);
        Dispatcher.on('viewport_iframe.unload', this.viewportUnloadHandler);
    }

    private onPowerClick(button:Button) {
        var active = button.get('active');
        if (active && !this.enabled) {
            this.enabled = true;
            this.enableEdition();
        } else if (this.enabled && !active) {
            this.enabled = false;
            this.disableEdition();
        } else {
            this.enabled = active;
        }
    }

    private onDocumentClick(e:JQueryEventObject):void {
        var origin:ClickOrigin = {left: e.clientX, top: e.clientY},
            $target:JQuery = $(e.target),
            $selection:JQuery;

        /* Do nothing on Toolbar click */
        if (0 < $target.closest('#editor-document-toolbar').length) {
            return null;
        }
        /* Do nothing on Tinymce click */
        if (0 < $target.parents('.mce-container').length) {
            return null;
        }

        // Remove toolbar if exists
        if (this.toolbar) {
            this.toolbar.remove();
        }

        // Block test
        $selection = $target.closest('.cms-block');
        if (1 == $selection.length) {
            console.log('Block selection');
            this.displayBlockToolbar(origin, $selection);
            return;
        }

        // Row test
        $selection = $target.closest('.cms-row');
        if (1 == $selection.length) {
            console.log('Row selection');
            this.displayRowToolbar(origin, $selection);
            return;
        }

        // Container test
        $selection = $target.closest('.cms-container');
        if (1 == $selection.length) {
            this.displayContainerToolbar(origin, $selection);
            console.log('Container selection');
            return;
        }
    }

    private displayBlockToolbar(origin:ClickOrigin, $block:JQuery):DocumentManager {
        var toolbar = new Toolbar({
            origin: origin
        });

        // Edit button
        toolbar.addButton('default', new Button({
            name: 'edit',
            title: 'Edit',
            icon: 'pencil',
            event: 'edit-block',
            data: {$block: $block}
        }));
        toolbar.addButton('default', new Button({
            name: 'add',
            title: 'Add',
            icon: 'plus',
            event: 'add-block',
            data: {$block: $block}
        }));
        // Remove
        toolbar.addButton('default', new Button({
            name: 'remove',
            title: 'Remove',
            icon: 'remove',
            event: 'remove-block',
            data: {$block: $block}
        }));
        // Move left
        toolbar.addButton('move', new Button({
            name: 'move-left',
            title: 'Move left',
            icon: 'arrow-left',
            event: 'move-block-left',
            data: {$block: $block}
        }));
        // Move right
        toolbar.addButton('move', new Button({
            name: 'move-right',
            title: 'Move right',
            icon: 'arrow-right',
            event: 'move-block-right',
            data: {$block: $block}
        }));
        // Move top
        toolbar.addButton('move', new Button({
            name: 'move-up',
            title: 'Move up',
            icon: 'arrow-up',
            event: 'move-block-up',
            data: {$block: $block}
        }));
        // Move bottom
        toolbar.addButton('move', new Button({
            name: 'move-down',
            title: 'Move down',
            icon: 'arrow-down',
            event: 'move-block-down',
            data: {$block: $block}
        }));
        // Grow
        toolbar.addButton('resize', new Button({
            name: 'expand',
            title: 'Expand size',
            icon: 'expand',
            event: 'expand-block',
            data: {$block: $block}
        }));
        // Reduce
        toolbar.addButton('resize', new Button({
            name: 'compress',
            title: 'Compress size',
            icon: 'compress',
            event: 'compress-block',
            data: {$block: $block}
        }));


        this.toolbar = new ToolbarView<Toolbar>({
            model: toolbar
        });

        $(document).find('body').append(this.toolbar.render().$el);

        return this;
    }

    private displayRowToolbar(origin:ClickOrigin, $row:JQuery):DocumentManager {
        var toolbar = new Toolbar({
            origin: origin
        });

        // Move top
        toolbar.addButton('move', new Button({
            name: 'move-up',
            title: 'Move up',
            icon: 'arrow-up',
            event: 'move-block-up',
            data: {$row: $row}
        }));
        // Move bottom
        toolbar.addButton('move', new Button({
            name: 'move-down',
            title: 'Move down',
            icon: 'arrow-down',
            event: 'move-block-down',
            data: {$row: $row}
        }));
        // Grow
        toolbar.addButton('add', new Button({
            name: 'Add',
            title: 'Add after',
            icon: 'plus',
            event: 'add-row',
            data: {$row: $row}
        }));


        this.toolbar = new ToolbarView<Toolbar>({
            model: toolbar
        });

        $(document).find('body').append(this.toolbar.render().$el);

        return this;
    }

    private displayContainerToolbar(origin:ClickOrigin, $container:JQuery):DocumentManager {
        var toolbar = new Toolbar({
            origin: origin
        });

        // Move top
        toolbar.addButton('move', new Button({
            name: 'move-up',
            title: 'Move up',
            icon: 'arrow-up',
            event: 'move-block-up',
            data: {$container: $container}
        }));
        // Move bottom
        toolbar.addButton('move', new Button({
            name: 'move-down',
            title: 'Move down',
            icon: 'arrow-down',
            event: 'move-block-down',
            data: {$container: $container}
        }));
        // Grow
        toolbar.addButton('add', new Button({
            name: 'Add',
            title: 'Add after',
            icon: 'plus',
            event: 'add-row',
            data: {$container: $container}
        }));

        this.toolbar = new ToolbarView<Toolbar>({
            model: toolbar
        });

        $(document).find('body').append(this.toolbar.render().$el);
        return this;
    }

    /**
     * New document has been loaded in the viewport iFrame.
     *
     * @param doc
     */
    private onViewportLoad(doc:Document):DocumentManager {
        this.$document = $(doc);

        // Intercept anchors click
        this.$document.find('a[href]').off('click').on('click', (e:Event) => {
            e.preventDefault();
            e.stopPropagation();

            var anchor:HTMLAnchorElement = <HTMLAnchorElement>e.currentTarget;

            if (anchor.hostname !== this.hostname) {
                console.log('Attempt to navigate out of the website has been blocked.');
            } else {
                Dispatcher.trigger('document_manager.navigate', anchor.href);
            }
        });

        if (this.enabled) {
            this.enableEdition();
        }

        return this;
    }

    private onViewportUnload():DocumentManager {
        this.$document = null;

        return this;
    }

    private enableEdition():DocumentManager {
        if (!this.enabled || null === this.$document) {
            return;
        }

        if (0 == this.$document.find('link#cms-editor-stylesheet').length) {
            console.log('Document editor stylesheet not found.');
            var stylesheet:HTMLLinkElement = document.createElement('link');
            stylesheet.id = 'cms-editor-stylesheet';
            stylesheet.href = '/bundles/ekynacms/css/editor-document.css';
            stylesheet.type = 'text/css';
            stylesheet.rel = 'stylesheet';
            this.$document.find('head').append(stylesheet);
        } else {
            console.log('Document editor stylesheet found.');
        }

        this.$document.on('click', this.documentClickHandler);

        return this;
    }

    private disableEdition():DocumentManager {
        if (this.enabled || null === this.$document) {
            return;
        }

        // Remove toolbar if exists
        if (this.toolbar) {
            this.toolbar.remove();
        }

        this.$document.off('click', this.documentClickHandler);

        var $stylesheet:JQuery = this.$document.find('link#cms-editor-stylesheet');
        if ($stylesheet.length) {
            $stylesheet.remove();
        }

        return this;
    }
}
