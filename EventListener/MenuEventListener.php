<?php

namespace Ekyna\Bundle\CmsBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Ekyna\Bundle\CmsBundle\Model\MenuInterface;
use Ekyna\Component\Resource\Event\ResourceEventInterface;
use Ekyna\Component\Resource\Event\ResourceMessage;
use Ekyna\Bundle\CmsBundle\Event\MenuEvents;
use Ekyna\Component\Resource\Exception\InvalidArgumentException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class MenuEventListener
 * @package Ekyna\Bundle\CmsBundle\EventListener
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class MenuEventListener implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var
     */
    private $menuClass;

    /**
     * @var
     */
    private $pageClass;


    /**
     * Constructor.
     *
     * @param EntityManagerInterface $em
     * @param string                 $menuClass
     * @param string                 $pageClass
     */
    public function __construct(EntityManagerInterface $em, $menuClass, $pageClass)
    {
        $this->em        = $em;
        $this->menuClass = $menuClass;
        $this->pageClass = $pageClass;
    }

    /**
     * Pre create event handler.
     *
     * @param ResourceEventInterface $event
     */
    public function onPreUpdate(ResourceEventInterface $event)
    {
        $menu = $this->getMenuFromEvent($event);

        // Don't disable if locked
        if (!$menu->getEnabled() && $menu->getLocked()) {
            $menu->setEnabled(true);
        }

        // Don't enable if disabled relative page
        if ($menu->getEnabled() && !$menu->getLocked() && 0 < strlen($menu->getRoute())) {
            /** @noinspection SqlDialectInspection */
            $disabledPageId = $this->em
                ->createQuery("SELECT p.id FROM {$this->pageClass} p WHERE p.route = :route AND p.enabled = 0")
                ->setParameter('route', $menu->getRoute())
                ->getOneOrNullResult(Query::HYDRATE_SCALAR)
            ;
            if (null !== $disabledPageId) {
                $event->addMessage(new ResourceMessage(
                    'ekyna_cms.menu.alert.cant_enable_as_disabled_page',
                    ResourceMessage::TYPE_ERROR
                ));
                return;
            }
        }

        // Disable menu children
        if (!$menu->getEnabled()) {
            $this->em->createQuery(sprintf(
                'UPDATE %s m SET m.enabled = 0 WHERE m.root = :root AND m.left > :left AND m.right < :right',
                $this->menuClass
            ))->execute(array(
                'root'  => $menu->getRoot(),
                'left'  => $menu->getLeft(),
                'right' => $menu->getRight(),
            ));
        }
    }

    /**
     * Returns the menu from the event.
     *
     * @param ResourceEventInterface $event
     *
     * @return MenuInterface
     */
    private function getMenuFromEvent(ResourceEventInterface $event)
    {
        $resource = $event->getResource();

        if (!$resource instanceof MenuInterface) {
            throw new InvalidArgumentException("Expected instance of MenuInterface");
        }

        return $resource;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            MenuEvents::PRE_UPDATE  => array('onPreUpdate', -1024),
        );
    }
}
