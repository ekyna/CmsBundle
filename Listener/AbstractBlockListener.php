<?php

namespace Ekyna\Bundle\CmsBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Ekyna\Bundle\CmsBundle\Model\BlockInterface;
use Ekyna\Bundle\CoreBundle\Event\HttpCacheEvent;
use Ekyna\Bundle\CoreBundle\Event\HttpCacheEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class AbstractBlockListener
 * @package Ekyna\Bundle\CmsBundle\Listener
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class AbstractBlockListener
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * Constructor.
     *
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Pre persist event handler.
     *
     * @param BlockInterface $block
     * @param LifecycleEventArgs $event
     */
    public function prePersist(BlockInterface $block, LifecycleEventArgs $event)
    {
        $this->invalidateBlockContent($block);
    }

    /**
     * Pre update event handler.
     *
     * @param BlockInterface $block
     * @param PreUpdateEventArgs $event
     */
    public function preUpdate(BlockInterface $block, PreUpdateEventArgs $event)
    {
        $this->invalidateBlockContent($block);
    }

    /**
     * Invalidate the block's content http cache tag.
     *
     * @param BlockInterface $block
     */
    private function invalidateBlockContent(BlockInterface $block)
    {
        if (null !== $content = $block->getContent()) {
            $this->eventDispatcher->dispatch(
                HttpCacheEvents::INVALIDATE_TAG,
                new HttpCacheEvent(array($content->getEntityTag()))
            );
        }
    }
}
