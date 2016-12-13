<?php

namespace Ekyna\Bundle\CmsBundle\Event;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class MenuEvent
 * @package Ekyna\Bundle\CmsBundle\Event
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class MenuEvent extends Event
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var ItemInterface
     */
    private $menu;


    /**
     * Constructor.
     *
     * @param FactoryInterface $factory
     * @param ItemInterface    $menu
     */
    public function __construct(FactoryInterface $factory, ItemInterface $menu)
    {
        $this->factory = $factory;
        $this->menu = $menu;
    }

    /**
     * Returns the menu factory.
     *
     * @return \Knp\Menu\FactoryInterface
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * Returns the menu item.
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function getMenu()
    {
        return $this->menu;
    }
}
