<?php

namespace Ekyna\Bundle\CmsBundle\Service\Updater;

use Behat\Transliterator\Transliterator;
use Doctrine\ORM\EntityManagerInterface;
use Ekyna\Bundle\CmsBundle\Model\MenuInterface;
use Ekyna\Bundle\CmsBundle\Repository\PageRepository;
use Ekyna\Bundle\CoreBundle\Cache\TagManager;

/**
 * Class MenuUpdater
 * @package Ekyna\Bundle\CmsBundle\Service\Updater
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class MenuUpdater
{
    private const NAME_REGEX = '#^[a-z0-9_]+$#';

    /**
     * @var PageRepository
     */
    private $pageRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var TagManager
     */
    private $tagManager;

    /**
     * @var string
     */
    private $menuClass;


    /**
     * Constructor.
     *
     * @param PageRepository $pageRepository
     * @param EntityManagerInterface $entityManager
     * @param TagManager $tagManager
     * @param string $menuClass
     */
    public function __construct(
        PageRepository $pageRepository,
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
     *
     * @param MenuInterface $menu
     *
     * @return bool
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
     *
     * @param MenuInterface $menu
     *
     * @return bool
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
     * Checks the menu(s 'enabled' property.
     *
     * @param MenuInterface $menu
     *
     * @return bool
     */
    public function checkEnabled(MenuInterface $menu): bool
    {
        if ($menu->isLocked()) {
            // Don't disable if locked
            if (!$menu->isEnabled()) {
                $menu->setEnabled(true);
            }

            return true;
        }

        if ($menu->isEnabled() && !empty($route = $menu->getRoute())) {
            // Don't enable if relative page is disabled
            $page = $this->pageRepository->findOneByRoute($route);
            if ($page && !$page->isEnabled()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Disables the menus recursively.
     *
     * @param MenuInterface[] $menus
     *
     * @return bool
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

            $changed |= $this->disabledMenuRecursively($menu->getChildren()->toArray());
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
