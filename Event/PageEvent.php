<?php

namespace Ekyna\Bundle\CmsBundle\Event;

use Ekyna\Bundle\AdminBundle\Event\ResourceEvent;
use Ekyna\Bundle\CmsBundle\Model\PageInterface;

/**
 * Class PageEvent
 * @package Ekyna\Bundle\CmsBundle\Event
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class PageEvent extends ResourceEvent
{
    /**
     * Constructor.
     *
     * @param PageInterface $page
     */
    public function __construct(PageInterface $page)
    {
        $this->setResource($page);
    }

    /**
     * @return PageInterface
     */
    public function getPage()
    {
        return $this->getResource();
    }
} 