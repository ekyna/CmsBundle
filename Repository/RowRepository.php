<?php

namespace Ekyna\Bundle\CmsBundle\Repository;

use Ekyna\Component\Resource\Doctrine\ORM\ResourceRepository;

/**
 * Class RowRepository
 * @package Ekyna\Bundle\CmsBundle\Repository
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class RowRepository extends ResourceRepository
{
    /**
     * Finds the row by id.
     *
     * @param int $name
     *
     * @return \Ekyna\Bundle\CmsBundle\Editor\Model\RowInterface|null
     */
    public function findOneByName($name)
    {
        $qb = $this->getQueryBuilder();

        return $qb
            ->leftJoin('r.blocks', 'block')
            ->leftJoin('block.translations', 'translation')
            ->addSelect('block', 'translation')
            ->andWhere($qb->expr()->eq('r.name', ':name'))
            ->getQuery()
            ->useQueryCache(true)
            // TODO ->useResultCache(true, 3600, Row::getEntityTagPrefix() . '[name:'.$name.']')
            ->setParameter('name', $name)
            ->getOneOrNullResult();
    }

    /**
     * Finds the row by id.
     *
     * @param int $id
     *
     * @return \Ekyna\Bundle\CmsBundle\Editor\Model\RowInterface|null
     */
    public function findOneById($id)
    {
        $qb = $this->getQueryBuilder();

        return $qb
            ->leftJoin('r.blocks', 'block')
            ->leftJoin('block.translations', 'translation')
            ->addSelect('block', 'translation')
            ->andWhere($qb->expr()->eq('r.id', ':id'))
            ->getQuery()
            ->useQueryCache(true)
            // TODO ->useResultCache(true, 3600, Row::getEntityTagPrefix() . '[id:'.$id.']')
            ->setParameter('id', $id)
            ->getOneOrNullResult();
    }

    /**
     * @inheritdoc
     */
    protected function getAlias()
    {
        return 'r';
    }
}
