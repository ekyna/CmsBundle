<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Repository;

use Doctrine\ORM\Query\Expr;
use Ekyna\Bundle\CmsBundle\Editor\Model\RowInterface;
use Ekyna\Component\Resource\Doctrine\ORM\Repository\LocaleAwareRepositoryTrait;
use Ekyna\Component\Resource\Doctrine\ORM\Repository\ResourceRepository;
use Ekyna\Component\Resource\Locale\LocaleProviderAwareInterface;

/**
 * Class RowRepository
 * @package Ekyna\Bundle\CmsBundle\Repository
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class RowRepository extends ResourceRepository implements LocaleProviderAwareInterface
{
    use LocaleAwareRepositoryTrait;

    /**
     * Finds the row by its name.
     *
     * @param string $name
     *
     * @return RowInterface|null
     */
    public function findOneByName(string $name): ?RowInterface
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
     * Finds the row by its id.
     *
     * @param int $id
     *
     * @return RowInterface|null
     */
    public function findOneById(int $id): ?RowInterface
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
     * @inheritDoc
     */
    protected function getAlias(): string
    {
        return 'r';
    }
}
