/// <reference path="../../../../../../../../assets/typings/index.d.ts" />
/// <reference path="../../typings/slide-show.d.ts" />

import * as es6Promise from 'es6-promise';
import * as _ from 'underscore';
import TimelineLite = require('gsap/TimelineLite');
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
            this.timeline.seek(0, false);
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

interface SlideShowConfig {
    id: string
    types: { [name: string]: string }
    ui: boolean
    uiHover: boolean
    uiSpeed: number,
    auto: boolean
    autoDelay: number
    debug: boolean
}

let DEFAULT_OPTIONS = {
    id: null,
    types: {},
    ui: true,
    uiHover: false,
    uiSpeed: .2,
    auto: true,
    autoDelay: 7,
    debug: false
};

class SlideShow extends Dispatcher {
    static create(options: SlideShowConfig) {
        let slideShow = new SlideShow();
        slideShow.init(options);
        return slideShow;
    }

    config: SlideShowConfig;
    root: HTMLElement;
    nav: HTMLUListElement;
    slides: Array<Slide>;
    busy: boolean;
    initialized: boolean;
    index: number;
    current: Slide;
    autoPlaying: boolean;
    timerTimeline: gsap.TimelineLite;
    uiTimeline: gsap.TimelineLite;

    /**
     * Initializes the slide show.
     *
     * @param {SlideShowConfig} options
     */
    init(options: SlideShowConfig) {
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

        this.config = _.defaults({}, options, config, DEFAULT_OPTIONS);
        this.slides = [];
        this.busy = false;
        this.autoPlaying = true;

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

                this.on('ekyna_cms.slide.timeout', () => {
                    this.nextSlide();
                });
                //this.autoNext();

                this.log('initialized');
            }, reason => {
                console.log(reason);
            });
    }

    public autoNext()
    {
        if (!this.config.auto) {
            return;
        }

        if (!this.autoPlaying) {
            return;
        }

        this.timerTimeline.restart(false, false);
        //this.timerTween.play();

        /*if (this.autoTimeout) {
            clearTimeout(this.autoTimeout);
        }

        this.autoTimeout = setTimeout(() => { this.nextSlide() }, this.options.autoDelay * 1000);*/
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

        if (this.busy) {
            return;
        }

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

        if (this.busy) {
            return;
        }

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

        if (this.config.ui) {
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
        if (!this.config.ui || 1 >= this.slides.length) {
            return;
        }

        if (this.config.uiHover) {
            this.uiTimeline = new TimelineLite();
        }

        this.nav = document.createElement('ul');

        // Slides buttons
        let offset:number = .1;
        for (let i:number = 0; i < this.slides.length; i++) {
            let li: HTMLLIElement = document.createElement('li'),
                a: HTMLAnchorElement = document.createElement('a');

            a.href = 'javascript: void(0)';
            //a.innerText = '&nbsp;';
            a.classList.add('slide');
            a.addEventListener('click', () => {
                if (this.busy) {
                    return;
                }
                this.timerTimeline.pause();
                this.transitionTo(i)
            });
            li.appendChild(a);
            this.nav.appendChild(li);

            if (this.config.uiHover) {
                this.uiTimeline.from(li, this.config.uiSpeed, {y: 20, opacity: 0, ease: Back.easeOut}, offset * i);
            }
        }

        // Play / pause button
        if (this.config.auto) {
            let li: HTMLLIElement = document.createElement('li'),
                a: HTMLAnchorElement = document.createElement('a');
            a.href = 'javascript: void(0)';
            a.classList.add('play');
            a.classList.add('pause');
            a.addEventListener('click', () => {
                if (this.busy) {
                    return;
                }
                this.autoPlaying = !this.autoPlaying;
                if (this.autoPlaying) {
                    a.classList.add('pause');
                    this.timerTimeline.resume();
                } else {
                    a.classList.remove('pause');
                    this.timerTimeline.pause();
                }
            });
            li.appendChild(a);
            this.nav.appendChild(li);

            if (this.config.uiHover) {
                this.uiTimeline.from(li, this.config.uiSpeed, {
                    y: 20,
                    opacity: 0,
                    ease: Back.easeOut
                }, offset * this.slides.length);
            }
        }

        let nav: HTMLDivElement = document.createElement('div');
        nav.classList.add('cms-slide-show-nav');
        nav.appendChild(this.nav);
        this.root.appendChild(nav);


        // Timer
        let timer: HTMLDivElement = document.createElement('div');
        timer.classList.add('cms-slide-show-timer');
        this.root.appendChild(timer);

        this.timerTimeline = new TimelineLite();
        this.timerTimeline.to(timer, this.config.autoDelay, {width: '100%', ease: Power0.easeNone, onComplete: () => {
            this.trigger('ekyna_cms.slide.timeout');
        }});
        this.timerTimeline.to(timer, .15, {opacity: 0});
        this.timerTimeline.pause();


        // Next /prev buttons
        let prev: HTMLAnchorElement = document.createElement('a'),
            next: HTMLAnchorElement = document.createElement('a');

        prev.classList.add('cms-slide-show-prev');
        prev.href = 'javascript: void(0)';
        prev.addEventListener('click', () => {
            this.timerTimeline.pause();
            this.prevSlide();
        });
        this.root.appendChild(prev);

        next.classList.add('cms-slide-show-next');
        next.href = 'javascript: void(0)';
        next.addEventListener('click', () => {
            this.timerTimeline.pause();
            this.nextSlide();
        });
        this.root.appendChild(next);

        if (this.config.uiHover) {
            this.uiTimeline.from(prev, this.config.uiSpeed, {x: '-100% 0'}, 0);
            this.uiTimeline.from(next, this.config.uiSpeed, {x: '100% 0'}, 0);

            this.uiTimeline.pause();

            this.root.addEventListener('mouseenter', () => {
                this.uiTimeline.play();
            });
            this.root.addEventListener('mouseleave', () => {
                this.uiTimeline.reverse();
            });
        }
    }

    /**
     * Logs the given message to the console.
     *
     * @param message
     */
    private log(message) {
        if (this.config.debug) {
            console.log('[SlideShow]', message);
        }
    }
}

export = SlideShow;
