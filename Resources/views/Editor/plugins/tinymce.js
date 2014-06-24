;(function(win, $) {
    "use strict";

    function TinymceCmsPlugin($el) {
        CmsPlugin.call(this, $el);
        this.editor = null;
        this.name = 'TinymceCmsPlugin';
    }
    TinymceCmsPlugin.title = 'Tinymce';
    TinymceCmsPlugin.prototype.init = function() {
        CmsPlugin.prototype.init.apply(this, arguments);

        if (typeof win.tinymce == 'undefined') {
        	if (CmsEditor.debug) console.log('Tinymce is not available.');
            return;
        }

        this.$element.wrapInner('<div id="tinymce-plugin-editor"></div>');

        var self = this;
        var config = {
            selector: "#tinymce-plugin-editor",
            theme: "modern",
            add_unload_trigger: false,
            schema: "html5",
            inline: true,
            menubar: false,
            paste_as_text: true,
            relative_urls: false,
            image_advtab: true,
            image_class_list: [
                {title: 'Responsive', value: 'img-responsive'},
                {title: 'Flottant à gauche', value: 'img-float-left'},
                {title: 'Flottant à droite', value: 'img-float-right'}
            ],
            plugins: ["advlist autolink lists link image anchor paste textcolor nonbreaking table contextmenu directionality"],
            external_plugins: {filemanager: "/bundles/ekynafilemanager/js/tinymce.plugin.js"},
            toolbar_items_size: 'small',
            toolbar1: "undo redo removeformat | styleselect | bold italic underline strikethrough | forecolor backcolor",
            toolbar2: "alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table link image",
            setup : function(ed) {
                ed.on('click', function(e) {
                    e.stopPropagation();
                });
                ed.on('init', function(e) {
                    ed.focus();
                });
                ed.on('change', function(e) {
                    self.setUpdated(true);
                });
            }
        };

        this.editor = new win.tinymce.Editor('tinymce-plugin-editor', config, win.tinymce.EditorManager);
        this.editor.render();
    };
    TinymceCmsPlugin.prototype.destroy = function() {
        CmsPlugin.prototype.destroy.apply(this, arguments);
        if (this.editor !== null) {
            var content = this.editor.getContent();
            this.$element.html(content);
            
            this.editor.remove();
            this.editor.destroy();
            this.editor = null;
        }
    };
    TinymceCmsPlugin.prototype.focus = function() {
        CmsPlugin.prototype.focus.apply(this, arguments);
        if (this.editor !== null) {
        	this.editor.focus();
        }
    };
    TinymceCmsPlugin.prototype.getDatas = function() {
    	if (this.editor !== null) {
            return {html: this.editor.getContent()};
        }
    };
    
    $(win.document).ready(function() {
		win.cmsEditor.registerPlugin('tinymce', TinymceCmsPlugin);
    });
    
})(window, jQuery);