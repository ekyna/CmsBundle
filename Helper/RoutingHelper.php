<?php

namespace Ekyna\Bundle\CmsBundle\Helper;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class RoutingHelper
 * @package Ekyna\Bundle\CmsBundle\Helper
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class RoutingHelper
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var RouteCollection
     */
    private $routes;


    /**
     * Constructor.
     *
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * Returns the router.
     *
     * @return RouterInterface
     */
    public function getRouter(): RouterInterface
    {
        return $this->router;
    }

    /**
     * Finds the route by its name.
     *
     * @param string $name
     *
     * @return Route|null
     */
    public function findRouteByName(string $name): ?Route
    {
        return $this->getRoutes()->get($name);
    }

    /**
     * Returns the routes.
     *
     * @return RouteCollection
     */
    public function getRoutes(): RouteCollection
    {
        if (null !== $this->routes) {
            return $this->routes;
        }

        $i18nRouterClass = 'JMS\I18nRoutingBundle\Router\I18nRouterInterface';
        if (interface_exists($i18nRouterClass) && $this->router instanceof $i18nRouterClass) {
            return $this->routes = $this->router->getOriginalRouteCollection();
        }

        return $this->routes = $this->router->getRouteCollection();
    }
}
