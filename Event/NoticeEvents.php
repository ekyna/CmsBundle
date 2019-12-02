<?php

namespace Ekyna\Bundle\CmsBundle\Event;

/**
 * Class NoticeEvents
 * @package Ekyna\Bundle\CmsBundle\Event
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
final class NoticeEvents
{
    // Persistence
    const INSERT      = 'ekyna_cms.notice.insert';
    const UPDATE      = 'ekyna_cms.notice.update';
    const DELETE      = 'ekyna_cms.notice.delete';

    // Domain
    const INITIALIZE  = 'ekyna_cms.notice.initialize';

    const PRE_CREATE  = 'ekyna_cms.notice.pre_create';
    const POST_CREATE = 'ekyna_cms.notice.post_create';

    const PRE_UPDATE  = 'ekyna_cms.notice.pre_update';
    const POST_UPDATE = 'ekyna_cms.notice.post_update';

    const PRE_DELETE  = 'ekyna_cms.notice.pre_delete';
    const POST_DELETE = 'ekyna_cms.notice.post_delete';

    /**
     * Disabled constructor.
     */
    private function __construct()
    {
    }
}
