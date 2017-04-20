<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Event;

/**
 * Class PageEvents
 * @package Ekyna\Bundle\CmsBundle\Event
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
final class PageEvents
{
    // Persistence
    public const INSERT      = 'ekyna_cms.page.insert';
    public const UPDATE      = 'ekyna_cms.page.update';
    public const DELETE      = 'ekyna_cms.page.delete';

    // Domain
    public const PRE_CREATE  = 'ekyna_cms.page.pre_create';
    public const POST_CREATE = 'ekyna_cms.page.post_create';
    public const PRE_UPDATE  = 'ekyna_cms.page.pre_update';
    public const POST_UPDATE = 'ekyna_cms.page.post_update';
    public const PRE_DELETE  = 'ekyna_cms.page.pre_delete';
    public const POST_DELETE = 'ekyna_cms.page.post_delete';
    public const PUBLIC_URL  = 'ekyna_cms.page.public_url';

    /**
     * Disabled constructor.
     */
    private function __construct()
    {
    }
}
