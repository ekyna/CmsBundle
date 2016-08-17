<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Ekyna\Component\Resource\Doctrine\ORM\TranslatableResourceRepositoryInterface;
use Ekyna\Component\Resource\Doctrine\ORM\Util\TranslatableResourceRepositoryTrait;
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
     * {@inheritdoc}
     */
    public function createQueryBuilder($alias, $indexBy = null)
    {
        return parent::createQueryBuilder($alias, $indexBy)
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
