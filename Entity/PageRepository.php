<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Doctrine\ORM\Query\Expr;
use Ekyna\Bundle\CmsBundle\Model\PageInterface;
use Ekyna\Component\Resource\Doctrine\ORM\TranslatableResourceRepositoryInterface;
use Ekyna\Component\Resource\Doctrine\ORM\Util\TranslatableResourceRepositoryTrait;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

/**
 * Class PageRepository
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class PageRepository extends NestedTreeRepository implements TranslatableResourceRepositoryInterface
{
    use TranslatableResourceRepositoryTrait;


    /**
     * Returns the last updated at date time.
     *
     * @return \DateTime|null
     */
    public function getLastUpdatedAt()
    {
        $qb = $this->createQueryBuilder('p');

        $date = $qb
            ->select('p.updatedAt')
            ->addOrderBy('p.updatedAt', 'DESC')
            ->getQuery()
            ->setMaxResults(1)
            ->getSingleScalarResult();

        if (null !== $date) {
            return new \DateTime($date);
        }

        return null;
    }

    /**
     * Finds a page by request.
     *
     * @param string $routeName
     *
     * @return null|PageInterface
     */
    public function findOneByRoute($routeName)
    {
        $qb = $this->getQueryBuilder('p');

        return $qb
            ->leftJoin('p.seo', 's')
            ->leftJoin('s.translations', 's_t', Expr\Join::WITH, $this->getLocaleCondition('s_t'))
            ->addSelect('s', 's_t')
            ->andWhere($qb->expr()->eq('p.route', ':route_name'))
            ->getQuery()
            ->setParameter('route_name', $routeName)
            ->useQueryCache(true)
            // TODO ->useResultCache(true, 3600, 'ekyna_cms.page[route:' . $routeName . ']')
            ->getOneOrNullResult();
    }

    /**
     * Finds the parents pages (including the given one) for breadcrumb.
     *
     * @param PageInterface $current
     *
     * @return array
     */
    public function findParentsForBreadcrumb(PageInterface $current)
    {
        $qb = $this->createQueryBuilder('p');

        return $qb
            ->select(['p.id', 'p.route', 'p.dynamicPath', 't.breadcrumb'])
            ->leftJoin('p.translations', 't', Expr\Join::WITH, $this->getLocaleCondition('t'))
            ->andWhere($qb->expr()->lte('p.left', ':left'))
            ->andWhere($qb->expr()->gte('p.right', ':right'))
            ->addOrderBy('p.left', 'asc')
            ->addGroupBy('p.id')
            ->getQuery()
            ->setParameters([
                'left'  => $current->getLeft(),
                'right' => $current->getRight(),
            ])
            ->useQueryCache(true)
            // TODO ->useResultCache(true, 3600, $this->getCachePrefix())
            ->getArrayResult();
    }

    /**
     * Returns the pages routes.
     *
     * @return array
     */
    public function getPagesRoutes()
    {
        $qb = $this->createQueryBuilder('p');

        $results = $qb
            ->select('p.route')
            ->getQuery()
            ->getScalarResult();

        return array_column($results, 'route');
    }

    /**
     * @inheritDoc
     */
    protected function getAlias()
    {
        return 'p';
    }
}
