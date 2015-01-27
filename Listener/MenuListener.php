<?php

namespace Ekyna\Bundle\CmsBundle\Listener;

use Behat\Transliterator\Transliterator;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
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
    const NAME_REGEX = '#^[a-z0-9_]+$#';

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
     * @param MenuInterface $menu
     * @param LifecycleEventArgs $event
     */
    public function prePersist(MenuInterface $menu, LifecycleEventArgs $event)
    {
        if (null !== $page = $menu->getPage()) {
            $menu->setRoute($page->getRoute());
        }
        if (!preg_match(self::NAME_REGEX, $menu->getName())) {
            $menu->setName(Transliterator::urlize($menu->getName(), '_'));
        }
    }

    /**
     * Pre update event handler.
     *
     * @param MenuInterface $menu
     * @param PreUpdateEventArgs $event
     */
    public function preUpdate(MenuInterface $menu, PreUpdateEventArgs $event)
    {
        if (null !== $page = $menu->getPage()) {
            $event->setNewValue('route', $page->getRoute());
        }
        if ($event->hasChangedField('name') && !preg_match(self::NAME_REGEX, $name = $event->getNewValue('name'))) {
            $event->setNewValue('name', Transliterator::urlize($name, '_'));
        }
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
