<?php

namespace Ekyna\Bundle\CmsBundle\Repository;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Query\Expr;
use Ekyna\Component\Resource\Doctrine\ORM\TranslatableResourceRepository;

/**
 * Class NoticeRepository
 * @package Ekyna\Bundle\CmsBundle\Repository
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class NoticeRepository extends TranslatableResourceRepository implements NoticeRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function findActives(): array
    {
        $now = new \DateTime();
        $now->setTime($now->format('H'), floor($now->format('i') / 15) * 15, 0, 0);

        $qb = $this->createQueryBuilder('n');
        $ex = $qb->expr();

        return $qb
            ->select('n', 't')
            ->leftJoin('n.translations', 't', Expr\Join::WITH, $this->getLocaleCondition('t'))
            ->andWhere($ex->lte('n.startAt', ':now'))
            ->andWhere($ex->gte('n.endAt', ':now'))
            ->getQuery()
            ->useQueryCache(true)
            ->useResultCache(true, 15 * 60, NoticeRepositoryInterface::CACHE_KEY)
            ->setParameter('now', $now, Type::DATETIME)
            ->getResult();
    }
}
