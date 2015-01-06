<?php

namespace Ekyna\Bundle\CmsBundle\Menu;

use Ekyna\Bundle\CmsBundle\Entity\PageRepository;
use Ekyna\Bundle\CmsBundle\Model\PageInterface;
use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class MenuBuilder
 * @package Ekyna\Bundle\CmsBundle\Menu
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
     * @param string $homeRouteName
     */
    public function __construct(
        FactoryInterface         $factory, 
        PageRepository           $pageRepository,
        RequestStack             $requestStack,
        $homeRouteName         = 'home'
    ) {
        $this->factory         = $factory;
        $this->pageRepository  = $pageRepository;
        $this->requestStack    = $requestStack;
        $this->homeRouteName   = $homeRouteName;
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
            if (null === $home = $this->pageRepository->findOneByRoute($this->homeRouteName)) {
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
            $this->createBreadcrumbRoot();

            // Retrieve the current page.
            $currentPage = null;
            if (null !== $request = $this->requestStack->getCurrentRequest()) {
                $currentPage = $this->pageRepository->findOneByRequest($request);
            }
            if (null !== $currentPage) {
                // Loop through parents
                $pages = array();
                do {
                    $pages[] = $currentPage;
                } while (null !== $currentPage = $currentPage->getParent());
                $pages = array_reverse($pages);

                // Fill the menu
                /** @var PageInterface[] $pages */
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
        return $this->breadcrumb;
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
