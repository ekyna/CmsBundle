<?php

namespace Ekyna\Bundle\CmsBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Ekyna\Bundle\CmsBundle\Event\PageEvent;
use Ekyna\Bundle\CmsBundle\Event\PageEvents;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class PageListener
 * @package Ekyna\Bundle\CmsBundle\EventListener
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class PageListener implements EventSubscriberInterface
{
    /**
     * @var string
     */
    protected $defaultController;

    /**
     * Constructor.
     *
     * @param string $defaultController
     */
    public function __construct($defaultController)
    {
        $this->defaultController = $defaultController;
    }

    /**
     * Pre create event handler.
     *
     * @param PageEvent $event
     */
    public function onPreCreate(PageEvent $event)
    {
        $page = $event->getPage();

        // Generate random route name.
        if (null === $page->getRoute()) {
            $page->setRoute(sprintf('cms_page_%s', uniqid()));
        }

        // Generate path
        if (!$page->getStatic()) {
            $parentPath = $page->getParent()->getPath();
            $path = $page->getPath();
            if (strlen($path) == 0) {
                $path = $page->getName();
            }
            $page->setPath($parentPath . '/' . Urlizer::urlize(trim($path, '/')));

            // Set default controller
            if (null === $page->getController()) {
                $page->setController($this->defaultController);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            PageEvents::PRE_CREATE  => array('onPreCreate', 0),
        );
    }
}
