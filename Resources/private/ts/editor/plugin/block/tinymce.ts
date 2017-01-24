/// <reference path="../../../../../../../../../typings/index.d.ts" />

import * as es6Promise from 'es6-promise';

import Dispatcher from '../../dispatcher';
import {BasePlugin} from '../base-plugin';
import {BlockManager} from '../../document-manager';

es6Promise.polyfill();
let Promise = es6Promise.Promise;

declare let clone:(object:Object) => Object;

interface TinymceConfig {
    language: string
    language_url: string
    selector: string
    theme: any
    tinymce_url: string

    external_plugins?: any
    tinymce_buttons?: any
    use_callback_tinymce_init?: any
}

interface TinyMceObservable {
    off: (name?:string, callback?:Function) => Object
    on: (name:string, callback:Function) => Object
    fire: (name:string, args?:Object, bubble?:Boolean) => Event
}

interface TinyMceEditor extends TinyMceObservable {
    constructor(id:string, settings:Object, em:TinyMceEditorManager):void
    id: string
    undoManager: TinyMceUndoManager
    settings: Object

    addButton: (name:string, settings:Object) => void
    destroy: (automatic?:boolean) => void
    remove: () => void
    render: () => void
    hide: () => void
    show: () => void
    getContent: (args?:Object) => string
    setContent: (content:string, args?:Object) => string
    focus: (skip_focus?:Boolean) => void
}

interface TinyMceUndoManager {
    undo: () => Object
    clear: () => void
    hasUndo: () => Boolean
}

interface TinyMceAddOnManager {
    load: (name:String, addOnUrl:String, callback?:Function, scope?:Object) => void
}

interface TinyMceEditorManager {

}

interface TinyMceStatic extends TinyMceObservable {
    baseURL: string
    suffix: string
    activeEditor: TinyMceEditor
    editors: Array<TinyMceEditor>

    Editor: any
    PluginManager:TinyMceAddOnManager
    EditorManager:TinyMceEditorManager

    init: (settings:Object) => void
    createEditor: (id:string, settings:Object) => TinyMceEditor
    execCommand: (c:string, u:Boolean, v:string) => Boolean
    get: (id:String) => TinyMceEditor

    remove: (selector?:any) => void
}

Dispatcher.on('viewport_iframe.unload', function() {
    TinymcePlugin.clear();
});

class TinymcePlugin extends BasePlugin {
    private static initPromise:Promise<TinyMceStatic>;
    private static config:TinymceConfig;
    private static externalPlugins:any;
    private static tinymce:TinyMceStatic;

    public static clear() {
        TinymcePlugin.initPromise = null;
        TinymcePlugin.config = null;
        TinymcePlugin.externalPlugins = null;
        TinymcePlugin.tinymce = null;
    }

    edit() {
        super.edit();

        this.initialize()
            .then(() => {
                if (this.destroyed) {
                    return Promise.resolve();
                }

                this.createEditor();
            });
    }

    save():Promise<any> {
        if (this.isUpdated()) {
            return this
                .initialize()
                .then(() => {

                    Dispatcher.trigger('editor.set_busy');
                    //console.log('Tinymce block plugin : save.');
                    let editor = TinymcePlugin.tinymce.get('tinymce-plugin-editor');
                    if (!editor) {
                        throw 'Failed to get tinymce editor instance.';
                    }

                    let content:string = editor.getContent();

                    return BlockManager.request(
                            this.$element,
                            'ekyna_cms_editor_block_edit',
                            null,
                            {data: {data: {content: content}}}
                        )
                        .then(() => {
                            this.$element.html(content);
                            this.updated = false;
                            Dispatcher.trigger('editor.unset_busy');
                        });
                });
        }

        return Promise.resolve();
    }

    destroy():Promise<any> {
        return this
            .save()
            .then(() => {
                //console.log('Tinymce block plugin : remove editor.');
                let editor = TinymcePlugin.tinymce.get('tinymce-plugin-editor');
                if (editor) {
                    editor.remove();
                }

                let $wrapper = this.$element.find('#tinymce-plugin-editor');
                if ($wrapper.length) {
                    $wrapper.children().first().unwrap();
                }

                return super.destroy();
            });
    }

    preventDocumentSelection ($target:JQuery):boolean {
        return 0 < $target.closest('#tinymce-plugin-editor, .mce-container, .mce-widget, .mce-reset, .mce-tooltip').length;
    }

    private initialize():Promise<any> {
        if (!TinymcePlugin.initPromise) {
            Dispatcher.trigger('editor.set_busy');
            TinymcePlugin.initPromise = new Promise((resolve) => {
                if (TinymcePlugin.tinymce) {
                    resolve();
                }

                if (!this.window.hasOwnProperty('require') || typeof this.window['require'] !== 'function') {
                    throw 'requireJs is not available the content window.';
                } else {
                    this.window['require'](['json!tinymce_config', 'tinymce'], (cfg:TinymceConfig) => {
                        if (typeof this.window['tinymce'] === 'undefined') {
                            throw 'Failed to load tinymce from the content iFrame.';
                        }

                        TinymcePlugin.config = cfg;

                        TinymcePlugin.tinymce = this.window['tinymce'];
                        TinymcePlugin.tinymce.baseURL = TinymcePlugin.config.tinymce_url;
                        TinymcePlugin.tinymce.suffix = '.min';

                        // Load external plugins
                        TinymcePlugin.externalPlugins = [];
                        if (typeof TinymcePlugin.config.external_plugins == 'object') {
                            for (let pluginId in TinymcePlugin.config.external_plugins) {
                                if (!TinymcePlugin.config.external_plugins.hasOwnProperty(pluginId)) {
                                    continue;
                                }
                                let opts:any = TinymcePlugin.config.external_plugins[pluginId],
                                    url:string = opts.url || null;
                                if (url) {
                                    TinymcePlugin.externalPlugins.push({
                                        id: pluginId,
                                        url: url
                                    });
                                    TinymcePlugin.tinymce.PluginManager.load(pluginId, url);
                                }
                            }
                        }

                        Dispatcher.trigger('editor.unset_busy');

                        resolve();
                    });
                }
            });
        }

        return TinymcePlugin.initPromise;
    }

    private createEditor() {
        if (0 == this.$element.find('#tinymce-plugin-editor').length) {
            this.$element.wrapInner('<div id="tinymce-plugin-editor"></div>');
        }

        let settings:any = TinymcePlugin.config.theme['advanced'];

        settings.external_plugins = settings.external_plugins || {};
        for (let p = 0; p < TinymcePlugin.externalPlugins.length; p++) {
            settings.external_plugins[TinymcePlugin.externalPlugins[p]['id']] = TinymcePlugin.externalPlugins[p]['url'];
        }

        settings.add_unload_trigger = false;
        settings.inline = true;
        settings.menubar = false;
        settings.entity_encoding = 'raw';
        settings.toolbar_items_size = 'small';
        settings.paste_as_text = true;
        settings.relative_urls = false;
        settings.content_css = [];

        settings.setup = (editor:TinyMceEditor) => {
            if (typeof TinymcePlugin.config.tinymce_buttons == 'object') {
                for (let buttonId in TinymcePlugin.config.tinymce_buttons) {
                    if (!TinymcePlugin.config.tinymce_buttons.hasOwnProperty(buttonId)) continue;
                    // Some tricky function to isolate letiables values
                    (function (id, opts) {
                        opts.onclick = function () {
                            let callback = this.window['tinymce_button_' + id];
                            if (typeof callback == 'function') {
                                callback(editor);
                            } else {
                                alert('You have to create callback function: "tinymce_button_' + id + '"');
                            }
                        };
                        editor.addButton(id, opts);
                    })(buttonId, <any>clone(TinymcePlugin.config.tinymce_buttons[buttonId]));
                }
            }

            editor.on('click', (e:Event) => {
                //console.log('tinymce editor click');
                e.stopPropagation();
            });
            editor.on('init', () => {
                //console.log('tinymce editor init');
                if (TinymcePlugin.config.use_callback_tinymce_init) {
                    let callback = this.window['callback_tinymce_init'];
                    if (typeof callback == 'function') {
                        callback(editor);
                    } else {
                        alert('You have to create callback function: callback_tinymce_init');
                    }
                }
                editor.focus();
            });
            editor.on('change', () => {
                //console.log('tinymce editor change');
                this.setUpdated(true);
            });
        };

        let editor:TinyMceEditor = new TinymcePlugin.tinymce.Editor(
            'tinymce-plugin-editor',
            settings,
            TinymcePlugin.tinymce.EditorManager
        );
        editor.render();
        editor.show();
    }
}

export = TinymcePlugin;

