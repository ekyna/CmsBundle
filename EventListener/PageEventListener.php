<?php

namespace Ekyna\Bundle\CmsBundle\EventListener;

use Doctrine\Common\Cache\CacheProvider;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Ekyna\Component\Resource\Event\ResourceEventInterface;
use Ekyna\Component\Resource\Event\ResourceMessage;
use Ekyna\Bundle\CmsBundle\Event\PageEvents;
use Ekyna\Bundle\CmsBundle\Model\PageInterface;
use Ekyna\Bundle\CoreBundle\Cache\TagManager;
use Ekyna\Component\Resource\Exception\InvalidArgumentException;
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
     * @param ResourceEventInterface $event
     */
    public function onPreCreate(ResourceEventInterface $event)
    {
        $page = $this->getPageFromEvent($event);

        // Generate random route name.
        if (null === $page->getRoute()) {
            $class = get_class($page);
            /** @noinspection SqlDialectInspection */
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
     * Post update event handler.
     *
     * @param ResourceEventInterface $event
     */
    public function onPostUpdate(ResourceEventInterface $event)
    {
        $page = $this->getPageFromEvent($event);

        $this->deletePageCache($page);
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
     * Post delete event handler.
     *
     * @param ResourceEventInterface $event
     */
    public function onPostDelete(ResourceEventInterface $event)
    {
        $page = $this->getPageFromEvent($event);

        $this->deletePageCache($page);
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
        if (!$page->isEnabled()) {
            if (0 < $page->getChildren()->count()) {
                foreach ($page->getChildren() as $child) {
                    if ($child->isEnabled()) {
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
        if (!$page->isEnabled()) {

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
                    if ($menu->isEnabled()) {
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
        return array(
            PageEvents::PRE_CREATE  => array('onPreCreate', -1024),
            PageEvents::PRE_UPDATE  => array('onPreUpdate', -1024),
            PageEvents::POST_UPDATE => array('onPostUpdate', -1024),
            PageEvents::POST_DELETE => array('onPostDelete', -1024),
        );
    }
}
