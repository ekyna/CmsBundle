/// <reference path="../../../../../../../typings/index.d.ts" />

import * as Backbone from 'backbone';
import * as _ from 'underscore';
import TimelineLite = require('gsap/TimelineLite');


export class Dispatcher {
    constructor() {
        _.extend(this, Backbone.Events);
    }
    on(eventName: string, callback?: Function, context?: any): any{ return; };
    off(eventName?: string, callback?: Function, context?: any): any{ return; };
    trigger(eventName: string, ...args: any[]): any{ return; };
    bind(eventName: string, callback: Function, context?: any): any{ return; };
    unbind(eventName?: string, callback?: Function, context?: any): any{ return; };
    once(events: string, callback: Function, context?: any): any{ return; };
    listenTo(object: any, events: string, callback: Function): any{ return; };
    listenToOnce(object: any, events: string, callback: Function): any{ return; };
    stopListening(object?: any, events?: string, callback?: Function): any{ return; };
}

export interface TypeInterface {
    build(element: HTMLDivElement, dispatcher: Dispatcher): TimelineLite
}

export class BaseType implements TypeInterface {
    private background: HTMLElement;
    private bgInitialTransform: string;
    private bgOpacityTween: gsap.TweenLite;
    private bgTransformTween: gsap.TweenLite;

    private runBgTween() {
        if (this.bgTransformTween) {
            this.bgTransformTween.kill();
        }

        if (this.background.hasOwnProperty('_gsTransform')) {
            delete this.background['_gsTransform'];
            delete this.background['_gsTweenID'];
        }

        this.background.style.setProperty('transform', this.bgInitialTransform);

        this.bgTransformTween = TweenLite.fromTo(this.background, 40, {scale: 1}, {scale: 1.2});
    }

    build(element: HTMLDivElement, dispatcher: Dispatcher): TimelineLite {
        let tl:TimelineLite = new TimelineLite({
            onStart: function() {
                element.style.setProperty('display', 'block');
                dispatcher.trigger('ekyna_cms.slide.show');
            },
            onComplete: function() {
                dispatcher.trigger('ekyna_cms.slide.shown');
            },
            onReverse: function() {
                dispatcher.trigger('ekyna_cms.slide.hide');
            },
            onReverseComplete: function() {
                element.style.setProperty('display', 'none');
                dispatcher.trigger('ekyna_cms.slide.hidden');
            }
        });

        this.background = <HTMLElement>element.querySelector('div.background');
        if (this.background) {
            element.style.backgroundColor = 'transparent';
            this.bgInitialTransform = this.background.style.getPropertyValue('transform');

            tl.eventCallback('onStart', () => {
                element.style.setProperty('display', 'block');
                dispatcher.trigger('ekyna_cms.slide.show');

                if (this.bgOpacityTween) {
                    this.bgOpacityTween.kill();
                }
                this.bgOpacityTween = TweenLite.fromTo(this.background, 1, {opacity: 0}, {opacity: 1});

                this.runBgTween();
            });

            tl.eventCallback('onReverse', () => {
                dispatcher.trigger('ekyna_cms.slide.hide');

                if (this.bgOpacityTween) {
                    this.bgOpacityTween.kill();
                }
                this.bgOpacityTween = TweenLite.fromTo(this.background, 1, {opacity: 1}, {opacity: 0});
            });

            tl.eventCallback('onReverseComplete', () => {
                element.style.setProperty('display', 'none');
                dispatcher.trigger('ekyna_cms.slide.hidden');

                this.bgTransformTween.pause();
            });

            window.addEventListener('resize', () => {
                this.runBgTween();
            });
        } else if (element.style.backgroundColor && element.style.backgroundColor != 'transparent') {
            tl.from(element, .3, {backgroundColor: 'transparent'}, 0);
        }

        tl.pause();

        return tl;
    }
}
