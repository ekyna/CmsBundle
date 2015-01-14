<?php

namespace Ekyna\Bundle\CmsBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Ekyna\Bundle\CmsBundle\Model\SeoInterface;
use Ekyna\Bundle\CoreBundle\Event\HttpCacheEvent;
use Ekyna\Bundle\CoreBundle\Event\HttpCacheEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class SeoListener
 * @package Ekyna\Bundle\CmsBundle\Listener
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class SeoListener
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
     * @param SeoInterface $seo
     * @param LifecycleEventArgs $event
     */
    public function postUpdate(SeoInterface $seo, LifecycleEventArgs $event)
    {
        $this->invalidateTag($seo);
    }

    /**
     * Post remove event handler.
     *
     * @param SeoInterface $seo
     * @param LifecycleEventArgs $event
     */
    public function postRemove(SeoInterface $seo, LifecycleEventArgs $event)
    {
        $this->invalidateTag($seo);
    }

    /**
     * Invalidates the http cache tag.
     *
     * @param SeoInterface $seo
     */
    private function invalidateTag(SeoInterface $seo)
    {
        $this->eventDispatcher->dispatch(
            HttpCacheEvents::INVALIDATE_TAG,
            new HttpCacheEvent('ekyna_cms.seo[id:'.$seo->getId().']')
        );
    }
}
