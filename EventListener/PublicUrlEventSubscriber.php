<?php

namespace Ekyna\Bundle\CmsBundle\EventListener;

use Ekyna\Bundle\CmsBundle\Event\PageEvents;
use Ekyna\Bundle\CmsBundle\Model\PageInterface;
use Ekyna\Component\Resource\Event\ResourceEventInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class PublicUrlEventSubscriber
 * @package Ekyna\Bundle\CmsBundle\EventListener
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class PublicUrlEventSubscriber implements EventSubscriberInterface
{
    /**
     * Page public url event handler.
     *
     * @param ResourceEventInterface $event
     */
    public function onPagePublicUrl(ResourceEventInterface $event)
    {
        $resource = $event->getResource();

        if (!$resource instanceof PageInterface) {
            return;
        }

        $event->stopPropagation();

        if (!$resource->isEnabled() || $resource->isDynamicPath()) {
            return;
        }

        $event->addData('route', $resource->getRoute());
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            PageEvents::PUBLIC_URL  => ['onPagePublicUrl', 0],
        ];
    }
}