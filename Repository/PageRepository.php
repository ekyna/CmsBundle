<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Repository;

use DateTime;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Ekyna\Bundle\CmsBundle\Entity\Page;
use Ekyna\Bundle\CmsBundle\Model\PageInterface;
use Ekyna\Component\Resource\Doctrine\ORM\Repository\TranslatableRepository;

/**
 * Class PageRepository
 * @package Ekyna\Bundle\CmsBundle\Repository
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class PageRepository extends TranslatableRepository implements PageRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function getLastUpdatedAt(): ?DateTime
    {
        $qb = $this->createQueryBuilder('p');

        /** @noinspection PhpUnhandledExceptionInspection */
        $date = $qb
            ->select('p.updatedAt')
            ->addOrderBy('p.updatedAt', 'DESC')
            ->getQuery()
            ->setMaxResults(1)
            ->getSingleScalarResult();

        if (null !== $date) {
            /** @noinspection PhpUnhandledExceptionInspection */
            return new DateTime($date);
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function findOneByRoute(string $routeName, bool $cached = false): ?PageInterface
    {
        $qb = $this->getQueryBuilder('p');

        $qb->andWhere($qb->expr()->eq('p.route', ':route'));

        if (!$cached) {
            /** @noinspection PhpUnhandledExceptionInspection */
            return $qb
                ->getQuery()
                ->setParameter('route', $routeName)
                ->getOneOrNullResult();
        }

        /** @noinspection PhpUnhandledExceptionInspection */
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
     * @inheritDoc
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
            ->addGroupBy('t.id')
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
     * @inheritDoc
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
    public function getIndexablePages(): array
    {
        $qb = $this->createQueryBuilder('p');

        return $qb
            ->innerJoin('p.seo', 's')
            ->andWhere($qb->expr()->eq('s.index', true))
            ->orderBy('p.left', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @inheritDoc
     */
    public function getRoutesDataByPath(string $path): array
    {
        $qb = $this->createRouteQueryBuilder();

        return $qb
            ->andWhere($qb->expr()->eq('t.path', ':path'))
            ->getQuery()
            ->useQueryCache(true)
            ->setParameter('path', $path)
            // TODO Caching
            ->getArrayResult();
    }

    /**
     * @inheritDoc
     */
    public function getRoutesDataByNames(?array $names): array
    {
        $qb = $this->createRouteQueryBuilder();
        $parameters = [];

        if (!empty($names)) {
            $qb->andWhere($qb->expr()->in('p.route', ':routes'));
            $parameters['routes'] = $names;
        }

        return $qb
            ->getQuery()
            ->useQueryCache(true)
            ->setParameters($parameters)
            // TODO Caching
            ->getArrayResult();
    }

    /**
     * @inheritDoc
     */
    public function getRouteDataByName(string $name): ?array
    {
        $qb = $this->createRouteQueryBuilder();

        return $qb
            ->andWhere($qb->expr()->eq('p.route', ':route'))
            ->getQuery()
            ->setParameter('route', $name)
            // TODO Caching
            ->getOneOrNullResult(Query::HYDRATE_ARRAY);
    }

    /**
     * Creates a route data query builder.
     *
     * @return QueryBuilder
     */
    protected function createRouteQueryBuilder(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('p');

        return $qb
            ->select('p.route, p.controller, t.path, t.locale')
            ->join('p.translations', 't')
            ->andWhere($qb->expr()->eq('p.static', 0));
    }

    /**
     * @inheritDoc
     */
    protected function getAlias(): string
    {
        return 'p';
    }
}
