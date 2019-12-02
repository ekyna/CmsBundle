<?php

namespace Ekyna\Bundle\CmsBundle\Repository;

use Ekyna\Bundle\CmsBundle\Model\NoticeInterface;

/**
 * Interface NoticeRepositoryInterface
 * @package Ekyna\Bundle\CmsBundle\Repository
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
interface NoticeRepositoryInterface
{
    public const CACHE_KEY = 'cms_active_notices';

    /**
     * Finds the active notices.
     *
     * @return NoticeInterface[]
     */
    public function findActives(): array;
}
