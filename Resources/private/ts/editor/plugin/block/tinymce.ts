/// <reference path="../../../../../../../../../typings/index.d.ts" />

import * as $ from 'jquery';
import * as es6Promise from 'es6-promise';

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
    private initPromise:Promise<TinyMceStatic>;
    private config:TinymceConfig;
    private externalPlugins:Array<Object>;
    private tinymce:TinyMceStatic;

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
                    //console.log('Tinymce block plugin : save.');
                    var editor = this.tinymce.get('tinymce-plugin-editor');
                    if (!editor) {
                        throw 'Failed to get tinymce editor instance.';
                    }

                    var content:string = editor.getContent();

                    return BlockManager.request(
                            'ekyna_cms_editor_block_edit',
                            this.$element,
                            {data: {data: {content: content}}}
                        )
                        .then(() => {
                            this.$element.html(content);
                            this.updated = false;
                        });
                }
            });
    }

    destroy():Promise<any> {
        return this
            .save()
            .then(() => {
                //console.log('Tinymce block plugin : remove editor.');
                var editor = this.tinymce.get('tinymce-plugin-editor');
                if (editor) {
                    editor.remove();
                }
                var $wrapper = this.$element.find('#tinymce-plugin-editor');
                if ($wrapper.length) {
                    $wrapper.children().first().unwrap();
                }
            });
    }

    focus() {
        this.initialize()
            .then(() => {
                var editor = this.tinymce.get('tinymce-plugin-editor');
                if (editor) {
                    editor.focus();
                }
            });
    }

    preventDocumentSelection ($target:JQuery):boolean {
        return 0 < $target.closest('#tinymce-plugin-editor, .mce-container').length;
    }

    private initialize():Promise<any> {
        if (!this.initPromise) {
            this.initPromise = new Promise((resolve) => {
                if (this.tinymce) {
                    resolve();
                }

                if (!this.window.hasOwnProperty('require') || typeof this.window['require'] !== 'function') {
                    throw 'requireJs is not available the content window.';
                } else {
                    this.window['require'](['json!tinymce_config', 'tinymce'], (cfg:TinymceConfig) => {
                        if (typeof this.window['tinymce'] === 'undefined') {
                            throw 'Failed to load tinymce from the content iFrame.';
                        }

                        this.config = cfg;

                        this.tinymce = this.window['tinymce'];
                        this.tinymce.baseURL = this.config.tinymce_url;
                        this.tinymce.suffix = '.min';

                        // Load external plugins
                        this.externalPlugins = [];
                        if (typeof this.config.external_plugins == 'object') {
                            for (var pluginId in this.config.external_plugins) {
                                if (!this.config.external_plugins.hasOwnProperty(pluginId)) {
                                    continue;
                                }
                                var opts:any = this.config.external_plugins[pluginId],
                                    url:string = opts.url || null;
                                if (url) {
                                    this.externalPlugins.push({
                                        id: pluginId,
                                        url: url
                                    });
                                    this.tinymce.PluginManager.load(pluginId, url);
                                }
                            }
                        }

                        resolve();
                    });
                }
            });
        }

        return this.initPromise;
    }

    private createEditor() {
        if (0 == this.$element.find('#tinymce-plugin-editor').length) {
            this.$element.wrapInner('<div id="tinymce-plugin-editor"></div>');
        }

        var settings:any = this.config.theme['advanced'];

        settings.external_plugins = settings.external_plugins || {};
        for (var p = 0; p < this.externalPlugins.length; p++) {
            settings.external_plugins[this.externalPlugins[p]['id']] = this.externalPlugins[p]['url'];
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
            if (typeof this.config.tinymce_buttons == 'object') {
                for (var buttonId in this.config.tinymce_buttons) {
                    if (!this.config.tinymce_buttons.hasOwnProperty(buttonId)) continue;
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
                    })(buttonId, clone(this.config.tinymce_buttons[buttonId]));
                }
            }

            editor.on('click', (e) => {
                //console.log('tinymce editor click');
                e.stopPropagation();
            });
            editor.on('init', () => {
                //console.log('tinymce editor init');
                if (this.config.use_callback_tinymce_init) {
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

        var editor:TinyMceEditor = new this.tinymce.Editor(
            'tinymce-plugin-editor',
            settings,
            this.tinymce.EditorManager
        );
        editor.render();
        editor.show();
    }
}

export = TinymcePlugin;

