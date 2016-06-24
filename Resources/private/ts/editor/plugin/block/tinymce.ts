/// <reference path="../../../../../../../../../typings/index.d.ts" />

import * as $ from 'jquery';
import * as es6Promise from 'es6-promise';

import Dispatcher from '../../dispatcher';
import {BasePlugin} from '../base-plugin';
import {BlockManager} from '../../document-manager';

es6Promise.polyfill();
var Promise = es6Promise.Promise;

declare var clone:(object:Object) => Object;

interface TinymceConfig {
    language: string
    language_url: string
    selector: string
    theme: Object
    tinymce_url: string

    external_plugins?: Object
    tinymce_buttons?: Object
    use_callback_tinymce_init?: any
}

interface TinyMceObservable {
    off: (name?:string, callback?:Function) => Object
    on: (name:string, callback:Function) => Object
    fire: (name:string, args?:Object, bubble?:Boolean) => Event
}

interface TinyMceEditor extends TinyMceObservable {
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

    Editor: (id:string, settings:Object, em:TinyMceEditorManager) => void
    PluginManager:TinyMceAddOnManager
    EditorManager:TinyMceEditorManager

    init: (settings:Object) => void
    createEditor: (id:string, settings:Object) => TinyMceEditor
    execCommand: (c:string, u:Boolean, v:string) => Boolean
    get: (id:String) => TinyMceEditor

    remove: (selector?:any) => void
}

class TinymcePlugin extends BasePlugin {
    private static initPromise:Promise<TinyMceStatic>;
    private static config:TinymceConfig;
    private static externalPlugins:Array<Object>;
    private static tinymce:TinyMceStatic;

    static setup():Promise<any> {
        return new Promise(function(resolve, reject) {
            resolve();
        })
    }

    static tearDown():Promise<any> {
        return new Promise(function(resolve, reject) {
            resolve();
        });
    }

    private static clear() {
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
                    return;
                }

                this.createEditor();
            });
    }

    save():Promise<any> {
        return this
            .initialize()
            .then(() => {
                if (this.isUpdated()) {
                    Dispatcher.trigger('editor.set_busy');
                    //console.log('Tinymce block plugin : save.');
                    var editor = TinymcePlugin.tinymce.get('tinymce-plugin-editor');
                    if (!editor) {
                        throw 'Failed to get tinymce editor instance.';
                    }

                    var content:string = editor.getContent();

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
                }
            });
    }

    destroy():Promise<any> {
        return this
            .save()
            .then(() => {
                //console.log('Tinymce block plugin : remove editor.');
                var editor = TinymcePlugin.tinymce.get('tinymce-plugin-editor');
                if (editor) {
                    editor.remove();
                }
                var $wrapper = this.$element.find('#tinymce-plugin-editor');
                if ($wrapper.length) {
                    $wrapper.children().first().unwrap();
                }
                TinymcePlugin.clear();
            });
    }

    preventDocumentSelection ($target:JQuery):boolean {
        return 0 < $target.closest('#tinymce-plugin-editor, .mce-container').length;
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
                            for (var pluginId in TinymcePlugin.config.external_plugins) {
                                if (!TinymcePlugin.config.external_plugins.hasOwnProperty(pluginId)) {
                                    continue;
                                }
                                var opts:any = TinymcePlugin.config.external_plugins[pluginId],
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

        var settings:any = TinymcePlugin.config.theme['advanced'];

        settings.external_plugins = settings.external_plugins || {};
        for (var p = 0; p < TinymcePlugin.externalPlugins.length; p++) {
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
                for (var buttonId in TinymcePlugin.config.tinymce_buttons) {
                    if (!TinymcePlugin.config.tinymce_buttons.hasOwnProperty(buttonId)) continue;
                    // Some tricky function to isolate variables values
                    (function (id, opts) {
                        opts.onclick = function () {
                            var callback = window['tinymce_button_' + id];
                            if (typeof callback == 'function') {
                                callback(editor);
                            } else {
                                alert('You have to create callback function: "tinymce_button_' + id + '"');
                            }
                        };
                        editor.addButton(id, opts);
                    })(buttonId, clone(TinymcePlugin.config.tinymce_buttons[buttonId]));
                }
            }

            editor.on('click', (e) => {
                //console.log('tinymce editor click');
                e.stopPropagation();
            });
            editor.on('init', () => {
                //console.log('tinymce editor init');
                if (TinymcePlugin.config.use_callback_tinymce_init) {
                    var callback = window['callback_tinymce_init'];
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

        var editor:TinyMceEditor = new TinymcePlugin.tinymce.Editor(
            'tinymce-plugin-editor',
            settings,
            TinymcePlugin.tinymce.EditorManager
        );
        editor.render();
        editor.show();
    }
}

export = TinymcePlugin;

