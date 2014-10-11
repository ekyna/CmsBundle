<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Ekyna\Bundle\AdminBundle\Doctrine\ORM\ResourceRepositoryInterface;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

/**
 * Class PageRepository
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class PageRepository extends NestedTreeRepository implements ResourceRepositoryInterface
{
    /**
     * @return \Ekyna\Bundle\CmsBundle\Model\PageInterface
     */
    public function createNew()
    {
        $class = $this->getClassName();
        return new $class;
    }
}
