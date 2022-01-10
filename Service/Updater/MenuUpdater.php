<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Service\Updater;

use Behat\Transliterator\Transliterator;
use Doctrine\ORM\EntityManagerInterface;
use Ekyna\Bundle\CmsBundle\Model\MenuInterface;
use Ekyna\Bundle\CmsBundle\Repository\PageRepositoryInterface;
use Ekyna\Bundle\ResourceBundle\Service\Http\TagManager;

/**
 * Class MenuUpdater
 * @package Ekyna\Bundle\CmsBundle\Service\Updater
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class MenuUpdater
{
    private const NAME_REGEX = '#^[a-z0-9_]+$#';

    private PageRepositoryInterface $pageRepository;
    private EntityManagerInterface  $entityManager;
    private TagManager              $tagManager;
    private string                  $menuClass;

    public function __construct(
        PageRepositoryInterface $pageRepository,
        EntityManagerInterface $entityManager,
        TagManager $tagManager,
        string $menuClass
    ) {
        $this->pageRepository = $pageRepository;
        $this->entityManager = $entityManager;
        $this->tagManager = $tagManager;
        $this->menuClass = $menuClass;
    }

    /**
     * Update the menu's 'route' property.
     */
    public function updateRoute(MenuInterface $menu): bool
    {
        if (null === $page = $menu->getPage()) {
            return false;
        }

        if ($menu->getRoute() === $page->getRoute()) {
            return false;
        }

        $menu->setRoute($page->getRoute());

        return true;
    }

    /**
     * Update the menu's 'name' property.
     */
    public function updateName(MenuInterface $menu): bool
    {
        if (preg_match(self::NAME_REGEX, $menu->getName())) {
            return false;
        }

        $menu->setName(Transliterator::urlize(strtolower($menu->getName()), '_'));

        return true;
    }

    /**
     * Checks the menu's 'enabled' property.
     *
     * @return bool Whether the menu should not be enabled
     */
    public function checkEnabled(MenuInterface $menu): bool
    {
        if (!$menu->isEnabled()) {
            return false;
        }

        if ((!$page = $menu->getPage()) && !empty($route = $menu->getRoute())) {
            $page = $this->pageRepository->findOneByRoute($route);
        }

        // Don't enable if relative page is disabled
        if ($page && !$page->isEnabled()) {
            return true;
        }

        return false;
    }

    /**
     * Disables the menus recursively.
     *
     * @param array<MenuInterface> $menus
     */
    public function disabledMenuRecursively(array $menus): bool
    {
        $changed = false;

        foreach ($menus as $menu) {
            if ($menu->isEnabled()) {
                $menu->setEnabled(false);

                $this->entityManager->persist($menu);

                $changed = true;
            }

            $changed = $this->disabledMenuRecursively($menu->getChildren()->toArray()) || $changed;
        }

        return $changed;
    }

    /**
     * Clears the menu cache.
     */
    public function clearCache(): void
    {
        $this
            ->tagManager
            ->addTags(call_user_func($this->menuClass . '::getEntityTagPrefix'));
    }
}
