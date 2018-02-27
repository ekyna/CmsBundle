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
        $alias = $this->getAlias();
        $qb = $this->getQueryBuilder();

        return $qb
            ->leftJoin($alias . '.seo', 's')
            ->leftJoin('s.translations', 's_t', Expr\Join::WITH, $this->getLocaleCondition('s_t'))
            ->addSelect('s', 's_t')
            ->andWhere($qb->expr()->eq($alias . '.route', ':route_name'))
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
        $qb = $this->createQueryBuilder();
        $alias = $this->getAlias();

        return $qb
            ->select([$alias . '.id', $alias . '.route', $alias . '.dynamicPath', 't.breadcrumb'])
            ->leftJoin($alias . '.translations', 't', Expr\Join::WITH, $this->getLocaleCondition('t'))
            ->andWhere($qb->expr()->lte($alias . '.left', ':left'))
            ->andWhere($qb->expr()->gte($alias . '.right', ':right'))
            ->addOrderBy($alias . '.left', 'asc')
            ->addGroupBy($alias . '.id')
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
     * @inheritDoc
     */
    protected function getAlias()
    {
        return 'p';
    }
}
