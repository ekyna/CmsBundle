<?php

namespace Ekyna\Bundle\CmsBundle\Listener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Ekyna\Bundle\CmsBundle\Helper\RoutingHelper;
use Ekyna\Bundle\CmsBundle\Install\Generator\Util;
use Ekyna\Bundle\CmsBundle\Model\PageInterface;
use Ekyna\Bundle\SettingBundle\Event\BuildRedirectionEvent;
use Ekyna\Bundle\SettingBundle\Event\DiscardRedirectionEvent;
use Ekyna\Bundle\SettingBundle\Event\RedirectionEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class PageListener
 * @package Ekyna\Bundle\CmsBundle\Listener
 * @author  Étienne Dauvergne <contact@ekyna.com>
 *
 * @todo    resource (persistence) event subscriber ?
 */
class PageListener
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var RoutingHelper
     */
    private $routingHelper;

    /**
     * @var array
     */
    private $pageConfig;

    /**
     * @var array
     */
    private $locales;


    /**
     * Constructor.
     *
     * @param EventDispatcherInterface $dispatcher
     * @param RoutingHelper            $routingHelper
     * @param array                    $pageConfig
     * @param array                    $locales
     */
    public function __construct(
        EventDispatcherInterface $dispatcher,
        RoutingHelper $routingHelper,
        array $pageConfig,
        array $locales
    ) {
        $this->dispatcher    = $dispatcher;
        $this->routingHelper = $routingHelper;
        $this->pageConfig    = $pageConfig;
        $this->locales       = $locales;
    }

    /**
     * Pre persist event handler.
     *
     * @param PageInterface $page
     */
    public function prePersist(PageInterface $page): void
    {
        $this->handlePage($page);
    }

    /**
     * Handles the page.
     *
     * @param PageInterface $page
     *
     * @return bool
     */
    private function handlePage(PageInterface $page): bool
    {
        $doRecompute = false;

        $dynamicPath = $this->hasDynamicPath($page);
        if ($dynamicPath != $page->isDynamicPath()) {
            $page->setDynamicPath($dynamicPath);
            $doRecompute = true;
        }

        $advanced = $this->isAdvanced($page);
        if (!is_null($advanced) && ($advanced != $page->isAdvanced())) {
            $page->setAdvanced($advanced);
            $doRecompute = true;
        }

        return $doRecompute;
    }

    /**
     * Returns whether the page has dynamic path or not.
     *
     * @param PageInterface $page
     *
     * @return bool
     */
    private function hasDynamicPath(PageInterface $page): bool
    {
        if (empty($route = $page->getRoute())) {
            return false;
        }

        if (null === $route = $this->routingHelper->findRouteByName($route)) {
            return false;
        }

        return Util::isDynamic($route);
    }

    /**
     * Returns whether the page is advanced or not.
     *
     * @param PageInterface $page
     *
     * @return bool|null
     */
    private function isAdvanced(PageInterface $page): ?bool
    {
        if (null !== $controller = $page->getController()) {
            if (array_key_exists($controller, $this->pageConfig['controllers'])) {
                return $this->pageConfig['controllers'][$controller]['advanced'];
            }

            throw new \RuntimeException("Undefined page controller '{$controller}'.");
        }

        return null;
    }

    /**
     * Pre update event handler.
     *
     * @param PageInterface      $page
     * @param PreUpdateEventArgs $event
     */
    public function preUpdate(PageInterface $page, PreUpdateEventArgs $event): void
    {
        $em  = $event->getEntityManager();
        $uow = $em->getUnitOfWork();

        if ($this->handlePage($page)) {
            $metadata = $em->getClassMetadata(get_class($page));
            $uow->recomputeSingleEntityChangeSet($metadata, $page);
        }
    }

    /**
     * Post update event handler.
     *
     * @param PageInterface      $page
     * @param LifecycleEventArgs $event
     */
    public function postUpdate(PageInterface $page, LifecycleEventArgs $event): void
    {
        /** @var \Doctrine\ORM\EntityManagerInterface $em */
        $em  = $event->getObjectManager();
        $uow = $em->getUnitOfWork();

        $changeSet = $uow->getEntityChangeSet($page);
        if (!array_key_exists('enabled', $changeSet)) {
            return;
        }

        if ($page->isEnabled()) {
            $this->discardPageRedirections($page);
        } else {
            $this->buildPageRedirections($page);
        }
    }

    /**
     * Discards redirections for the page.
     *
     * @param PageInterface $page
     */
    private function discardPageRedirections(PageInterface $page): void
    {
        if (!$page->isEnabled()) {
            return;
        }

        foreach ($page->getTranslations() as $locale => $translation) {
            // TODO use url generator or i18n routing prefix strategy
            $localePrefix = $locale != 'fr' ? '/' . $locale : '';
            $event        = new DiscardRedirectionEvent($localePrefix . $translation->getPath());
            $this->dispatcher->dispatch(RedirectionEvents::DISCARD, $event);
        }
    }

    /**
     * Builds redirections for the page.
     *
     * @param PageInterface $page
     */
    private function buildPageRedirections(PageInterface $page): void
    {
        if ($page->isEnabled()) {
            return;
        }

        $redirections = [];

        // Store "from" paths for each locale
        $locales = [];
        foreach ($page->getTranslations() as $locale => $translation) {
            $locales[$locale]      = $locale;
            $redirections[$locale] = [
                'from' => $translation->getPath(),
            ];
        }

        // Find the first enabled ancestor
        $parentPage = $page;
        while (null !== $parentPage = $parentPage->getParent()) {
            if ($parentPage->isEnabled()) {
                // Store "to" paths for each locale
                foreach ($parentPage->getTranslations() as $locale => $translation) {
                    if (array_key_exists($locale, $redirections)) {
                        $redirections[$locale]['to'] = $translation->getPath();
                        unset($locales[$locale]);
                    }

                    // Check that all locales has been handled
                    if (empty($locales)) {
                        break 2;
                    }
                }
            }
        }

        if (!empty($redirections)) {
            foreach ($redirections as $locale => $redirection) {
                if (!(array_key_exists('from', $redirection) && array_key_exists('to', $redirection))) {
                    continue;
                }
                // TODO use url generator or i18n routing prefix strategy
                $localePrefix = $locale != 'fr' ? '/' . $locale : '';

                $event = new BuildRedirectionEvent(
                    $localePrefix . $redirection['from'],
                    $localePrefix . $redirection['to'],
                    true
                );

                $this->dispatcher->dispatch(RedirectionEvents::BUILD, $event);
            }
        }

    }

    /**
     * Post remove event handler.
     *
     * @param PageInterface $page
     */
    public function postRemove(PageInterface $page): void
    {
        $this->buildPageRedirections($page);
    }
}
