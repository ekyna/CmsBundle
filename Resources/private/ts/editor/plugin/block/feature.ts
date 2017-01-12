/// <reference path="../../../../../../../../../typings/index.d.ts" />

import * as $ from 'jquery';
import * as AOS from 'aos';
import * as es6Promise from 'es6-promise';
import * as Modal from 'ekyna-modal';
import Dispatcher from '../../dispatcher';

import {BasePlugin} from '../base-plugin';
import {BlockManager, SelectionEvent} from '../../document-manager';

es6Promise.polyfill();
let Promise = es6Promise.Promise;

/**
 * FeaturePlugin
 */
class FeaturePlugin extends BasePlugin {
    modal:Ekyna.Modal;

    edit() {
        super.edit();

        this.modal = new Modal();
        this.modal.load({
            url: BlockManager.generateUrl(this.$element, 'ekyna_cms_editor_block_edit'),
            method: 'GET'
        });

        $(this.modal).on('ekyna.modal.response', (e:Ekyna.ModalResponseEvent) => {
            if (e.contentType == 'json') {
                e.preventDefault();

                if (e.content.hasOwnProperty('blocks')) {
                    BlockManager.parse(e.content.blocks);

                    let event:SelectionEvent = new SelectionEvent();
                    event.$element = this.$element;
                    Dispatcher.trigger('document_manager.select', event);

                    AOS.refresh();
                }
            }
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

                return super.destroy();
            });
    }
}

export = FeaturePlugin;
