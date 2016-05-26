/// <reference path="../../../../../../../typings/jquery/jquery.d.ts" />
/// <reference path="../../../../../../../typings/backbone/backbone.d.ts" />
var __extends = (this && this.__extends) || function (d, b) {
    for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p];
    function __() { this.constructor = d; }
    d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
};
define(["require", "exports", 'backbone', 'underscore', './Dispatcher'], function (require, exports, Backbone, _, Dispatcher_1) {
    var Editor;
    (function (Editor) {
        var buttonDefaults = {
            icon: null,
            title: null,
            active: false
        };
        var ButtonModel = (function (_super) {
            __extends(ButtonModel, _super);
            function ButtonModel() {
                _super.apply(this, arguments);
            }
            ButtonModel.prototype.defaults = function () {
                return buttonDefaults;
            };
            ButtonModel.prototype.toggle = function () {
                this.set('active', !this.get('active'));
            };
            ButtonModel.prototype.activate = function () {
                this.set('active', true);
            };
            ButtonModel.prototype.deactivate = function () {
                this.set('active', false);
            };
            return ButtonModel;
        })(Backbone.Model);
        var ButtonView = (function (_super) {
            __extends(ButtonView, _super);
            function ButtonView(options) {
                this.tagName = 'button';
                this.attributes = {
                    type: 'button',
                    'class': 'btn btn-default'
                };
                _super.call(this, options);
                this.template = _.template('<span class="fa fa-<%= icon %>"></span>');
                _.bindAll(this, 'render');
                this.model.bind('change', this.render);
            }
            ButtonView.prototype.events = function () {
                return {
                    'click': 'onClick'
                };
            };
            ButtonView.prototype.onClick = function () {
                this.model.toggle();
            };
            ButtonView.prototype.render = function () {
                this.$el
                    .html(this.template({ icon: this.model.get('icon') }))
                    .attr('title', this.model.get('title'))
                    .toggleClass('active', this.model.get('active'));
                return this;
            };
            return ButtonView;
        })(Backbone.View);
        var ViewportButtonModel = (function (_super) {
            __extends(ViewportButtonModel, _super);
            function ViewportButtonModel() {
                _super.apply(this, arguments);
            }
            ViewportButtonModel.prototype.defaults = function () {
                return _.extend(buttonDefaults, {
                    width: null,
                    height: null,
                    rotate: false,
                    collection: null,
                });
            };
            ViewportButtonModel.prototype.activate = function () {
                _.forEach(this.get('collection').without(this), function (button) {
                    button.deactivate();
                });
                if (this.get('active')) {
                    this.set('rotate', !this.get('rotate'));
                }
                else {
                    this.set('active', true);
                }
                var viewport = null, width = this.get('width'), height = this.get('height'), rotate = this.get('rotate');
                if (width && height) {
                    viewport = {
                        width: rotate ? height : width,
                        height: rotate ? width : height
                    };
                }
                Dispatcher_1.default.trigger('viewport_button.click', viewport);
            };
            return ViewportButtonModel;
        })(ButtonModel);
        var ViewportButtonView = (function (_super) {
            __extends(ViewportButtonView, _super);
            function ViewportButtonView() {
                _super.apply(this, arguments);
            }
            ViewportButtonView.prototype.onClick = function () {
                this.model.activate();
            };
            ViewportButtonView.prototype.render = function () {
                _super.prototype.render.call(this).$el.toggleClass('rotate', this.model.get('rotate'));
                return this;
            };
            return ViewportButtonView;
        })(ButtonView);
        var ControlsModel = (function (_super) {
            __extends(ControlsModel, _super);
            function ControlsModel() {
                _super.apply(this, arguments);
            }
            ControlsModel.prototype.defaults = function () {
                return {
                    power_button: new ButtonModel({
                        title: 'Toggle editor on/off',
                        icon: 'power-off'
                    }),
                    viewport_buttons: new Backbone.Collection()
                };
            };
            ControlsModel.prototype.initialize = function (attributes, options) {
                if (options.viewports) {
                    var buttons = this.get('viewport_buttons');
                    _.forEach(options.viewports, function (viewport) {
                        buttons.add(new ViewportButtonModel(_.extend(viewport, {
                            collection: buttons
                        })));
                    });
                }
            };
            return ControlsModel;
        })(Backbone.Model);
        Editor.ControlsModel = ControlsModel;
        var ControlsView = (function (_super) {
            __extends(ControlsView, _super);
            function ControlsView(options) {
                this.tagName = 'div';
                this.attributes = {
                    id: 'editor-control-bar'
                };
                _super.call(this, options);
                this.template = _.template("\n                <div data-power-button-placeholder></div>\n                <div id=\"editor-control-viewport\" class=\"btn-group\" role=\"group\" aria-label=\"...\"></div>\n            ");
            }
            ControlsView.prototype.render = function () {
                this.$el.html(this.template());
                this.$('[data-power-button-placeholder]').replaceWith(new ButtonView({
                    model: this.model.get('power_button')
                }).render().$el);
                var $viewportButtons = this.$('#editor-control-viewport');
                this.model.get('viewport_buttons').each(function (button) {
                    $viewportButtons.append(new ViewportButtonView({
                        model: button
                    }).render().$el);
                });
                return this;
            };
            return ControlsView;
        })(Backbone.View);
        Editor.ControlsView = ControlsView;
    })(Editor = exports.Editor || (exports.Editor = {}));
});
