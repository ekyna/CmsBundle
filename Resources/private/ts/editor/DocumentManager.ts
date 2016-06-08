/// <reference path="../../../../../../../typings/tsd.d.ts" />
/// <reference path="../../../../../../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/ts/router.d.ts" />


import $ = require('jquery');

declare var require:(moduleId:string) => any;
var Router:FOS.Router = require('routing');

import Dispatcher from './Dispatcher';
import {OriginInterface, Button, Toolbar, ToolbarView} from './Ui';

interface ElementAttributes {
    id: string;
    classes: string;
    data: Object;
}
interface BlockData {
    attributes: ElementAttributes;
    plugin_attributes: ElementAttributes;
    content: string;
}
interface RowData {
    attributes: ElementAttributes;
    blocks: Array<BlockData>;
}
interface ContainerData {
    attributes: ElementAttributes;
    rows: Array<RowData>;
}
interface ContentData {
    attributes: ElementAttributes;
    containers: Array<ContainerData>;
}

interface ResponseData {
    content?: ContentData;
    containers?: Array<ContainerData>;
    rows: Array<RowData>;
    blocks: Array<BlockData>;
}

class BaseManager {
    static $document:JQuery;

    static setDocument($document:JQuery):void {
        this.$document = $document;
    }

    static findElementById(id:string):JQuery {
        return this.$document.find('#' + id);
    }

    static createElement(id:string, $parent:JQuery):JQuery {
        if (!$parent) {
            throw "Undefined parent.";
        }
        return $('<div></div>').attr('id', id).appendTo($parent);
    }

    static findOrCreateElement(id:string, $parent:JQuery) {
        var $element = this.findElementById(id);
        if (0 == $element.length) {
            $element = this.createElement(id, $parent);
        }
        return $element;
    }

    static setElementAttributes($element:JQuery, attributes:ElementAttributes):void {
        $element
            .removeAttr('class').attr('class', attributes.classes)
            .removeAttr('data-cms').data('cms', attributes.data);
    }

    static sortChildren($element:JQuery) {
        let $children:JQuery = $element.children();
        $children.detach().get().sort(function(a, b) {
            let aPos = $(a).data('cms').position,
                bPos = $(b).data('cms').position;
            return (aPos == bPos) ? (aPos > bPos) ? 1 : -1 : 0;
        });
        $element.append($children);
    }


    static request(url:string) {
        $.ajax({
            url: url,
            method: 'POST'
        }).done(function(data:ResponseData) {
            if (data.hasOwnProperty('content')) {
                ContentManager.parse(data.content);
            } else if (data.hasOwnProperty('containers')) {
                ContainerManager.parse(data.containers);
            } else if (data.hasOwnProperty('rows')) {
                RowManager.parse(data.rows);
            } else if (data.hasOwnProperty('blocks')) {
                BlockManager.parse(data.blocks);
            }
            Dispatcher.trigger('base_manager.response_parsed');
        }).fail(function() {
            console.log('Editor request failed.');
        });
    }
}

class ContentManager {
    static parse(content:ContentData) {
        // TODO parse layout

        // Parse children
        if (content.hasOwnProperty('containers')) {
            ContainerManager.parse(content.containers);
        }

        // TODO reorder containers
        //BaseManager.sortChildren($content);
    }
}

class ContainerManager {
    static parse(containers:Array<ContainerData>) {
        containers.forEach((container:ContainerData) => {
            // TODO parse layout

            // Parse children
            if (container.hasOwnProperty('rows')) {
                RowManager.parse(container.rows);
            }

            // TODO reorder rows
            //BaseManager.sortChildren($container);
        });
    }
}

class RowManager {
    static parse(rows:Array<RowData>, $container?:JQuery) {
        rows.forEach((row:RowData) => {
            // Parse layout
            if (!row.hasOwnProperty('attributes')) {
                throw 'Unexpected row data';
            }
            var $row:JQuery = BaseManager.findOrCreateElement(row.attributes.id, $container);
            BaseManager.setElementAttributes($row, row.attributes);

            // Parse children
            if (row.hasOwnProperty('blocks')) {
                BlockManager.parse(row.blocks, $row);
            }

            // Reorder blocks
            BaseManager.sortChildren($row);
        });
    }
}

class BlockManager {
    static parse(blocks:Array<BlockData>, $row?:JQuery) {
        blocks.forEach((block:BlockData) => {
            // Parse layout
            if (!block.hasOwnProperty('attributes')) {
                throw 'Unexpected block data';
            }
            var $column:JQuery = BaseManager.findOrCreateElement(block.attributes.id, $row);
            BaseManager.setElementAttributes($column, block.attributes);

            // Parse block
            if (block.hasOwnProperty('plugin_attributes')) {
                var $block:JQuery = BaseManager.findOrCreateElement(block.plugin_attributes.id, $column);
                BaseManager.setElementAttributes($block, block.plugin_attributes);

                // Parse content
                if (block.hasOwnProperty('content')) {
                    $block.html(block.content);
                }
            }
        });
    }

    static edit($block:JQuery) {

    }

    static remove($block:JQuery) {

    }

    static add($block:JQuery) {
        var $row = $block.closest('.cms-row');
        if (1 != $row.length) {
            throw 'Block row not found.';
        }
        var id = $row.data('cms').id;
        if (!id) {
            throw 'Invalid id';
        }
        BaseManager.request(Router.generate('ekyna_cms_editor_row_create_block', {'rowId': id}));
    }

    static moveLeft($block:JQuery) {

    }

    static moveRight($block:JQuery) {

    }

    static moveUp($block:JQuery) {

    }

    static moveDown($block:JQuery) {

    }

    static expand($block:JQuery) {

    }

    static compress($block:JQuery) {

    }
}

Dispatcher.on('block.edit', (button:Button) => BlockManager.edit(button.get('data').$block));
Dispatcher.on('block.remove', (button:Button) => BlockManager.remove(button.get('data').$block));
Dispatcher.on('block.add', (button:Button) => BlockManager.add(button.get('data').$block));
Dispatcher.on('block.move-left', (button:Button) => BlockManager.moveLeft(button.get('data').$block));
Dispatcher.on('block.move-right', (button:Button) => BlockManager.moveRight(button.get('data').$block));
Dispatcher.on('block.move-up', (button:Button) => BlockManager.moveUp(button.get('data').$block));
Dispatcher.on('block.move-down', (button:Button) => BlockManager.moveDown(button.get('data').$block));
Dispatcher.on('block.expand', (button:Button) => BlockManager.expand(button.get('data').$block));
Dispatcher.on('block.compress', (button:Button) => BlockManager.compress(button.get('data').$block));



/**
 * DocumentManager
 */
export class DocumentManager {

    private hostname:string;

    private viewportOrigin:OriginInterface;
    private $document:JQuery;
    private selectionId:string;

    private enabled:boolean = false;
    private toolbar:ToolbarView<Toolbar>;

    private powerClickHandler:(button:Button) => void;
    private viewportLoadHandler:(doc:Document) => void;
    private viewportUnloadHandler:() => void;
    private documentClickHandler:(e:JQueryEventObject) => void;

    constructor(hostname:string) {
        this.hostname = hostname;

        this.viewportOrigin = {top: 50, left:0};
        this.$document = null;
        this.selectionId = null;

        this.powerClickHandler = (button:Button) => this.onPowerClick(button);
        this.viewportLoadHandler = (doc:Document) => this.onViewportLoad(doc);
        this.viewportUnloadHandler = () => this.onViewportUnload();
        this.documentClickHandler = (e:JQueryEventObject) => this.onDocumentClick(e);
    }

    initialize() {
        Dispatcher.on('controls.power.click', this.powerClickHandler);
        Dispatcher.on('viewport_iframe.load', this.viewportLoadHandler);
        Dispatcher.on('viewport_iframe.unload', this.viewportUnloadHandler);

        Dispatcher.on('viewport.resize', (origin:OriginInterface) => this.onViewportResize(origin));

        Dispatcher.on('base_manager.response_parsed', () => this.highlightSelection());
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

    private highlightSelection() {
        if (this.selectionId) {
            this.$document.find('#' + this.selectionId).addClass('selected');
        }
    }


    private onDocumentClick(e:JQueryEventObject):void {
        var origin:OriginInterface = {top: e.clientY, left: e.clientX},
            $target:JQuery = $(e.target),
            toolbar:Toolbar;

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
        // Clear selection
        if (this.selectionId) {
            this.$document.find('#' + this.selectionId).removeClass('selected');
            this.selectionId = null;
        }

        // Block test
        var $selection = $target.closest('.cms-block');
        if (1 == $selection.length) {
            console.log('Block selection');
            toolbar = this.createBlockToolbar(origin, $selection);
        } else {
            // Row test
            $selection = $target.closest('.cms-row');
            if (1 == $selection.length) {
                console.log('Row selection');
                toolbar = this.createRowToolbar(origin, $selection);
            } else {
                // Container test
                $selection = $target.closest('.cms-container');
                if (1 == $selection.length) {
                    console.log('Container selection');
                    toolbar = this.createContainerToolbar(origin, $selection);
                }
            }
        }
        // Store the selection id and highlight selection
        if ($selection.length) {
            this.selectionId = $selection.attr('id');
            this.highlightSelection();
        }

        // Create and render the toolbar view
        if (toolbar) {
            this.toolbar = new ToolbarView<Toolbar>({
                model: toolbar
            });

            $(document).find('body').append(
                this.toolbar.render().applyOriginOffset(this.viewportOrigin).$el
            );
        }
    }

    private createBlockToolbar(origin:OriginInterface, $block:JQuery):Toolbar {
        var toolbar = new Toolbar({
            origin: origin
        });

        // Edit button
        toolbar.addButton('default', new Button({
            name: 'edit',
            title: 'Edit',
            icon: 'pencil',
            event: 'block.edit',
            data: {$block: $block}
        }));
        toolbar.addButton('default', new Button({
            name: 'add',
            title: 'Add',
            icon: 'plus',
            event: 'block.add',
            data: {$block: $block}
        }));
        // Remove
        toolbar.addButton('default', new Button({
            name: 'remove',
            title: 'Remove',
            icon: 'remove',
            event: 'block.remove',
            data: {$block: $block}
        }));
        // Move left
        toolbar.addButton('move', new Button({
            name: 'move-left',
            title: 'Move left',
            icon: 'arrow-left',
            event: 'block.move-left',
            data: {$block: $block}
        }));
        // Move right
        toolbar.addButton('move', new Button({
            name: 'move-right',
            title: 'Move right',
            icon: 'arrow-right',
            event: 'document.move-right',
            data: {$block: $block}
        }));
        // Move top
        toolbar.addButton('move', new Button({
            name: 'move-up',
            title: 'Move up',
            icon: 'arrow-up',
            event: 'block.move-up',
            data: {$block: $block}
        }));
        // Move bottom
        toolbar.addButton('move', new Button({
            name: 'move-down',
            title: 'Move down',
            icon: 'arrow-down',
            event: 'block.move-down',
            data: {$block: $block}
        }));
        // Grow
        toolbar.addButton('resize', new Button({
            name: 'expand',
            title: 'Expand size',
            icon: 'expand',
            event: 'block.expand',
            data: {$block: $block}
        }));
        // Reduce
        toolbar.addButton('resize', new Button({
            name: 'compress',
            title: 'Compress size',
            icon: 'compress',
            event: 'block.compress',
            data: {$block: $block}
        }));

        return toolbar;
    }

    private createRowToolbar(origin:OriginInterface, $row:JQuery):Toolbar {
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

        return toolbar;
    }

    private createContainerToolbar(origin:OriginInterface, $container:JQuery):Toolbar {
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

        return toolbar;
    }

    /**
     * New document has been loaded in the viewport iFrame.
     *
     * @param doc
     */
    private onViewportLoad(doc:Document):DocumentManager {
        this.$document = $(doc);

        BaseManager.setDocument(this.$document);

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

        BaseManager.setDocument(null);

        return this;
    }

    private onViewportResize(origin:OriginInterface):DocumentManager {
        this.viewportOrigin = origin;

        if (this.toolbar) {
            this.toolbar.applyOriginOffset(origin);
        }

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
