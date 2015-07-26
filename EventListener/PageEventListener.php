<?php

namespace Ekyna\Bundle\CmsBundle\EventListener;

use Behat\Transliterator\Transliterator;
use Doctrine\Common\Cache\CacheProvider;
use Ekyna\Bundle\CmsBundle\Event\PageEvent;
use Ekyna\Bundle\CmsBundle\Event\PageEvents;
use Ekyna\Bundle\CmsBundle\Model\PageInterface;
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
     * @var CacheProvider
     */
    private $cache;


    /**
     * Constructor.
     *
     * @param array $locales
     * @param CacheProvider $cache
     */
    public function __construct(array $locales, CacheProvider $cache = null)
    {
        $this->locales = $locales;
        $this->cache   = $cache;
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

        // Handle paths.
        $this->generateTranslationPaths($page);
        $this->watchDynamicPaths($page);
    }

    /**
     * Post create event handler.
     *
     * @param PageEvent $event
     */
    public function onPostCreate(PageEvent $event)
    {
        $this->savePageCache($event->getPage());
    }

    /**
     * Pre update event handler.
     *
     * @param PageEvent $event
     */
    public function onPreUpdate(PageEvent $event)
    {
        $page = $event->getPage();

        // Handle paths.
        $this->generateTranslationPaths($page);
        $this->watchDynamicPaths($page);
    }

    /**
     * Post update event handler.
     *
     * @param PageEvent $event
     */
    public function onPostUpdate(PageEvent $event)
    {
        $this->savePageCache($event->getPage());
    }

    /**
     * Post delete event handler.
     *
     * @param PageEvent $event
     */
    public function onPostDelete(PageEvent $event)
    {
        $this->deletePageCache($event->getPage());
    }

    /**
     * Saves the page in doctrine cache.
     *
     * @param PageInterface $page
     */
    private function savePageCache(PageInterface $page)
    {
        if (null !== $this->cache) {
            $this->cache->save('ekyna_cms.page[id:' . $page->getId() . ']', $page);
            $this->cache->save('ekyna_cms.page[route:' . $page->getRoute() . ']', $page);
        }
    }

    /**
     * Saves the page in doctrine cache.
     *
     * @param PageInterface $page
     */
    private function deletePageCache(PageInterface $page)
    {
        if (null !== $this->cache) {
            $this->cache->delete('ekyna_cms.page[id:' . $page->getId() . ']');
            $this->cache->delete('ekyna_cms.page[route:' . $page->getRoute() . ']');
        }
    }

    /**
     * @param PageInterface $page
     */
    private function generateTranslationPaths(PageInterface $page)
    {
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
     * @param PageInterface $page
     */
    private function watchDynamicPaths(PageInterface $page)
    {
        foreach ($this->locales as $locale) {
            if (0 < preg_match('~\{.*\}~', $page->translate($locale)->getPath())) {
                $page->setDynamicPath(true);
                return;
            }
        }
        $page->setDynamicPath(false);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            PageEvents::PRE_CREATE  => array('onPreCreate', 0),
            PageEvents::POST_CREATE => array('onPostCreate', 0),
            PageEvents::PRE_UPDATE  => array('onPreUpdate', 0),
            PageEvents::POST_UPDATE => array('onPostUpdate', 0),
            PageEvents::POST_DELETE => array('onPostDelete', 0),
        );
    }
}
