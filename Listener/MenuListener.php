<?php

namespace Ekyna\Bundle\CmsBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Ekyna\Bundle\CmsBundle\Model\MenuInterface;
use Ekyna\Bundle\CoreBundle\Event\HttpCacheEvent;
use Ekyna\Bundle\CoreBundle\Event\HttpCacheEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class MenuListener
 * @package Ekyna\Bundle\CmsBundle\Listener
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class MenuListener
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
     * @param MenuInterface $menu
     * @param LifecycleEventArgs $event
     */
    public function postUpdate(MenuInterface $menu, LifecycleEventArgs $event)
    {
        $this->invalidateTag($menu);
    }

    /**
     * Post remove event handler.
     *
     * @param MenuInterface $menu
     * @param LifecycleEventArgs $event
     */
    public function postRemove(MenuInterface $menu, LifecycleEventArgs $event)
    {
        $this->invalidateTag($menu);
    }

    /**
     * Invalidates the http cache tag.
     *
     * @param MenuInterface $menu
     */
    private function invalidateTag(MenuInterface $menu)
    {
        $tags = ['ekyna_cms.menu[id:'.$menu->getId().']'];
        while (null !== $menu = $menu->getParent()) {
            $tags[] = 'ekyna_cms.menu[id:'.$menu->getId().']';
        }

        $this->eventDispatcher->dispatch(
            HttpCacheEvents::INVALIDATE_TAG,
            new HttpCacheEvent($tags)
        );
    }
}
