/// <reference path="../../../../../../../../../assets/typings/index.d.ts" />

import {Dispatcher, BaseType} from "../model";

class HeroType extends BaseType {
    build(element: HTMLDivElement, dispatcher: Dispatcher): gsap.TimelineLite {
        let tl = super.build(element, dispatcher);

        let title: HTMLElement = <HTMLElement>element.querySelector('.title'),
            content: HTMLElement = <HTMLElement>element.querySelector('div.content'),
            image: HTMLElement = <HTMLElement>element.querySelector('div.right'),
            button: HTMLElement = <HTMLElement>element.querySelector('p.button');

        if (title && content && image) {
            let offset = 0 == tl.getChildren(false).length ? 0 : .15;

            tl.fromTo(title, .3, {x: -40, opacity: 0}, {x: 0, opacity: 1}, offset);
            tl.fromTo(content, .3, {x: -40, opacity: 0}, {x: 0, opacity: 1}, offset + .15);

            if (button) {
                offset += .15;
                tl.fromTo(button, .3, {x: -40, opacity: 0}, {x: 0, opacity: 1}, offset + .15);
            }

            tl.fromTo(image, .3, {x: 40, opacity: 0, filter: 'blur(15px)'}, {x: 0, opacity: 1, filter: 'blur(0)'}, offset + .3);
        } else {
            console.log('[Hero type] Missing children');
        }

        return tl;
    }
}

export = HeroType;
