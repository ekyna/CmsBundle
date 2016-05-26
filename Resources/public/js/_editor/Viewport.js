/// <reference path="../../../../../../../typings/jquery/jquery.d.ts" />
define(["require", "exports"], function (require, exports) {
    var Viewport = (function () {
        function Viewport($element) {
            this.$element = $element;
        }
        Viewport.prototype.resize = function (size) {
            if (size === void 0) { size = null; }
            if (size) {
            }
            else {
            }
            this.$element.css(size);
        };
        return Viewport;
    })();
    return Viewport;
});
