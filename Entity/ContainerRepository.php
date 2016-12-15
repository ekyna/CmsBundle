<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Ekyna\Component\Resource\Doctrine\ORM\ResourceRepository;

/**
 * Class ContainerRepository
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class ContainerRepository extends ResourceRepository
{
    /**
     * Finds the container by id.
     *
     * @param int $name
     *
     * @return \Ekyna\Bundle\CmsBundle\Model\ContainerInterface|null
     */
    public function findOneByName($name)
    {
        $qb = $this->getQueryBuilder();

        return $qb
            ->join('c.rows', 'row')
            ->join('row.blocks', 'block')
            ->join('block.translations', 'translation')
            ->addSelect('row', 'block', 'translation')
            ->andWhere($qb->expr()->eq('c.name', ':name'))
            ->setMaxResults(1)
            ->getQuery()
            ->useQueryCache(true)
            // TODO ->useResultCache(true, 3600, Container::getEntityTagPrefix() . '[name:'.$name.']')
            ->setParameter('name', $name)
            ->getOneOrNullResult();
    }

    /**
     * Finds the container by id.
     *
     * @param int $id
     *
     * @return \Ekyna\Bundle\CmsBundle\Model\ContainerInterface|null
     */
    public function findOneById($id)
    {
        $qb = $this->getQueryBuilder();

        return $qb
            ->join('c.rows', 'row')
            ->join('row.blocks', 'block')
            ->join('block.translations', 'translation')
            ->addSelect('row', 'block', 'translation')
            ->andWhere($qb->expr()->eq('c.id', ':id'))
            ->setMaxResults(1)
            ->getQuery()
            ->useQueryCache(true)
            // TODO ->useResultCache(true, 3600, Container::getEntityTagPrefix() . '[id:'.$id.']')
            ->setParameter('id', $id)
            ->getOneOrNullResult();
    }

    /**
     * @inheritdoc
     */
    protected function getAlias()
    {
        return 'c';
    }
}
