/// <reference path="../../../../../../../typings/jquery/jquery.d.ts" />
/// <reference path="../../../../../../../typings/backbone/backbone.d.ts" />

import $ = require('jquery');
import Backbone = require('backbone');
import _ = require('underscore');

import Dispatcher from './Dispatcher';

export namespace Editor {

    let buttonDefaults:Backbone.ObjectHash = {
        icon: null,
        title: null,
        active: false
    };

    /**
     * Button model
     */
    class ButtonModel extends Backbone.Model {
        defaults():Backbone.ObjectHash {
            return buttonDefaults;
        }

        public toggle():void {
            this.set('active', !this.get('active'));
        }

        public activate():void {
            this.set('active', true);
        }

        public deactivate():void {
            this.set('active', false);
        }
    }

    /**
     * Button view
     */
    class ButtonView<T extends ButtonModel> extends Backbone.View<T> {
        template:(data:{icon: string}) => string;

        events():Backbone.EventsHash {
            return {
                'click': 'onClick'
            }
        }

        constructor(options?:Backbone.ViewOptions<T>) {
            this.tagName = 'button';

            this.attributes = {
                type: 'button',
                'class': 'btn btn-default'
            };

            super(options);

            this.template = _.template('<span class="fa fa-<%= icon %>"></span>');

            _.bindAll(this, 'render');
            this.model.bind('change', this.render);
        }

        /*initialize(options?:Backbone.ViewOptions<T>) {
         }*/

        onClick() {
            this.model.toggle();
        }

        render() {
            this.$el
                .html(this.template({icon: this.model.get('icon')}))
                .attr('title', this.model.get('title'))
                .toggleClass('active', this.model.get('active'));

            return this;
        }
    }

    /**
     * Viewport button model
     */
    class ViewportButtonModel extends ButtonModel {
        defaults():Backbone.ObjectHash {
            return _.extend(buttonDefaults, {
                width: null,
                height: null,
                rotate: false,
                collection: null,
            });
        }

        public activate():void {
            _.forEach(this.get('collection').without(this), (button:ViewportButtonModel) => {
                button.deactivate();
            });

            if (this.get('active')) {
                this.set('rotate', !this.get('rotate'));
            } else {
                this.set('active', true);
            }

            var viewport:any = null,
                width:number = this.get('width'),
                height:number = this.get('height'),
                rotate:boolean = this.get('rotate');

            if (width && height) {
                viewport = {
                    width: rotate ? height : width,
                    height: rotate ? width : height
                };
            }

            Dispatcher.trigger('viewport_button.click', viewport);
        }
    }

    /**
     * Viewport button view
     */
    class ViewportButtonView<T extends ViewportButtonModel> extends ButtonView<T> {
        onClick() {
            this.model.activate();
        }

        render() {
            super.render().$el.toggleClass('rotate', this.model.get('rotate'));

            return this;
        }
    }

    /**
     * Controls model
     */
    export class ControlsModel extends Backbone.Model {
        defaults():Backbone.ObjectHash {
            return {
                power_button: new ButtonModel({
                    title: 'Toggle editor on/off',
                    icon: 'power-off'
                }),
                viewport_buttons: new Backbone.Collection()
            }
        }

        initialize(attributes?:any, options?:any):void {
            if (options.viewports) {
                var buttons:Backbone.Collection<ViewportButtonModel> = this.get('viewport_buttons');
                _.forEach(options.viewports, (viewport:{width: number; height: number, icon: string, title: string}) => {
                    buttons.add(new ViewportButtonModel(_.extend(viewport, {
                        collection: buttons
                    })));
                });
            }
        }
    }

    /**
     * Controls view
     */
    export class ControlsView extends Backbone.View<ControlsModel> {
        template:(data?:Object) => string;

        /*events(): Backbone.EventsHash {
         return {
         'click #editor-control-viewport button': 'onViewportButtonClick'
         }
         }*/

        constructor(options?:Backbone.ViewOptions<ControlsModel>) {
            this.tagName = 'div';
            this.attributes = {
                id: 'editor-control-bar'
            };

            super(options);

            this.template = _.template(`
                <div data-power-button-placeholder></div>
                <div id="editor-control-viewport" class="btn-group" role="group" aria-label="..."></div>
            `);

            //_.bindAll(this, 'onViewportButtonClick');
            //Dispatcher.on('viewport_button.click', this.onViewportButtonClick);
        }

        render() {
            this.$el.html(this.template());

            this.$('[data-power-button-placeholder]').replaceWith(
                new ButtonView({
                    model: this.model.get('power_button')
                }).render().$el
            );

            var $viewportButtons = this.$('#editor-control-viewport');
            this.model.get('viewport_buttons').each((button:ViewportButtonModel) => {
                $viewportButtons.append(
                    new ViewportButtonView({
                        model: button
                    }).render().$el
                );
            });

            return this;
        }
    }
}
