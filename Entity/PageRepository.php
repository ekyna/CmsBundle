<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Doctrine\ORM\QueryBuilder;
use Ekyna\Bundle\AdminBundle\Doctrine\ORM\TranslatableResourceRepositoryInterface;
use Ekyna\Bundle\AdminBundle\Doctrine\ORM\Util\TranslatableResourceRepositoryTrait;
use Ekyna\Bundle\CmsBundle\Model\PageInterface;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PageRepository
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class PageRepository extends NestedTreeRepository implements TranslatableResourceRepositoryInterface
{
    use TranslatableResourceRepositoryTrait;

    /**
     * Creates a new QueryBuilder instance.
     *
     * @param string $alias
     *
     * @return QueryBuilder
     */
    public function createQueryBuilder($alias)
    {
        return parent::createQueryBuilder($alias)
            ->innerJoin($alias.'.seo', 'seo');
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
    public function findOneByRoute($routeName)
    {
        $qb = $this->createQueryBuilder('p');
        $query = $qb
            ->andWhere($qb->expr()->eq('p.route', $qb->expr()->literal($routeName)))
            ->setMaxResults(1)
            ->getQuery()
            ->useResultCache(true, 3600, 'ekyna_cms.page[route:'.$routeName.']')
        ;
        return $query->getOneOrNullResult();
    }

    /**
     * Finds a page by id.
     *
     * @param integer $pageId
     * @return null|\Ekyna\Bundle\CmsBundle\Model\PageInterface
     */
    public function findOneById($pageId)
    {
        $qb = $this->createQueryBuilder('p');
        $query = $qb
            ->andWhere($qb->expr()->eq('p.id', $pageId))
            ->setMaxResults(1)
            ->getQuery()
            ->useResultCache(true, 3600, 'ekyna_cms.page[id:'.$pageId.']')
        ;
        return $query->getOneOrNullResult();
    }

    /**
     * Refreshes the page (to load translations).
     *
     * @param PageInterface $page
     */
    public function refresh(PageInterface $page)
    {
        $this->getEntityManager()->refresh($page);
    }
}
