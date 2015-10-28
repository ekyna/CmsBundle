<?php

namespace Ekyna\Bundle\CmsBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Ekyna\Bundle\AdminBundle\Event\ResourceMessage;
use Ekyna\Bundle\CmsBundle\Event\MenuEvent;
use Ekyna\Bundle\CmsBundle\Event\MenuEvents;
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
     * @param MenuEvent $event
     */
    public function onPreUpdate(MenuEvent $event)
    {
        $menu = $event->getMenu();

        // Don't disable if locked
        if (!$menu->getEnabled() && $menu->getLocked()) {
            $menu->setEnabled(true);
        }

        // Don't enable if disabled relative page
        if ($menu->getEnabled() && !$menu->getLocked() && 0 < strlen($menu->getRoute())) {
            $disabledPage = $this->em
                ->createQuery("SELECT p.id FROM {$this->pageClass} p WHERE p.route = :route AND p.enabled = 0")
                ->setParameter('route', $menu->getRoute())
                ->getArrayResult()
            ;
            if (null !== $disabledPage) {
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
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            MenuEvents::PRE_UPDATE  => array('onPreUpdate', -1024),
        );
    }
}