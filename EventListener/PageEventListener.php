<?php

namespace Ekyna\Bundle\CmsBundle\EventListener;

use Doctrine\Common\Cache\CacheProvider;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Ekyna\Bundle\CmsBundle\Entity\Page;
use Ekyna\Bundle\CmsBundle\Event\PageEvents;
use Ekyna\Bundle\CmsBundle\Exception\RuntimeException;
use Ekyna\Bundle\CmsBundle\Helper\PageHelper;
use Ekyna\Bundle\CmsBundle\Model\PageInterface;
use Ekyna\Bundle\CoreBundle\Cache\TagManager;
use Ekyna\Component\Resource\Event\ResourceEventInterface;
use Ekyna\Component\Resource\Event\ResourceMessage;
use Ekyna\Component\Resource\Exception\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\AdapterInterface;
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
     * @var AdapterInterface
     */
    private $cmsCache;

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
    private $resultCache;


    /**
     * Constructor.
     *
     * @param EntityManagerInterface $em
     * @param TagManager             $tm
     * @param AdapterInterface       $cmsCache
     * @param array                  $locales
     * @param array                  $config
     * @param string                 $menuClass
     * @param CacheProvider          $resultCache
     */
    public function __construct(
        EntityManagerInterface $em,
        TagManager $tm,
        AdapterInterface $cmsCache,
        array $locales,
        array $config,
        $menuClass,
        CacheProvider $resultCache = null
    ) {
        $this->em = $em;
        $this->tm = $tm;
        $this->cmsCache = $cmsCache;
        $this->locales = $locales;
        $this->config = $config;
        $this->menuClass = $menuClass;
        $this->resultCache = $resultCache;
    }

    /**
     * Initialize event handler.
     *
     * @param ResourceEventInterface $event
     */
    public function onInitialize(ResourceEventInterface $event)
    {
        $page = $this->getPageFromEvent($event);

        $parent = $page->getParent();
        if ($parent && $parent->isLocked()) {
            throw new RuntimeException("Cannot create child page under a locked parent page.");
        }
    }

    /**
     * Pre create event handler.
     *
     * @param ResourceEventInterface $event
     */
    public function onPreCreate(ResourceEventInterface $event)
    {
        $page = $this->getPageFromEvent($event);

        // Generate random route name.
        if (null === $page->getRoute()) {
            $class = get_class($page);

            /** @noinspection SqlResolve */
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
     * Insert event handler.
     */
    public function onInsert(): void
    {
        $this->purgeRoutesCache();
    }

    /**
     * Pre update event handler.
     *
     * @param ResourceEventInterface $event
     */
    public function onPreUpdate(ResourceEventInterface $event)
    {
        $page = $this->getPageFromEvent($event);

        // Don't disable if static
        if (!$page->isEnabled() && $page->isStatic()) {
            $page->setEnabled(true);
        }

        // Don't enable if at least one ancestor is disabled.
        if ($page->isEnabled()) {
            $parentPage = $page;
            while (null !== $parentPage = $parentPage->getParent()) {
                if (!$parentPage->isEnabled()) {
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
     * Insert event handler.
     *
     * @param ResourceEventInterface $event
     */
    public function onUpdate(ResourceEventInterface $event)
    {
        $page = $this->getPageFromEvent($event);

        $this->purgeRoutesCache();
        $this->purgePageCache($page);
    }

    /**
     * Pre delete event handler.
     *
     * @param ResourceEventInterface $event
     */
    public function onPreDelete(ResourceEventInterface $event)
    {
        $page = $this->getPageFromEvent($event);

        if ($page->isStatic()) {
            $event->addMessage(new ResourceMessage(
                'ekyna_cms.page.alert.do_not_remove_static',
                ResourceMessage::TYPE_ERROR
            ));
        }
    }

    /**
     * Insert event handler.
     *
     * @param ResourceEventInterface $event
     */
    public function onDelete(ResourceEventInterface $event)
    {
        $page = $this->getPageFromEvent($event);

        $this->purgeRoutesCache();
        $this->purgePageCache($page);
    }

    /**
     * Purges the pages routes cache.
     */
    private function purgeRoutesCache(): void
    {
        $this->cmsCache->deleteItem(PageHelper::PAGES_ROUTES_CACHE_KEY);
    }

    /**
     * Purges the page cache.
     *
     * @param PageInterface $page
     */
    private function purgePageCache(PageInterface $page): void
    {
        if (!$this->resultCache) {
            return;
        }

        $this->resultCache->delete(Page::getRouteCacheTag($page->getRoute()));
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
        if (!$page->isEnabled()) {
            if (0 < $page->getChildren()->count()) {
                foreach ($page->getChildren() as $child) {
                    if ($child->isEnabled()) {
                        $child->setEnabled(false);
                        $childrenDisabled = true;

                        $this->em->persist($child);

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
        if (!$page->isEnabled()) {
            // Disable menu children query
            /** @noinspection SqlResolve */
            $disableChildrenQuery = $this->em->createQuery(sprintf(
                'UPDATE %s m SET m.enabled = 0 WHERE m.root = :root AND m.left > :left AND m.right < :right',
                $this->menuClass
            ));

            // Disable pointing menus
            /** @var \Ekyna\Bundle\CmsBundle\Model\MenuInterface[] $menus */
            /** @noinspection SqlResolve */
            $menus = $this->em
                ->createQuery("SELECT m FROM {$this->menuClass} m WHERE m.route = :route")
                ->setParameter('route', $page->getRoute())
                ->getResult();
            if (!empty($menus)) {
                foreach ($menus as $menu) {
                    if ($menu->isEnabled()) {
                        $menu->setEnabled(false);
                        $this->em->persist($menu);
                        $disabledMenus = true;

                        $disableChildrenQuery->execute([
                            'root'  => $menu->getRoot(),
                            'left'  => $menu->getLeft(),
                            'right' => $menu->getRight(),
                        ]);
                    }
                }
            }
        }

        return $disabledMenus;
    }

    /**
     * Returns the page from the event.
     *
     * @param ResourceEventInterface $event
     *
     * @return PageInterface
     */
    private function getPageFromEvent(ResourceEventInterface $event)
    {
        $resource = $event->getResource();

        if (!$resource instanceof PageInterface) {
            throw new InvalidArgumentException("Expected instance of PageInterface");
        }

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            PageEvents::INITIALIZE  => ['onInitialize',  1024],
            PageEvents::PRE_CREATE  => ['onPreCreate',  -1024],
            PageEvents::INSERT      => ['onInsert',      1024],
            PageEvents::PRE_UPDATE  => ['onPreUpdate',  -1024],
            PageEvents::UPDATE      => ['onUpdate',      1024],
            PageEvents::PRE_DELETE  => ['onPreDelete',   1024],
            PageEvents::DELETE      => ['onDelete',      1024],
        ];
    }
}
