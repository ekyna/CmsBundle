<?php

namespace Ekyna\Bundle\CmsBundle\Repository;

use DateTime;
use Doctrine\ORM\Query\Expr;
use Ekyna\Bundle\CmsBundle\Entity\Page;
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
     * @return DateTime|null
     */
    public function getLastUpdatedAt(): ?DateTime
    {
        $qb = $this->createQueryBuilder('p');

        $date = $qb
            ->select('p.updatedAt')
            ->addOrderBy('p.updatedAt', 'DESC')
            ->getQuery()
            ->setMaxResults(1)
            ->getSingleScalarResult();

        if (null !== $date) {
            return new DateTime($date);
        }

        return null;
    }

    /**
     * Finds a page by request.
     *
     * @param string $routeName
     * @param bool   $cached
     *
     * @return PageInterface|null
     */
    public function findOneByRoute(string $routeName, bool $cached = false): ?PageInterface
    {
        $qb = $this->getQueryBuilder('p');

        $qb->andWhere($qb->expr()->eq('p.route', ':route'));

        if (!$cached) {
            return $qb
                ->getQuery()
                ->setParameter('route', $routeName)
                ->getOneOrNullResult();
        }

        return $qb
            ->leftJoin('p.seo', 's')
            ->leftJoin('s.translations', 's_t', Expr\Join::WITH, $this->getLocaleCondition('s_t'))
            ->addSelect('s', 's_t')
            ->getQuery()
            ->setParameter('route', $routeName)
            ->useQueryCache(true)
            ->enableResultCache(3600, Page::getRouteCacheTag($routeName))
            ->getOneOrNullResult();
    }

    /**
     * Finds the parents pages (including the given one) for breadcrumb.
     *
     * @param PageInterface $current
     *
     * @return array
     */
    public function findParentsForBreadcrumb(PageInterface $current): array
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
            // TODO ->enableResultCache(3600, $this->getCachePrefix())
            ->getArrayResult();
    }

    /**
     * Returns the pages routes.
     *
     * @return array
     */
    public function getPagesRoutes(): array
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
