<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Ekyna\Bundle\AdminBundle\Doctrine\ORM\ResourceRepositoryInterface;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PageRepository
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class PageRepository extends NestedTreeRepository implements ResourceRepositoryInterface
{
    /**
     * Creates a new page.
     *
     * @return \Ekyna\Bundle\CmsBundle\Model\PageInterface
     */
    public function createNew()
    {
        $class = $this->getClassName();
        return new $class;
    }

    /**
     * Finds a page by request.
     *
     * @param Request $request
     * @return null|\Ekyna\Bundle\CmsBundle\Model\PageInterface
     */
    public function findByRequest(Request $request)
    {
        return $this->findOneBy(array('route' => $request->attributes->get('_route')));
    }
}
