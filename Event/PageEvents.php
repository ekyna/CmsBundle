<?php

namespace Ekyna\Bundle\CmsBundle\Event;

/**
 * Class PageEvents
 * @package Ekyna\Bundle\CmsBundle\Event
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class PageEvents
{
    // Persistence
    const INSERT      = 'ekyna_cms.page.insert';
    const UPDATE      = 'ekyna_cms.page.update';
    const DELETE      = 'ekyna_cms.page.delete';

    // Domain
    const INITIALIZE  = 'ekyna_cms.page.initialize';

    const PRE_CREATE  = 'ekyna_cms.page.pre_create';
    const POST_CREATE = 'ekyna_cms.page.post_create';

    const PRE_UPDATE  = 'ekyna_cms.page.pre_update';
    const POST_UPDATE = 'ekyna_cms.page.post_update';

    const PRE_DELETE  = 'ekyna_cms.page.pre_delete';
    const POST_DELETE = 'ekyna_cms.page.post_delete';

    const PUBLIC_URL  = 'ekyna_cms.page.public_url';
}
