(function(root, factory) {
    "use strict";
    if (typeof module !== 'undefined' && module.exports) {
        module.exports = factory(require('jquery'), require('ekyna-cms-editor/plugin-base'), require('json!tinymce_config'), require('tinymce'));
    }
    else if (typeof define === 'function' && define.amd) {
        define('ekyna-cms-editor/tinymce', ['jquery', 'ekyna-cms-editor/plugin-base', 'json!tinymce_config', 'tinymce'], function($, Base, config) {
            return factory($, Base, config);
        });
    } else {
        root.EkynaCmsEditorTinymcePlugin = factory(root.jQuery, root.EkynaCmsEditorBasePlugin); // TODO
    }
}(this, function($, Base, config) {
    "use strict";

    if (typeof tinymce == 'undefined') {
        throw 'Tinymce is not available.';
    }

    tinymce.baseURL = config.tinymce_url;
    tinymce.suffix = '.min';

    // Load external plugins
    var externalPlugins = [];
    if (typeof config.external_plugins == 'object') {
        for (var pluginId in config.external_plugins) {
            if (!config.external_plugins.hasOwnProperty(pluginId)) {
                continue;
            }
            var opts = config.external_plugins[pluginId],
                url = opts.url || null;
            if (url) {
                externalPlugins.push({
                    'id': pluginId,
                    'url': url
                });
                tinymce.PluginManager.load(pluginId, url);
            }
        }
    }

    var TinymcePlugin = function ($el) {
        Base.call(this, $el);
        this.editor = null;
    };

    TinymcePlugin.prototype = {
        init: function () {
            Base.prototype.init.apply(this, arguments);

            this.$element.wrapInner('<div id="tinymce-plugin-editor"></div>');

            var self = this;

            var settings = config.theme['advanced'];

            settings.external_plugins = settings.external_plugins || {};
            for (var p = 0; p < externalPlugins.length; p++) {
                settings.external_plugins[externalPlugins[p]['id']] = externalPlugins[p]['url'];
            }

            settings.add_unload_trigger = false;
            settings.inline = true;
            settings.menubar = false;
            settings.entity_encoding = 'raw';
            settings.toolbar_items_size = 'small';
            settings.paste_as_text = true;
            settings.relative_urls = false;
            settings.content_css = [];
            settings.setup = function (editor) {

                if (typeof config.tinymce_buttons == 'object') {
                    for (var buttonId in config.tinymce_buttons) {
                        if (!config.tinymce_buttons.hasOwnProperty(buttonId)) continue;

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

                        })(buttonId, clone(config.tinymce_buttons[buttonId]));
                    }
                }

                editor.on('click', function (e) {
                    e.stopPropagation();
                });
                editor.on('init', function () {
                    if (config.use_callback_tinymce_init) {
                        var callback = window['callback_tinymce_init'];
                        if (typeof callback == 'function') {
                            callback(editor);
                        } else {
                            alert('You have to create callback function: callback_tinymce_init');
                        }
                    }
                    editor.focus();
                });
                editor.on('change', function () {
                    self.setUpdated(true);
                });
            };

            this.editor = new tinymce.Editor('tinymce-plugin-editor', settings, tinymce.EditorManager);
            this.editor.render();
        },
        destroy: function () {
            Base.prototype.destroy.apply(this, arguments);
            if (this.editor !== null) {
                var content = this.editor.getContent();
                this.$element.html(content);

                this.editor.remove();
                this.editor.destroy();
                this.editor = null;
            }
        },
        focus: function () {
            Base.prototype.focus.apply(this, arguments);
            if (this.editor !== null) {
                this.editor.focus();
            }
        },
        getDatas: function () {
            Base.prototype.destroy.apply(this, arguments);
            if (this.editor !== null) {
                return {html: this.editor.getContent()};
            }
        }
    };

    return {
        name: 'tinymce',
        title: 'Tinymce',
        create: function ($element) {
            return new TinymcePlugin($element);
        }
    }
}));