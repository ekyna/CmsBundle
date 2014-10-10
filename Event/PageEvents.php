<?php

namespace Ekyna\Bundle\CmsBundle\Event;

/**
 * Class PageEvents
 * @package Ekyna\Bundle\CmsBundle\Event
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class PageEvents
{
    const PRE_CREATE  = 'ekyna_cms.page.pre_create';
    const POST_CREATE = 'ekyna_cms.page.post_create';

    const PRE_UPDATE  = 'ekyna_cms.page.pre_update';
    const POST_UPDATE = 'ekyna_cms.page.pre_delete';

    const PRE_DELETE  = 'ekyna_cms.page.pre_update';
    const POST_DELETE = 'ekyna_cms.page.pre_delete';
}
