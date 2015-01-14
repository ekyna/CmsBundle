<?php

namespace Ekyna\Bundle\CmsBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Ekyna\Bundle\CmsBundle\Model\ContentInterface;
use Ekyna\Bundle\CoreBundle\Event\HttpCacheEvent;
use Ekyna\Bundle\CoreBundle\Event\HttpCacheEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class ContentListener
 * @package Ekyna\Bundle\CmsBundle\Listener
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class ContentListener
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
     * @param ContentInterface $content
     * @param LifecycleEventArgs $event
     */
    public function postUpdate(ContentInterface $content, LifecycleEventArgs $event)
    {
        $this->invalidateTag($content);
    }

    /**
     * Post remove event handler.
     *
     * @param ContentInterface $content
     * @param LifecycleEventArgs $event
     */
    public function postRemove(ContentInterface $content, LifecycleEventArgs $event)
    {
        $this->invalidateTag($content);
    }

    /**
     * Invalidates the http cache tag.
     *
     * @param ContentInterface $content
     */
    private function invalidateTag(ContentInterface $content)
    {
        $this->eventDispatcher->dispatch(
            HttpCacheEvents::INVALIDATE_TAG,
            new HttpCacheEvent('ekyna_cms.content[id:'.$content->getId().']')
        );
    }
}
