/// <reference path="../../../../../../../../../../assets/typings/index.d.ts" />

import {BasePlugin} from '../base-plugin';
import {BlockManager} from '../../document-manager';
import * as AOS from 'aos';


/**
 * FeaturePlugin
 */
class FeaturePlugin extends BasePlugin {
    edit() {
        super.edit();

        this.openModal(
            BlockManager.generateUrl(this.$element, 'admin_ekyna_cms_editor_block_edit'),
            (e:Ekyna.ModalResponseEvent) => {
                if (e.contentType == 'json') {
                    AOS.refresh();
                    e.modal.close();
                }
            }
        );
    }
}

export = FeaturePlugin;
