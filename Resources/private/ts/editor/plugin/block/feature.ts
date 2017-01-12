/// <reference path="../../../../../../../../../typings/index.d.ts" />

import Dispatcher from '../../dispatcher';
import {BasePlugin} from '../base-plugin';
import {BlockManager, SelectionEvent} from '../../document-manager';
import * as AOS from 'aos';


/**
 * FeaturePlugin
 */
class FeaturePlugin extends BasePlugin {
    edit() {
        super.edit();

        this.openModal(
            BlockManager.generateUrl(this.$element, 'ekyna_cms_editor_block_edit'),
            (e:Ekyna.ModalResponseEvent) => {
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
            }
        );
    }
}

export = FeaturePlugin;
