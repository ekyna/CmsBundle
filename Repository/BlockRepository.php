<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Repository;

use Ekyna\Bundle\CmsBundle\Editor\Model\BlockInterface;
use Ekyna\Component\Resource\Doctrine\ORM\Repository\TranslatableRepository;

/**
 * Class BlockRepository
 * @package Ekyna\Bundle\CmsBundle\Repository
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class BlockRepository extends TranslatableRepository
{
    /**
     * Finds the block by its name.
     *
     * @param string $name
     *
     * @return BlockInterface|null
     */
    public function findOneByName(string $name): ?BlockInterface
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
     * Finds the block by its id.
     *
     * @param int $id
     *
     * @return BlockInterface|null
     */
    public function findOneById(int $id): ?BlockInterface
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
     * @inheritDoc
     */
    protected function getAlias(): string
    {
        return 'b';
    }
}
