<?php

namespace Ekyna\Bundle\CmsBundle\Menu;

use Ekyna\Bundle\CmsBundle\Entity\Page;
use Ekyna\Bundle\CmsBundle\Entity\PageRepository;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * MenuBuilder
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class MenuBuilder
{
    /**
     * @var \Knp\Menu\FactoryInterface
     */
    private $factory;

    /**
     * @var \Ekyna\Bundle\CmsBundle\Entity\PageRepository
     */
    private $pageRepository;

    /**
     * @var \Symfony\Component\Security\Core\SecurityContext
     */
    private $securityContext;

    /**
     * @param \Knp\Menu\FactoryInterface $factory
     */
    public function __construct(
        FactoryInterface $factory, 
        PageRepository $pageRepository,
        SecurityContext $securityContext
    ) {
        $this->factory = $factory;
        $this->pageRepository = $pageRepository;
        $this->securityContext = $securityContext;
    }

    /**
     * Create main menu
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * 
     * @return \Knp\Menu\ItemInterface
     */
    public function createUserMenu(Request $request)
    {
        $menu = $this->factory->createItem('root');

        if ($this->securityContext->isGranted('IS_AUTHENTICATED_FULLY') || $this->securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $user = $this->securityContext->getToken()->getUser();
            $item = $menu->addChild($user->getEmail(), array('uri' => '#'));
            $item->addChild('Mon profil', array('route' => 'fos_user_profile_show')); // TODO: use FOSUser translations
            if ($this->securityContext->isGranted('ROLE_ADMIN')) {
                $item->addChild('Administration', array('route' => 'ekyna_admin'));
            }
            $item->addChild('Se déconnecter', array('route' => 'fos_user_security_logout'));
        } else {
            $menu->addChild('Connection', array('route' => 'fos_user_security_login'));
        }

        return $menu;
    }

    /**
     * Create main menu
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * 
     * @return \Knp\Menu\ItemInterface
     */
    public function createMainMenu(Request $request)
    {
        $menu = $this->factory->createItem('root', array('style' => 'navbar'));

        if (null !== $home = $this->pageRepository->findOneBy(array('route' => 'home'))) {
            if ($home->getMenu()) {
                $menu->addChild($home->getName(), array('route' => $home->getRoute()));
            }
            $this->appendChildren($menu, $home);
        }

        return $menu;
    }

    /**
     * Creates menu items for given page's children
     * 
     * @param \Knp\Menu\ItemInterface $menu
     * @param \Ekyna\Bundle\CmsBundle\Entity\Page $parent
     */
    public function appendChildren(ItemInterface $menu, Page $parent)
    {
        foreach ($parent->getChildren() as $page) {
            if (!$page->getMenu()) {
                continue;
            }
            $item = $menu->addChild($page->getName(), array('route' => $page->getRoute()));
            if ($page->hasChildren() && $page->getLevel() < 2) {
                $this->appendChildren($item, $page);
            }
        }
    }
}
