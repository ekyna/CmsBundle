<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Class ContentRepository
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class ContentRepository extends EntityRepository
{
    /**
     * Finds the content by his id.
     *
     * @param integer $contentId
     * @return null|\Ekyna\Bundle\CmsBundle\Model\ContentInterface
     */
    public function findOneById($contentId)
    {
        $qb = $this->createQueryBuilder('s');
        $query = $qb
            ->andWhere($qb->expr()->eq('s.id', $contentId))
            ->setMaxResults(1)
            ->getQuery()
            //->useResultCache(true, 3600, 'ekyna_cms.content[id:'.$contentId.']') // TODO doctrine cache clear/update
        ;
        return $query->getOneOrNullResult();
    }
}
