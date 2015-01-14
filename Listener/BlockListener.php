<?php

namespace Ekyna\Bundle\CmsBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Ekyna\Bundle\CmsBundle\Model\BlockInterface;
use Ekyna\Bundle\CoreBundle\Event\HttpCacheEvent;
use Ekyna\Bundle\CoreBundle\Event\HttpCacheEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class BlockListener
 * @package Ekyna\Bundle\CmsBundle\Listener
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class BlockListener
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
        $this->invalidateTag($block);
    }

    /**
     * Post remove event handler.
     *
     * @param BlockInterface $block
     * @param LifecycleEventArgs $event
     */
    public function postRemove(BlockInterface $block, LifecycleEventArgs $event)
    {
        $this->invalidateTag($block);
    }

    /**
     * Invalidates the http cache tag.
     *
     * @param BlockInterface $block
     */
    private function invalidateTag(BlockInterface $block)
    {
        $tags = array('ekyna_cms.block[id:'.$block->getId().']');

        if (null !== $content = $block->getContent()) {
            $tags[] = 'ekyna_cms.content[id:'.$content->getId().']';
        }

        $this->eventDispatcher->dispatch(
            HttpCacheEvents::INVALIDATE_TAG,
            new HttpCacheEvent($tags)
        );
    }
}
