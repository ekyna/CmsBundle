/// <reference path="../../../../../../../../typings/index.d.ts" />

import * as es6Promise from 'es6-promise';
import * as Modal from 'ekyna-modal';
import Dispatcher from '../dispatcher';

es6Promise.polyfill();
let Promise = es6Promise.Promise;

export class BasePlugin {
    protected window:any;

    protected $element:JQuery;
    protected updated:boolean;
    protected destroyed:boolean;
    protected modal:Ekyna.Modal;

    static setup():Promise<any> {
        //noinspection JSUnusedLocalSymbols
        return new Promise(function(resolve, reject) {
            resolve();
        })
    }

    static tearDown():Promise<any> {
        //noinspection JSUnusedLocalSymbols
        return new Promise(function(resolve, reject) {
            resolve();
        })
    }

    constructor($element:JQuery, win:Window) {
        this.window = win;
        this.$element = $element;
        this.updated = false;
    }

    public isUpdated():boolean {
        return this.updated;
    }

    edit ():void {
        this.destroyed = false;
    }

    save ():Promise<any> {
        //noinspection JSUnusedLocalSymbols
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
                if (this.modal) {
                    this.modal.close();
                    this.modal = null;
                }

                this.destroyed = true;
            });
    }

    preventDocumentSelection ($target:JQuery):boolean {
        return false;
    }

    protected setUpdated(updated:boolean):void {
        this.updated = updated;
    }

    protected openModal(url: string, jsonCallback: (e:Ekyna.ModalResponseEvent) => void):void {
        Dispatcher.trigger('editor.set_busy');

        this.modal = new Modal();
        this.modal.load({
            url: url,
            method: 'GET'
        });

        $(this.modal)
            .on('ekyna.modal.response', (e:Ekyna.ModalResponseEvent) => {
                if (e.contentType == 'json') {
                    e.preventDefault();

                    jsonCallback(e);
                }
            })
            .on('ekyna.modal.show', () => {
                Dispatcher.trigger('editor.unset_busy');
            })
            .on('ekyna.modal.hide', () => {
                Dispatcher.trigger('editor.unset_busy');
            });
    }
}
