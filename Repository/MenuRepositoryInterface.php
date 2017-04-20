<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Repository;

use Ekyna\Bundle\CmsBundle\Model\MenuInterface;
use Ekyna\Component\Resource\Repository\TranslatableRepositoryInterface;

/**
 * Interface MenuRepositoryInterface
 * @package Ekyna\Bundle\CmsBundle\Repository
 * @author  Étienne Dauvergne <contact@ekyna.com>
 *
 * @method MenuInterface|null find(int $id)
 * @method MenuInterface|null findOneBy(array $criteria, array $sorting = [])
 * @method MenuInterface[] findAll()
 * @method MenuInterface[] findBy(array $criteria, array $sorting = [], int $limit = null, int $offset = null)
 */
interface MenuRepositoryInterface extends TranslatableRepositoryInterface
{

}
