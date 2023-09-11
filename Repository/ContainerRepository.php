<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr;
use Ekyna\Bundle\CmsBundle\Editor\Model\ContainerInterface;
use Ekyna\Component\Resource\Doctrine\ORM\Repository\LocaleAwareRepositoryTrait;
use Ekyna\Component\Resource\Doctrine\ORM\Repository\ResourceRepository;
use Ekyna\Component\Resource\Locale\LocaleProviderAwareInterface;

/**
 * Class ContainerRepository
 * @package Ekyna\Bundle\CmsBundle\Repository
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class ContainerRepository extends ResourceRepository implements LocaleProviderAwareInterface
{
    use LocaleAwareRepositoryTrait;

    /**
     * Finds the container by is name.
     *
     * @param string $name
     *
     * @return ContainerInterface|null
     */
    public function findOneByName(string $name): ?ContainerInterface
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
     * Finds the container by is id.
     *
     * @param int $id
     *
     * @return ContainerInterface|null
     */
    public function findOneById(int $id): ?ContainerInterface
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
     * Returns the containers that copies the given one.
     *
     * @param ContainerInterface $container
     *
     * @return array<int, ContainerInterface>
     */
    public function findByCopy(ContainerInterface $container): array
    {
        $qb = $this->createQueryBuilder('c');

        return $qb
            ->andWhere($qb->expr()->eq('c.copy', ':container'))
            ->getQuery()
            ->setParameter('container', $container)
            ->getResult();
    }

    /**
     * Returns the numbers of containers that copies the given one.
     *
     * @param ContainerInterface $container
     *
     * @return int
     */
    public function getCopyCount(ContainerInterface $container): int
    {
        $qb = $this->createQueryBuilder('c');

        return (int) $qb
            ->select('COUNT(c.id)')
            ->andWhere($qb->expr()->eq('c.copy', ':copy'))
            ->getQuery()
            ->setParameter('copy', $container)
            ->getSingleResult(Query::HYDRATE_SINGLE_SCALAR);
    }

    /**
     * @inheritDoc
     */
    protected function getAlias(): string
    {
        return 'c';
    }
}
