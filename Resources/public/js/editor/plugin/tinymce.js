(function(root, factory) {
    "use strict";
    if (typeof module !== 'undefined' && module.exports) {
        module.exports = factory(require('jquery'), require('ekyna-cms-editor/plugin-base'), require('tinymce'));
    }
    else if (typeof define === 'function' && define.amd) {
        define('ekyna-cms-editor/tinymce', ['jquery', 'ekyna-cms-editor/plugin-base', 'tinymce'], function($, Base) {
            return factory($, Base);
        });
    } else {
        root.EkynaCmsEditorTinymcePlugin = factory(root.jQuery, root.EkynaCmsEditorBasePlugin);
    }
}(this, function($, Base) {
    "use strict";

    if (typeof tinymce == 'undefined') {
        throw 'Tinymce is not available.';
    }

    tinymce.baseURL = '/assets/tinymce';
    tinymce.suffix = '.min';

    var TinymcePlugin = function ($el) {
        Base.call(this, $el);
        this.editor = null;
        this.name = 'TinymcePlugin';
        this.title = 'Tinymce';
    };

    TinymcePlugin.prototype = {
        init: function () {
            Base.prototype.init.apply(this, arguments);

            this.$element.wrapInner('<div id="tinymce-plugin-editor"></div>');

            var self = this;
            var config = {};
            if (typeof cms_editor_tinymce_config != 'undefined') {
                config = cms_editor_tinymce_config;
            } else {
                config = {
                    theme: "modern",
                    plugins: ["advlist autolink lists link image anchor paste textcolor nonbreaking table contextmenu directionality code"],
                    image_advtab: true,
                    table_adv_tab: true,
                    //external_plugins: {filemanager: "/bundles/ekynafilemanager/js/tinymce.plugin.js"},
                    toolbar1: "undo redo removeformat | styleselect | bold italic underline strikethrough | forecolor backcolor",
                    toolbar2: "alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table link image code"
                };
            }
            config.add_unload_trigger = false;
            config.inline = true;
            config.menubar = false;
            config.entity_encoding = 'raw';
            config.toolbar_items_size = 'small';
            config.paste_as_text = true;
            config.relative_urls = false;
            config.content_css = [];
            config.setup = function (ed) {
                ed.on('click', function (e) {
                    e.stopPropagation();
                });
                ed.on('init', function (e) {
                    ed.focus();
                });
                ed.on('change', function (e) {
                    self.setUpdated(true);
                });
            };

            this.editor = new tinymce.Editor('tinymce-plugin-editor', config, tinymce.EditorManager);
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
            if (this.editor !== null) {
                return {html: this.editor.getContent()};
            }
        }
    };
    
    /*$(function() {
        // TODO configurable tinymce url
        $.getScript('/assets/tinymce/tinymce.min.js', function () {
            tinymce.baseURL = '/assets/tinymce';
            tinymce.suffix = '.min';
            CmsEditor.registerPlugin('tinymce', TinymcePlugin);
        });
    });*/

    return {
        create: function ($element) {
            return new TinymcePlugin($element);
        }
    }

}));