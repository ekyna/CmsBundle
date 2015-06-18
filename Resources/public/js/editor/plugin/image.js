;(function($) {
    "use strict";

    function ImageCmsPlugin($el) {
        CmsPlugin.call(this, $el);
        this.editor = null;
        this.name = 'ImageCmsPlugin';
    }
    ImageCmsPlugin.title = 'Image';
    /*ImageCmsPlugin.prototype.init = function() {
        CmsPlugin.prototype.init.apply(this, arguments);
    };
    ImageCmsPlugin.prototype.destroy = function() {
        CmsPlugin.prototype.destroy.apply(this, arguments);
    };
    ImageCmsPlugin.prototype.focus = function() {
        CmsPlugin.prototype.focus.apply(this, arguments);
    };
    ImageCmsPlugin.prototype.update = function() {
        CmsPlugin.prototype.update.apply(this, arguments);
    };*/
    
    $(function() {
		CmsEditor.registerPlugin('image', ImageCmsPlugin);
    });
    
})(jQuery);