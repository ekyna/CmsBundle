<?php

namespace Ekyna\Bundle\CmsBundle\Menu;

use Doctrine\ORM\Query\Expr;
use Ekyna\Bundle\CmsBundle\Entity\PageRepository;
use Ekyna\Bundle\CmsBundle\Model\PageInterface;
use Ekyna\Bundle\CoreBundle\Locale\LocaleProviderInterface;
use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class MenuBuilder
 * @package Ekyna\Bundle\CmsBundle\Menu
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class MenuBuilder
{
    /**
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * @var RouterInterface|\JMS\I18nRoutingBundle\Router\I18nRouter
     */
    protected $router;

    /**
     * @var PageRepository
     */
    protected $pageRepository;

    /**
     * @var LocaleProviderInterface
     */
    protected $localeProvider;

    /**
     * @var RequestStack
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
     * @param FactoryInterface        $factory
     * @param RouterInterface         $router
     * @param PageRepository          $pageRepository
     * @param LocaleProviderInterface $localeProvider
     * @param RequestStack            $requestStack
     * @param string                  $homeRouteName
     */
    public function __construct(
        FactoryInterface        $factory,
        RouterInterface         $router,
        PageRepository          $pageRepository,
        LocaleProviderInterface $localeProvider,
        RequestStack            $requestStack,
        $homeRouteName        = 'home'
    ) {
        $this->factory         = $factory;
        $this->router          = $router;
        $this->pageRepository  = $pageRepository;
        $this->localeProvider  = $localeProvider;
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
            $this->createBreadcrumb();
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
     * @ItemInterface
     */
    public function createBreadcrumb()
    {
        if (null === $this->breadcrumb) {
            $this->createBreadcrumbRoot();

            // Retrieve the current page.
            $currentPage = null;
            if (null !== $request = $this->requestStack->getCurrentRequest()) {
                $currentPage = $this->pageRepository->findOneByRequest($request);

                // If not found look for a parent
                if (null === $currentPage) {
                    // TODO create a route finder ? (same in AdminBundle ResourceHelper)
                    $routeName = $request->attributes->get('_route');
                    $i18nRouterClass = 'JMS\I18nRoutingBundle\Router\I18nRouterInterface';
                    if (interface_exists($i18nRouterClass) && $this->router instanceof $i18nRouterClass) {
                        $route = $this->router->getOriginalRouteCollection()->get($routeName);
                    } else {
                        $route = $this->router->getRouteCollection()->get($routeName);
                    }
                    if (null !== $route) {
                        $cmsOptions = $route->getOption('_cms');
                        if (null !== $cmsOptions && array_key_exists('parent', $cmsOptions) && 0 < strlen($cmsOptions['parent'])) {
                            $currentPage = $this->pageRepository->findOneByRoute($cmsOptions['parent']);
                        }
                    }
                }
            }

            // If found, build the breadcrumb
            if (null !== $currentPage) {
                $qb = $this->pageRepository->createQueryBuilder('p');
                $qb
                    ->select('p.id, p.route, p.dynamicPath, t.title')
                    ->join('p.translations', 't', Expr\Join::WITH, $qb->expr()->eq('t.locale',
                        $qb->expr()->literal($this->localeProvider->getCurrentLocale())
                    ))
                    ->andWhere('p.left <= ' . $currentPage->getLeft())
                    ->andWhere('p.right >= ' . $currentPage->getRight())
                    ->orderBy('p.left', 'asc')
                ;
                $pages = $qb->getQuery()->getArrayResult();

                // Fill the menu
                foreach ($pages as $page) {
                    if ($page['dynamicPath']) {
                        $params = array('uri' => null);
                    } else {
                        $params = array('route' => $page['route']);
                    }
                    $this->breadcrumb
                        ->addChild('page-'.$page['id'], $params)
                        ->setLabel($page['title'])
                    ;
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
            if (null === $home = $this->pageRepository->findOneByRoute($this->homeRouteName)) {
                throw new \RuntimeException('Home page not found.');
            }
            $this->breadcrumb = $this->factory->createItem('root', array(
                'childrenAttributes' => array(
                    'class' => 'breadcrumb hidden-xs'
                )
            ));
        }
    }
}
