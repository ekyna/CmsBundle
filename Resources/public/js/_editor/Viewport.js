/// <reference path="../../../../../../../typings/backbone/backbone.d.ts" />
var __extends = (this && this.__extends) || function (d, b) {
    for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p];
    function __() { this.constructor = d; }
    d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
};
define(["require", "exports", 'jquery', 'backbone', 'underscore', './Dispatcher'], function (require, exports, $, Backbone, _, Dispatcher_1) {
    var Editor;
    (function (Editor) {
        var ViewportModel = (function (_super) {
            __extends(ViewportModel, _super);
            function ViewportModel() {
                _super.apply(this, arguments);
            }
            ViewportModel.prototype.defaults = function () {
                return {
                    url: null,
                    size: null
                };
            };
            return ViewportModel;
        })(Backbone.Model);
        Editor.ViewportModel = ViewportModel;
        var ViewportView = (function (_super) {
            __extends(ViewportView, _super);
            function ViewportView(options) {
                this.tagName = 'div';
                this.attributes = {
                    id: 'editor-viewport'
                };
                this.template = _.template('<iframe src="<%= url %>" frameborder="0"></iframe>');
                _super.call(this, options);
                _.bindAll(this, 'resizeViewport', 'setSize');
                this.model.bind('change:size', this.resizeViewport);
                $(window).resize(this.resizeViewport);
                Dispatcher_1.default.on('viewport_button.click', this.setSize);
            }
            ViewportView.prototype.resizeViewport = function () {
                var size = this.model.get('size'), css = {
                    top: 50,
                    bottom: 0,
                    left: 0,
                    right: 0
                };
                if (size) {
                    var window_width = window.innerWidth, window_height = window.innerHeight;
                    css = {
                        top: (window_height / 2 - size.height / 2) + 25,
                        bottom: (window_height / 2 - size.height / 2) - 25,
                        left: window_width / 2 - size.width / 2,
                        right: window_width / 2 - size.width / 2
                    };
                }
                this.$el.css(css);
            };
            ViewportView.prototype.setSize = function (size) {
                if (size === void 0) { size = null; }
                this.model.set('size', size);
                return this;
            };
            ViewportView.prototype.render = function () {
                this.$el.html(this.template({ url: this.model.get('url') }));
                return this;
            };
            return ViewportView;
        })(Backbone.View);
        Editor.ViewportView = ViewportView;
    })(Editor = exports.Editor || (exports.Editor = {}));
});
