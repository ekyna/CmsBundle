/// <reference path="../../../../../../../../../typings/index.d.ts" />

import {BasePlugin} from '../base-plugin';
import {BlockManager} from '../../document-manager';

/**
 * ImagePlugin
 * @todo use CamanJS (http://camanjs.com/guides/)
 */
class ImagePlugin extends BasePlugin {
    edit() {
        super.edit();

        this.openModal(BlockManager.generateUrl(this.$element, 'ekyna_cms_editor_block_edit'));
    }
}

export = ImagePlugin;

