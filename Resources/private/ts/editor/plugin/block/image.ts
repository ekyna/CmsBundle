/// <reference path="../../../../../../../../../typings/index.d.ts" />

import * as $ from 'jquery';
import * as es6Promise from 'es6-promise';
import * as Router from 'routing';
import * as Modal from 'ekyna-modal';

import {BasePlugin} from '../base-plugin';
import {BlockManager, ElementAttributes} from '../../document-manager';

es6Promise.polyfill();
var Promise = es6Promise.Promise;

/**
 * ImagePlugin
 * @todo use CamanJS (http://camanjs.com/guides/)
 */
class ImagePlugin extends BasePlugin {
    //private initPromise:Promise<Modal>;
    modal:Ekyna.Modal;

    edit() {
        var id = (<ElementAttributes>this.$element.data('cms')).id;
        if (!id) {
            throw 'Invalid block id';
        }

        //this.initialize()
        //    .then((Modal:Modal) => {
                this.modal = new Modal();
                this.modal.load({
                    url: Router.generate('ekyna_cms_editor_block_edit', {blockId: id}),
                    method: 'GET'
                });

                $(this.modal).on('ekyna.modal.response', (e:Ekyna.ModalResponseEvent) => {
                    if (e.contentType == 'json') {
                        if (e.content.hasOwnProperty('blocks')) {
                            BlockManager.parse(e.content.blocks);
                        }
                        this.modal.close();
                    }
                });
            //});
    }

    save():Promise<any> {
        return new Promise((resolve, reject) => {
            // TODO

            resolve();
        });
    }

    destroy():Promise<any> {
        return this
            .save()
            .then(() => {
                if (this.modal) {
                    this.modal.close();
                    this.modal = null;
                }
            });
    }

    focus() {

    }

    preventDocumentSelection ($target:JQuery):boolean {
        return false;
    }

    /*private initialize():Promise<any> {
        if (!this.initPromise) {
            this.initPromise = new Promise((resolve) => {
                //noinspection JSFileReferences
                require(['ekyna-modal'], function(modal) {
                    resolve(modal);
                });
            });
        }
        return this.initPromise;
    }*/

}

export = ImagePlugin;

