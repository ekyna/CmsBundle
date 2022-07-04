<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Repository;

use Ekyna\Bundle\CmsBundle\Model\SeoInterface;
use Ekyna\Component\Resource\Repository\ResourceRepositoryInterface;
use Ekyna\Component\Resource\Repository\TranslatableRepositoryInterface;

/**
 * Interface SeoRepositoryInterface
 * @package Ekyna\Bundle\CmsBundle\Repository
 * @author  Étienne Dauvergne <contact@ekyna.com>
 *
 * @implements ResourceRepositoryInterface<SeoInterface>
 */
interface SeoRepositoryInterface extends TranslatableRepositoryInterface
{

}
