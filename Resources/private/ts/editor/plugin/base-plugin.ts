/// <reference path="../../../../../../../../typings/tsd.d.ts" />

import es6Promise = require('es6-promise');
es6Promise.polyfill();
var Promise = es6Promise.Promise;

export class BasePlugin {
    protected window:Window;
    protected $block:JQuery;
    protected updated:boolean;
    protected destroyed:boolean;

    constructor($block:JQuery, win:Window) {
        this.window = win;
        this.$block = $block;
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

    focus ():void {

    }

    preventDocumentSelection ($target:JQuery):boolean {
        return false;
    }
}
