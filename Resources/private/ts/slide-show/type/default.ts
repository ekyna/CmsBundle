/// <reference path="../../../../../../../../typings/index.d.ts" />

import {Dispatcher, BaseType} from "../model";

class DefaultType extends BaseType {
    build(element: HTMLDivElement, dispatcher: Dispatcher): gsap.TimelineLite {
        let tl = super.build(element, dispatcher);

        let title: HTMLElement = <HTMLElement>element.querySelector('.title'),
            content: HTMLElement = <HTMLElement>element.querySelector('div.content'),
            button: HTMLElement = <HTMLElement>element.querySelector('p.button');

        if (title && content && button) {
            let offset = 0 == tl.getChildren(false).length ? 0 : .15;

            tl.fromTo(title, .3, {y: 40, opacity: 0, scale: .8}, {y: 0, opacity: 1, scale: 1}, offset);
            tl.fromTo(content, .3, {y: 40, opacity: 0, scale: .8}, {y: 0, opacity: 1, scale: 1}, offset + .15);
            tl.fromTo(button, .3, {y: 40, opacity: 0, scale: .8}, {y: 0, opacity: 1, scale: 1}, offset + .3);
        } else {
            console.log('[Hero type] Missing children');
        }

        return tl;
    }
}

export = DefaultType;
