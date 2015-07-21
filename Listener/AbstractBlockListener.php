<?php

namespace Ekyna\Bundle\CmsBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
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
     * Post update event handler.
     *
     * @param BlockInterface $block
     * @param LifecycleEventArgs $event
     */
    public function postUpdate(BlockInterface $block, LifecycleEventArgs $event)
    {
        $this->invalidateBlockContent($block);
    }

    /**
     * Post remove event handler.
     *
     * @param BlockInterface $block
     * @param LifecycleEventArgs $event
     */
    public function postRemove(BlockInterface $block, LifecycleEventArgs $event)
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
