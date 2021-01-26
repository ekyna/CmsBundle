<?php

namespace Ekyna\Bundle\CmsBundle\Service\Updater;

use Doctrine\Common\Cache\CacheProvider;
use Doctrine\ORM\EntityManagerInterface;
use Ekyna\Bundle\CmsBundle\Helper\PageHelper;
use Ekyna\Bundle\CmsBundle\Helper\RoutingHelper;
use Ekyna\Bundle\CmsBundle\Model\PageInterface;
use Ekyna\Bundle\CmsBundle\Repository\MenuRepository;
use Ekyna\Bundle\CmsBundle\Repository\PageRepository;
use Ekyna\Bundle\CoreBundle\Cache\TagManager;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * Class PageUpdater
 * @package Ekyna\Bundle\CmsBundle\Service\Updater
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class PageUpdater
{
    /**
     * @var PageRepository
     */
    private $pageRepository;

    /**
     * @var RoutingHelper
     */
    private $routingHelper;

    /**
     * @var MenuUpdater
     */
    private $menuUpdater;

    /**
     * @var MenuRepository
     */
    private $menuRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var TagManager
     */
    private $tagManager;

    /**
     * @var AdapterInterface
     */
    private $cmsCache;

    /**
     * @var CacheProvider
     */
    private $resultCache;

    /**
     * @var array
     */
    private $config;

    /**
     * @var string
     */
    private $pageClass;


    /**
     * Constructor.
     *
     * @param PageRepository         $pageRepository
     * @param RoutingHelper          $routingHelper
     * @param MenuUpdater            $menuUpdater
     * @param MenuRepository         $menuRepository
     * @param EntityManagerInterface $entityManager
     * @param TagManager             $tagManager
     * @param AdapterInterface       $cmsCache
     * @param array                  $config
     * @param string                 $pageClass
     * @param CacheProvider|null     $resultCache
     */
    public function __construct(
        PageRepository $pageRepository,
        RoutingHelper $routingHelper,
        MenuUpdater $menuUpdater,
        MenuRepository $menuRepository,
        EntityManagerInterface $entityManager,
        TagManager $tagManager,
        AdapterInterface $cmsCache,
        array $config,
        string $pageClass,
        CacheProvider $resultCache = null
    ) {
        $this->pageRepository = $pageRepository;
        $this->routingHelper = $routingHelper;
        $this->menuUpdater = $menuUpdater;
        $this->menuRepository = $menuRepository;
        $this->entityManager = $entityManager;
        $this->tagManager = $tagManager;
        $this->cmsCache = $cmsCache;
        $this->config = $config;
        $this->pageClass = $pageClass;
        $this->resultCache = $resultCache;
    }

    /**
     * Updates the page's route property.
     *
     * @param PageInterface $page
     *
     * @return bool
     */
    public function updateRoute(PageInterface $page): bool
    {
        // Generate random route name.
        if (null !== $page->getRoute()) {
            return false;
        }

        do {
            $route = sprintf('cms_page_%s', uniqid());
            $duplicate = $this->pageRepository->findOneByRoute($route, false);
        } while (null !== $duplicate);

        $page->setRoute($route);

        return true;
    }

    /**
     * Updates the page's isDynamic property.
     *
     * @param PageInterface $page
     *
     * @return bool
     */
    public function updateIsDynamic(PageInterface $page): bool
    {
        $dynamicPath = $this->routingHelper->isPagePathDynamic($page->getRoute());
        if ($dynamicPath !== $page->isDynamicPath()) {
            $page->setDynamicPath($dynamicPath);

            return true;
        }

        return false;
    }

    /**
     * Updates the page's 'isAdvanced' property.
     *
     * @param PageInterface $page
     *
     * @return bool
     */
    public function updateIsAdvanced(PageInterface $page): bool
    {
        $advanced = $this->isAdvanced($page);
        if (!is_null($advanced) && ($advanced !== $page->isAdvanced())) {
            $page->setAdvanced($advanced);

            return true;
        }

        return false;
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
            if (array_key_exists($controller, $this->config['controllers'])) {
                return $this->config['controllers'][$controller]['advanced'];
            }

            throw new \RuntimeException("Undefined page controller '{$controller}'.");
        }

        return null;
    }

    /**
     * Disables the page children if needed.
     *
     * @param PageInterface $page
     *
     * @return bool
     */
    public function disablePageChildren(PageInterface $page): bool
    {
        if ($page->isEnabled() || $page->getChildren()->isEmpty()) {
            return false;
        }

        $childrenDisabled = false;

        foreach ($page->getChildren() as $child) {
            if ($child->isEnabled()) {
                $child->setEnabled(false);
                $childrenDisabled = true;

                $this->entityManager->persist($child);

                $this->tagManager->addTags($page->getEntityTag());
            }

            $this->disablePageRelativeMenus($child);

            $childrenDisabled |= $this->disablePageChildren($child);
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
    public function disablePageRelativeMenus(PageInterface $page): bool
    {
        if ($page->isEnabled()) {
            return false;
        }

        $menus = $this->menuRepository->findByRoute($page->getRoute());

        if ($this->menuUpdater->disabledMenuRecursively($menus)) {
            $this->menuUpdater->clearCache();

            return true;
        }

        return false;
    }

    /**
     * Purges the pages routes cache.
     */
    public function purgeRoutesCache(): void
    {
        $this->cmsCache->deleteItem(PageHelper::PAGES_ROUTES_CACHE_KEY);
    }

    /**
     * Purges the page cache.
     *
     * @param PageInterface $page
     */
    public function purgePageCache(PageInterface $page): void
    {
        if (!$this->resultCache) {
            return;
        }

        $this->resultCache->delete(
            call_user_func($this->pageClass . '::getRouteCacheTag', $page->getRoute())
        );
    }
}
