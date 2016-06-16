/// <reference path="../../../../../../../typings/tsd.d.ts" />
/// <reference path="../../../../../../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/ts/router.d.ts" />

import $ = require('jquery');
import es6Promise = require('es6-promise');
es6Promise.polyfill();
var Promise = es6Promise.Promise;

declare var require:(modules:any, callback?:Function) => any;
//noinspection JSUnusedAssignment
var Router:FOS.Router = require('routing');

import Dispatcher from './dispatcher';
import {OffsetInterface, Button, Toolbar, ToolbarView} from './ui';
import {BasePlugin} from './plugin/base-plugin';

interface ElementAttributes {
    id: string
    type?: string
    classes: string
    data: Object
}
interface BlockData {
    attributes: ElementAttributes
    plugin_attributes: ElementAttributes
    content: string
}
interface RowData {
    attributes: ElementAttributes
    blocks: Array<BlockData>
}
interface ContainerData {
    attributes: ElementAttributes
    inner_attributes: ElementAttributes
    rows: Array<RowData>
}
interface ContentData {
    attributes: ElementAttributes
    containers: Array<ContainerData>
}

interface ResponseData {
    created?: string
    removed?: Array<string>
    content?: ContentData
    containers?: Array<ContainerData>
    rows: Array<RowData>
    blocks: Array<BlockData>
}

class BaseManager {
    static window:Window;

    static setWindow(win:Window):void {
        this.window = win;
    }

    static $document:JQuery;

    static setDocument($doc:JQuery):void {
        this.$document = $doc;
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
        $children.detach().get().sort(function (a, b) {
            let aPos = $(a).data('cms').position,
                bPos = $(b).data('cms').position;
            return (aPos == bPos) ? 0 : (aPos > bPos) ? 1 : -1;
        }).forEach(function (e:JQuery) {
            $element.append(e);
        });
    }

    static request(settings:JQueryAjaxSettings):JQueryXHR {
        settings = _.extend({
            method: 'POST'
        }, settings);

        var xhr = $.ajax(settings);
        xhr.done((data:ResponseData) => {
            // Remove elements by id
            if (data.hasOwnProperty('removed')) {
                data.removed.forEach((id:string) => {
                    this.$document.find('#' + id).remove();
                });
            }
            // Parse elements
            if (data.hasOwnProperty('content')) {
                ContentManager.parse(data.content);
            } else if (data.hasOwnProperty('containers')) {
                ContainerManager.parse(data.containers);
            } else if (data.hasOwnProperty('rows')) {
                RowManager.parse(data.rows);
            } else if (data.hasOwnProperty('blocks')) {
                BlockManager.parse(data.blocks);
            }
            // Dispatch response parsed
            Dispatcher.trigger(
                'base_manager.response_parsed',
                data.hasOwnProperty('created') ? data.created : undefined
            );
        });
        xhr.fail(function () {
            throw 'Editor request failed.';
        });

        return xhr;
    }
}

class ContentManager {
    static parse(content:ContentData) {
        // Parse layout
        if (!content.hasOwnProperty('attributes')) {
            throw 'Unexpected content data';
        }
        var $content:JQuery = BaseManager.findElementById(content.attributes.id);
        if (0 == $content.length) {
            throw 'Content not found.';
        }
        BaseManager.setElementAttributes($content, content.attributes);

        // Parse children
        if (content.hasOwnProperty('containers')) {
            ContainerManager.parse(content.containers, $content);
        }

        // Reorder containers
        BaseManager.sortChildren($content);
    }
}

class ContainerManager {
    static parse(containers:Array<ContainerData>, $content?:JQuery) {
        containers.forEach((container:ContainerData, i:number) => {
            // Parse layout
            if (!container.hasOwnProperty('attributes')) {
                throw 'Unexpected container data';
            }

            var $container:JQuery = BaseManager.findOrCreateElement(container.attributes.id, $content);
            BaseManager.setElementAttributes($container, container.attributes);

            // Inner container
            var $innerContainer:JQuery;
            if (container.hasOwnProperty('inner_attributes')) {
                $innerContainer = BaseManager.findOrCreateElement(container.inner_attributes.id, $container);
                BaseManager.setElementAttributes($innerContainer, container.inner_attributes);
            } else {
                $innerContainer = $container.find('> .cms-inner-container');
            }

            // Parse children
            if (container.hasOwnProperty('rows')) {
                RowManager.parse(container.rows, $innerContainer);
            }

            // Reorder rows
            BaseManager.sortChildren($innerContainer);

            // Sort containers if not made by the content manager.
            if (!$content && i == containers.length - 1) {
                BaseManager.sortChildren($container.closest('.cms-content'));
            }
        });
    }

    static request(route:string, $container:JQuery):JQueryXHR {
        var id = (<ElementAttributes>$container.data('cms')).id;
        if (!id) {
            throw 'Invalid id';
        }
        return BaseManager.request({
            url: Router.generate(route, {'containerId': id})
        });
    }

    static edit($container:JQuery) {
    }

    static remove($container:JQuery) {
        ContainerManager.request('ekyna_cms_editor_container_remove', $container);
    }

    static add($container:JQuery) {
        var $content = $container.closest('.cms-content');
        if (1 != $content.length) {
            throw 'Container content not found.';
        }
        var id = $content.data('cms').id;
        if (!id) {
            throw 'Invalid id';
        }
        BaseManager.request({
            url: Router.generate('ekyna_cms_editor_content_create_container', {'contentId': id})
        });
    }

    static moveUp($container:JQuery) {
        ContainerManager.request('ekyna_cms_editor_container_move_up', $container);
    }

    static moveDown($container:JQuery) {
        ContainerManager.request('ekyna_cms_editor_container_move_down', $container);
    }
}

class RowManager {
    static parse(rows:Array<RowData>, $container?:JQuery) {
        rows.forEach((row:RowData, i:number) => {
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

            // Sort rows if not made by the container manager.
            if (!$container && i == rows.length - 1) {
                BaseManager.sortChildren($row.closest('.cms-inner-container'));
            }
        });
    }

    static request(route:string, $row:JQuery):JQueryXHR {
        var id = (<ElementAttributes>$row.data('cms')).id;
        if (!id) {
            throw 'Invalid id';
        }
        return BaseManager.request({
            url: Router.generate(route, {'rowId': id})
        });
    }

    static edit($row:JQuery) {
    }

    static remove($row:JQuery) {
        RowManager.request('ekyna_cms_editor_row_remove', $row);
    }

    static add($row:JQuery) {
        var $container = $row.closest('.cms-container');
        if (1 != $container.length) {
            throw 'Row container not found.';
        }
        var id = $container.data('cms').id;
        if (!id) {
            throw 'Invalid id';
        }
        BaseManager.request({
            url: Router.generate('ekyna_cms_editor_container_create_row', {'containerId': id})
        });
    }

    static moveUp($row:JQuery) {
        RowManager.request('ekyna_cms_editor_row_move_up', $row);
    }

    static moveDown($row:JQuery) {
        RowManager.request('ekyna_cms_editor_row_move_down', $row);
    }
}

export class BlockManager {
    static parse(blocks:Array<BlockData>, $row?:JQuery) {
        blocks.forEach((block:BlockData, i:number) => {
            var $column:JQuery, $block:JQuery;

            // Parse layout
            if (block.hasOwnProperty('attributes')) {
                $column = BaseManager.findOrCreateElement(block.attributes.id, $row);
                BaseManager.setElementAttributes($column, block.attributes);
            }

            // Parse block
            if (block.hasOwnProperty('plugin_attributes')) {
                $block = BaseManager.findOrCreateElement(block.plugin_attributes.id, $column);
                BaseManager.setElementAttributes($block, block.plugin_attributes);

                // Parse content
                if (block.hasOwnProperty('content')) {
                    $block.html(block.content);
                }
            }

            // Sort columns if not made by the row manager.
            if (($block || $column) && !$row && i == blocks.length - 1) {
                if (!$column) {
                    $column = $block.closest('.cms-column');
                }
                BaseManager.sortChildren($column.closest('.cms-row'));
            }
        });
    }

    static request(route:string, $block:JQuery, settings?:JQueryAjaxSettings):JQueryXHR {
        var id = (<ElementAttributes>$block.data('cms')).id;
        if (!id) {
            throw 'Invalid id';
        }

        settings = settings || {};
        settings.url = Router.generate(route, {'blockId': id});

        return BaseManager.request(settings);
    }

    static edit($block:JQuery) {
        PluginManager.createBlockPlugin((<ElementAttributes>$block.data('cms')).type, $block);
    }

    static remove($block:JQuery) {
        BlockManager.request('ekyna_cms_editor_block_remove', $block);
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
        BaseManager.request({
            url: Router.generate('ekyna_cms_editor_row_create_block', {'rowId': id})
        });
    }

    static moveLeft($block:JQuery) {
        BlockManager.request('ekyna_cms_editor_block_move_left', $block);
    }

    static moveRight($block:JQuery) {
        BlockManager.request('ekyna_cms_editor_block_move_right', $block);
    }

    static moveUp($block:JQuery) {

    }

    static moveDown($block:JQuery) {

    }

    static expand($block:JQuery) {
        BlockManager.request('ekyna_cms_editor_block_expand', $block);
    }

    static compress($block:JQuery) {
        BlockManager.request('ekyna_cms_editor_block_compress', $block);
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

Dispatcher.on('row.edit', (button:Button) => RowManager.edit(button.get('data').$row));
Dispatcher.on('row.remove', (button:Button) => RowManager.remove(button.get('data').$row));
Dispatcher.on('row.add', (button:Button) => RowManager.add(button.get('data').$row));
Dispatcher.on('row.move-up', (button:Button) => RowManager.moveUp(button.get('data').$row));
Dispatcher.on('row.move-down', (button:Button) => RowManager.moveDown(button.get('data').$row));

Dispatcher.on('container.edit', (button:Button) => ContainerManager.edit(button.get('data').$container));
Dispatcher.on('container.remove', (button:Button) => ContainerManager.remove(button.get('data').$container));
Dispatcher.on('container.add', (button:Button) => ContainerManager.add(button.get('data').$container));
Dispatcher.on('container.move-up', (button:Button) => ContainerManager.moveUp(button.get('data').$container));
Dispatcher.on('container.move-down', (button:Button) => ContainerManager.moveDown(button.get('data').$container));

interface PluginConfig {
    name:string
    path:string
}

interface PluginRegistryConfig {
    block: Array<PluginConfig>
    container: Array<PluginConfig>
}

export class PluginManager {
    private static activePlugin:BasePlugin;
    private static registry:PluginRegistryConfig;

    static load(config:PluginRegistryConfig):void {
        this.registry = config;
    }

    static getActivePlugin():BasePlugin {
        if (!this.hasActivePlugin()) {
            throw 'Active plugin is not set';
        }
        return this.activePlugin;
    }

    static hasActivePlugin():boolean {
        return !!this.activePlugin;
    }

    static clearActivePlugin():Promise<any> {
        if (this.hasActivePlugin()) {
            return this.activePlugin
                .destroy()
                .then(() => {
                    this.activePlugin = null;
                });
        }
        return Promise.resolve();
    }

    static createBlockPlugin(type:string, $block:JQuery):void {
        if (!this.registry) {
            throw 'Plugins registry is not configured';
        }
        this.registry.block.forEach((config:PluginConfig) => {
            if (config.name === type) {
                require([config.path], (plugin:any) => {
                    this.activePlugin = new plugin($block, BaseManager.window);
                    this.activePlugin.edit();
                });
                return;
            }
        });
    }
}

class ToolbarManager {
    private static toolbar:ToolbarView<Toolbar>;

    static getToolbar():ToolbarView<Toolbar> {
        if (!this.hasToolbar()) {
            throw 'Toolbar is not set';
        }
        return this.toolbar;
    }

    static hasToolbar():boolean {
        return !!this.toolbar;
    }

    static clearToolbar():void {
        if (this.hasToolbar()) {
            this.toolbar.remove();
            this.toolbar = null;
        }
    }

    private static createToolbar(toolbar:Toolbar):void {
        this.clearToolbar();

        // Create and render the toolbar view
        this.toolbar = new ToolbarView<Toolbar>({
            model: toolbar
        });
        $(document).find('body').append(
            this.toolbar.render().$el
        );
    }

    static createBlockToolbar($block:JQuery, origin:OffsetInterface):void {
        var $column = $block.closest('.cms-column'),
            $row = $column.closest('.cms-row'),
            toolbar = new Toolbar({
                classes: ['vertical', 'block-toolbar'],
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
        if (1 == $row.length) {
            // Remove
            toolbar.addButton('default', new Button({
                name: 'remove',
                title: 'Remove',
                icon: 'remove',
                disabled: 1 >= $row.children('.cms-column').length,
                event: 'block.remove',
                data: {$block: $block}
            }));
            // Move left
            toolbar.addButton('horizontal', new Button({
                name: 'move-left',
                title: 'Move left',
                icon: 'arrow-left',
                disabled: $column.is(':first-child'),
                event: 'block.move-left',
                data: {$block: $block}
            }));
            // Move right
            toolbar.addButton('horizontal', new Button({
                name: 'move-right',
                title: 'Move right',
                icon: 'arrow-right',
                disabled: $column.is(':last-child'),
                event: 'block.move-right',
                data: {$block: $block}
            }));
            // Move top
            toolbar.addButton('vertical', new Button({
                name: 'move-up',
                title: 'Move up',
                icon: 'arrow-up',
                disabled: (function ($row:JQuery) {
                    var $prev = $row.prev('.cms-row');
                    return (0 == $prev.length) || (6 <= $prev.children('.cms-column').length); // TODO min size parameter
                })($row),
                event: 'block.move-up',
                data: {$block: $block}
            }));
            // Move bottom
            toolbar.addButton('vertical', new Button({
                name: 'move-down',
                title: 'Move down',
                icon: 'arrow-down',
                disabled: (function ($row:JQuery) {
                    var $next = $row.next('.cms-row');
                    return (0 == $next.length) || (6 <= $next.children('.cms-column').length); // TODO min size parameter
                })($row),
                event: 'block.move-down',
                data: {$block: $block}
            }));
            // Grow
            toolbar.addButton('resize', new Button({
                name: 'expand',
                title: 'Expand size',
                icon: 'expand',
                disabled: 6 <= $row.children('.cms-column').length, // TODO min size parameter
                event: 'block.expand',
                data: {$block: $block}
            }));
            // Reduce
            toolbar.addButton('resize', new Button({
                name: 'compress',
                title: 'Compress size',
                icon: 'compress',
                disabled: 2 >= parseInt($column.data('cms').size), // TODO min size parameter
                event: 'block.compress',
                data: {$block: $block}
            }));
            // Add
            toolbar.addButton('add', new Button({
                name: 'add',
                title: 'Create a new block',
                icon: 'plus',
                disabled: 6 <= $row.children('.cms-column').length, // TODO min size parameter
                event: 'block.add',
                data: {$block: $block}
            }));
        }

        this.createToolbar(toolbar);
    }

    static createRowToolbar($row:JQuery, origin:OffsetInterface):void {
        var $container:JQuery = $row.closest('.cms-inner-container'),
            toolbar = new Toolbar({
                classes: ['vertical', 'row-toolbar'],
                origin: origin
            });

        // Edit button
        toolbar.addButton('default', new Button({
            name: 'edit',
            title: 'Edit',
            icon: 'pencil',
            disabled: true,
            event: 'row.edit',
            data: {$row: $row}
        }));
        if (1 == $container.length) {
            // Remove
            toolbar.addButton('default', new Button({
                name: 'remove',
                title: 'Remove',
                icon: 'remove',
                disabled: 1 >= $container.children('.cms-row').length,
                event: 'row.remove',
                data: {$row: $row}
            }));
            // Move top
            toolbar.addButton('move', new Button({
                name: 'move-up',
                title: 'Move up',
                icon: 'arrow-up',
                disabled: $row.is(':first-child'),
                event: 'row.move-up',
                data: {$row: $row}
            }));
            // Move bottom
            toolbar.addButton('move', new Button({
                name: 'move-down',
                title: 'Move down',
                icon: 'arrow-down',
                disabled: $row.is(':last-child'),
                event: 'row.move-down',
                data: {$row: $row}
            }));
            // Add
            toolbar.addButton('add', new Button({
                name: 'add',
                title: 'Create a new row',
                icon: 'plus',
                event: 'row.add',
                data: {$row: $row}
            }));
        }

        this.createToolbar(toolbar);
    }

    static createContainerToolbar($container:JQuery, origin:OffsetInterface):void {
        var $content:JQuery = $container.closest('.cms-content'),
            toolbar = new Toolbar({
                classes: ['vertical', 'container-toolbar'],
                origin: origin
            });

        toolbar.addButton('default', new Button({
            name: 'edit',
            title: 'Edit',
            icon: 'pencil',
            disabled: true,
            event: 'container.edit',
            data: {$container: $container}
        }));
        if (1 == $content.length) {
            // Remove
            toolbar.addButton('default', new Button({
                name: 'remove',
                title: 'Remove',
                icon: 'remove',
                disabled: 1 >= $content.children('.cms-container').length,
                event: 'container.remove',
                data: {$container: $container}
            }));
            // Move top
            toolbar.addButton('move', new Button({
                name: 'move-up',
                title: 'Move up',
                icon: 'arrow-up',
                disabled: $container.is(':first-child'),
                event: 'container.move-up',
                data: {$container: $container}
            }));
            // Move bottom
            toolbar.addButton('move', new Button({
                name: 'move-down',
                title: 'Move down',
                icon: 'arrow-down',
                disabled: $container.is(':last-child'),
                event: 'container.move-down',
                data: {$container: $container}
            }));
            // Add
            toolbar.addButton('add', new Button({
                name: 'Add',
                title: 'Create a new container',
                icon: 'plus',
                event: 'container.add',
                data: {$container: $container}
            }));
        }

        this.createToolbar(toolbar);
    }
}


/**
 * DocumentManager
 */
export class DocumentManager {

    private hostname:string;

    private viewportOrigin:OffsetInterface;
    private selectionOffset:OffsetInterface;

    private selectionId:string;

    private enabled:boolean = false;

    /**
     * Store the click target between mouseDown and mouseUp handlers
     * @type {JQuery|null}
     */
    private $clickTarget:JQuery = null;
    private clickOrigin:OffsetInterface = null;
    private documentMouseDownHandler:(e:JQueryEventObject) => void;
    private documentMouseUpHandler:() => void;

    powerClickHandler:(button:Button) => void;
    viewportLoadHandler:(win:Window, doc:Document) => void;
    viewportUnloadHandler:(e:BeforeUnloadEvent) => void;
    viewportResizeHandler:(origin:OffsetInterface) => void;

    constructor(hostname:string) {
        this.hostname = hostname;

        this.viewportOrigin = {top: 50, left: 0}; // Editor relative : viewport top-left corner
        this.selectionOffset = {top: 0, left: 0}; // Document relative : offset between click origin and element top-left corner
        this.selectionId = null;

        this.documentMouseDownHandler = (e:JQueryEventObject) => this.onDocumentMouseDown(e);
        this.documentMouseUpHandler = () => this.onDocumentMouseUp();

        this.powerClickHandler = (button:Button) => this.onPowerClick(button);
        this.viewportLoadHandler = (win:Window, doc:Document) => this.onViewportLoad(win, doc);
        this.viewportUnloadHandler = (e:BeforeUnloadEvent) => this.onViewportUnload(e);
        this.viewportResizeHandler = (origin:OffsetInterface) => this.onViewportResize(origin);
    }

    initialize() {
        Dispatcher.on('viewport.resize', this.viewportResizeHandler);

        Dispatcher.on('base_manager.response_parsed', (selectionId?:string) => {
            var $element:JQuery;
            if (selectionId) {
                $element = BaseManager.findElementById(selectionId);
            } else if (this.selectionId) {
                $element = BaseManager.findElementById(this.selectionId);
            }
            this.deselect()
                .then(() => {
                    if ($element && $element.length == 1) {
                        this.select($element);
                    }
                });
        });

        Dispatcher.on('block.edit', () => ToolbarManager.clearToolbar());
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

    /**
     * New document has been loaded in the viewport iFrame.
     *
     * @param win
     * @param doc
     */
    private onViewportLoad(win:Window, doc:Document):DocumentManager {

        BaseManager.setWindow(win);
        BaseManager.setDocument($(doc));

        // Intercept anchors click
        BaseManager.$document.find('a[href]').off('click').on('click', (e:Event) => {
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

    private onViewportUnload(e:BeforeUnloadEvent):DocumentManager {
        if (e.defaultPrevented) {
            return;
        }

        // Cancel if active plugin is updated
        if (PluginManager.hasActivePlugin()) {
            if (PluginManager.getActivePlugin().isUpdated()) {
                e.preventDefault();
                e.returnValue = "Vos changements n'ont pas été sauvegardés !";
                return this;
            }
        }

        // Clear active plugin.
        PluginManager.clearActivePlugin();

        // Clear viewport window and document
        BaseManager.setWindow(null);
        BaseManager.setDocument(null);

        return this;
    }

    private onViewportResize(origin:OffsetInterface):DocumentManager {
        this.viewportOrigin = origin;

        if (ToolbarManager.hasToolbar()) {
            ToolbarManager.getToolbar().applyOriginOffset(origin);
        }

        return this;
    }

    private onDocumentMouseDown(e:JQueryEventObject):void {
        //console.log('onDocumentMouseDown');
        this.$clickTarget = null;
        this.clickOrigin = null;

        var origin: OffsetInterface = {top: e.clientY, left: e.clientX},
            $target:JQuery = $(e.target);

        // Do nothing on toolbars click
        if (0 < $target.closest('#editor-document-toolbar').length) {
            return;
        }
        // Active plugin test
        if (PluginManager.hasActivePlugin()) {
            if (PluginManager.getActivePlugin().preventDocumentSelection($target)) {
                return;
            }
        }

        var $element:JQuery = $target.closest('.cms-block, .cms-row, .cms-container');
        if (1 == $element.length) {
            if ($element.attr('id') != this.selectionId) {
                this.clickOrigin = origin;
                this.$clickTarget = $element;
            }
        } else {
            this.clickOrigin = origin;
        }
    }

    private onDocumentMouseUp():void {
        //console.log('onDocumentMouseUp');
        if (this.clickOrigin) {
            this.deselect()
                .then(() => {
                    if (this.$clickTarget) {
                        this.select(this.$clickTarget, this.clickOrigin);
                    } else {
                        this.createToolbar();
                    }
                    this.$clickTarget = null;
                    this.clickOrigin = null;
                });
        }
    }

    private deselect():Promise<any> {
        return PluginManager
            .clearActivePlugin()
            .then(() => {
                // Clear toolbar
                ToolbarManager.clearToolbar();

                // Remove selection highlight
                if (this.selectionId) {
                    BaseManager.findElementById(this.selectionId).removeClass('selected');
                    this.selectionId = null;
                }
            });
    }

    private select($element:JQuery, origin?:OffsetInterface):void {
        if (1 != $element.length) {
            return;
        }

        this.selectionId = $element.addClass('selected').attr('id');

        this.createToolbar($element, origin);
    }

    private createToolbar($element?:JQuery, origin?:OffsetInterface):void {
        $element = $element || BaseManager.findElementById(this.selectionId);
        if (1 != $element.length) {
            return;
        }

        if (origin) {
            this.selectionOffset = {
                top: (origin.top - $element.offset().top),
                left: (origin.left - $element.offset().left)
            };
        } else {
            origin = {
                top: ($element.offset().top + this.selectionOffset.top),
                left: ($element.offset().left + this.selectionOffset.left)
            }
        }

        if ($element.hasClass('cms-block')) {
            ToolbarManager.createBlockToolbar($element, origin);
        } else if ($element.hasClass('cms-row')) {
            ToolbarManager.createRowToolbar($element, origin);
        } else if ($element.hasClass('cms-container')) {
            ToolbarManager.createContainerToolbar($element, origin);
        } else {
            throw 'Unexpected element';
        }

        ToolbarManager.getToolbar().applyOriginOffset(this.viewportOrigin);
    }

    private enableEdition():DocumentManager {
        var $document = BaseManager.$document;

        if (!this.enabled || null === $document) {
            return;
        }

        if (0 == $document.find('link#cms-editor-stylesheet').length) {
            var stylesheet:HTMLLinkElement = document.createElement('link');
            stylesheet.id = 'cms-editor-stylesheet';
            stylesheet.href = '/bundles/ekynacms/css/editor-document.css';
            stylesheet.type = 'text/css';
            stylesheet.rel = 'stylesheet';
            $document.find('head').append(stylesheet);
        }

        $document.on('mousedown', this.documentMouseDownHandler);
        $document.on('mouseup', this.documentMouseUpHandler);

        return this;
    }

    private disableEdition():DocumentManager {
        var $document = BaseManager.$document;

        if (this.enabled || null === $document) {
            return;
        }

        this.deselect();

        $document.off('mousedown', this.documentMouseDownHandler);
        $document.off('mouseup', this.documentMouseUpHandler);

        var $stylesheet:JQuery = $document.find('link#cms-editor-stylesheet');
        if ($stylesheet.length) {
            $stylesheet.remove();
        }

        return this;
    }
}
