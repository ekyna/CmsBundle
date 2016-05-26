/// <reference path="../../../../../../../typings/backbone/backbone.d.ts" />

import $ = require('jquery');
import Backbone = require('backbone');
import _ = require('underscore');

import Dispatcher from './Dispatcher';

export namespace Editor {

    export class ViewportModel extends Backbone.Model {
        defaults():Backbone.ObjectHash {
            return {
                url: null,
                size: null
            }
        }
    }

    export class ViewportView extends Backbone.View<ViewportModel> {
        template:(data:{url: string}) => string;

        constructor(options?:Backbone.ViewOptions<ViewportModel>) {
            this.tagName = 'div';
            this.attributes = {
                id: 'editor-viewport'
            };

            //noinspection HtmlUnknownTarget
            this.template = _.template('<iframe src="<%= url %>" frameborder="0"></iframe>');

            super(options);

            _.bindAll(this, 'resizeViewport', 'setSize');
            this.model.bind('change:size', this.resizeViewport);
            $(window).resize(this.resizeViewport);

            Dispatcher.on('viewport_button.click', this.setSize)
        }

        private resizeViewport():void {
            var size = this.model.get('size'),
                css = {
                    top: 50,
                    bottom: 0,
                    left: 0,
                    right: 0
                };

            if (size) {
                var window_width:number = window.innerWidth,
                    window_height:number = window.innerHeight;

                css = {
                    top: (window_height / 2 - size.height / 2) + 25,
                    bottom: (window_height / 2 - size.height / 2) - 25,
                    left: window_width / 2 - size.width / 2,
                    right: window_width / 2 - size.width / 2
                };
            }

            this.$el.css(css);
        }

        setSize(size:{width: number, height: number} = null):ViewportView {
            this.model.set('size', size);

            return this;
        }

        render():ViewportView {
            this.$el.html(this.template({url: this.model.get('url')}));

            return this;
        }
    }
}
