<?php

namespace Ekyna\Bundle\CmsBundle\EventListener;

use Ekyna\Bundle\CmsBundle\Event\PageEvents;
use Ekyna\Bundle\CmsBundle\Exception\RuntimeException;
use Ekyna\Bundle\CmsBundle\Model\PageInterface;
use Ekyna\Bundle\CmsBundle\Service\Updater\PageRedirectionUpdater;
use Ekyna\Bundle\CmsBundle\Service\Updater\PageUpdater;
use Ekyna\Component\Resource\Event\ResourceEventInterface;
use Ekyna\Component\Resource\Event\ResourceMessage;
use Ekyna\Component\Resource\Exception\InvalidArgumentException;
use Ekyna\Component\Resource\Persistence\PersistenceHelperInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class PageEventListener
 * @package Ekyna\Bundle\CmsBundle\EventListener
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class PageEventListener implements EventSubscriberInterface
{
    /**
     * @var PersistenceHelperInterface
     */
    private $persistenceHelper;

    /**
     * @var PageUpdater
     */
    private $pageUpdater;

    /**
     * @var PageRedirectionUpdater
     */
    private $redirectionUpdater;


    /**
     * Constructor.
     *
     * @param PersistenceHelperInterface $persistenceHelper
     * @param PageUpdater                $pageUpdater
     * @param PageRedirectionUpdater     $redirectionUpdater
     */
    public function __construct(
        PersistenceHelperInterface $persistenceHelper,
        PageUpdater $pageUpdater,
        PageRedirectionUpdater $redirectionUpdater
    ) {
        $this->persistenceHelper = $persistenceHelper;
        $this->pageUpdater = $pageUpdater;
        $this->redirectionUpdater = $redirectionUpdater;
    }

    /**
     * Initialize event handler.
     *
     * @param ResourceEventInterface $event
     */
    public function onInitialize(ResourceEventInterface $event): void
    {
        $page = $this->getPageFromEvent($event);

        $parent = $page->getParent();
        if ($parent && $parent->isLocked()) {
            throw new RuntimeException("Cannot create child page under a locked parent page.");
        }

        $this->pageUpdater->updateRoute($page);
    }

    /**
     * Pre create event handler.
     *
     * @param ResourceEventInterface $event
     */
    public function onPreCreate(ResourceEventInterface $event): void
    {
        $page = $this->getPageFromEvent($event);

        if (!$this->checkEnabled($page)) {
            $event->addMessage(new ResourceMessage(
                'ekyna_cms.page.alert.parent_disabled',
                ResourceMessage::TYPE_WARNING
            ));
        }
    }

    /**
     * Insert event handler.
     *
     * @param ResourceEventInterface $event
     */
    public function onInsert(ResourceEventInterface $event): void
    {
        $page = $this->getPageFromEvent($event);

        $changed = $this->pageUpdater->updateIsDynamic($page);
        $changed |= $this->pageUpdater->updateIsAdvanced($page);

        if ($changed) {
            $this->persistenceHelper->persistAndRecompute($page, false);
        }

        $this->pageUpdater->purgeRoutesCache();
    }

    /**
     * Pre update event handler.
     *
     * @param ResourceEventInterface $event
     */
    public function onPreUpdate(ResourceEventInterface $event): void
    {
        $page = $this->getPageFromEvent($event);

        if (!$this->checkEnabled($page)) {
            $event->addMessage(new ResourceMessage(
                'ekyna_cms.page.alert.parent_disabled',
                ResourceMessage::TYPE_WARNING
            ));
        }

        // Bubble disable
        if ($this->pageUpdater->disablePageChildren($page)) {
            $event->addMessage(new ResourceMessage(
                'ekyna_cms.page.alert.children_disabled',
                ResourceMessage::TYPE_WARNING
            ));
        }
        if ($this->pageUpdater->disablePageRelativeMenus($page)) {
            $event->addMessage(new ResourceMessage(
                'ekyna_cms.page.alert.menus_disabled',
                ResourceMessage::TYPE_WARNING
            ));
        }
    }

    /**
     * Insert event handler.
     *
     * @param ResourceEventInterface $event
     */
    public function onUpdate(ResourceEventInterface $event): void
    {
        $page = $this->getPageFromEvent($event);

        $changed = $this->pageUpdater->updateIsDynamic($page);
        $changed |= $this->pageUpdater->updateIsAdvanced($page);

        if ($changed) {
            $this->persistenceHelper->persistAndRecompute($page, false);
        }

        $this->pageUpdater->purgeRoutesCache();
        $this->pageUpdater->purgePageCache($page);

        if (!$this->persistenceHelper->isChanged($page, 'enabled')) {
            return;
        }

        if ($page->isEnabled()) {
            $this->redirectionUpdater->discardPageRedirections($page);
        } else {
            $this->redirectionUpdater->buildPageRedirections($page);
        }
    }

    /**
     * Pre delete event handler.
     *
     * @param ResourceEventInterface $event
     */
    public function onPreDelete(ResourceEventInterface $event): void
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
    public function onDelete(ResourceEventInterface $event): void
    {
        $page = $this->getPageFromEvent($event);

        $this->pageUpdater->purgeRoutesCache();
        $this->pageUpdater->purgePageCache($page);

        $this->redirectionUpdater->buildPageRedirections($page);
    }

    /**
     * Changes the page's 'enabled' property if needed.
     *
     * @param PageInterface $page
     *
     * @return bool
     */
    private function checkEnabled(PageInterface $page): bool
    {
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

                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Returns the page from the event.
     *
     * @param ResourceEventInterface $event
     *
     * @return PageInterface
     */
    private function getPageFromEvent(ResourceEventInterface $event): PageInterface
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
    public static function getSubscribedEvents(): array
    {
        return [
            PageEvents::INITIALIZE => ['onInitialize', 1024],
            PageEvents::PRE_CREATE => ['onPreCreate', -1024],
            PageEvents::INSERT     => ['onInsert', 1024],
            PageEvents::PRE_UPDATE => ['onPreUpdate', -1024],
            PageEvents::UPDATE     => ['onUpdate', 1024],
            PageEvents::PRE_DELETE => ['onPreDelete', 1024],
            PageEvents::DELETE     => ['onDelete', 1024],
        ];
    }
}
