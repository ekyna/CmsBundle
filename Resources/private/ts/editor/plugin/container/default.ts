/// <reference path="../../../../../../../../../../assets/typings/index.d.ts" />

import {BasePlugin} from '../base-plugin';
import {ContainerManager} from '../../document-manager';

/**
 * DefaultPlugin
 */
class DefaultPlugin extends BasePlugin {
    edit() {
        super.edit();

        this.openModal(ContainerManager.generateUrl(this.$element, 'admin_ekyna_cms_editor_container_edit'));
    }
}

export = DefaultPlugin;

