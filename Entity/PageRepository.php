<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

/**
 * PageRepository
 */
class PageRepository extends NestedTreeRepository
{
    /**
     * @return \Ekyna\Bundle\CmsBundle\Entity\Page
     */
    public function createNew()
    {
        $class = $this->getClassName();
        return new $class;
    }
}
