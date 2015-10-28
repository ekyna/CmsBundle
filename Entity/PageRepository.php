<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Doctrine\ORM\QueryBuilder;
use Ekyna\Bundle\AdminBundle\Doctrine\ORM\TranslatableResourceRepositoryInterface;
use Ekyna\Bundle\AdminBundle\Doctrine\ORM\Util\TranslatableResourceRepositoryTrait;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

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
     * @param string $routeName
     * @return null|\Ekyna\Bundle\CmsBundle\Model\PageInterface
     */
    public function findOneByRoute($routeName)
    {
        $qb = $this->createQueryBuilder('p');

        return $qb
            ->andWhere($qb->expr()->eq('p.route', $qb->expr()->literal($routeName)))
            ->setMaxResults(1)
            ->getQuery()
            ->useResultCache(true, 3600, 'ekyna_cms.page[route:'.$routeName.']')
            ->getOneOrNullResult()
        ;
    }
}
