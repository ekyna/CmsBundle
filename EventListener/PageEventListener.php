<?php

namespace Ekyna\Bundle\CmsBundle\EventListener;

use Doctrine\Common\Cache\CacheProvider;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Ekyna\Bundle\AdminBundle\Event\ResourceMessage;
use Ekyna\Bundle\CmsBundle\Event\PageEvent;
use Ekyna\Bundle\CmsBundle\Event\PageEvents;
use Ekyna\Bundle\CmsBundle\Model\PageInterface;
use Ekyna\Bundle\CoreBundle\Cache\TagManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class PageEventListener
 * @package Ekyna\Bundle\CmsBundle\EventListener
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class PageEventListener implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var TagManager
     */
    private $tm;

    /**
     * @var array
     */
    private $locales;

    /**
     * @var array
     */
    private $config;

    /**
     * @var string
     */
    private $menuClass;

    /**
     * @var CacheProvider
     */
    private $cache;


    /**
     * Constructor.
     *
     * @param EntityManagerInterface   $em
     * @param TagManager               $tm
     * @param array                    $locales
     * @param array                    $config
     * @param string                   $menuClass
     * @param CacheProvider            $cache
     */
    public function __construct(
        EntityManagerInterface $em,
        TagManager $tm,
        array $locales,
        array $config,
        $menuClass,
        CacheProvider $cache = null
    ) {
        $this->em = $em;
        $this->tm = $tm;
        $this->locales = $locales;
        $this->config = $config;
        $this->menuClass = $menuClass;
        $this->cache = $cache;
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
            $class = get_class($page);
            $query = $this->em->createQuery("SELECT p.id FROM {$class} p WHERE p.route = :route");
            $query->setMaxResults(1);

            do {
                $route = sprintf('cms_page_%s', uniqid());
                $result = $query
                    ->setParameter('route', $route)
                    ->getOneOrNullResult(Query::HYDRATE_SCALAR);
            } while (null !== $result);

            $page->setRoute($route);
        }
    }

    /**
     * Pre update event handler.
     *
     * @param PageEvent $event
     */
    public function onPreUpdate(PageEvent $event)
    {
        $page = $event->getPage();

        // Don't disable if static
        if (!$page->getEnabled() && $page->getStatic()) {
            $page->setEnabled(true);
        }

        // Don't enable if at least one ancestor is disabled.
        if ($page->getEnabled()) {
            $parentPage = $page;
            while (null !== $parentPage = $parentPage->getParent()) {
                if (!$parentPage->getEnabled()) {
                    $page->setEnabled(false);
                    $event->addMessage(new ResourceMessage(
                        'ekyna_cms.page.alert.parent_disabled',
                        ResourceMessage::TYPE_WARNING
                    ));
                    break;
                }
            }
        }

        // Bubble disable
        if ($this->disablePageChildren($page)) {
            $event->addMessage(new ResourceMessage(
                'ekyna_cms.page.alert.children_disabled',
                ResourceMessage::TYPE_WARNING
            ));
        }
        if ($this->disablePageRelativeMenus($page)) {
            $event->addMessage(new ResourceMessage(
                'ekyna_cms.page.alert.menus_disabled',
                ResourceMessage::TYPE_WARNING
            ));
            $this->tm->addTags(call_user_func($this->menuClass . '::getEntityTagPrefix'));
        }
    }

    /**
     * Post update event handler.
     *
     * @param PageEvent $event
     */
    public function onPostUpdate(PageEvent $event)
    {
        $this->deletePageCache($event->getPage());
    }

    /**
     * Pre delete event handler.
     *
     * @param PageEvent $event
     */
    public function onPreDelete(PageEvent $event)
    {
        if ($event->getPage()->getStatic()) {
            $event->addMessage(new ResourceMessage(
                'ekyna_cms.page.alert.do_not_remove_static',
                ResourceMessage::TYPE_ERROR
            ));
        }
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
    private function deletePageCache(PageInterface $page)
    {
        if (null !== $this->cache) {
            $this->cache->delete('ekyna_cms.page[route:' . $page->getRoute() . ']');
        }
    }

    /**
     * Disables the page children if needed.
     *
     * @param PageInterface $page
     *
     * @return bool
     */
    private function disablePageChildren(PageInterface $page)
    {
        $childrenDisabled = false;
        if (!$page->getEnabled()) {
            if (0 < $page->getChildren()->count()) {
                foreach ($page->getChildren() as $child) {
                    if ($child->getEnabled()) {
                        $child->setEnabled(false);
                        $childrenDisabled = true;

                        $this->em->persist($child);

                        $this->deletePageCache($page);

                        $this->tm->addTags($page->getEntityTag());
                    }

                    $this->disablePageRelativeMenus($child);

                    $childrenDisabled |= $this->disablePageChildren($child);
                }
            }
        }
        return $childrenDisabled;
    }

    /**
     * Disable the page relative menus if needed.
     *
     * @param PageInterface $page
     *
     * @return bool
     */
    private function disablePageRelativeMenus(PageInterface $page)
    {
        $disabledMenus = false;
        if (!$page->getEnabled()) {

            // Disable menu children query
            $disableChildrenQuery = $this->em->createQuery(sprintf(
                'UPDATE %s m SET m.enabled = 0 WHERE m.root = :root AND m.left > :left AND m.right < :right',
                $this->menuClass
            ));

            // Disable pointing menus
            /** @var \Ekyna\Bundle\CmsBundle\Model\MenuInterface[] $menus */
            $menus = $this->em
                ->createQuery("SELECT m FROM {$this->menuClass} m WHERE m.route = :route")
                ->setParameter('route', $page->getRoute())
                ->getResult();
            if (!empty($menus)) {
                foreach ($menus as $menu) {
                    if ($menu->getEnabled()) {
                        $menu->setEnabled(false);
                        $this->em->persist($menu);
                        $disabledMenus = true;

                        $disableChildrenQuery->execute(array(
                            'root'  => $menu->getRoot(),
                            'left'  => $menu->getLeft(),
                            'right' => $menu->getRight(),
                        ));
                    }
                }
            }
        }
        return $disabledMenus;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            PageEvents::PRE_CREATE  => array('onPreCreate', -1024),
            PageEvents::PRE_UPDATE  => array('onPreUpdate', -1024),
            PageEvents::POST_UPDATE => array('onPostUpdate', -1024),
            PageEvents::POST_DELETE => array('onPostDelete', -1024),
        );
    }
}
