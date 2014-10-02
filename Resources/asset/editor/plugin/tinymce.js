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
        	if (win.CmsEditor.debug) console.log('Tinymce is not available.');
            return;
        }

        this.$element.wrapInner('<div id="tinymce-plugin-editor"></div>');

        var self = this;
        var config = {
            selector: "#tinymce-plugin-editor",
            theme: "modern",
            plugins: ["advlist autolink lists link image anchor paste textcolor nonbreaking table contextmenu directionality code"],
            add_unload_trigger: false,
            inline: true,
            menubar: false,
            paste_as_text: true,
            relative_urls: false,
            image_advtab: true,
            table_adv_tab: true,
            image_class_list: [
                {title: 'Responsive', value: 'img-responsive'},
                {title: 'Flottant à gauche', value: 'img-float-left'},
                {title: 'Flottant à droite', value: 'img-float-right'}
            ],
            style_formats: [
				{title: "Headers", items: [
				    {title: "Header 1", format: "h1"},
				    {title: "Header 2", format: "h2"},
				    {title: "Header 3", format: "h3"},
				    {title: "Header 4", format: "h4"},
				    {title: "Header 5", format: "h5"},
				    {title: "Header 6", format: "h6"}
				]},
				{title: "Inline", items: [
				    {title: "Bold", icon: "bold", format: "bold"},
				    {title: "Italic", icon: "italic", format: "italic"},
				    {title: "Underline", icon: "underline", format: "underline"},
				    {title: "Strikethrough", icon: "strikethrough", format: "strikethrough"},
				    {title: "Superscript", icon: "superscript", format: "superscript"},
				    {title: "Subscript", icon: "subscript", format: "subscript"},
				    {title: "Code", icon: "code", format: "code"}
				]},
				{title: "Blocks", items: [
				    {title: "Paragraph", format: "p"},
				    {title: "Blockquote", format: "blockquote"},
				    {title: "Div", format: "div"},
				    {title: "Pre", format: "pre"}
				]},
				{title: "Alignment", items: [
				    {title: "Left", icon: "alignleft", format: "alignleft"},
				    {title: "Center", icon: "aligncenter", format: "aligncenter"},
				    {title: "Right", icon: "alignright", format: "alignright"},
				    {title: "Justify", icon: "alignjustify", format: "alignjustify"}
				]},
				{title: "Tables", items: [
				    {title: 'Table', selector: 'table', classes: 'table'},
	                {title: 'Table striped', selector: 'table', classes: 'table-striped'},
	                {title: 'Table bordered', selector: 'table', classes: 'table-bordered'},
	                {title: 'Table condensed', selector: 'table', classes: 'table-condensed'}
				]}
            ],
            external_plugins: {filemanager: "/bundles/ekynafilemanager/js/tinymce.plugin.js"},
            toolbar_items_size: 'small',
            toolbar1: "undo redo removeformat | styleselect | bold italic underline strikethrough | forecolor backcolor",
            toolbar2: "alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table link image code",
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
        CmsEditor.registerPlugin('tinymce', TinymceCmsPlugin);
    });
    
})(window, jQuery);