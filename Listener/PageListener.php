<?php

namespace Ekyna\Bundle\CmsBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Ekyna\Bundle\CmsBundle\Model\PageInterface;

/**
 * Class PageListener
 * @package Ekyna\Bundle\CmsBundle\Listener
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class PageListener
{
    /**
     * Pre persist event handler.
     *
     * @param PageInterface $page
     * @param LifecycleEventArgs $event
     */
    public function prePersist(PageInterface $page, LifecycleEventArgs $event)
    {
        if (preg_match('#\{.*\}#', $page->getPath())) {
            $page->setDynamicPath(true);
        } else {
            $page->setDynamicPath(false);
        }
    }

    /**
     * Pre update event handler.
     *
     * @param PageInterface $page
     * @param PreUpdateEventArgs $event
     */
    public function preUpdate(PageInterface $page, PreUpdateEventArgs $event)
    {
        if ($event->hasChangedField('path')) {
            $recompute = false;
            if (preg_match('#\{.*\}#', $event->getNewValue('path'))) {
                if (!$page->getDynamicPath()) {
                    $recompute = true;
                    $page->setDynamicPath(true);
                }
            } else {
                if ($page->getDynamicPath()) {
                    $recompute = true;
                    $page->setDynamicPath(false);
                }
            }

            if ($recompute) {
                $em = $event->getEntityManager();
                $uow = $em->getUnitOfWork();
                $meta = $em->getClassMetadata(get_class($page));
                $uow->recomputeSingleEntityChangeSet($meta, $page);
            }
        }
    }
}
