<?php

namespace Ekyna\Bundle\CmsBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Ekyna\Bundle\CmsBundle\Entity\Page;
use Gedmo\Sluggable\Util\Urlizer;

/**
 * PageListener
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class PageListener
{
    protected $defaultController;

    public function __construct($defaultController)
    {
        $this->defaultController = $defaultController;
    }

    public function prePersist(Page $page, LifecycleEventArgs $eventArgs)
    {
        if (null === $page->getRoute()) {
            $page->setRoute(sprintf('cms_page_%s', uniqid()));
        }
        if (!$page->getStatic()) {
            $parentPath = $page->getParent()->getPath();
            $path = $page->getPath();
            if (strlen($path) == 0) {
                $path = $page->getName();
            }
            $page->setPath($parentPath . '/' . Urlizer::urlize(trim($path, '/')));

            if (null === $page->getController()) {
                $page->setController($this->defaultController);
            }
        }
    }
}
