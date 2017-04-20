/// <reference path="../../../../../../../../../../assets/typings/index.d.ts" />

import {BasePlugin} from '../base-plugin';
import {BlockManager} from '../../document-manager';
import * as Tabs from 'ekyna-cms/cms/tabs';

/**
 * TabsPlugin
 */
class TabsPlugin extends BasePlugin {
    edit() {
        super.edit();

        this.openModal(
            BlockManager.generateUrl(this.$element, 'admin_ekyna_cms_editor_block_edit'),
            (e:Ekyna.ModalResponseEvent) => {
                if (e.contentType == 'json') {
                    Tabs.init(this.$element);
                    e.modal.close();
                }
            }
        );
    }
}

export = TabsPlugin;
