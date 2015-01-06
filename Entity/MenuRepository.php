<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Ekyna\Bundle\AdminBundle\Doctrine\ORM\ResourceRepositoryInterface;

/**
 * Class MenuRepository
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class MenuRepository extends NestedTreeRepository implements ResourceRepositoryInterface
{
    /**
     * Creates a new menu.
     *
     * @return \Ekyna\Bundle\CmsBundle\Entity\Menu
     */
    public function createNew()
    {
        $class = $this->getClassName();
        return new $class;
    }
}
