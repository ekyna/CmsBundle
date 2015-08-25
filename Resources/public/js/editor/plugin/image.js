(function(root, factory) {
    "use strict";
    if (typeof module !== 'undefined' && module.exports) {
        module.exports = factory(require('jquery'), require('ekyna-cms-editor/plugin-base'));
    }
    else if (typeof define === 'function' && define.amd) {
        define('ekyna-cms-editor/image', ['jquery', 'ekyna-cms-editor/plugin-base'], function($, Base) {
            return factory($, Base);
        });
    } else {
        root.EkynaCmsEditorImagePlugin = factory(root.jQuery, root.EkynaCmsEditorBasePlugin);
    }
}(this, function($, Base) {
    "use strict";

    var ImageCmsPlugin = function ($el) {
        Base.call(this, $el);
    };

    ImageCmsPlugin.prototype = {
        init: function () {
            Base.prototype.init.apply(this, arguments);
        },
        destroy: function () {
            Base.prototype.destroy.apply(this, arguments);
        },
        focus: function () {
            Base.prototype.destroy.apply(this, arguments);
        },
        getDatas: function () {
            Base.prototype.destroy.apply(this, arguments);
        }
    };

    return {
        name: 'image',
        title: 'Image',
        create: function ($element) {
            return new ImageCmsPlugin($element);
        }
    }
}));