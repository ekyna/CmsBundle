<?php

namespace Ekyna\Bundle\CmsBundle\Repository;

use Doctrine\ORM\Query;
use Ekyna\Bundle\CmsBundle\Editor\Model\ContainerInterface;
use Ekyna\Component\Resource\Doctrine\ORM\ResourceRepository;

/**
 * Class ContainerRepository
 * @package Ekyna\Bundle\CmsBundle\Repository
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class ContainerRepository extends ResourceRepository
{
    /**
     * Finds the container by id.
     *
     * @param int $name
     *
     * @return \Ekyna\Bundle\CmsBundle\Editor\Model\ContainerInterface|null
     */
    public function findOneByName($name)
    {
        $qb = $this->getQueryBuilder();

        return $qb
            ->leftJoin('c.rows', 'row')
            ->leftJoin('row.blocks', 'block')
            ->leftJoin('block.translations', 'translation')
            ->addSelect('row', 'block', 'translation')
            ->andWhere($qb->expr()->eq('c.name', ':name'))
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
     * @return \Ekyna\Bundle\CmsBundle\Editor\Model\ContainerInterface|null
     */
    public function findOneById($id)
    {
        $qb = $this->getQueryBuilder();

        return $qb
            ->leftJoin('c.rows', 'row')
            ->leftJoin('row.blocks', 'block')
            ->leftJoin('block.translations', 'translation')
            ->addSelect('row', 'block', 'translation')
            ->andWhere($qb->expr()->eq('c.id', ':id'))
            ->getQuery()
            ->useQueryCache(true)
            // TODO ->useResultCache(true, 3600, Container::getEntityTagPrefix() . '[id:'.$id.']')
            ->setParameter('id', $id)
            ->getOneOrNullResult();
    }

    /**
     * Returns the numbers of containers that copies the given one.
     *
     * @param ContainerInterface $container
     *
     * @return int
     */
    public function getCopyCount(ContainerInterface $container)
    {
        $query = $this->getEntityManager()->createQuery(
            "SELECT COUNT(c.id) FROM {$this->getClassName()} c WHERE c.copy = :copied"
        );

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $query
            ->setParameter('copied', $container)
            ->getSingleResult(Query::HYDRATE_SINGLE_SCALAR);
    }

    /**
     * @inheritdoc
     */
    protected function getAlias()
    {
        return 'c';
    }
}
