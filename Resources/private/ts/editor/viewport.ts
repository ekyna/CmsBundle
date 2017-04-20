/// <reference path="../../../../../../../../assets/typings/index.d.ts" />

import * as $ from 'jquery';
import * as Backbone from 'backbone';
import * as _ from 'underscore';

import Dispatcher from './dispatcher';
import {Button, OffsetInterface, Util} from './ui';

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
            id: 'editor-viewport-wrapper'
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

        this.resize();

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
        let viewport: HTMLElement = <HTMLElement>window.document.querySelector('#editor-viewport'),
            width: number = viewport.offsetWidth,
            //height: number = viewport.offsetHeight,
            size: SizeInterface = this.model.get('size'),
            origin: OffsetInterface = {top: 50, left: 0};

        this.$el.removeAttr('style');

        if (size) {
            this.$el.removeClass('auto').css({width: size.width, height: size.height});
            if (size.width > width - 25) {
                origin.left = 25;
            } else {
                origin.left = (width / 2) - (size.width / 2);
            }
            origin.top = 75; // Top bar height (50) + viewport margin (25)
        } else {
            this.$el.addClass('auto'); //.css({width: width, height: height});
        }

        Dispatcher.trigger('viewport.resize', <ResizeEventData>{
            origin: origin,
            size: size
        });
    }
}
