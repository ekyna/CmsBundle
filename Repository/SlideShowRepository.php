<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Repository;

use Ekyna\Bundle\CmsBundle\Model\SlideShowInterface;
use Ekyna\Component\Resource\Doctrine\ORM\Repository\ResourceRepository;

/**
 * Class SlideShowRepository
 * @package Ekyna\Bundle\CmsBundle\Repository
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class SlideShowRepository extends ResourceRepository implements SlideShowRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function findOnByTag(string $tag): ?SlideShowInterface
    {
        $qb = $this->createQueryBuilder('s');

        return $qb
            ->andWhere($qb->expr()->eq('s.tag', ':tag'))
            ->getQuery()
            ->setParameter('tag', $tag)
            ->getOneOrNullResult();
    }
}
