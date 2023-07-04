<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\EventListener;

use Doctrine\Common\Cache\Psr6\DoctrineProvider;
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
    private EntityManagerInterface $manager;


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
    public function onInsert(): void
    {
        $this->clearQueryCache();
    }

    /**
     * Notice update event handler.
     */
    public function onUpdate(): void
    {
        $this->clearQueryCache();
    }

    /**
     * Notice delete event handler.
     */
    public function onDelete(): void
    {
        $this->clearQueryCache();
    }

    /**
     * Clears the active notices query result cache.
     */
    private function clearQueryCache(): void
    {
        $cache = $this->manager->getConfiguration()->getResultCache();

        $cache = DoctrineProvider::wrap($cache);

        $cache->delete(NoticeRepositoryInterface::CACHE_KEY);
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            NoticeEvents::INSERT => ['onInsert'],
            NoticeEvents::UPDATE => ['onUpdate'],
            NoticeEvents::DELETE => ['onDelete'],
        ];
    }
}

