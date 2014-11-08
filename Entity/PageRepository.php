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
    public function findOneByRequest(Request $request)
    {
        if (null !== $routeName = $request->attributes->get('_route', null)) {
            return $this->findOneByRoute($routeName);
        }
        return null;
    }

    /**
     * Finds a page by request.
     *
     * @param string $routeName
     * @return null|\Ekyna\Bundle\CmsBundle\Model\PageInterface
     */
    public function findByRoute($routeName)
    {

        $qb = $this->createQueryBuilder('p');
        $query = $qb
            ->andWhere($qb->expr()->eq('p.route', $qb->expr()->literal($routeName)))
            ->getQuery()
            ->useResultCache(true, 3600, 'page:route['.$routeName.']')
        ;
        return $query->getOneOrNullResult();
    }

    /**
     * Finds a page by request.
     *
     * @param string $routeName
     * @return null|\Ekyna\Bundle\CmsBundle\Model\PageInterface
     */
    public function findOneByRoute($routeName)
    {

        $qb = $this->createQueryBuilder('p');
        $query = $qb
            ->andWhere($qb->expr()->eq('p.route', $qb->expr()->literal($routeName)))
            ->setMaxResults(1)
            ->getQuery()
            ->useResultCache(true, 3600, 'page:route['.$routeName.']')
        ;
        return $query->getOneOrNullResult();
    }
}
