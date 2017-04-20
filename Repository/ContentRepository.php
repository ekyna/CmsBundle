<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Repository;

use Doctrine\ORM\Query\Expr;
use Ekyna\Bundle\CmsBundle\Editor\Model\ContentInterface;
use Ekyna\Bundle\CmsBundle\Model\ContentSubjectInterface;
use Ekyna\Component\Resource\Doctrine\ORM\Repository\LocaleAwareRepositoryTrait;
use Ekyna\Component\Resource\Doctrine\ORM\Repository\ResourceRepository;
use Ekyna\Component\Resource\Locale\LocaleProviderAwareInterface;

/**
 * Class ContentRepository
 * @package Ekyna\Bundle\CmsBundle\Repository
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class ContentRepository extends ResourceRepository implements LocaleProviderAwareInterface
{
    use LocaleAwareRepositoryTrait;

    /**
     * Finds the content by its name.
     *
     * @param string $name
     *
     * @return ContentInterface|null
     */
    public function findOneByName(string $name): ?ContentInterface
    {
        $qb = $this->getQueryBuilder();

        return $qb
            ->leftJoin('c.containers', 'container')
            ->leftJoin('container.rows', 'row')
            ->leftJoin('row.blocks', 'block')
            ->leftJoin('block.translations', 'block_t', Expr\Join::WITH, $this->getLocaleCondition('block_t'))
            ->addSelect('container', 'row', 'block', 'block_t')
            ->andWhere($qb->expr()->eq('c.name', ':name'))
            ->getQuery()
            ->useQueryCache(true)
            // TODO ->enableResultCache(3600, Content::getEntityTagPrefix() . '[name:'.$name.']')
            ->setParameter('name', $name)
            ->getOneOrNullResult();
    }

    /**
     * Finds the content by its id.
     *
     * @param int $id
     *
     * @return ContentInterface|null
     */
    public function findOneById(int $id): ?ContentInterface
    {
        $qb = $this->getQueryBuilder();

        return $qb
            ->leftJoin('c.containers', 'container')
            ->leftJoin('container.rows', 'row')
            ->leftJoin('row.blocks', 'block')
            ->leftJoin('block.translations', 'block_t', Expr\Join::WITH, $this->getLocaleCondition('block_t'))
            ->addSelect('container', 'row', 'block', 'block_t')
            ->andWhere($qb->expr()->eq('c.id', ':id'))
            ->getQuery()
            ->useQueryCache(true)
            // TODO ->enableResultCache(3600, Content::getEntityTagPrefix() . '[id:'.$id.']')
            ->setParameter('id', $id)
            ->getOneOrNullResult();
    }

    /**
     * Finds the content by subject.
     *
     * @param ContentSubjectInterface $subject
     *
     * @return ContentInterface|null
     */
    public function findBySubject(ContentSubjectInterface $subject): ?ContentInterface
    {
        if (!$content = $subject->getContent()) {
            return null;
        }

        if (property_exists($content, '__isInitialized__') && !$content->{'__isInitialized__'}) {
            $qb = $this->getQueryBuilder();
            $qb
                ->leftJoin('c.containers', 'container')
                ->leftJoin('container.rows', 'row')
                ->leftJoin('row.blocks', 'block')
                ->leftJoin('block.translations', 'block_t', Expr\Join::WITH, $this->getLocaleCondition('block_t'))
                ->select('PARTIAL c.{id}', 'container', 'row', 'block', 'block_t')
                ->andWhere($qb->expr()->eq('c.id', ':id'))
                ->getQuery()
                ->useQueryCache(true)
                // TODO ->enableResultCache(3600, Content::getEntityTagPrefix() . '[id:'.$id.']')
                ->setParameter('id', $content->getId())
                ->getOneOrNullResult();
        }

        return $content;
    }

    /**
     * @inheritDoc
     */
    protected function getAlias(): string
    {
        return 'c';
    }
}
