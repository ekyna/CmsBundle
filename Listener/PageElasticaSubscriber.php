<?php

namespace Ekyna\Bundle\CmsBundle\Listener;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Ekyna\Bundle\CmsBundle\Model as Cms;
use FOS\ElasticaBundle\Persister\ObjectPersisterInterface;

/**
 * Class PageElasticaSubscriber
 * @package Ekyna\Bundle\CmsBundle\Listener
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class PageElasticaSubscriber
{
    /**
     * @var ObjectPersisterInterface
     */
    private $persister;

    /**
     * @var string
     */
    private $pageClass;

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var ArrayCollection
     */
    private $pages;


    /**
     * Constructor.
     *
     * @param ObjectPersisterInterface $persister
     * @param string                   $pageClass
     */
    public function __construct(
        ObjectPersisterInterface $persister,
        $pageClass
    ) {
        $this->persister = $persister;
        $this->pageClass = $pageClass;
    }

    /**
     * Flush event handler.
     *
     * @param OnFlushEventArgs $eventArgs
     */
    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        $this->pages = new ArrayCollection();

        $this->manager = $eventArgs->getEntityManager();
        $uow = $this->manager->getUnitOfWork();

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            $this->handleEntity($entity);
        }
        /** @var \Doctrine\Common\Collections\ArrayCollection $col */
        foreach ($uow->getScheduledCollectionUpdates() as $col) {
            foreach ($col as $entity) {
                $this->handleEntity($entity);
            }
        }

        if (0 < $this->pages->count()) {
            $this->persister->replaceMany($this->pages->toArray());
        }
    }

    /**
     * Handles the entity.
     *
     * @param object $entity
     */
    private function handleEntity($entity)
    {
        if (null !== $page = $this->findRelatedPage($entity)) {
            if (null === $page->getId()) {
                return;
            }
            if (!$this->pages->contains($page)) {
                $this->pages->add($page);
            }
        }
    }

    /**
     * Finds the related page.
     *
     * @param object $entity
     * @return Cms\PageInterface
     */
    private function findRelatedPage($entity)
    {
        // By Translation
        if ($entity instanceof Cms\PageTranslationInterface) {
            return $entity->getTranslatable();
        }

        // By Seo
        if ($entity instanceof Cms\SeoTranslationInterface && null !== $entity->getId()) {
            return $this->manager
                ->createQuery("SELECT p FROM {$this->pageClass} p WHERE p.seo = :seo")
                ->setMaxResults(1)
                ->setParameter('seo', $entity->getTranslatable())
                ->getOneOrNullResult();
        }

        // By Content
        /** @var \Ekyna\Bundle\CmsBundle\Editor\Model\ContentInterface $content */
        $content = null;
//       TODO if ($entity instanceof Cms\BlockTranslationInterface) {
//            /** @var Cms\BlockInterface $block */
//            $block = $entity->getTranslatable();
//            $content = $block->getContent();
//        }
        if (null !== $content && null !== $content->getId()) {
            return $this->manager
                ->createQuery("SELECT p FROM {$this->pageClass} p WHERE p.content = :content")
                ->setMaxResults(1)
                ->setParameter('content', $content)
                ->getOneOrNullResult();
        }

        return null;
    }
}
