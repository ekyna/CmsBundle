<?php

namespace Ekyna\Bundle\CmsBundle\Repository;

use Ekyna\Component\Resource\Doctrine\ORM\TranslatableResourceRepository;

/**
 * Class BlockRepository
 * @package Ekyna\Bundle\CmsBundle\Repository
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class BlockRepository extends TranslatableResourceRepository
{
    /**
     * Finds the block by name
     *
     * @param string $name
     *
     * @return \Ekyna\Bundle\CmsBundle\Editor\Model\BlockInterface|null
     */
    public function findOneByName($name)
    {
        $qb = $this->getQueryBuilder();

        return $qb
            ->andWhere($qb->expr()->eq('b.name', ':name'))
            ->andWhere($qb->expr()->isNull('b.row'))
            ->getQuery()
            ->useQueryCache(true)
            // TODO ->enableResultCache(3600, Block::getEntityTagPrefix() . '[name:'.$name.']')
            ->setParameter('name', $name)
            ->getOneOrNullResult();
    }

    /**
     * Finds the block by id.
     *
     * @param int $id
     *
     * @return \Ekyna\Bundle\CmsBundle\Editor\Model\BlockInterface|null
     */
    public function findOneById($id)
    {
        $qb = $this->getQueryBuilder();

        return $qb
            ->andWhere($qb->expr()->eq('b.id', ':id'))
            ->getQuery()
            ->useQueryCache(true)
            // TODO ->enableResultCache(3600, Block::getEntityTagPrefix() . '[id:'.$id.']')
            ->setParameter('id', $id)
            ->getOneOrNullResult();
    }

    /**
     * @inheritdoc
     */
    protected function getAlias()
    {
        return 'b';
    }
}
