/// <reference path="../../../../../../../../../../assets/typings/index.d.ts" />

import {BasePlugin} from '../base-plugin';
import {BaseManager, ContainerManager} from '../../document-manager';

/**
 * BackgroundPlugin
 */
class BackgroundPlugin extends BasePlugin {
    edit() {
        super.edit();

        this.openModal(
            ContainerManager.generateUrl(this.$element, 'admin_ekyna_cms_editor_container_edit'),
            (e:Ekyna.ModalResponseEvent) => {
                if (e.contentType == 'json') {

                    let videos:HTMLCollectionOf<HTMLVideoElement> = BaseManager.getContentWindow().document.getElementsByTagName('video');
                    for (let i = 0; i < videos.length; i++) {
                        videos.item(i).play();
                    }

                    e.modal.close();
                }
            }
        );
    }
}

export = BackgroundPlugin;

