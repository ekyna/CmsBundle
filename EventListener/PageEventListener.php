<?php

namespace Ekyna\Bundle\CmsBundle\EventListener;

use Behat\Transliterator\Transliterator;
use Ekyna\Bundle\CmsBundle\Event\PageEvent;
use Ekyna\Bundle\CmsBundle\Event\PageEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class PageEventListener
 * @package Ekyna\Bundle\CmsBundle\EventListener
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class PageEventListener implements EventSubscriberInterface
{
    /**
     * @var array
     */
    private $locales;


    /**
     * @param array $locales
     */
    public function __construct(array $locales)
    {
        $this->locales = $locales;
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

        // Generate paths
        if (!$page->getStatic()) {
            $parentPage = $page->getParent();
            foreach ($this->locales as $locale) {
                $parentPath = $parentPage->translate($locale)->getPath();
                $path = $page->translate($locale)->getPath();
                if (strlen($path) == 0) {
                    $path = $page->translate($locale)->getTitle();
                }
                $page->translate($locale)->setPath(rtrim($parentPath, '/').'/'.Transliterator::urlize(trim($path, '/')));
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
