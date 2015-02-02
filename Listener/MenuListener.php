<?php

namespace Ekyna\Bundle\CmsBundle\Listener;

use Behat\Transliterator\Transliterator;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Ekyna\Bundle\CmsBundle\Model\MenuInterface;

/**
 * Class MenuListener
 * @package Ekyna\Bundle\CmsBundle\Listener
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class MenuListener
{
    const NAME_REGEX = '#^[a-z0-9_]+$#';

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
}
