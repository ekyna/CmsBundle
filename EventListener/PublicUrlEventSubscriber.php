<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\EventListener;

use Ekyna\Bundle\CmsBundle\Event\PageEvents;
use Ekyna\Bundle\CmsBundle\Model\PageInterface;
use Ekyna\Component\Resource\Event\ResourceEventInterface;
use Ekyna\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class PublicUrlEventSubscriber
 * @package Ekyna\Bundle\CmsBundle\EventListener
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class PublicUrlEventSubscriber implements EventSubscriberInterface
{
    public function onPagePublicUrl(ResourceEventInterface $event): void
    {
        $resource = $event->getResource();

        if (!$resource instanceof PageInterface) {
            throw new UnexpectedTypeException($resource, PageInterface::class);
        }

        $event->stopPropagation();

        if (!$resource->isEnabled() || $resource->isDynamicPath()) {
            return;
        }

        $event->addData('route', $resource->getRoute());
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PageEvents::PUBLIC_URL => ['onPagePublicUrl', 0],
        ];
    }
}
