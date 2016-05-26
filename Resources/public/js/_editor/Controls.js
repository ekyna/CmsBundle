/// <reference path="../../../../../../../typings/jquery/jquery.d.ts" />
/// <reference path="../../../../../../../typings/backbone/backbone.d.ts" />
var __extends = (this && this.__extends) || function (d, b) {
    for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p];
    function __() { this.constructor = d; }
    d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
};
define(["require", "exports"], function (require, exports) {
    var ViewportButton = (function (_super) {
        __extends(ViewportButton, _super);
        function ViewportButton() {
            _super.apply(this, arguments);
        }
        ViewportButton.prototype.defaults = function () {
            return {
                width: null,
                height: null,
                landscape: false,
                active: false,
            };
        };
        ViewportButton.prototype.activate = function () {
            if (this.get('active')) {
                this.set('landscape', !this.get('landscape'));
            }
            else {
                this.set('active', true);
            }
        };
        ViewportButton.prototype.deactivate = function () {
            this.set('false', true);
        };
        return ViewportButton;
    })(Backbone.Model);
    var ViewportButtonView = (function (_super) {
        __extends(ViewportButtonView, _super);
        function ViewportButtonView() {
            _super.apply(this, arguments);
        }
        return ViewportButtonView;
    })(Backbone.View);
    var Controls = (function () {
        function Controls($element) {
            this.$element = $element;
        }
        Controls.prototype.init = function () {
        };
        return Controls;
    })();
    return Controls;
});
