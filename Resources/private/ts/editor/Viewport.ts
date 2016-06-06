/// <reference path="../../../../../../../typings/tsd.d.ts" />

import $ = require('jquery');
import Backbone = require('backbone');
import _ = require('underscore');

import Dispatcher from './Dispatcher';
import {Button} from './Ui';

/**
 * SizeInterface
 */
interface SizeInterface {width: number, height: number}

/**
 *  ViewportModel
 */
export class ViewportModel extends Backbone.Model {
    defaults():Backbone.ObjectHash {
        return {
            url: null,
            size: null
        }
    }
}

/**
 * ViewportView
 */
export class ViewportView extends Backbone.View<ViewportModel> {
    template:() => string;

    iFrame:HTMLIFrameElement;

    constructor(options?:Backbone.ViewOptions<ViewportModel>) {
        options.tagName = 'div';
        options.attributes = {
            id: 'editor-viewport'
        };

        super(options);

        this.template = _.template('<iframe id="editor-viewport-frame" frameborder="0"></iframe>');
    }

    initialize(options?:Backbone.ViewOptions<ViewportModel>) {
        _.bindAll(this, 'resizeViewport', 'reload');
        this.model.bind('change:size', this.resizeViewport);
        $(window).resize(this.resizeViewport);

        Dispatcher.on('controls.reload.click', () => this.reload());
        Dispatcher.on('controls.viewport.click', (button:Button) => this.onViewportButtonClick(button));
        Dispatcher.on('document_manager.navigate', (url:string) => this.load(url));
    }

    private reload():void {
        this.iFrame.contentWindow.location.reload();
    }

    /**
     * Resizes the viewport.
     */
    private resizeViewport():void {
        var size:SizeInterface = this.model.get('size'),
            css:any = {};

        if (size) {
            var window_width:number = window.innerWidth,
                window_height:number = window.innerHeight;

            if (window_height - 50 >= size.height) {
                css.top = (window_height / 2 - size.height / 2) + 25;
                css.bottom = (window_height / 2 - size.height / 2) - 25;
            } else {
                css.top = 50;
                css.height = size.height;
                css.marginTop = 50;
                css.marginBottom = 50;
            }
            if (window_width >= size.width) {
                css.left = window_width / 2 - size.width / 2;
                css.right = window_width / 2 - size.width / 2;
            } else {
                css.left = 0;
                css.width = size.width;
                css.marginLeft = 50;
                css.marginRight = 50;
            }
        } else {
            css = {
                top: 50,
                bottom: 0,
                left: 0,
                right: 0
            };
        }

        this.$el.removeAttr('style').css(css);
    }

    onViewportButtonClick(button:Button):void {
        var size:SizeInterface = null,
            data:SizeInterface = button.get('data');

        if (0 < data.width && 0 < data.height) {
            if (button.get('rotate')) {
                //noinspection JSSuspiciousNameCombination
                size = {
                    width: data.height,
                    height: data.width
                };
            } else {
                size = {
                    width: data.width,
                    height: data.height
                };
            }
        }

        this.model.set('size', size);
    }

    /**
     * Renders the viewport.
     *
     * @returns ViewportView
     */
    render():ViewportView {
        this.$el.html(this.template());

        return this;
    }

    /**
     * Initializes the viewport iFrame.
     *
     * @returns ViewportView
     */
    initIFrame(url:string):ViewportView {
        this.iFrame = <HTMLIFrameElement>document.getElementById('editor-viewport-frame');
        this.iFrame.onload = () => {
            Dispatcher.trigger('viewport_iframe.load', this.iFrame.contentDocument);

            this.iFrame.contentWindow.onbeforeunload = () => {
                Dispatcher.trigger('viewport_iframe.unload');
            }
        };

        this.load(url);

        return this;
    }

    /**
     * Loads the url in the editor content frame.
     * Adds the cms-editor-enable parameter if need.
     *
     * @param url
     */
    load(url:string) {
        var anchor:HTMLAnchorElement = <HTMLAnchorElement>document.createElement('a');
        anchor.href = url;

        // Parse search query string
        var params:Backbone.ObjectHash = {},
            seg:any = anchor.search.replace('?','').split('&'),
            len:number = seg.length, i:number = 0, s:any;
        for (;i<len;i++) {
            if (!seg[i]) { continue; }
            s = seg[i].split('=');
            params[s[0]] = s[1];
        }

        // Add cms-editor-enable parameter if not exists
        if (!params.hasOwnProperty('cms-editor-enable')) {
            params['cms-editor-enable'] = 1;

            // Rebuild search query string
            seg = [];
            for (var k in params) {
                if (params.hasOwnProperty(k)) {
                    seg.push(k + '=' + params[k]);
                }
            }
            anchor.search = '?' + seg.join('&');
        }

        this.iFrame.src = anchor.href;
    }
}
