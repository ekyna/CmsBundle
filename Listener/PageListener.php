<?php

namespace Ekyna\Bundle\CmsBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Ekyna\Bundle\CmsBundle\Model\PageInterface;
use Ekyna\Bundle\CoreBundle\Event\HttpCacheEvent;
use Ekyna\Bundle\CoreBundle\Event\HttpCacheEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class PageListener
 * @package Ekyna\Bundle\CmsBundle\Listener
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class PageListener
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
     * @param PageInterface $page
     * @param LifecycleEventArgs $event
     */
    public function prePersist(PageInterface $page, LifecycleEventArgs $event)
    {
        if (preg_match('#\{.*\}#', $page->getPath())) {
            $page->setDynamicPath(true);
        } else {
            $page->setDynamicPath(false);
        }
    }

    /**
     * Pre update event handler.
     *
     * @param PageInterface $page
     * @param PreUpdateEventArgs $event
     */
    public function preUpdate(PageInterface $page, PreUpdateEventArgs $event)
    {
        if ($event->hasChangedField('path')) {
            if (preg_match('#\{.*\}#', $event->getNewValue('path'))) {
                $event->setNewValue('dynamicPath', true);
            } else {
                $event->setNewValue('dynamicPath', false);
            }
        }
    }

    /**
     * Post update event handler.
     *
     * @param PageInterface $page
     * @param LifecycleEventArgs $event
     */
    public function postUpdate(PageInterface $page, LifecycleEventArgs $event)
    {
        $this->invalidateTag($page);
    }

    /**
     * Post remove event handler.
     *
     * @param PageInterface $page
     * @param LifecycleEventArgs $event
     */
    public function postRemove(PageInterface $page, LifecycleEventArgs $event)
    {
        $this->invalidateTag($page);
    }

    /**
     * Invalidates the http cache tag.
     *
     * @param PageInterface $page
     */
    private function invalidateTag(PageInterface $page)
    {
        $this->eventDispatcher->dispatch(
            HttpCacheEvents::INVALIDATE_TAG,
            new HttpCacheEvent('ekyna_cms.page[id:'.$page->getId().']')
        );
    }
}
