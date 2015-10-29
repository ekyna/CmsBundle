<?php

namespace Ekyna\Bundle\CmsBundle\Menu;

use Doctrine\ORM\Query\Expr;
use Ekyna\Bundle\CmsBundle\Helper\PageHelper;
use Ekyna\Bundle\CoreBundle\Cache\TagManager;
use Ekyna\Bundle\CoreBundle\Locale\LocaleProviderInterface;
use Knp\Menu\FactoryInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class BreadcrumbBuilder
 * @package Ekyna\Bundle\CmsBundle\Menu
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class BreadcrumbBuilder
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
     * @var PageHelper
     */
    protected $pageHelper;

    /**
     * @var LocaleProviderInterface
     */
    protected $localeProvider;

    /**
     * @var TagManager
     */
    protected $tagManager;

    /**
     * @var \Knp\Menu\ItemInterface
     */
    protected $breadcrumb;


    /**
     * Constructor.
     * 
     * @param FactoryInterface        $factory
     * @param RouterInterface         $router
     * @param PageHelper              $pageHelper
     * @param LocaleProviderInterface $localeProvider
     * @param TagManager              $tagManager
     */
    public function __construct(
        FactoryInterface        $factory,
        RouterInterface         $router,
        PageHelper              $pageHelper,
        LocaleProviderInterface $localeProvider,
        TagManager              $tagManager
    ) {
        $this->factory         = $factory;
        $this->router          = $router;
        $this->pageHelper      = $pageHelper;
        $this->localeProvider  = $localeProvider;
        $this->tagManager      = $tagManager;
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
            $currentPage = $this->pageHelper->getCurrent();

            // If not found look for a parent
            if (null === $currentPage && null !== $request = $this->pageHelper->getRequest()) {
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
                        $currentPage = $this->pageHelper->findByRoute($cmsOptions['parent']);
                    }
                }
            }

            // If found, build the breadcrumb
            if (null !== $currentPage) {
                $repository = $this->pageHelper->getPageRepository();
                $qb = $repository->createQueryBuilder('p');
                $qb
                    ->select('p.id, p.route, p.dynamicPath, t.breadcrumb')
                    ->join('p.translations', 't', Expr\Join::WITH, $qb->expr()->eq('t.locale',
                        $qb->expr()->literal($this->localeProvider->getCurrentLocale())
                    ))
                    ->andWhere('p.left <= ' . $currentPage->getLeft())
                    ->andWhere('p.right >= ' . $currentPage->getRight())
                    ->orderBy('p.left', 'asc')
                ;
                $pages = $qb->getQuery()->getArrayResult();

                // Fill the menu
                $tagPrefix = call_user_func($repository->getClassName().'::getEntityTagPrefix');
                foreach ($pages as $page) {
                    if ($page['dynamicPath']) {
                        $params = array('uri' => null);
                    } else {
                        $params = array('route' => $page['route']);
                    }
                    $this->breadcrumb
                        ->addChild('page-'.$page['id'], $params)
                        ->setLabel($page['breadcrumb'])
                    ;
                    $this->tagManager->addTags(sprintf('%s[id:%s]', $tagPrefix, $page['id']));
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
            if (null === $home = $this->pageHelper->getHomePage()) {
                throw new \RuntimeException('Home page not found.');
            }
            $this->breadcrumb = $this->factory->createItem('root', array(
                'childrenAttributes' => array(
                    'class' => 'breadcrumb hidden-xs'
                )
            ));
            $this->tagManager->addTags($home->getEntityTag());
        }
    }
}
