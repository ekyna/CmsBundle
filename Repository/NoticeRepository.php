<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Repository;

use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Query\Expr;
use Ekyna\Component\Resource\Doctrine\ORM\Repository\TranslatableRepository;

/**
 * Class NoticeRepository
 * @package Ekyna\Bundle\CmsBundle\Repository
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class NoticeRepository extends TranslatableRepository implements NoticeRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function findActives(): array
    {
        $now = new DateTime();
        $now->setTime((int)$now->format('H'), (int)floor($now->format('i') / 15) * 15);

        $qb = $this->createQueryBuilder('n');
        $ex = $qb->expr();

        return $qb
            ->select('n', 't')
            ->leftJoin('n.translations', 't', Expr\Join::WITH, $this->getLocaleCondition('t'))
            ->andWhere($ex->lte('n.startAt', ':now'))
            ->andWhere($ex->gte('n.endAt', ':now'))
            ->getQuery()
            ->useQueryCache(true)
            ->enableResultCache(15 * 60, NoticeRepositoryInterface::CACHE_KEY)
            ->setParameter('now', $now, Types::DATETIME_MUTABLE)
            ->getResult();
    }
}
