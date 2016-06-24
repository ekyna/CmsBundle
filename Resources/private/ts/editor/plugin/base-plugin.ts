/// <reference path="../../../../../../../../typings/index.d.ts" />

import * as es6Promise from 'es6-promise';

es6Promise.polyfill();
var Promise = es6Promise.Promise;

export class BasePlugin {
    protected window:Window;

    protected $element:JQuery;
    protected updated:boolean;
    protected destroyed:boolean;

    static setup():Promise<any> {
        return new Promise(function(resolve, reject) {
            resolve();
        })
    }

    static tearDown():Promise<any> {
        return new Promise(function(resolve, reject) {
            resolve();
        })
    }

    constructor($element:JQuery, win:Window) {
        this.window = win;
        this.$element = $element;
        this.updated = false;
    }

    protected setUpdated(updated:boolean):void {
        this.updated = updated;
    }

    public isUpdated():boolean {
        return this.updated;
    }

    edit ():void {
        this.destroyed = false;
    }

    save ():Promise<any> {
        return new Promise((resolve, reject) => {
            if (this.isUpdated()) {
                throw 'Plugin has updates.';
            }
            resolve();
        });
    }

    destroy ():Promise<any> {
        return this
            .save()
            .then(() => {
                this.destroyed = true;
            });
    }

    preventDocumentSelection ($target:JQuery):boolean {
        return false;
    }
}
