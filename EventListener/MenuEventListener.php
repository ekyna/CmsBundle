<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\EventListener;

use Ekyna\Bundle\CmsBundle\Event\MenuEvents;
use Ekyna\Bundle\CmsBundle\Model\MenuInterface;
use Ekyna\Bundle\CmsBundle\Service\Updater\MenuUpdater;
use Ekyna\Component\Resource\Event\ResourceEventInterface;
use Ekyna\Component\Resource\Event\ResourceMessage;
use Ekyna\Component\Resource\Exception\UnexpectedTypeException;
use Ekyna\Component\Resource\Persistence\PersistenceHelperInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class MenuEventListener
 * @package Ekyna\Bundle\CmsBundle\EventListener
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class MenuEventListener implements EventSubscriberInterface
{
    private PersistenceHelperInterface $persistenceHelper;
    private MenuUpdater                $updater;


    /**
     * Constructor.
     *
     * @param PersistenceHelperInterface $persistenceHelper
     * @param MenuUpdater                $updater
     */
    public function __construct(PersistenceHelperInterface $persistenceHelper, MenuUpdater $updater)
    {
        $this->persistenceHelper = $persistenceHelper;
        $this->updater = $updater;
    }

    /**
     * Menu insert event handler.
     *
     * @param ResourceEventInterface $event
     */
    public function onInsert(ResourceEventInterface $event): void
    {
        $menu = $this->getMenuFromEvent($event);

        $changed = $this->updater->updateRoute($menu);

        $changed = $this->updater->updateName($menu) || $changed;

        if ($changed) {
            $this->persistenceHelper->persistAndRecompute($menu, false);
        }
    }

    /**
     * Menu update event handler.
     *
     * @param ResourceEventInterface $event
     */
    public function onUpdate(ResourceEventInterface $event): void
    {
        $menu = $this->getMenuFromEvent($event);

        $changed = $this->updater->updateRoute($menu);

        if ($this->persistenceHelper->isChanged($menu, 'name')) {
            $changed = $this->updater->updateName($menu) || $changed;
        }

        if ($changed) {
            $this->persistenceHelper->persistAndRecompute($menu, false);
        }
    }

    /**
     * Pre update event handler.
     *
     * @param ResourceEventInterface $event
     */
    public function onPreUpdate(ResourceEventInterface $event): void
    {
        $menu = $this->getMenuFromEvent($event);

        if ($this->updater->checkEnabled($menu)) {
            $message = ResourceMessage::create(
                'menu.alert.cant_enable_as_disabled_page',
                ResourceMessage::TYPE_ERROR
            )->setDomain('EkynaCms');

            $event->addMessage($message);

            return;
        }

        $this->updater->disabledMenuRecursively($menu->getChildren()->toArray());
    }

    /**
     * Returns the menu from the event.
     *
     * @param ResourceEventInterface $event
     *
     * @return MenuInterface
     */
    private function getMenuFromEvent(ResourceEventInterface $event): MenuInterface
    {
        $resource = $event->getResource();

        if (!$resource instanceof MenuInterface) {
            throw new UnexpectedTypeException($resource, MenuInterface::class);
        }

        return $resource;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            MenuEvents::PRE_UPDATE => ['onPreUpdate', -1024],
            MenuEvents::INSERT     => ['onInsert', 0],
            MenuEvents::UPDATE     => ['onUpdate', 0],
        ];
    }
}
