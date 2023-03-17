<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Service\Updater;

use Doctrine\ORM\EntityManagerInterface;
use Ekyna\Bundle\CmsBundle\Model\PageInterface;
use Ekyna\Bundle\CmsBundle\Repository\MenuRepositoryInterface;
use Ekyna\Bundle\CmsBundle\Repository\PageRepositoryInterface;
use Ekyna\Bundle\CmsBundle\Service\Helper\RoutingHelper;
use Ekyna\Bundle\ResourceBundle\Service\Http\TagManager;
use Ekyna\Bundle\UiBundle\Service\TwigHelper;
use RuntimeException;

/**
 * Class PageUpdater
 * @package Ekyna\Bundle\CmsBundle\Service\Updater
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class PageUpdater
{
    public function __construct(
        private readonly PageRepositoryInterface $pageRepository,
        private readonly RoutingHelper           $routingHelper,
        private readonly MenuUpdater             $menuUpdater,
        private readonly MenuRepositoryInterface $menuRepository,
        private readonly EntityManagerInterface  $entityManager,
        private readonly TagManager              $tagManager,
        private readonly TwigHelper              $twigHelper,
        private readonly array                   $config
    ) {
    }

    /**
     * Updates the page's route property.
     */
    public function updateRoute(PageInterface $page): void
    {
        // Generate random route name.
        if (null !== $page->getRoute()) {
            return;
        }

        do {
            $route = sprintf('cms_page_%s', uniqid());
            $duplicate = $this->pageRepository->findOneByRoute($route, false);
        } while (null !== $duplicate);

        $page->setRoute($route);
    }

    /**
     * Updates the page's isDynamic property.
     */
    public function updateIsDynamic(PageInterface $page): bool
    {
        $dynamicPath = $this->routingHelper->isPagePathDynamic($page->getRoute(), null);
        if ($dynamicPath !== $page->isDynamicPath()) {
            $page->setDynamicPath($dynamicPath);

            return true;
        }

        return false;
    }

    /**
     * Updates the page's 'isAdvanced' property.
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
     */
    private function isAdvanced(PageInterface $page): ?bool
    {
        if (null !== $template = $page->getTemplate()) {
            if (!$this->twigHelper->templateExists($template)) {
                throw new RuntimeException("Template '$template' does not exist.");
            }

            return true;
        }

        if (null !== $controller = $page->getController()) {
            if (!array_key_exists($controller, $this->config['controllers'])) {
                throw new RuntimeException("Undefined page controller '$controller'.");
            }

            return $this->config['controllers'][$controller]['advanced'];
        }

        return null;
    }

    /**
     * Disables the page children if needed.
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

            $childrenDisabled = $this->disablePageChildren($child) || $childrenDisabled;
        }

        return $childrenDisabled;
    }

    /**
     * Disable the page relative menus if needed.
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
}
