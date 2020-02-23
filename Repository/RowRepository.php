<?php

namespace Ekyna\Bundle\CmsBundle\Repository;

use Doctrine\ORM\Query\Expr;
use Ekyna\Component\Resource\Doctrine\ORM\ResourceRepository;
use Ekyna\Component\Resource\Doctrine\ORM\Util\LocaleAwareRepositoryTrait;

/**
 * Class RowRepository
 * @package Ekyna\Bundle\CmsBundle\Repository
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class RowRepository extends ResourceRepository
{
    use LocaleAwareRepositoryTrait;

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
            ->leftJoin('block.translations', 'block_t', Expr\Join::WITH, $this->getLocaleCondition('block_t'))
            ->addSelect('block', 'block_t')
            ->andWhere($qb->expr()->eq('r.name', ':name'))
            ->getQuery()
            ->useQueryCache(true)
            // TODO ->enableResultCache(3600, Row::getEntityTagPrefix() . '[name:'.$name.']')
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
            ->leftJoin('block.translations', 'block_t', Expr\Join::WITH, $this->getLocaleCondition('block_t'))
            ->addSelect('block', 'block_t')
            ->andWhere($qb->expr()->eq('r.id', ':id'))
            ->getQuery()
            ->useQueryCache(true)
            // TODO ->enableResultCache(3600, Row::getEntityTagPrefix() . '[id:'.$id.']')
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
