<?php

namespace Ekyna\Bundle\CmsBundle\Menu;

use Ekyna\Bundle\CmsBundle\Entity\Page;
use Ekyna\Bundle\CmsBundle\Entity\PageRepository;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\SecurityContextInterface;

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
    protected $factory;

    /**
     * @var \Ekyna\Bundle\CmsBundle\Entity\PageRepository
     */
    protected $pageRepository;

    /**
     * @var \Symfony\Component\Security\Core\SecurityContext
     */
    protected $securityContext;

    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    protected $requestStack;

    /**
     * @var string
     */
    protected $homeRouteName;

    /**
     * @var \Knp\Menu\ItemInterface
     */
    protected $breadcrumb;


    /**
     * Constructor.
     * 
     * @param \Knp\Menu\FactoryInterface                                $factory
     * @param \Ekyna\Bundle\CmsBundle\Entity\PageRepository             $pageRepository
     * @param \Symfony\Component\HttpFoundation\RequestStack            $requestStack
     * @param \Symfony\Component\Security\Core\SecurityContextInterface $securityContext
     * @param string $homeRouteName
     */
    public function __construct(
        FactoryInterface         $factory, 
        PageRepository           $pageRepository,
        RequestStack             $requestStack,
        SecurityContextInterface $securityContext,
        $homeRouteName         = 'home'
    ) {
        $this->factory         = $factory;
        $this->pageRepository  = $pageRepository;
        $this->requestStack    = $requestStack;
        $this->securityContext = $securityContext;
        $this->homeRouteName   = $homeRouteName;
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

        if (null !== $this->securityContext->getToken()) {
            if ($this->securityContext->isGranted('IS_AUTHENTICATED_FULLY') || $this->securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
                $user = $this->securityContext->getToken()->getUser();
                $item = $menu->addChild($user->getEmail(), array('uri' => '#'));
                $item->addChild('Mon profil', array('route' => 'fos_user_profile_show')); // TODO: use FOSUser translations
                if ($this->securityContext->isGranted('ROLE_ADMIN')) {
                    $item->addChild('Administration', array('route' => 'ekyna_admin'));
                }
                $item->addChild('Se déconnecter', array('route' => 'fos_user_security_logout'));
                return $menu;
            }
        }

        $menu->addChild('Connection', array('route' => 'fos_user_security_login'));

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
        $menu = $this->factory->createItem('root');

        if (null !== $home = $this->pageRepository->findOneBy(array('route' => 'home'))) {
            if ($home->getMenu()) {
                $menu->addChild($home->getName(), array('route' => $home->getRoute()));
            }
            $this->appendMainMenuChildren($menu, $home);
        }

        return $menu;
    }

    /**
     * Creates menu items for given page's children
     * 
     * @param \Knp\Menu\ItemInterface $menu
     * @param \Ekyna\Bundle\CmsBundle\Entity\Page $parent
     */
    public function appendMainMenuChildren(ItemInterface $menu, Page $parent)
    {
        foreach ($parent->getChildren() as $page) {
            if (!$page->getMenu()) {
                continue;
            }
            $item = $menu->addChild($page->getName(), array('route' => $page->getRoute()));
            if ($page->hasChildren() && $page->getLevel() < 2) {
                $this->appendMainMenuChildren($item, $page);
            }
        }
    }

    /**
     * Create footer page menu
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * 
     * @return \Knp\Menu\ItemInterface
     */
    public function createFooterMenu(Request $request)
    {
        $menu = $this->factory->createItem('root');

        if (null !== $home = $this->pageRepository->findOneBy(array('route' => 'home'))) {
            if ($home->getFooter()) {
                $menu->addChild($home->getName(), array('route' => $home->getRoute()));
            }
            $this->appendFooterMenuChildren($menu, $home);
        }

        return $menu;
    }

    /**
     * Creates menu items for given page's children
     * 
     * @param \Knp\Menu\ItemInterface $menu
     * @param \Ekyna\Bundle\CmsBundle\Entity\Page $parent
     */
    public function appendFooterMenuChildren(ItemInterface $menu, Page $parent)
    {
        foreach ($parent->getChildren() as $page) {
            if (!$page->getFooter()) {
                continue;
            }
            $item = $menu->addChild($page->getName(), array('route' => $page->getRoute()));
            if ($page->hasChildren() && $page->getLevel() < 2) {
                $this->appendFooterMenuChildren($item, $page);
            }
        }
    }

    /**
     * Appends an item to the breadcrumb.
     *
     * @param string $name
     * @param string $label
     * @param string $route
     * @param array $parameters
     * 
     * @throws \RuntimeException
     */
    public function breadcrumbAppend($name, $label, $route = null, array $parameters = array())
    {
        if (null === $this->breadcrumb) {
            if (null === $home = $this->pageRepository->findOneBy(array('route' => $this->homeRouteName))) {
                throw new \RuntimeException('Home page not found.');
            }
            $this->createBreadcrumbRoot();
            $this->breadcrumb->addChild('home', array('route' => $home->getRoute()))->setLabel($home->getName());
        }

        $this
            ->breadcrumb
            ->addChild($name, array('route' => $route, 'routeParameters' => $parameters))
            ->setLabel($label)
        ;
    }

    /**
     * Create if not exists and returns the breadcrumb.
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function createBreadcrumb()
    {
        if (null === $this->breadcrumb) {
            $this->generateBreadcrumb();
        }
        return $this->breadcrumb;
    }

    /**
     * Generates the breadcrumb from the current page.
     * 
     * @throws \RuntimeException
     */
    private function generateBreadcrumb()
    {
        if (null === $this->breadcrumb) {
            $this->createBreadcrumbRoot();

            // Retrieve the current page.
            $currentPage = null;
            if (null !== $request = $this->requestStack->getCurrentRequest()) {
                $currentPage = $this->pageRepository->findOneBy(array('route' => $request->attributes->get('_route')));
            }
            if (null !== $currentPage) {
                // Loop through parents
                $pages = array();
                do {
                    $pages[] = $currentPage;
                } while (null !== $currentPage = $currentPage->getParent());
                $pages = array_reverse($pages);

                // Fill the menu
                for ($i = 0; $i < count($pages); $i++) {
                    $page = $pages[$i];
                    if (($i === count($pages) - 1) || preg_match('#\{[\w]+\}#', $page->getPath())) {
                        $params = array('uri' => null);
                    } else {
                        $params = array('route' => $page->getRoute());
                    }
                    $this->breadcrumb->addChild('page-'.$page->getId(), $params)->setLabel($page->getName());
                }
            }
        }
    }

    /**
     * Creates the breadcrumb root item.
     */
    private function createBreadcrumbRoot()
    {
        if (null === $this->breadcrumb) {
            $this->breadcrumb = $this->factory->createItem('root', array(
                'childrenAttributes' => array(
                    'class' => 'breadcrumb hidden-xs'
                )
            ));
        }
    }
}
