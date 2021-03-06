<?php

namespace Ekyna\Bundle\CmsBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Ekyna\Bundle\CmsBundle\Event\NoticeEvents;
use Ekyna\Bundle\CmsBundle\Repository\NoticeRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class NoticeEventListener
 * @package Ekyna\Bundle\CmsBundle\EventListener
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class NoticeEventListener implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;


    /**
     * Constructor.
     *
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Notice insert event handler.
     */
    public function onInsert()
    {
        $this->clearQueryCache();
    }

    /**
     * Notice update event handler.
     */
    public function onUpdate()
    {
        $this->clearQueryCache();
    }

    /**
     * Notice delete event handler.
     */
    public function onDelete()
    {
        $this->clearQueryCache();
    }

    /**
     * Clears the active notices query result cache.
     */
    private function clearQueryCache(): void
    {
        $this
            ->manager
            ->getConfiguration()
            ->getResultCacheImpl()
            ->delete(NoticeRepositoryInterface::CACHE_KEY);
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            NoticeEvents::INSERT => ['onInsert'],
            NoticeEvents::UPDATE => ['onUpdate'],
            NoticeEvents::DELETE => ['onDelete'],
        ];
    }
}

