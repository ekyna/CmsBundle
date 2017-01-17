/// <reference path="../../../../../../../typings/index.d.ts" />

import * as $ from 'jquery';
import * as Backbone from 'backbone';
import * as _ from 'underscore';

import Dispatcher from './dispatcher';
import {Util, OffsetInterface, Button} from './ui';

/**
 * SizeInterface
 */
export interface SizeInterface {
    width: number,
    height: number
}

/**
 * ResizeEventData
 */
export interface ResizeEventData {
    origin: OffsetInterface,
    size: SizeInterface
}

/**
 *  ViewportModel
 */
export class ViewportModel extends Backbone.Model {
    defaults(): Backbone.ObjectHash {
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
    template: () => string;
    iFrame: HTMLIFrameElement;

    onControlsReloadClickHandler: (button: Button) => void;
    onControlsViewportClickHandler: (button: Button) => void;
    onDocumentManagerNavigateHandler: (url: string) => void;

    constructor(options?: Backbone.ViewOptions<ViewportModel>) {
        options.tagName = 'div';
        options.attributes = {
            id: 'editor-viewport'
        };

        super(options);

        this.template = _.template('<iframe id="editor-viewport-frame" frameborder="0"></iframe>');

        this.onControlsReloadClickHandler = (button: Button) => this.reload();
        this.onControlsViewportClickHandler = (button: Button) => this.onViewportButtonClick(button);
        this.onDocumentManagerNavigateHandler = (url: string) => this.load(url);
    }

    initialize(options?: Backbone.ViewOptions<ViewportModel>) {
        _.bindAll(this, 'resize', 'reload');
        this.model.bind('change:size', this.resize);
        $(window).resize(this.resize);
    }

    reload(): void {
        this.iFrame.contentWindow.location.reload();
    }

    onViewportButtonClick(button: Button): void {
        let size: SizeInterface = null,
            data: SizeInterface = button.get('data');

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
    render(): ViewportView {
        this.$el.html(this.template());

        return this;
    }

    /**
     * Loads the url in the editor content frame.
     * Adds the cms-editor-enable parameter if need.
     *
     * @param url
     */
    load(url: string) {
        this.iFrame.src = Util.addEditorParameterToUrl(url);
    }

    /**
     * Initializes the viewport iFrame.
     *
     * @returns ViewportView
     */
    initIFrame(url: string): ViewportView {
        this.iFrame = <HTMLIFrameElement>document.getElementById('editor-viewport-frame');
        this.iFrame.onload = () => {
            Dispatcher.trigger('viewport_iframe.load', this.iFrame.contentWindow || this.iFrame, this.iFrame.contentDocument);

            this.iFrame.contentWindow.onbeforeunload = (e: BeforeUnloadEvent) => {
                Dispatcher.trigger('viewport_iframe.unload', e);

                // https://developer.mozilla.org/fr/docs/Web/Events/beforeunload
                if (e.returnValue) {
                    return e.returnValue;
                }
                /*if (e.defaultPrevented) {
                 return false;
                 }*/
            }
        };

        this.load(url);

        return this;
    }

    /**
     * Resizes the viewport.
     */
    private resize(): void {
        let size: SizeInterface = this.model.get('size'),
            origin: OffsetInterface = {top: 50, left: 0},
            css: any = {
                top: 50,
                bottom: 0,
                left: 0,
                right: 0
            };

        let window_width: number = window.innerWidth,
            window_height: number = window.innerHeight;

        if (size) {
            if (window_height - 50 >= size.height) {
                origin.top = css.top = (window_height / 2 - size.height / 2) + 25;
                css.bottom = (window_height / 2 - size.height / 2) - 25;
            } else {
                css.top = 50;
                css.height = size.height;
                css.marginTop = 50;
                css.marginBottom = 50;
                origin.top = css.top + css.marginTop;
            }
            if (window_width >= size.width) {
                origin.left = css.left = window_width / 2 - size.width / 2;
                css.right = window_width / 2 - size.width / 2;
            } else {
                css.left = 0;
                css.width = size.width;
                css.marginLeft = 50;
                css.marginRight = 50;
                origin.left = css.left + css.marginLeft;
            }
        } else {
            size = {
                width: window_width,
                height: window_height,
            }
        }

        this.$el.removeAttr('style').css(css);

        Dispatcher.trigger('viewport.resize', <ResizeEventData>{
            origin: origin,
            size: size
        });
    }
}
