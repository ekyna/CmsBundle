<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Repository;

use DateTime;
use Doctrine\ORM\Query\Expr;
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

        $locale = $this->getLocaleProvider()->getCurrentLocale();

        return $qb
            ->leftJoin('p.seo', 's')
            ->leftJoin('s.translations', 's_t', Expr\Join::WITH, $this->getLocaleCondition('s_t'))
            ->addSelect('s', 's_t')
            ->getQuery()
            ->setParameter('route', $routeName)
            ->useQueryCache(true)
            ->enableResultCache(3600, Page::getRouteCacheTag($routeName, $locale))
            ->getOneOrNullResult();
    }

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
            // TODO ->enableResultCache(3600, $this->getCachePrefix()) + current locale
            ->getArrayResult();
    }

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

    public function getPagesRoutesNames(): array
    {
        $qb = $this->createQueryBuilder('p');

        $results = $qb
            ->select('p.route')
            ->getQuery()
            ->getScalarResult();

        return array_column($results, 'route');
    }

    public function getDynamicRouterData(): array
    {
        $qb = $this->createQueryBuilder('p');

        return $qb
            ->select('p.route, p.controller, t.path, t.locale')
            ->join('p.translations', 't')
            ->andWhere($qb->expr()->eq('p.static', 0))
            ->getQuery()
            ->getArrayResult();
    }

    protected function getAlias(): string
    {
        return 'p';
    }
}
