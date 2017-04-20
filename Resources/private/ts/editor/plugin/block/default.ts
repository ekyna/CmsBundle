/// <reference path="../../../../../../../../../../assets/typings/index.d.ts" />

import {BasePlugin} from '../base-plugin';
import {BlockManager} from '../../document-manager';

/**
 * DefaultPlugin
 */
class DefaultPlugin extends BasePlugin {
    edit() {
        super.edit();

        this.openModal(BlockManager.generateUrl(this.$element, 'admin_ekyna_cms_editor_block_edit'));
    }
}

export = DefaultPlugin;

