/// <reference path="../../../../../../../../typings/index.d.ts" />

import {Dispatcher, BaseType} from "../model";

class HeroType extends BaseType {
    build(element: HTMLDivElement, dispatcher: Dispatcher): gsap.TimelineLite {
        let tl = super.build(element, dispatcher);

        let title: HTMLElement = <HTMLElement>element.querySelector('h2'),
            paragraph: HTMLElement = <HTMLElement>element.querySelector('p.lead'),
            image: HTMLElement = <HTMLElement>element.querySelector('img'),
            button: HTMLElement = <HTMLElement>element.querySelector('p.button');

        if (title && paragraph && image) {
            let offset:number = 0;
            if (element.style.backgroundColor && element.style.backgroundColor != 'transparent') {
                tl.from(element, .3, {backgroundColor: 'transparent'}, 0);
                offset = .15;
            }
            tl.fromTo(title, .3, {x: -40, opacity: 0}, {x: 0, opacity: 1}, offset);
            tl.fromTo(paragraph, .3, {x: -40, opacity: 0}, {x: 0, opacity: 1}, offset + .15);
            if (button) {
                offset += .15;
                tl.fromTo(button, .3, {x: -40, opacity: 0}, {x: 0, opacity: 1}, offset + .15);
            }
            tl.fromTo(image, .3, {y: 40, opacity: 0, filter: 'blur(15px)'}, {y: 0, opacity: 1, filter: 'blur(0)'}, offset + .3);
        }

        return tl;
    }
}

export = HeroType;
