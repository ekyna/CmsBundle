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

        tl.pause();

        return tl;
    }
}
