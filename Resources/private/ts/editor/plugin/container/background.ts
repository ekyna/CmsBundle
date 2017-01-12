/// <reference path="../../../../../../../../../typings/index.d.ts" />

import {BasePlugin} from '../base-plugin';
import {ContainerManager} from '../../document-manager';

/**
 * BackgroundPlugin
 */
class BackgroundPlugin extends BasePlugin {
    edit() {
        super.edit();

        this.openModal(ContainerManager.generateUrl(this.$element, 'ekyna_cms_editor_container_edit'));
    }
}

export = BackgroundPlugin;

