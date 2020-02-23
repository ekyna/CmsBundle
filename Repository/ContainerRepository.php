<?php

namespace Ekyna\Bundle\CmsBundle\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr;
use Ekyna\Bundle\CmsBundle\Editor\Model\ContainerInterface;
use Ekyna\Component\Resource\Doctrine\ORM\ResourceRepository;
use Ekyna\Component\Resource\Doctrine\ORM\Util\LocaleAwareRepositoryTrait;

/**
 * Class ContainerRepository
 * @package Ekyna\Bundle\CmsBundle\Repository
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class ContainerRepository extends ResourceRepository
{
    use LocaleAwareRepositoryTrait;

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
            ->leftJoin('block.translations', 'block_t', Expr\Join::WITH, $this->getLocaleCondition('block_t'))
            ->addSelect('row', 'block', 'block_t')
            ->andWhere($qb->expr()->eq('c.name', ':name'))
            ->getQuery()
            ->useQueryCache(true)
            // TODO ->enableResultCache(3600, Container::getEntityTagPrefix() . '[name:'.$name.']')
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
            ->leftJoin('block.translations', 'block_t', Expr\Join::WITH, $this->getLocaleCondition('block_t'))
            ->addSelect('row', 'block', 'block_t')
            ->andWhere($qb->expr()->eq('c.id', ':id'))
            ->getQuery()
            ->useQueryCache(true)
            // TODO ->enableResultCache(3600, Container::getEntityTagPrefix() . '[id:'.$id.']')
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
        /** @noinspection SqlResolve */
        $query = $this->getEntityManager()->createQuery(
            "SELECT COUNT(c.id) FROM {$this->getClassName()} c WHERE c.copy = :copied"
        );

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
