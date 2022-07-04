<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Repository;

use Ekyna\Bundle\CmsBundle\Model\MenuInterface;
use Ekyna\Component\Resource\Repository\ResourceRepositoryInterface;
use Ekyna\Component\Resource\Repository\TranslatableRepositoryInterface;

/**
 * Interface MenuRepositoryInterface
 * @package Ekyna\Bundle\CmsBundle\Repository
 * @author  Étienne Dauvergne <contact@ekyna.com>
 *
 * @implements ResourceRepositoryInterface<MenuInterface>
 */
interface MenuRepositoryInterface extends TranslatableRepositoryInterface
{

}
