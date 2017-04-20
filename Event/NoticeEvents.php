<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Event;

/**
 * Class NoticeEvents
 * @package Ekyna\Bundle\CmsBundle\Event
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
final class NoticeEvents
{
    // Persistence
    public const INSERT      = 'ekyna_cms.notice.insert';
    public const UPDATE      = 'ekyna_cms.notice.update';
    public const DELETE      = 'ekyna_cms.notice.delete';

    // Domain
    public const PRE_CREATE  = 'ekyna_cms.notice.pre_create';
    public const POST_CREATE = 'ekyna_cms.notice.post_create';
    public const PRE_UPDATE  = 'ekyna_cms.notice.pre_update';
    public const POST_UPDATE = 'ekyna_cms.notice.post_update';
    public const PRE_DELETE  = 'ekyna_cms.notice.pre_delete';
    public const POST_DELETE = 'ekyna_cms.notice.post_delete';

    /**
     * Disabled constructor.
     */
    private function __construct()
    {
    }
}
