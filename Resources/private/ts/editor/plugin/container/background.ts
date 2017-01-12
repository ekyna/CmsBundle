/// <reference path="../../../../../../../../../typings/index.d.ts" />

import Dispatcher from '../../dispatcher';
import {BasePlugin} from '../base-plugin';
import {ContainerManager, SelectionEvent} from '../../document-manager';

/**
 * BackgroundPlugin
 * @todo use CamanJS (http://camanjs.com/guides/)
 */
class BackgroundPlugin extends BasePlugin {
    edit() {
        super.edit();

        this.openModal(
            ContainerManager.generateUrl(this.$element, 'ekyna_cms_editor_container_edit'),
            (e:Ekyna.ModalResponseEvent) => {
                if (e.contentType == 'json') {
                    e.preventDefault();

                    if (e.content.hasOwnProperty('containers')) {
                        ContainerManager.parse(e.content.containers);

                        let event:SelectionEvent = new SelectionEvent();
                        event.$element = this.$element;
                        Dispatcher.trigger('document_manager.select', event);
                    }
                }
            }
        );
    }
}

export = BackgroundPlugin;

