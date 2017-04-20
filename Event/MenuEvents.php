<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Event;

/**
 * Class MenuEvents
 * @package Ekyna\Bundle\CmsBundle\Event
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
final class MenuEvents
{
    // Persistence
    public const INSERT      = 'ekyna_cms.menu.insert';
    public const UPDATE      = 'ekyna_cms.menu.update';
    public const DELETE      = 'ekyna_cms.menu.delete';

    // Domain
    public const PRE_CREATE  = 'ekyna_cms.menu.pre_create';
    public const POST_CREATE = 'ekyna_cms.menu.post_create';
    public const PRE_UPDATE  = 'ekyna_cms.menu.pre_update';
    public const POST_UPDATE = 'ekyna_cms.menu.post_update';
    public const PRE_DELETE  = 'ekyna_cms.menu.pre_delete';
    public const POST_DELETE = 'ekyna_cms.menu.post_delete';

    /**
     * Disabled constructor.
     */
    private function __construct()
    {
    }
}
