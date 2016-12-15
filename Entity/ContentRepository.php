<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Ekyna\Bundle\CmsBundle\Model\ContentSubjectInterface;
use Ekyna\Component\Resource\Doctrine\ORM\ResourceRepository;

/**
 * Class ContentRepository
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class ContentRepository extends ResourceRepository
{
    /**
     * Finds the content by id.
     *
     * @param int $name
     *
     * @return \Ekyna\Bundle\CmsBundle\Model\ContentInterface|null
     */
    public function findOneByName($name)
    {
        $qb = $this->getQueryBuilder();

        return $qb
            ->leftJoin('c.containers', 'container')
            ->leftJoin('container.rows', 'row')
            ->leftJoin('row.blocks', 'block')
            ->leftJoin('block.translations', 'block_t')
            ->addSelect('container', 'row', 'block', 'block_t')
            ->andWhere($qb->expr()->eq('c.name', ':name'))
            ->getQuery()
            ->useQueryCache(true)
            // TODO ->useResultCache(true, 3600, Content::getEntityTagPrefix() . '[name:'.$name.']')
            ->setParameter('name', $name)
            ->getOneOrNullResult();
    }

    /**
     * Finds the content by id.
     *
     * @param int $id
     *
     * @return \Ekyna\Bundle\CmsBundle\Model\ContentInterface|null
     */
    public function findOneById($id)
    {
        $qb = $this->getQueryBuilder();

        return $qb
            ->leftJoin('c.containers', 'container')
            ->leftJoin('container.rows', 'row')
            ->leftJoin('row.blocks', 'block')
            ->leftJoin('block.translations', 'block_t')
            ->addSelect('container', 'row', 'block', 'block_t')
            ->andWhere($qb->expr()->eq('c.id', ':id'))
            ->getQuery()
            ->useQueryCache(true)
            // TODO ->useResultCache(true, 3600, Content::getEntityTagPrefix() . '[id:'.$id.']')
            ->setParameter('id', $id)
            ->getOneOrNullResult();
    }

    /**
     * Finds the content by subject.
     *
     * @param ContentSubjectInterface $subject
     *
     * @return \Ekyna\Bundle\CmsBundle\Model\ContentInterface|null
     */
    public function findBySubject(ContentSubjectInterface $subject)
    {
        $content = $subject->getContent();

        if (null !== $content && property_exists($content, '__isInitialized__') && !$content->{'__isInitialized__'}) {
            $qb = $this->getQueryBuilder();
            $qb
                ->leftJoin('c.containers', 'container')
                ->leftJoin('container.rows', 'row')
                ->leftJoin('row.blocks', 'block')
                ->leftJoin('block.translations', 'block_t')
                ->select('PARTIAL c.{id}', 'container', 'row', 'block', 'block_t')
                ->andWhere($qb->expr()->eq('c.id', ':id'))
                ->getQuery()
                ->useQueryCache(true)
                // TODO ->useResultCache(true, 3600, Content::getEntityTagPrefix() . '[id:'.$id.']')
                ->setParameter('id', $content->getId())
                ->getOneOrNullResult();
        }

        return $content;
    }

    /**
     * @inheritdoc
     */
    protected function getAlias()
    {
        return 'c';
    }
}
