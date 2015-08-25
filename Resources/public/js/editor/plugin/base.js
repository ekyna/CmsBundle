(function(root, factory) {
    "use strict";
    if (typeof module !== 'undefined' && module.exports) {
        module.exports = factory(require('jquery'), require('ekyna-cms-editor/plugin-base'));
    }
    else if (typeof define === 'function' && define.amd) {
        define('ekyna-cms-editor/plugin-base', ['jquery'], function($) {
            return factory($);
        });
    } else {
        root.EkynaCmsEditorBasePlugin = factory(root.jQuery);
    }
}(this, function($) {
    "use strict";

    var BasePlugin = function($el) {
        this.$element = $el;
        this.title = 'none';
        this.name = 'CmsPlugin';

        var updated = false;
        this.setUpdated = function (bool) {
            updated = bool;
        };
        this.isUpdated = function () {
            return updated;
        };
    };

    BasePlugin.prototype = {
        constructor: BasePlugin,
        init: function () {
            //console.log(this.name + ' :: init');
        },
        destroy: function () {
            //console.log(this.name + ' :: destroy');
        },
        focus: function () {
            //console.log(this.name + ' :: focus');
        },
        getDatas: function () {
            //console.log(this.name + ' :: focus');
            return {};
        }
    };

    return BasePlugin;

}));