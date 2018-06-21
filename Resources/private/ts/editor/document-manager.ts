/// <reference path="../../../../../../../typings/index.d.ts" />

import * as $ from 'jquery';
import 'jquery-ui/slider';
import * as _ from 'underscore';
import * as es6Promise from 'es6-promise';
import * as Router from 'routing';

import Dispatcher from './dispatcher';
import {Util, OffsetInterface, Button, ButtonChoiceConfig, Toolbar, ToolbarView, Slider, ControlInterface} from './ui';
import {SizeInterface, ResizeEventData} from "./viewport";
import {BasePlugin} from './plugin/base-plugin';
import RouteParams = FOS.RouteParams;
import {Bootstrap3Adapter} from "./layout/bootstrap3";
import {CommonAdapter} from "./layout/common";


es6Promise.polyfill();
let Promise = es6Promise.Promise;


const DEFAULT_WIDGET_ACTIONS = {
    edit: false,
};

const DEFAULT_BLOCK_ACTIONS = {
    edit: false,
    layout: false,
    change_type: false,
    move_left: false,
    move_right: false,
    move_up: false,
    move_down: false,
    add: false,
    remove: false
};

const DEFAULT_ROW_ACTIONS = {
    //edit: false,
    layout: false,
    //change_type: false,
    move_up: false,
    move_down: false,
    add: false,
    remove: false
};

const DEFAULT_CONTAINER_ACTIONS = {
    //edit: false,
    layout: false,
    //change_type: false,
    move_up: false,
    move_down: false,
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
    id: string
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
    inner_content: string
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

export interface LayoutDataInterface {
    [property: string]: any
}

export interface AdapterInterface {
    initialize(): void
    onResize(width: number, toolbar: ToolbarView<Toolbar>): void
    setData(property: string, value: string): void
    apply(data: LayoutDataInterface): void
}

interface ResponseData {
    created?: string
    removed?: Array<string>
    content?: ContentData
    containers?: Array<ContainerData>
    rows?: Array<RowData>
    blocks?: Array<BlockData>
    widgets?: Array<WidgetData>
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

        let data: DocumentData = <DocumentData>$doc.find('html').data('cms-editor-document');
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
        let dom = $element.get(0),
            currentAttributes = dom.attributes,
            i = currentAttributes.length;
        while( i-- ) {
            dom.removeAttributeNode(currentAttributes[i]);
        }
        for (let key in attributes) {
            if (key == 'data') {
                $element.removeAttr('data-cms').data('cms', attributes[key]);
                continue;
            }
            $element.removeAttr(key).attr(key, attributes[key]);
        }
        BaseManager.appendHelpers($element);
    }

    static sortChildren($element: JQuery, selector: string) {
        let $children: JQuery = $element.find(selector);
        $children.detach().get().sort(function (a, b) {
            let aPos = $(a).data('cms').position,
                bPos = $(b).data('cms').position;
            return (aPos == bPos) ? 0 : (aPos > bPos) ? 1 : -1;
        }).forEach(function (e: HTMLElement) {
            $element.append(e);
        });
    }

    static generateUrl(route: string, params?: RouteParams) {
        return Router.generate(route, _.extend({}, {
            _document_locale: BaseManager.getDocumentLocale()
        }, params || {}));
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
            // Parse elements
            BaseManager.parse(data);
        });
        xhr.fail(function () {
            console.log('Editor request failed.');
        });
        xhr.always(function () {
            Dispatcher.trigger('editor.unset_busy');
        });

        return xhr;
    }

    static parse(data: ResponseData) {
        // Remove elements by id
        if (data.hasOwnProperty('removed')) {
            data.removed.forEach((id: string) => {
                BaseManager.findElementById(id).remove();
            });
        }

        if (data.hasOwnProperty('content')) {
            ContentManager.parse(data.content);
        } else if (data.hasOwnProperty('containers')) {
            ContainerManager.parse(data.containers);
        } else if (data.hasOwnProperty('rows')) {
            RowManager.parse(data.rows);
        } else if (data.hasOwnProperty('blocks')) {
            BlockManager.parse(data.blocks);
        } else if (data.hasOwnProperty('widgets')) {
            WidgetManager.parse(data.widgets);
        }

        // Dispatch response parsed
        let event: SelectionEvent = new SelectionEvent();
        if (data.hasOwnProperty('created')) {
            event.$element = BaseManager.findElementById(data.created);
        }
        Dispatcher.trigger('base_manager.response_parsed', event);
    }

    static appendHelpers($elements?: JQuery) {
        if (($elements == undefined || 0 == $elements.length) && null != this.$contentDocument) {
            $elements = this.$contentDocument.find('.cms-block, .cms-row, .cms-container');
        }
        if ($elements == undefined || 0 == $elements.length) {
            return;
        }
        $elements
            .filter('.cms-block, .cms-row, .cms-container')
            .each(function (i: number, element: Element) {
                let $element = $(element);
                if ($element.find('i.cms-helper').length == 0) {
                    $element.prepend('<i class="cms-helper"></i>');
                }
            });
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
        BaseManager.sortChildren($content, '> .cms-container');
    }

    static generateUrl($content: JQuery, route: string, params?: RouteParams) {
        let id = (<ElementData>$content.data('cms')).id;
        if (!id) {
            throw 'Invalid id';
        }
        return BaseManager.generateUrl(route, _.extend({}, {
            contentId: id
        }, params || {}));
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

            // Inner container
            let $innerContainer: JQuery;
            let $container: JQuery = BaseManager.findOrCreateElement(container.attributes['id'], $content);
            BaseManager.setElementAttributes($container, container.attributes);

            // Parse content
            let content: string = container.hasOwnProperty('content') ? container.content : null;
            if (content && 0 < content.length) {
                let $innerContainer = $container.find('> .cms-inner-container').detach();
                $container.html(content).append($innerContainer);
            }

            $innerContainer = $container.find('> .cms-inner-container');
            if (container.hasOwnProperty('inner_attributes')) {
                $innerContainer.each(function(index: number, element: Element) {
                    if ($(element).attr('id') !== container.inner_attributes['id']) {
                        $(element).remove();
                    }
                });

                $innerContainer = BaseManager.findOrCreateElement(container.inner_attributes['id'], $container);
                BaseManager.setElementAttributes($innerContainer, container.inner_attributes);
            }

            let innerContent: string = container.hasOwnProperty('inner_content') ? container.inner_content : null;
            if (innerContent && 0 < innerContent.length) {
                $innerContainer.html(innerContent);
            } else {
                $innerContainer.empty();

                // Parse children (if no inner content)
                if (container.hasOwnProperty('rows')) {
                    RowManager.parse(container.rows, $innerContainer);
                }

                // Reorder rows
                BaseManager.sortChildren($innerContainer, '> .cms-row');

                // Sort containers if not made by the content manager.
                if (!$content && i == containers.length - 1) {
                    BaseManager.sortChildren($container.closest('.cms-content'), '> .cms-container');
                }
            }
        });
    }

    static generateUrl($container: JQuery, route: string, params?: RouteParams) {
        let id = (<ElementData>$container.data('cms')).id;
        if (!id) {
            throw 'Invalid id';
        }
        return BaseManager.generateUrl(route, _.extend({}, {
            containerId: id
        }, params || {}));
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
            BaseManager.sortChildren($row, '> .cms-block');

            // Sort rows if not made by the container manager.
            if (!$container && i == rows.length - 1) {
                BaseManager.sortChildren($row.closest('.cms-inner-container'), '> .cms-row');
            }
        });
    }

    static generateUrl($row: JQuery, route: string, params?: RouteParams) {
        let id = (<ElementData>$row.data('cms')).id;
        if (!id) {
            throw 'Invalid id';
        }
        return BaseManager.generateUrl(route, _.extend({}, {
            rowId: id
        }, params || {}));
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
            // Parse layout
            if (!block.hasOwnProperty('attributes')) {
                throw 'Unexpected block data';
            }

            let $block: JQuery = BaseManager.findOrCreateElement(block.attributes.id, $row);
            BaseManager.setElementAttributes($block, block.attributes);

            // Parse children
            if (block.hasOwnProperty('widgets')) {
                WidgetManager.parse(block.widgets, $block);
            }

            // Reorder widgets
            BaseManager.sortChildren($block, '> .cms-widget');

            // Sort blocks if not made by the row manager.
            if (!$row && i == blocks.length - 1) {
                BaseManager.sortChildren($block.closest('.cms-row'), '> .cms-block');
            }
        });
    }

    static generateUrl($block: JQuery, route: string, params?: RouteParams) {
        let data: ElementData = <ElementData>$block.data('cms');
        if (!data.id) {
            throw 'Invalid id';
        }
        return BaseManager.generateUrl(route, _.extend({}, {
            blockId: data.id,
            widgetType: data.type,
        }, params || {}));
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
        BlockManager.request($block, 'ekyna_cms_editor_block_move_down');
    }

    static moveLeft($block: JQuery) {
        BlockManager.request($block, 'ekyna_cms_editor_block_move_left');
    }

    static moveRight($block: JQuery) {
        BlockManager.request($block, 'ekyna_cms_editor_block_move_right');
    }
}

export class WidgetManager {
    static parse(widgets: Array<WidgetData>, $block?: JQuery) {
        widgets.forEach((widget: WidgetData, i: number) => {
            // Parse layout
            if (!widget.hasOwnProperty('attributes')) {
                throw 'Unexpected block data';
            }

            let $widget: JQuery = BaseManager.findOrCreateElement(widget.attributes.id, $block);
            BaseManager.setElementAttributes($widget, widget.attributes);

            // Parse content
            if (widget.hasOwnProperty('content')) {
                $widget.html(widget.content);
            }

            // Sort widgets if not made by the block manager.
            if (!$block && i == widgets.length - 1) {
                BaseManager.sortChildren($widget.closest('.cms-block'), '> .cms-widget');
            }
        });
    }
}

class LayoutManager {
    static adapters: Array<AdapterInterface>;
    static $element: JQuery;
    static data: LayoutDataInterface;
    static backup: LayoutDataInterface;

    static onResizeHandler = (e: ResizeEventData) => {
        let toolbarView = ToolbarManager.getToolbar();

        if (toolbarView.model.getName() != 'layout') {
            throw 'Unexpected toolbar';
        }

        LayoutManager.adapters.forEach((adapter: AdapterInterface) => {
            adapter.onResize(e.size.width, toolbarView);
        });

        toolbarView.render();
    };

    static setUp($element: JQuery, e?: JQueryEventObject) {
        this.$element = $element;
        this.data = {};

        let event = new SelectionEvent();
        event.$element = $element;
        event.origin = {top: e.clientY, left: e.clientX};

        ToolbarManager.createLayoutToolbar(event);

        let toolbarView = ToolbarManager.getToolbar();

        // Creates adapters (todo require => promise)
        let deviceWidth: number = BaseManager.getContentWindow().innerWidth;

        this.adapters = [
            new CommonAdapter(this.data, this.$element),
            new Bootstrap3Adapter(this.data, this.$element),
        ];
        this.adapters.forEach((adapter: AdapterInterface) => {
            adapter.initialize();
            adapter.onResize(deviceWidth, toolbarView);
        });

        this.backup = JSON.parse(JSON.stringify(this.data));

        Dispatcher.on('viewport.resize', LayoutManager.onResizeHandler);
    }

    static tearDown() {
        Dispatcher.off('viewport.resize', LayoutManager.onResizeHandler);

        this.data = null;
        this.backup = null;
        this.$element = null;
        this.adapters = [];
    }

    static hasChanges() {
        return this.data != this.backup;
    }

    static setData(property: string, value: string) {
        this.adapters.forEach((adapter: AdapterInterface) => {
            adapter.setData(property, value);
        });

        this.apply();
    }

    static apply() {
        this.adapters.forEach((adapter: AdapterInterface) => {
            adapter.apply(this.data);
        });
    }

    static submit() {
        if (!this.hasChanges()) {
            this.cancel();
            return;
        }

        Dispatcher.trigger('editor.set_busy');

        let route: string = null,
            parameters: any = {},
            typeMap: {[key: string]: string} = {
                'cms-container': 'container',
                'cms-row': 'row',
                'cms-block': 'block'
            };
        for (let key in typeMap) {
            if (this.$element.hasClass(key)) {
                route = 'ekyna_cms_editor_' + typeMap[key] + '_layout';
                parameters[typeMap[key] + 'Id'] = this.$element.data('cms').id;
                break;
            }
        }
        if (null === route) {
            throw 'Unexpected element type.';
        }

        let xhr: JQueryXHR = BaseManager.request({
            url: Router.generate(route, parameters),
            data: {
                data: this.data
            }
        });
        xhr.done(() => {
            this.tearDown();

            ToolbarManager.clearToolbar();
        });
        xhr.fail(() => {
            this.cancel();
        });
        xhr.always(() => {
            Dispatcher.trigger('editor.unset_busy');
        });
    }

    static cancel() {
        this.adapters.forEach((adapter: AdapterInterface) => {
            adapter.apply(this.backup);
        });

        LayoutManager.tearDown();
        ToolbarManager.clearToolbar();
    }
}

/**
 * Layout events
 */
Dispatcher.on('toolbar.remove', (e: ToolbarEvent) => {
    if (e.toolbar.getName() == 'layout' && LayoutManager.hasChanges()) {
        if (confirm('Souhaitez-vous appliquer les changements ?')) {
            LayoutManager.submit();
        } else {
            LayoutManager.cancel();
        }
    }

});
Dispatcher.on('layout.change', (control: ControlInterface) => {
    LayoutManager.setData(control.getName(), control.getValue());
});
Dispatcher.on('layout.submit', () => LayoutManager.submit());
Dispatcher.on('layout.cancel', () => LayoutManager.cancel());

/**
 * Block events
 */
Dispatcher.on('block.edit',
    (button: Button) => BlockManager.edit(button.get('data').$block)
);
Dispatcher.on('block.layout',
    (button: Button, e: JQueryEventObject) => LayoutManager.setUp(button.get('data').$block, e)
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
Dispatcher.on('row.layout',
    (button: Button, e: JQueryEventObject) => LayoutManager.setUp(button.get('data').$row, e)
);
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
Dispatcher.on('container.layout',
    (button: Button, e: JQueryEventObject) => LayoutManager.setUp(button.get('data').$container, e)
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

abstract class EditorEvent {
    private defaultPrevented: boolean = false;

    public preventDefault() {
        this.defaultPrevented = true;
    }

    public isDefaultPrevented() {
        return this.defaultPrevented;
    }
}

class ToolbarEvent extends EditorEvent {
    toolbar: Toolbar;
}

class ToolbarManager {
    private static toolbar: ToolbarView<Toolbar> = null;

    static getToolbar(): ToolbarView<Toolbar> {
        if (!ToolbarManager.hasToolbar()) {
            throw 'Toolbar is not set';
        }
        return ToolbarManager.toolbar;
    }

    static hasToolbar(): boolean {
        return null != ToolbarManager.toolbar;
    }

    static clearToolbar(): boolean {
        if (ToolbarManager.hasToolbar()) {
            let event: ToolbarEvent = new ToolbarEvent();
            event.toolbar = ToolbarManager.toolbar.model;

            Dispatcher.trigger('toolbar.remove', event);
            if (event.isDefaultPrevented()) {
                return false;
            }

            ToolbarManager.toolbar.remove();
            ToolbarManager.toolbar = null;
        }

        return true;
    }

    private static createToolbar(toolbar: Toolbar): void {
        if (ToolbarManager.clearToolbar()) {
            // Create and render the toolbar view
            ToolbarManager.toolbar = new ToolbarView<Toolbar>({
                model: toolbar
            });
            $(document).find('body').append(this.toolbar.$el);
            ToolbarManager.toolbar.render();
        }
    }

    static createWidgetToolbar(e: SelectionEvent): void {
        let $widget = e.$element,
            toolbar = new Toolbar({
                name: 'widget',
                classes: ['vertical', 'widget-toolbar'],
                origin: e.origin
            }),
            actions = _.extend(
                DEFAULT_WIDGET_ACTIONS,
                $widget.data('cms').actions
            );

        // Edit button
        toolbar.addControl('default', new Button({
            name: 'edit',
            title: 'Edit',
            icon: 'content',
            disabled: !actions.edit,
            event: 'block.edit',
            data: {$block: $widget}
        }));

        ToolbarManager.createToolbar(toolbar);
    }

    static createBlockToolbar(e: SelectionEvent): void {
        let $block = e.$element,
            $row = $block.closest('.cms-row'),
            toolbar = new Toolbar({
                name: 'block',
                classes: ['vertical', 'block-toolbar'],
                origin: e.origin
            }),
            actions = _.extend(
                DEFAULT_BLOCK_ACTIONS,
                $block.data('cms').actions
            );

        // Edit button
        if (actions.edit) {
            toolbar.addControl('default', new Button({
                name: 'edit',
                title: 'Edit',
                icon: 'content',
                //disabled: false,
                event: 'block.edit',
                data: {$block: $block}
            }));
        }

        // Layout
        toolbar.addControl('default', new Button({
            name: 'layout',
            title: 'Layout',
            icon: 'layout',
            disabled: !actions.layout,
            event: 'block.layout',
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
            icon: 'change-type',
            disabled: !actions.change_type && 1 < choices.length,
            event: 'block.change-type',
            data: {$block: $block},
            choices: choices
        }));

        if (1 == $row.length) {
            // Move top
            toolbar.addControl('vertical', new Button({
                name: 'move-up',
                title: 'Move up',
                icon: 'move-up',
                disabled: !actions.move_up,
                event: 'block.move-up',
                data: {$block: $block}
            }));
            // Move bottom
            toolbar.addControl('vertical', new Button({
                name: 'move-down',
                title: 'Move down',
                icon: 'move-down',
                disabled: !actions.move_down,
                event: 'block.move-down',
                data: {$block: $block}
            }));

            // Move left
            toolbar.addControl('horizontal', new Button({
                name: 'move-left',
                title: 'Move left',
                icon: 'move-left',
                disabled: !actions.move_left,
                event: 'block.move-left',
                data: {$block: $block}
            }));
            // Move right
            toolbar.addControl('horizontal', new Button({
                name: 'move-right',
                title: 'Move right',
                icon: 'move-right',
                disabled: !actions.move_right,
                event: 'block.move-right',
                data: {$block: $block}
            }));

            // Remove
            toolbar.addControl('add', new Button({
                name: 'remove',
                title: 'Remove',
                icon: 'remove-block',
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
                icon: 'add-block',
                disabled: !actions.add,
                event: 'block.add',
                data: {$block: $block},
                choices: choices,
            }));
        }

        ToolbarManager.createToolbar(toolbar);
    }

    static createRowToolbar(e: SelectionEvent): void {
        let $row = e.$element,
            $container: JQuery = $row.closest('.cms-inner-container'),
            toolbar = new Toolbar({
                name: 'row',
                classes: ['vertical', 'row-toolbar'],
                origin: e.origin
            }),
            actions = _.extend(
                DEFAULT_ROW_ACTIONS,
                $row.data('cms').actions
            );

        // Layout
        toolbar.addControl('default', new Button({
            name: 'layout',
            title: 'Layout',
            icon: 'layout',
            disabled: !actions.layout,
            event: 'row.layout',
            data: {$row: $row}
        }));

        // Edit button
        /*toolbar.addButton('default', new Button({
         name: 'edit',
         title: 'Edit',
         icon: 'pencil',
         disabled: !actions.edit,
         event: 'row.edit',
         data: {$row: $row}
         }));*/
        if (0 < $container.length) {
            // Move top
            toolbar.addControl('move', new Button({
                name: 'move-up',
                title: 'Move up',
                icon: 'move-up',
                disabled: !actions.move_up,
                event: 'row.move-up',
                data: {$row: $row}
            }));
            // Move bottom
            toolbar.addControl('move', new Button({
                name: 'move-down',
                title: 'Move down',
                icon: 'move-down',
                disabled: !actions.move_down,
                event: 'row.move-down',
                data: {$row: $row}
            }));
            // Remove
            toolbar.addControl('default', new Button({
                name: 'remove',
                title: 'Remove',
                icon: 'remove-row',
                disabled: !actions.remove,
                confirm: 'Êtes-vous sûr de vouloir supprimer cette ligne ?',
                event: 'row.remove',
                data: {$row: $row}
            }));
            // Add
            toolbar.addControl('default', new Button({
                name: 'add',
                title: 'Create a new row',
                icon: 'add-row',
                disabled: !actions.add,
                event: 'row.add',
                data: {$row: $row}
            }));
        }

        ToolbarManager.createToolbar(toolbar);
    }

    static createContainerToolbar(e: SelectionEvent): void {
        let $container = e.$element,
            $content: JQuery = $container.closest('.cms-content'),
            toolbar = new Toolbar({
                name: 'container',
                classes: ['vertical', 'container-toolbar'],
                origin: e.origin
            }),
            actions = _.extend(
                DEFAULT_CONTAINER_ACTIONS,
                $container.data('cms').actions
            );

        toolbar.addControl('default', new Button({
            name: 'edit',
            title: 'Edit',
            icon: 'content',
            disabled: !actions.edit,
            event: 'container.edit',
            data: {$container: $container}
        }));
        toolbar.addControl('default', new Button({
            name: 'layout',
            title: 'Layout',
            icon: 'layout',
            disabled: !actions.layout,
            event: 'container.layout',
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
            icon: 'change-type',
            disabled: !actions.change_type && 1 < choices.length,
            event: 'container.change-type',
            data: {$container: $container},
            choices: choices
        }));
        if (1 == $content.length) {
            // Move top
            toolbar.addControl('move', new Button({
                name: 'move-up',
                title: 'Move up',
                icon: 'move-up',
                disabled: !actions.move_up,
                event: 'container.move-up',
                data: {$container: $container}
            }));
            // Move bottom
            toolbar.addControl('move', new Button({
                name: 'move-down',
                title: 'Move down',
                icon: 'move-down',
                disabled: !actions.move_down,
                event: 'container.move-down',
                data: {$container: $container}
            }));
            // Remove
            toolbar.addControl('add', new Button({
                name: 'remove',
                title: 'Remove',
                icon: 'remove-container',
                disabled: !actions.remove,
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
                icon: 'add-container',
                disabled: !actions.add,
                event: 'container.add',
                data: {$container: $container},
                choices: choices,
            }));
        }

        ToolbarManager.createToolbar(toolbar);
    }

    static createLayoutToolbar(e: SelectionEvent): void {
        let $element = e.$element,
            toolbar = new Toolbar({
                name: 'layout',
                classes: ['vertical', 'layout-toolbar'],
                origin: e.origin
            });

        if ($element.hasClass('cms-block')) {
            // Size / Offset
            toolbar.addControl('default', new Slider({
                name: 'size',
                title: 'Size',
                event: 'layout.change',
                min: 1,
                max: 12
            }));
            toolbar.addControl('default', new Slider({
                name: 'offset',
                title: 'Offset',
                event: 'layout.change',
                min: 0,
                max: 11
            }));
        }

        // Padding top/bottom
        toolbar.addControl('default', new Slider({
            name: 'padding_top',
            title: 'Padding top',
            event: 'layout.change',
            min: 0,
            max: 300
        }));
        toolbar.addControl('default', new Slider({
            name: 'padding_bottom',
            title: 'Padding bottom',
            event: 'layout.change',
            min: 0,
            max: 300
        }));

        // Submit / Cancel
        toolbar.addControl('footer', new Button({
            name: 'submit',
            title: 'Ok',
            theme: 'primary',
            icon: 'ok',
            event: 'layout.submit',
        }));
        toolbar.addControl('footer', new Button({
            name: 'cancel',
            title: 'Cancel',
            icon: 'cancel',
            event: 'layout.cancel',
        }));

        ToolbarManager.createToolbar(toolbar);
    }
}

export class SelectionEvent extends EditorEvent {
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

            this.tweakAnchorsAndForms();
        });

        // TODO why here ???
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

        this.tweakAnchorsAndForms();

        if (this.enabled) {
            this.enableEdition();
        }

        Dispatcher.trigger('document_manager.document_data', BaseManager.getDocumentData());

        return this;
    }

    private tweakAnchorsAndForms() {
        let $doc: JQuery = BaseManager.getContentDocument();

        // Intercept anchors click
        $doc.off('click', 'a[href]').on('click', 'a[href]', (e: Event) => {
            e.preventDefault();
            e.stopPropagation();

            let anchor: HTMLAnchorElement = <HTMLAnchorElement>e.currentTarget;

            if (anchor.hostname !== this.config.hostname) {
                console.log('Attempt to navigate out of the website has been blocked.');
            } else if(!this.enabled) {
                Dispatcher.trigger('document_manager.navigate', anchor.href);
            }

            return false;
        });

        // Fix forms actions or intercept submit
        $doc.find('form').each((i: number, element: Element) => {
            let $form = $(element),
                action = <string>$form.attr('action'),
                anchor: HTMLAnchorElement = document.createElement('a');

            anchor.href = action;

            if (anchor.hostname !== this.config.hostname) {
                $form.off('submit').on('submit', function (e) {
                    console.log('Attempt to navigate out of the website has been blocked.');

                    e.preventDefault();
                    return false;
                });
            } else {
                $form.attr('action', Util.addEditorParameterToUrl(action))
            }
        });
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
        if (!ToolbarManager.clearToolbar()) {
            // Abort reload
            e.preventDefault();
        }

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
            if (this.selectionId != <string>$element.attr('id')) {
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
                if (!ToolbarManager.clearToolbar()) {
                    return Promise.reject('Layout has changes.');
                }

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

        this.selectionId = <string>e.$element.addClass('selected').attr('id');

        this.createToolbar(e);
    }

    private createToolbar(e?: SelectionEvent): void {
        if (!e.$element) {
            let $element: JQuery = BaseManager.findElementById(this.selectionId);
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

        // Insert elements helpers
        BaseManager.appendHelpers();

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

        $document.find('i.cms-helper').remove();

        return this;
    }
}
