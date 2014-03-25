<?php

namespace Ekyna\Bundle\CmsBundle\Routing;

use Ekyna\Bundle\CmsBundle\Entity\Page;
use Ekyna\Bundle\CmsBundle\Entity\PageRepository;
use Symfony\Cmf\Component\Routing\RouteProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * RouteProvider
 * 
 * @see http://symfony.com/doc/master/cmf/components/routing/nested_matcher.html#the-routeprovider
 */
class RouteProvider implements RouteProviderInterface
{
    /**
     * @var \Ekyna\Bundle\CmsBundle\Entity\PageRepository
     */
    protected $pageRepository;

    public function __construct(PageRepository $pageRepository)
    {
        $this->pageRepository = $pageRepository;
    }

    public function getRouteCollectionForRequest(Request $request)
    {
        $pages = $this->pageRepository->findBy(array(
            'path' => rawurldecode($request->getPathInfo()),
            'static' => false
        ));

        $collection = new RouteCollection();
        foreach($pages as $page) {
            $collection->add($page->getRoute(), $this->routeFromPage($page));
        }

        return $collection;
    }

    public function getRoutesByNames($names, $parameters = array())
    {
        $pages = $this->pageRepository->findBy(array('name' => $names));

        $routes = array();
        foreach($pages as $page) {
            $routes[] = $this->routeFromPage($page);
        }

        return $routes;
    }

    public function getRouteByName($name, $parameters = array())
    {
        if(null !== $page = $this->pageRepository->findOneBy(array('name' => $name))) {
            return $this->routeFromPage($page);
        }
        throw new ResourceNotFoundException(sprintf('Unable to find route named "%s".', $name));
    }

    protected function routeFromPage(Page $page)
    {
        $route = new Route($page->getPath());

        $route
            ->setDefault('_controller', $page->getController())
            ->setMethods(array('GET'))
        ;

        return $route;
    }
}
