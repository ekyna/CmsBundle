<?php

namespace Ekyna\Bundle\CmsBundle\Event;

/**
 * Class MenuEvents
 * @package Ekyna\Bundle\CmsBundle\Event
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
final class MenuEvents
{
    // Persistence
    const INSERT      = 'ekyna_cms.menu.insert';
    const UPDATE      = 'ekyna_cms.menu.update';
    const DELETE      = 'ekyna_cms.menu.delete';

    // Domain
    const INITIALIZE  = 'ekyna_cms.menu.initialize';

    const PRE_CREATE  = 'ekyna_cms.menu.pre_create';
    const POST_CREATE = 'ekyna_cms.menu.post_create';

    const PRE_UPDATE  = 'ekyna_cms.menu.pre_update';
    const POST_UPDATE = 'ekyna_cms.menu.post_update';

    const PRE_DELETE  = 'ekyna_cms.menu.pre_delete';
    const POST_DELETE = 'ekyna_cms.menu.post_delete';

    const CONFIGURE   = 'ekyna_cms.menu.configure';
}
