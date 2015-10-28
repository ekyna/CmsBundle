<?php

namespace Ekyna\Bundle\CmsBundle\Event;

use Ekyna\Bundle\AdminBundle\Event\ResourceEvent;
use Ekyna\Bundle\CmsBundle\Model\MenuInterface;

/**
 * Class MenuEvent
 * @package Ekyna\Bundle\CmsBundle\Event
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class MenuEvent extends ResourceEvent
{
    /**
     * Constructor.
     *
     * @param MenuInterface $menu
     */
    public function __construct(MenuInterface $menu)
    {
        $this->setResource($menu);
    }

    /**
     * @return MenuInterface
     */
    public function getMenu()
    {
        return $this->getResource();
    }
} 