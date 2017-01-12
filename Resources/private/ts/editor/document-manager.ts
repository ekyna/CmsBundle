/// <reference path="../../../../../../../typings/index.d.ts" />

import * as $ from 'jquery';
import * as _ from 'underscore';
import * as es6Promise from 'es6-promise';
import * as Router from 'routing';

import Dispatcher from './dispatcher';
import {Util, OffsetInterface, Button, ButtonChoiceConfig, Toolbar, ToolbarView} from './ui';
import {SizeInterface, ResizeEventData} from "./viewport";
import {BasePlugin} from './plugin/base-plugin';
import RouteParams = FOS.RouteParams;


es6Promise.polyfill();
let Promise = es6Promise.Promise;


const DEFAULT_WIDGET_ACTIONS = {
    edit: false,
    change_type: false,
};

const DEFAULT_BLOCK_ACTIONS = {
    edit: false,
    change_type: false,
    move_left: false,
    move_right: false,
    move_up: false,
    move_down: false,
    pull: false,
    push: false,
    offset_left: false,
    offset_right: false,
    compress: false,
    expand: false,
    add: false,
    remove: false
};

export interface ElementData {
    id: string
    type: string
    position: number
    actions: {[key: string]: any}
}

export interface ElementAttributes {
    data: ElementData
    [key: string]: any
}
interface WidgetData {
    attributes: ElementAttributes
    content: string
}
interface BlockData {
    attributes: ElementAttributes
    widgets: Array<WidgetData>
}
interface RowData {
    attributes: ElementAttributes
    blocks: Array<BlockData>
}
interface ContainerData {
    attributes: ElementAttributes
    inner_attributes: ElementAttributes
    content: string
    rows: Array<RowData>
}
interface ContentData {
    attributes: ElementAttributes
    containers: Array<ContainerData>
}
export interface DocumentData {
    locale: string
    id?: string
}

interface ResponseData {
    created?: string
    removed?: Array<string>
    content?: ContentData
    containers?: Array<ContainerData>
    rows: Array<RowData>
    blocks: Array<BlockData>
}

export class BaseManager {
    private static contentWindow: Window;

    static setContentWindow(win: Window): void {
        this.contentWindow = win;
    }

    static getContentWindow(): Window {
        if (!this.contentWindow) {
            throw 'Window is not defined.';
        }
        return this.contentWindow;
    }

    private static $contentDocument: JQuery;

    static setContentDocument($doc: JQuery): void {
        this.$contentDocument = $doc;

        let data: DocumentData = $doc.find('html').data('cms-editor-document');
        if (!data) {
            throw "Undefined document data.\n" +
            "Did you forget to use the cms_document_data() twig function in your template ?";
        }

        this.setDocumentData(data);
    }

    static getContentDocument(): JQuery {
        if (!this.$contentDocument) {
            throw 'Document is not defined.';
        }
        return this.$contentDocument;
    }

    private static documentData: DocumentData;

    static setDocumentData(data: DocumentData): void {
        this.documentData = data;
    }

    static getDocumentData(): DocumentData {
        return this.documentData;
    }

    static getDocumentLocale(): string {
        if (!this.documentData) {
            throw 'Content data is not defined.';
        }
        return this.documentData.locale;
    }

    static clear(): void {
        this.contentWindow = null;
        this.$contentDocument = null;
        this.documentData = null;
    }

    static findElementById(id: string): JQuery {
        return this.$contentDocument.find('#' + id);
    }

    static createElement(id: string, $parent: JQuery): JQuery {
        if (!$parent) {
            throw "Undefined parent.";
        }

        return $('<div></div>').attr('id', id).appendTo($parent);
    }

    static findOrCreateElement(id: string, $parent: JQuery) {
        let $element = this.findElementById(id);
        if (0 == $element.length) {
            $element = this.createElement(id, $parent);
        }
        return $element;
    }

    static setElementAttributes($element: JQuery, attributes: ElementAttributes): void {
        for (let key in attributes) {
            if (key == 'data') {
                $element.removeAttr('data-cms').data('cms', attributes[key]);
                continue;
            }
            $element.removeAttr(key).attr(key, attributes[key]);
        }
    }

    static sortChildren($element: JQuery) {
        let $children: JQuery = $element.children();
        $children.detach().get().sort(function (a, b) {
            let aPos = $(a).data('cms').position,
                bPos = $(b).data('cms').position;
            return (aPos == bPos) ? 0 : (aPos > bPos) ? 1 : -1;
        }).forEach(function (e: JQuery) {
            $element.append(e);
        });
    }

    static generateUrl(route: string, params?: RouteParams) {
        return Router.generate(route, _.extend({}, params || {}, {
            _document_locale: BaseManager.getDocumentLocale()
        }));
    }

    static request(settings: JQueryAjaxSettings): JQueryXHR {
        Dispatcher.trigger('editor.set_busy');

        settings = _.extend({}, settings, {
            method: 'POST'
        });

        if (!settings.data) {
            settings.data = {};
        }
        settings.data.cms_viewport_width = BaseManager.getContentWindow().innerWidth;

        let xhr = $.ajax(settings);
        xhr.done((data: ResponseData) => {
            // Remove elements by id
            if (data.hasOwnProperty('removed')) {
                data.removed.forEach((id: string) => {
                    this.$contentDocument.find('#' + id).remove();
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
            let event:SelectionEvent = new SelectionEvent();
            if (data.hasOwnProperty('created')) {
                event.$element = BaseManager.findElementById(data.created);
            }
            Dispatcher.trigger('base_manager.response_parsed', event);
        });
        xhr.fail(function () {
            throw 'Editor request failed.';
        });
        xhr.always(function () {
            Dispatcher.trigger('editor.unset_busy');
        });

        return xhr;
    }
}

export class ContentManager {
    static parse(content: ContentData) {
        // Parse layout
        if (!content.hasOwnProperty('attributes')) {
            throw 'Unexpected content data';
        }
        let $content: JQuery = BaseManager.findElementById(content.attributes['id']);
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

    static generateUrl($content: JQuery, route: string, params?: RouteParams) {
        let id = (<ElementData>$content.data('cms')).id;
        if (!id) {
            throw 'Invalid id';
        }
        return BaseManager.generateUrl(route, _.extend({}, params || {}, {
            contentId: id
        }));
    }

    static request($container: JQuery, route: string, params?: RouteParams, settings?: JQueryAjaxSettings): JQueryXHR {
        settings = settings || {};
        settings.url = this.generateUrl($container, route, params);

        return BaseManager.request(settings);
    }
}

export class ContainerManager {
    static parse(containers: Array<ContainerData>, $content?: JQuery) {
        containers.forEach((container: ContainerData, i: number) => {
            // Parse layout
            if (!container.hasOwnProperty('attributes')) {
                throw 'Unexpected container data';
            }

            let $container: JQuery = BaseManager.findOrCreateElement(container.attributes['id'], $content);
            BaseManager.setElementAttributes($container, container.attributes);

            // Parse content
            let content: string = container.hasOwnProperty('content') ? container.content : null;
            if (content && 0 < content.length) {
                $container.html(container.content);
            } else {
                // Inner container
                let $innerContainer: JQuery;
                if (container.hasOwnProperty('inner_attributes')) {
                    $innerContainer = BaseManager.findOrCreateElement(container.inner_attributes['id'], $container);
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
            }
        });
    }

    static generateUrl($container: JQuery, route: string, params?: RouteParams) {
        let id = (<ElementData>$container.data('cms')).id;
        if (!id) {
            throw 'Invalid id';
        }
        return BaseManager.generateUrl(route, _.extend({}, params || {}, {
            containerId: id
        }));
    }

    static request($container: JQuery, route: string, params?: RouteParams, settings?: JQueryAjaxSettings): JQueryXHR {
        settings = settings || {};
        settings.url = this.generateUrl($container, route, params);

        return BaseManager.request(settings);
    }

    static edit($container: JQuery) {
        PluginManager.createContainerPlugin((<ElementData>$container.data('cms')).type, $container);
    }

    static changeType($container: JQuery, type: string) {
        ContainerManager.request($container, 'ekyna_cms_editor_container_change_type', null, {data: {type: type}});
    }

    static remove($container: JQuery) {
        ContainerManager.request($container, 'ekyna_cms_editor_container_remove');
    }

    static add($container: JQuery, type: string) {
        let $content = $container.closest('.cms-content');
        if (1 != $content.length) {
            throw 'Container content not found.';
        }
        ContentManager.request($content, 'ekyna_cms_editor_content_create_container', null, {data: {type: type}});
    }

    static moveUp($container: JQuery) {
        ContainerManager.request($container, 'ekyna_cms_editor_container_move_up');
    }

    static moveDown($container: JQuery) {
        ContainerManager.request($container, 'ekyna_cms_editor_container_move_down');
    }
}

export class RowManager {
    static parse(rows: Array<RowData>, $container?: JQuery) {
        rows.forEach((row: RowData, i: number) => {
            // Parse layout
            if (!row.hasOwnProperty('attributes')) {
                throw 'Unexpected row data';
            }
            let $row: JQuery = BaseManager.findOrCreateElement(row.attributes['id'], $container);
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

    static generateUrl($row: JQuery, route: string, params?: RouteParams) {
        let id = (<ElementData>$row.data('cms')).id;
        if (!id) {
            throw 'Invalid id';
        }
        return BaseManager.generateUrl(route, _.extend({}, params || {}, {
            rowId: id
        }));
    }

    static request($row: JQuery, route: string, params?: RouteParams, settings?: JQueryAjaxSettings): JQueryXHR {
        settings = settings || {};
        settings.url = this.generateUrl($row, route, params);

        return BaseManager.request(settings);
    }

    static remove($row: JQuery) {
        RowManager.request($row, 'ekyna_cms_editor_row_remove');
    }

    static add($row: JQuery) {
        let $container = $row.closest('.cms-container');
        if (1 != $container.length) {
            throw 'Row container not found.';
        }
        ContainerManager.request($container, 'ekyna_cms_editor_container_create_row');
    }

    static moveUp($row: JQuery) {
        RowManager.request($row, 'ekyna_cms_editor_row_move_up');
    }

    static moveDown($row: JQuery) {
        RowManager.request($row, 'ekyna_cms_editor_row_move_down');
    }
}

export class BlockManager {
    static parse(blocks: Array<BlockData>, $row?: JQuery) {
        blocks.forEach((block: BlockData, i: number) => {
            let $block: JQuery,
                $widget: JQuery;

            // Block layout
            if (block.hasOwnProperty('attributes')) {
                $block = BaseManager.findOrCreateElement(block.attributes['id'], $row);
                BaseManager.setElementAttributes($block, block.attributes);
            }

            // Parse children
            if (block.hasOwnProperty('widgets')) {
                block.widgets.forEach((widget: WidgetData) => {
                    // Parse block
                    if (widget.hasOwnProperty('attributes')) {
                        $widget = BaseManager.findOrCreateElement(widget.attributes['id'], $block);
                        BaseManager.setElementAttributes($widget, widget.attributes);

                        // Parse content
                        if (widget.hasOwnProperty('content')) {
                            $widget.html(widget.content);
                        }
                    }
                });
            }

            // Sort blocks if not made by the row manager.
            if ($block && !$row && i == blocks.length - 1) {
                BaseManager.sortChildren($block.closest('.cms-row'));
            }
        });
    }

    static generateUrl($block: JQuery, route: string, params?: RouteParams) {
        let data:ElementData = <ElementData>$block.data('cms');
        if (!data.id) {
            throw 'Invalid id';
        }
        return BaseManager.generateUrl(route, _.extend({}, params || {}, {
            blockId: data.id,
            widgetType: data.type,
        }));
    }

    static request($block: JQuery, route: string, params?: RouteParams, settings?: JQueryAjaxSettings): JQueryXHR {
        settings = settings || {};
        settings.url = this.generateUrl($block, route, params);

        return BaseManager.request(settings);
    }

    static edit($block: JQuery) {
        PluginManager.createBlockPlugin((<ElementData>$block.data('cms')).type, $block);
    }

    static changeType($block: JQuery, type: string) {
        BlockManager.request($block, 'ekyna_cms_editor_block_change_type', null, {data: {type: type}});
    }

    static remove($block: JQuery) {
        BlockManager.request($block, 'ekyna_cms_editor_block_remove');
    }

    static add($block: JQuery, type: string) {
        let $row = $block.closest('.cms-row');
        if (1 != $row.length) {
            throw 'Block row not found.';
        }
        RowManager.request($row, 'ekyna_cms_editor_row_create_block', null, {data: {type: type}});
    }

    static moveUp($block: JQuery) {
        BlockManager.request($block, 'ekyna_cms_editor_block_move_up');
    }

    static moveDown($block: JQuery) {
        BlockManager.request($block, 'ekyna_cms_editor_block_move_left');
    }

    static moveLeft($block: JQuery) {
        BlockManager.request($block, 'ekyna_cms_editor_block_move_left');
    }

    static moveRight($block: JQuery) {
        BlockManager.request($block, 'ekyna_cms_editor_block_move_right');
    }

    static pull($block: JQuery) {
        BlockManager.request($block, 'ekyna_cms_editor_block_pull');
    }

    static push($block: JQuery) {
        BlockManager.request($block, 'ekyna_cms_editor_block_push');
    }

    static offsetLeft($block: JQuery) {
        BlockManager.request($block, 'ekyna_cms_editor_block_offset_left');
    }

    static offsetRight($block: JQuery) {
        BlockManager.request($block, 'ekyna_cms_editor_block_offset_right');
    }

    static expand($block: JQuery) {
        BlockManager.request($block, 'ekyna_cms_editor_block_expand');
    }

    static compress($block: JQuery) {
        BlockManager.request($block, 'ekyna_cms_editor_block_compress');
    }
}

/**
 * Block events
 */
Dispatcher.on('block.edit',
    (button: Button) => BlockManager.edit(button.get('data').$block)
);
Dispatcher.on('block.change-type',
    (button: Button, choice: ButtonChoiceConfig) =>
        BlockManager.changeType(button.get('data').$block, choice.data.type)
);
Dispatcher.on('block.move-up',
    (button: Button) => BlockManager.moveUp(button.get('data').$block)
);
Dispatcher.on('block.move-down',
    (button: Button) => BlockManager.moveDown(button.get('data').$block)
);
Dispatcher.on('block.move-left',
    (button: Button) => BlockManager.moveLeft(button.get('data').$block)
);
Dispatcher.on('block.move-right',
    (button: Button) => BlockManager.moveRight(button.get('data').$block)
);
Dispatcher.on('block.pull',
    (button: Button) => BlockManager.pull(button.get('data').$block)
);
Dispatcher.on('block.push',
    (button: Button) => BlockManager.push(button.get('data').$block)
);
Dispatcher.on('block.offset-left',
    (button: Button) => BlockManager.offsetLeft(button.get('data').$block)
);
Dispatcher.on('block.offset-right',
    (button: Button) => BlockManager.offsetRight(button.get('data').$block)
);
Dispatcher.on('block.compress',
    (button: Button) => BlockManager.compress(button.get('data').$block)
);
Dispatcher.on('block.expand',
    (button: Button) => BlockManager.expand(button.get('data').$block)
);
Dispatcher.on('block.remove',
    (button: Button) => BlockManager.remove(button.get('data').$block)
);
Dispatcher.on('block.add',
    (button: Button, choice: ButtonChoiceConfig) =>
        BlockManager.add(button.get('data').$block, choice.data.type)
);

/**
 * Row events
 */
Dispatcher.on('row.move-up',
    (button: Button) => RowManager.moveUp(button.get('data').$row)
);
Dispatcher.on('row.move-down',
    (button: Button) => RowManager.moveDown(button.get('data').$row)
);
Dispatcher.on('row.remove',
    (button: Button) => RowManager.remove(button.get('data').$row)
);
Dispatcher.on('row.add',
    (button: Button) => RowManager.add(button.get('data').$row)
);

/**
 * Container events
 */
Dispatcher.on('container.edit',
    (button: Button) => ContainerManager.edit(button.get('data').$container)
);
Dispatcher.on('container.change-type',
    (button: Button, choice: ButtonChoiceConfig) =>
        ContainerManager.changeType(button.get('data').$container, choice.data.type)
);
Dispatcher.on('container.move-up',
    (button: Button) => ContainerManager.moveUp(button.get('data').$container)
);
Dispatcher.on('container.move-down',
    (button: Button) => ContainerManager.moveDown(button.get('data').$container)
);
Dispatcher.on('container.remove',
    (button: Button) => ContainerManager.remove(button.get('data').$container)
);
Dispatcher.on('container.add',
    (button: Button, choice: ButtonChoiceConfig) =>
        ContainerManager.add(button.get('data').$container, choice.data.type)
);


interface PluginInterface {
    new($element: JQuery, window: Window): BasePlugin
    setup(): Promise<void>
    tearDown(): Promise<void>
}

interface PluginConfig {
    name: string
    title: string
    path: string
}

export interface PluginRegistryConfig {
    block: Array<PluginConfig>
    container: Array<PluginConfig>
}

export class PluginManager {
    private static activePlugin: BasePlugin;
    private static registry: PluginRegistryConfig;

    // TODO store plugins after (requirejs) loading and call setup()

    // TODO call tearDown on all stored plugins when viewport unload

    static load(config: PluginRegistryConfig): void {
        this.registry = config;
    }

    static getActivePlugin(): BasePlugin {
        if (!this.hasActivePlugin()) {
            throw 'Active plugin is not set';
        }
        return this.activePlugin;
    }

    static hasActivePlugin(): boolean {
        return !!this.activePlugin;
    }

    static clearActivePlugin(): Promise<any> {
        if (this.hasActivePlugin()) {
            return this.activePlugin
                .destroy()
                .then(() => {
                    this.activePlugin = null;
                });
        }
        return Promise.resolve();
    }

    private static createPlugin(registry: Array<PluginConfig>, type: string, $element: JQuery) {
        this.clearActivePlugin()
            .then(() => {
                registry.forEach((config: PluginConfig) => {
                    if (config.name === type) {
                        require([config.path], (plugin: PluginInterface) => {
                            this.activePlugin = new plugin($element, BaseManager.getContentWindow());
                            this.activePlugin.edit();
                        });
                        return;
                    }
                });
                throw 'Plugin "' + type + '" not found.';
            });
    }

    static createBlockPlugin(type: string, $block: JQuery): void {
        this.createPlugin(this.getBlockPluginsConfig(), type, $block);
    }

    static createContainerPlugin(type: string, $container: JQuery): void {
        this.createPlugin(this.getContainerPluginsConfig(), type, $container);
    }

    static getBlockPluginsConfig(): Array<PluginConfig> {
        if (!this.registry) {
            throw 'Plugins registry is not configured';
        }
        return this.registry.block;
    }

    static getContainerPluginsConfig(): Array<PluginConfig> {
        if (!this.registry) {
            throw 'Plugins registry is not configured';
        }
        return this.registry.container;
    }
}

class ToolbarManager {
    private static toolbar: ToolbarView<Toolbar>;

    static getToolbar(): ToolbarView<Toolbar> {
        if (!this.hasToolbar()) {
            throw 'Toolbar is not set';
        }
        return this.toolbar;
    }

    static hasToolbar(): boolean {
        return !!this.toolbar;
    }

    static clearToolbar(): void {
        if (this.hasToolbar()) {
            this.toolbar.remove();
            this.toolbar = null;
        }
    }

    private static createToolbar(toolbar: Toolbar): void {
        this.clearToolbar();

        // Create and render the toolbar view
        this.toolbar = new ToolbarView<Toolbar>({
            model: toolbar
        });
        $(document).find('body').append(this.toolbar.$el);
        this.toolbar.render();
    }

    static createWidgetToolbar(e:SelectionEvent): void {
        let $widget = e.$element,
            toolbar = new Toolbar({
                classes: ['vertical', 'widget-toolbar'],
                origin: e.origin
            });

        let actions = _.extend(
            DEFAULT_WIDGET_ACTIONS,
            $widget.data('cms').actions
        );

        // Edit button
        toolbar.addControl('default', new Button({
            name: 'edit',
            title: 'Edit',
            icon: 'pencil',
            disabled: !actions.edit,
            event: 'block.edit',
            data: {$block: $widget}
        }));
        // Change type button
        let choices: Array<ButtonChoiceConfig> = [];
        PluginManager.getBlockPluginsConfig().forEach(function (config: PluginConfig) {
            choices.push({
                name: config.name,
                title: config.title,
                confirm: 'Êtes-vous sûr de vouloir changer le type de ce bloc ? (Le contenu actuel sera définitivement perdu).',
                data: {type: config.name}
            });
        });
        toolbar.addControl('default', new Button({
            name: 'change-type',
            title: 'Change type',
            icon: 'cog',
            disabled: !actions.change_type,
            event: 'block.change-type',
            data: {$block: $widget},
            choices: choices
        }));

        this.createToolbar(toolbar);
    }

    static createBlockToolbar(e:SelectionEvent): void {
        let $block = e.$element,
            $row = $block.closest('.cms-row'),
            toolbar = new Toolbar({
                classes: ['vertical', 'block-toolbar'],
                origin: e.origin
            });

        let actions = _.extend(
            DEFAULT_BLOCK_ACTIONS,
            $block.data('cms').actions,
            $block.length ? $block.data('cms').actions : {}
        );

        // Edit button
        toolbar.addControl('default', new Button({
            name: 'edit',
            title: 'Edit',
            icon: 'pencil',
            disabled: !actions.edit,
            event: 'block.edit',
            data: {$block: $block}
        }));
        // Change type button
        let choices: Array<ButtonChoiceConfig> = [];
        PluginManager.getBlockPluginsConfig().forEach(function (config: PluginConfig) {
            choices.push({
                name: config.name,
                title: config.title,
                confirm: 'Êtes-vous sûr de vouloir changer le type de ce bloc ? (Le contenu actuel sera définitivement perdu).',
                data: {type: config.name}
            });
        });
        toolbar.addControl('default', new Button({
            name: 'change-type',
            title: 'Change type',
            icon: 'cog',
            disabled: !actions.change_type,
            event: 'block.change-type',
            data: {$block: $block},
            choices: choices
        }));

        if (1 == $row.length) {
            // Move top
            toolbar.addControl('vertical', new Button({
                name: 'move-up',
                title: 'Move up',
                icon: 'arrow-up',
                disabled: !actions.move_up,
                event: 'block.move-up',
                data: {$block: $block}
            }));
            // Move bottom
            toolbar.addControl('vertical', new Button({
                name: 'move-down',
                title: 'Move down',
                icon: 'arrow-down',
                disabled: !actions.move_down,
                event: 'block.move-down',
                data: {$block: $block}
            }));

            // Move left
            toolbar.addControl('horizontal', new Button({
                name: 'move-left',
                title: 'Move left',
                icon: 'arrow-left',
                disabled: !actions.move_left,
                event: 'block.move-left',
                data: {$block: $block}
            }));
            // Move right
            toolbar.addControl('horizontal', new Button({
                name: 'move-right',
                title: 'Move right',
                icon: 'arrow-right',
                disabled: !actions.move_right,
                event: 'block.move-right',
                data: {$block: $block}
            }));

            // Pull
            toolbar.addControl('ordering', new Button({
                name: 'pull',
                title: 'Pull',
                icon: 'arrow-left',
                disabled: !actions.pull,
                event: 'block.pull',
                data: {$block: $block}
            }));
            // Push
            toolbar.addControl('ordering', new Button({
                name: 'push',
                title: 'Push',
                icon: 'arrow-right',
                disabled: !actions.push,
                event: 'block.push',
                data: {$block: $block}
            }));

            // Offset left
            toolbar.addControl('offset', new Button({
                name: 'offset-left',
                title: 'Offset left',
                icon: 'arrow-left',
                disabled: !actions.offset_left,
                event: 'block.offset-left',
                data: {$block: $block}
            }));
            // Offset right
            toolbar.addControl('offset', new Button({
                name: 'offset-right',
                title: 'Offset right',
                icon: 'arrow-right',
                disabled: !actions.offset_right,
                event: 'block.offset-right',
                data: {$block: $block}
            }));

            // Compress
            toolbar.addControl('resize', new Button({
                name: 'compress',
                title: 'Compress size',
                icon: 'compress',
                disabled: !actions.compress,
                event: 'block.compress',
                data: {$block: $block}
            }));
            // Expand
            toolbar.addControl('resize', new Button({
                name: 'expand',
                title: 'Expand size',
                icon: 'expand',
                disabled: !actions.expand,
                event: 'block.expand',
                data: {$block: $block}
            }));

            // Remove
            toolbar.addControl('add', new Button({
                name: 'remove',
                title: 'Remove',
                icon: 'remove',
                disabled: !actions.remove,
                confirm: 'Êtes-vous sûr de vouloir supprimer ce bloc ?',
                event: 'block.remove',
                data: {$block: $block}
            }));
            // Add
            choices = [];
            PluginManager.getBlockPluginsConfig().forEach(function (config: PluginConfig) {
                choices.push({
                    name: config.name,
                    title: config.title,
                    data: {type: config.name}
                });
            });
            toolbar.addControl('add', new Button({
                name: 'add',
                title: 'Create a new block after this one',
                icon: 'plus',
                disabled: !actions.add,
                event: 'block.add',
                data: {$block: $block},
                choices: choices,
            }));
        }

        this.createToolbar(toolbar);
    }

    static createRowToolbar(e:SelectionEvent): void {
        let $row = e.$element,
            $container: JQuery = $row.closest('.cms-inner-container'),
            toolbar = new Toolbar({
                classes: ['vertical', 'row-toolbar'],
                origin: e.origin
            });

        // Edit button
        /*toolbar.addButton('default', new Button({
         name: 'edit',
         title: 'Edit',
         icon: 'pencil',
         disabled: true,
         event: 'row.edit',
         data: {$row: $row}
         }));*/
        if (0 == $container.length) {
            return;
        }
        // Move top
        toolbar.addControl('move', new Button({
            name: 'move-up',
            title: 'Move up',
            icon: 'arrow-up',
            disabled: $row.is(':first-child'),
            event: 'row.move-up',
            data: {$row: $row}
        }));
        // Move bottom
        toolbar.addControl('move', new Button({
            name: 'move-down',
            title: 'Move down',
            icon: 'arrow-down',
            disabled: $row.is(':last-child'),
            event: 'row.move-down',
            data: {$row: $row}
        }));
        // Remove
        toolbar.addControl('add', new Button({
            name: 'remove',
            title: 'Remove',
            icon: 'remove',
            disabled: 1 >= $container.children('.cms-row').length,
            confirm: 'Êtes-vous sûr de vouloir supprimer cette ligne ?',
            event: 'row.remove',
            data: {$row: $row}
        }));
        // Add
        toolbar.addControl('add', new Button({
            name: 'add',
            title: 'Create a new row',
            icon: 'plus',
            event: 'row.add',
            data: {$row: $row}
        }));

        this.createToolbar(toolbar);
    }

    static createContainerToolbar(e:SelectionEvent): void {
        let $container = e.$element,
            $content: JQuery = $container.closest('.cms-content'),
            toolbar = new Toolbar({
                classes: ['vertical', 'container-toolbar'],
                origin: e.origin
            });

        toolbar.addControl('default', new Button({
            name: 'edit',
            title: 'Edit',
            icon: 'pencil',
            event: 'container.edit',
            data: {$container: $container}
        }));
        // Change type button
        let choices: Array<ButtonChoiceConfig> = [];
        PluginManager.getContainerPluginsConfig().forEach(function (config: PluginConfig) {
            choices.push({
                name: config.name,
                title: config.title,
                confirm: 'Êtes-vous sûr de vouloir changer le type de ce contener ? (Le contenu actuel sera définitivement perdu).',
                data: {type: config.name}
            });
        });
        toolbar.addControl('default', new Button({
            name: 'change-type',
            title: 'Change type',
            icon: 'cog',
            event: 'container.change-type',
            data: {$container: $container},
            choices: choices
        }));
        if (1 == $content.length) {
            // Move top
            toolbar.addControl('move', new Button({
                name: 'move-up',
                title: 'Move up',
                icon: 'arrow-up',
                disabled: $container.is(':first-child'),
                event: 'container.move-up',
                data: {$container: $container}
            }));
            // Move bottom
            toolbar.addControl('move', new Button({
                name: 'move-down',
                title: 'Move down',
                icon: 'arrow-down',
                disabled: $container.is(':last-child'),
                event: 'container.move-down',
                data: {$container: $container}
            }));
            // Remove
            toolbar.addControl('add', new Button({
                name: 'remove',
                title: 'Remove',
                icon: 'remove',
                disabled: 1 >= $content.children('.cms-container').length,
                confirm: 'Êtes-vous sûr de vouloir supprimer ce conteneur ?',
                event: 'container.remove',
                data: {$container: $container}
            }));// Add
            choices = [];
            PluginManager.getContainerPluginsConfig().forEach(function (config: PluginConfig) {
                choices.push({
                    name: config.name,
                    title: config.title,
                    data: {type: config.name}
                });
            });
            toolbar.addControl('add', new Button({
                name: 'add',
                title: 'Create a new container after this one',
                icon: 'plus',
                event: 'container.add',
                data: {$container: $container},
                choices: choices,
            }));
        }

        this.createToolbar(toolbar);
    }
}

export class SelectionEvent {
    $element: JQuery;
    $target: JQuery;
    origin: OffsetInterface;
}

interface DocumentManagerConfig {
    hostname: string
    css_path: string
}

/**
 * DocumentManager
 */
export class DocumentManager {
    private config: DocumentManagerConfig;

    private viewportOrigin: OffsetInterface;
    private viewportSize: SizeInterface;

    private selectionOffset: OffsetInterface;
    private selectionId: string;

    private enabled: boolean = false;

    private clickEvent: SelectionEvent = null;
    private documentMouseDownHandler: (e: JQueryEventObject) => void;
    private documentMouseUpHandler: () => void;
    private documentSelectHandler: (e: SelectionEvent) => void;

    powerClickHandler: (button: Button) => void;
    viewportLoadHandler: (win: Window, doc: Document) => void;
    viewportUnloadHandler: (e: BeforeUnloadEvent) => void;
    viewportResizeHandler: (event: ResizeEventData) => void;

    constructor(config: DocumentManagerConfig) {
        this.config = config;

        this.viewportOrigin = {top: 50, left: 0}; // Editor relative : viewport top-left corner
        this.viewportSize = {width: 0, height: 0}; // Editor relative : viewport size
        this.selectionOffset = {top: 0, left: 0}; // Document relative : offset between click origin and element top-left corner
        this.selectionId = null;

        this.documentMouseDownHandler = (e: JQueryEventObject) => this.onDocumentMouseDown(e);
        this.documentMouseUpHandler = () => this.onDocumentMouseUp();
        this.documentSelectHandler = (e: SelectionEvent) => this.select(e);

        this.powerClickHandler = (button: Button) => this.onPowerClick(button);
        this.viewportLoadHandler = (win: Window, doc: Document) => this.onViewportLoad(win, doc);
        this.viewportUnloadHandler = (e: BeforeUnloadEvent) => this.onViewportUnload(e);
        this.viewportResizeHandler = (e: ResizeEventData) => this.onViewportResize(e);
    }

    initialize() {
        Dispatcher.on('viewport.resize', this.viewportResizeHandler);
        Dispatcher.on('document_manager.select', this.documentSelectHandler);

        Dispatcher.on('base_manager.response_parsed', (e: SelectionEvent) => {
            if (!e.$element && this.selectionId) {
                // TODO where event is triggered : e.$element = BaseManager.findElementById(selectionId);
                e.$element = BaseManager.findElementById(this.selectionId);
            }
            this.deselect()
                .then(() => {
                    this.select(e);
                });
        });

        Dispatcher.on('block.edit', () => ToolbarManager.clearToolbar());
    }

    private onPowerClick(button: Button) {
        let active = button.get('active');
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
    private onViewportLoad(win: Window, doc: Document): DocumentManager {

        let $doc: JQuery = $(doc);

        BaseManager.setContentWindow(win);
        BaseManager.setContentDocument($doc);

        // Intercept anchors click
        $doc.find('a[href]').off('click').on('click', (e: Event) => {
            e.preventDefault();
            e.stopPropagation();

            let anchor: HTMLAnchorElement = <HTMLAnchorElement>e.currentTarget;

            if (anchor.hostname !== this.config.hostname) {
                console.log('Attempt to navigate out of the website has been blocked.');
            } else {
                Dispatcher.trigger('document_manager.navigate', anchor.href);
            }
        });

        // Fix forms actions or intercept submit
        $doc.find('form').each((index: number, element: any) => {
            let $form = $(element),
                action = $form.attr('action'),
                anchor: HTMLAnchorElement = document.createElement('a');

            anchor.href = action;

            if (anchor.hostname !== this.config.hostname) {
                $form.on('submit', function (e) {
                    console.log('Attempt to navigate out of the website has been blocked.');

                    e.preventDefault();
                    return false;
                });
            } else {
                $form.attr('action', Util.addEditorParameterToUrl(action))
            }
        });

        if (this.enabled) {
            this.enableEdition();
        }

        Dispatcher.trigger('document_manager.document_data', BaseManager.getDocumentData());

        return this;
    }

    private onViewportUnload(e: BeforeUnloadEvent): DocumentManager {
        if (e.defaultPrevented) {
            return;
        }

        // Cancel if active plugin is updated
        if (PluginManager.hasActivePlugin()) {
            // Abort reload
            e.preventDefault();

            // Ask user for pending document update
            if (PluginManager.getActivePlugin().isUpdated()) {
                e.returnValue = "Vos changements n'ont pas été sauvegardés !";
                return this;
            }

            // Deselect then re-trigger reload
            this.deselect()
                .then(() => {
                    Dispatcher.trigger('document_manager.reload');
                });

            return this;
        }

        // Clear content window, document and locale
        BaseManager.clear();

        // Clear toolbar
        ToolbarManager.clearToolbar();

        return this;
    }

    private onViewportResize(e: ResizeEventData): DocumentManager {
        this.viewportOrigin = e.origin;
        this.viewportSize = e.size;

        if (ToolbarManager.hasToolbar()) {
            ToolbarManager.getToolbar().applyOriginOffset(e.origin);
        }

        return this;
    }

    private onDocumentMouseDown(e: JQueryEventObject): void {
        this.clickEvent = null;

        let $target: JQuery = $(e.target);
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

        this.clickEvent = new SelectionEvent();
        this.clickEvent.origin = {top: e.clientY, left: e.clientX};

        let $element: JQuery = $target.closest('.cms-widget, .cms-block, .cms-row, .cms-container');
        if (1 == $element.length) {
            if ($element.attr('id') != this.selectionId) {
                this.clickEvent.$element = $element;
                this.clickEvent.$target = $target;
            }
        }
    }

    private onDocumentMouseUp(): void {
        if (this.clickEvent) {
            this.deselect()
                .then(() => {
                    if (this.clickEvent.$element) {
                        this.select(this.clickEvent);
                    } else {
                        this.createToolbar();
                    }
                    this.clickEvent = null;
                });
        }
    }

    private deselect(): Promise<any> {
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

    private select(e: SelectionEvent): void {
        if (!(e.$element && 1 == e.$element.length)) {
            return;
        }

        this.selectionId = e.$element.addClass('selected').attr('id');

        this.createToolbar(e);
    }

    private createToolbar(e?: SelectionEvent): void {
        if (!e.$element) {
            let $element:JQuery = BaseManager.findElementById(this.selectionId);
            if (1 != $element.length) {
                return;
            }
            e.$element = $element;
        }

        if (e.origin) {
            this.selectionOffset = {
                top: (e.origin.top - e.$element.offset().top),
                left: (e.origin.left - e.$element.offset().left)
            };
        } else {
            e.origin = {
                top: (e.$element.offset().top + this.selectionOffset.top),
                left: (e.$element.offset().left + this.selectionOffset.left)
            }
        }

        if (e.$element.hasClass('cms-widget')) {
            ToolbarManager.createWidgetToolbar(e);
        } else if (e.$element.hasClass('cms-block')) {
            ToolbarManager.createBlockToolbar(e);
        } else if (e.$element.hasClass('cms-row')) {
            ToolbarManager.createRowToolbar(e);
        } else if (e.$element.hasClass('cms-container')) {
            ToolbarManager.createContainerToolbar(e);
        } else {
            throw 'Unexpected element';
        }

        ToolbarManager.getToolbar().applyOriginOffset(this.viewportOrigin);
    }

    private enableEdition(): DocumentManager {
        let $document = BaseManager.getContentDocument();

        if (!this.enabled || null === $document) {
            return;
        }

        if (0 == $document.find('link#cms-editor-stylesheet').length) {
            let stylesheet: HTMLLinkElement = document.createElement('link');
            stylesheet.id = 'cms-editor-stylesheet';
            stylesheet.href = this.config.css_path;
            stylesheet.type = 'text/css';
            stylesheet.rel = 'stylesheet';
            $document.find('head').append(stylesheet);
        }

        $document.on('mousedown', this.documentMouseDownHandler);
        $document.on('mouseup', this.documentMouseUpHandler);

        return this;
    }

    private disableEdition(): DocumentManager {
        let $document = BaseManager.getContentDocument();

        if (this.enabled || null === $document) {
            return;
        }

        this.deselect();

        $document.off('mousedown', this.documentMouseDownHandler);
        $document.off('mouseup', this.documentMouseUpHandler);

        let $stylesheet: JQuery = $document.find('link#cms-editor-stylesheet');
        if ($stylesheet.length) {
            $stylesheet.remove();
        }

        return this;
    }
}
