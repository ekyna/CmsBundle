/// <reference path="../../../../../../../typings/index.d.ts" />
/// <reference path="../../typings/slide-show.d.ts" />

import * as es6Promise from 'es6-promise';
import * as _ from 'underscore';
import TimelineLite = gsap.TimelineLite;
import {TypeInterface, Dispatcher} from "./model";
import types = require('json!ekyna-cms/slide-types');


es6Promise.polyfill();
let Promise = es6Promise.Promise;

class TypeRegistry {
    static config: { [name: string]: string };
    static types: { [name: string]: TypeInterface };

    static initialize(config) {
        TypeRegistry.config = config;
        TypeRegistry.types = {};
    }

    static getType(name: string): Promise<TypeInterface> {
        return new Promise((resolve, reject) => {
            if (TypeRegistry.types.hasOwnProperty(name)) {
                resolve(TypeRegistry.types[name]);
            } else if (TypeRegistry.config.hasOwnProperty(name)) {
                try {
                    require([TypeRegistry.config[name]], (type) => {
                        TypeRegistry.types[name] = new type();
                        resolve(TypeRegistry.types[name]);
                    });
                } catch (e) {
                    reject(e);
                }
            } else {
                reject('Undefined type.');
            }
        });
    }
}

TypeRegistry.initialize(types);


class Slide {
    private timeline:TimelineLite;

    constructor(timeline:TimelineLite) {
        this.timeline = timeline;
    }

    show(): void {
        this.timeline.play(0, false);
    }

    hide(hard: boolean = false): void {
        if (hard) {
            this.timeline.pause();
        } else {
            let onReverse:Function;
            if (onReverse = this.timeline.eventCallback('onReverse')) {
                onReverse();
            }
            this.timeline.reverse();
        }
    }
}

interface SlideShowOptions {
    id: string
    types: { [name: string]: string }
    ui: boolean
    auto: boolean
    debug: boolean
}

let DEFAULT_OPTIONS = {
    id: null,
    types: {},
    ui: true,
    auto: true,
    debug: false
};

class SlideShow extends Dispatcher {
    static create(options: SlideShowOptions) {
        let slideShow = new SlideShow();
        slideShow.init(options);
        return slideShow;
    }

    options: SlideShowOptions;
    root: HTMLElement;
    nav: HTMLUListElement;
    slides: Array<Slide>;
    busy: boolean;
    initialized: boolean;
    index: number;
    current: Slide;
    autoTimeout: number;

    /**
     * Initializes the slide show.
     *
     * @param {SlideShowOptions} options
     */
    init(options: SlideShowOptions) {
        this.initialized = false;
        this.root = document.getElementById(options.id);
        if (!this.root) {
            console.log('Slide show\'s root element not found.');
            return;
        }

        let config = {};
        if (this.root.hasAttribute('data-config')) {
            config = JSON.parse(this.root.getAttribute('data-config'));
        }

        this.options = _.defaults({}, options, config, DEFAULT_OPTIONS);
        this.slides = [];
        this.busy = false;

        let elements:HTMLCollection = this.root.getElementsByClassName('cms-slides').item(0).children,
            loaders: Array<Promise<Slide>> = [];

        for (let i: number = 0; i < elements.length; i++) {
            let element:HTMLDivElement = <HTMLDivElement>elements.item(i);
            element.style.setProperty('display', 'none');

            loaders.push(
                TypeRegistry
                    .getType(element.getAttribute('data-type'))
                    .then((type:TypeInterface) => {
                        return new Slide(type.build(element, this));
                    })
            );
        }

        Promise
            .all(loaders)
            .then(slides => {
                this.slides = slides;
                this.buildUi();
                this.transitionTo(0);

                this.initialized = true;
                this.trigger('ekyna_cms.slide_show.initialized');

                this.autoNext();

                this.log('initialized');
            }, reason => {
                console.log(reason);
            });
    }

    public autoNext()
    {
        if (!this.options.auto) {
            return;
        }

        if (this.autoTimeout) {
            clearTimeout(this.autoTimeout);
        }

        this.autoTimeout = setTimeout(() => { this.nextSlide() }, 7000);
    }

    /**
     * Shows the slide for the given index.
     *
     * @param {number} index
     * @param {boolean} hard
     */
    public transitionTo(index: number, hard: boolean = false) {
        if ((!hard && this.busy) || this.index === index) {
            return;
        }

        if (!this.initialized) {
            this.once('ekyna_cms.slide_show.initialized', () => {
                this.transitionTo(index, hard)
            });
            return;
        }

        this.index = index;

        this.log('show ' + this.index);

        let next:Slide = this.slides[this.index];

        if (hard) {
            if (this.current) {
                this.current.hide(true);
            }
            this.showSlide(next);
        } else if (this.current) {
            this.hideSlide(this.current, next);
        } else {
            this.showSlide(next);
        }
    }

    /**
     * Shows the next slide
     */
    private nextSlide() {
        this.log('next');

        let index:number = this.index;
        index++;

        if (index >= this.slides.length) {
            index = 0;
        }

        this.transitionTo(index);
    }

    /**
     * Shows the previous slide.
     */
    private prevSlide() {
        this.log('prev');

        let index:number = this.index;
        index--;

        if (index < 0) {
            index = this.slides.length - 1;
        }

        this.transitionTo(index);
    }

    private hideSlide(slide:Slide, next: Slide)
    {
        this.busy = true;

        this.once('ekyna_cms.slide.hidden', () => {
            this.showSlide(next);
        });

        slide.hide();
    }

    private showSlide(slide: Slide)
    {
        this.busy = true;

        this.once('ekyna_cms.slide.shown', () => {
            this.busy = false;
            this.autoNext();
        });

        this.current = slide;

        this.current.show();

        if (this.options.ui) {
            for (let i: number = 0; i < this.nav.children.length; i++) {
                let li: Element = this.nav.children.item(i);
                li.classList.remove('active');
            }

            this.nav.children.item(this.index).classList.add('active');
        }
    }

    /**
     * Builds the slide show UI
     */
    private buildUi() {
        if (!this.options.ui || 1 >= this.slides.length) {
            return;
        }

        this.nav = document.createElement('ul');

        for (let i: number = 0; i < this.slides.length; i++) {
            let li: HTMLLIElement = document.createElement('li'),
                a: HTMLAnchorElement = document.createElement('a');

            a.href = 'javascript: void(0)';
            a.innerText = '&nbsp;';
            a.addEventListener('click', () => {
                this.transitionTo(i)
            });
            li.appendChild(a);
            this.nav.appendChild(li);
        }

        let div: HTMLDivElement = document.createElement('div');
        div.classList.add('cms-slide-show-nav');
        div.appendChild(this.nav);
        this.root.appendChild(div);

        let prev: HTMLAnchorElement = document.createElement('a'),
            next: HTMLAnchorElement = document.createElement('a');

        prev.classList.add('cms-slide-show-prev');
        prev.href = 'javascript: void(0)';
        prev.addEventListener('click', () => {
            this.prevSlide()
        });
        this.root.appendChild(prev);

        next.classList.add('cms-slide-show-next');
        next.href = 'javascript: void(0)';
        next.addEventListener('click', () => {
            this.nextSlide()
        });
        this.root.appendChild(next);
    }

    /**
     * Logs the given message to the console.
     *
     * @param message
     */
    private log(message) {
        if (this.options.debug) {
            console.log('[SlideShow]', message);
        }
    }
}

export = SlideShow;
