<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Event;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class MenuEvent
 * @package Ekyna\Bundle\CmsBundle\Event
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
final class MenuEvent extends Event
{
    private FactoryInterface $factory;
    private ItemInterface    $menu;

    public function __construct(FactoryInterface $factory, ItemInterface $menu)
    {
        $this->factory = $factory;
        $this->menu = $menu;
    }

    public function getFactory(): FactoryInterface
    {
        return $this->factory;
    }

    public function getMenu(): ItemInterface
    {
        return $this->menu;
    }
}
