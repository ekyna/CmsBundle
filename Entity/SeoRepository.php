<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Class SeoRepository
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class SeoRepository extends EntityRepository
{
    /**
     * Finds the seo by his id.
     *
     * @param integer $seoId
     * @return null|\Ekyna\Bundle\CmsBundle\Model\SeoInterface
     */
    public function findOneById($seoId)
    {
        $qb = $this->createQueryBuilder('s');
        $query = $qb
            ->andWhere($qb->expr()->eq('s.id', $seoId))
            ->setMaxResults(1)
            ->getQuery()
            ->useResultCache(true, 3600, 'ekyna_cms.seo[id:'.$seoId.']')
        ;
        return $query->getOneOrNullResult();
    }
}
