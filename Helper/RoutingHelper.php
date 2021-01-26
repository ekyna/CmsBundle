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
     * @var string
     */
    private $defaultLocale;


    /**
     * Constructor.
     *
     * @param RouterInterface $router
     * @param string          $defaultLocale
     */
    public function __construct(RouterInterface $router, string $defaultLocale)
    {
        $this->router = $router;
        $this->defaultLocale = $defaultLocale;
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
     * Builds the page's translation path.
     *
     * @param string      $name   The route name
     * @param string|null $locale The locale
     *
     * @return string
     */
    public function buildPagePath(string $name, string $locale = null): string
    {
        $locale = $locale ?: $this->defaultLocale;

        $route = $this->findRouteByName($name, $locale);
        $compiled = $route->compile();

        $path = '';
        $optional = true;

        foreach ($compiled->getTokens() as $token) {
            $value = '';
            if ('variable' === $token[0]) {
                $param = $token[3];
                if ($optional && $route->hasDefault($param)) {
                    continue;
                }

                if ($route->hasDefault($param) && !empty($default = $route->getDefault($param))) {
                    $value = $default;
                } else {
                    $value = "{{$param}}";
                }
            }

            $path = $token[1] . $value . $path;

            $optional = false;
        }

        if (0 === strpos($path, '/' . $locale . '/')) {
            $path = substr($path, strlen('/' . $locale));
        }

        return $path;
    }

    /**
     * Finds the route by its name.
     *
     * @param string $name
     * @param string $locale
     *
     * @return Route|null
     */
    public function findRouteByName(string $name, string $locale = null): ?Route
    {
        if (!empty($locale) && $this->isI18nRouter()) {
            $i18n = $locale .
                constant('JMS\I18nRoutingBundle\Router\Loader\I18nLoaderInterface::ROUTING_PREFIX') .
                $name;

            if ($route = $this->router->getRouteCollection()->get($i18n)) {
                return $route;
            }
        }

        return $this->getRoutes()->get($name);
    }

    /**
     * Returns whether the router supports i18n routes.
     *
     * @return bool
     */
    private function isI18nRouter(): bool
    {
        $i18nRouterClass = 'JMS\I18nRoutingBundle\Router\I18nRouterInterface';

        return interface_exists($i18nRouterClass) && $this->router instanceof $i18nRouterClass;
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

        if ($this->isI18nRouter()) {
            return $this->routes = $this->router->getOriginalRouteCollection();
        }

        return $this->routes = $this->router->getRouteCollection();
    }

    /**
     * Returns whether the page's path has at least one mandatory parameter.
     *
     * @param string $name
     *
     * @return bool
     */
    public function isPagePathDynamic(string $name): bool
    {
        if (null === $route = $this->findRouteByName($name)) {
            return false;
        }

        $compiled = $route->compile();

        $optional = true;

        foreach ($compiled->getTokens() as $token) {
            if ($token[0] === 'variable') {
                if ($optional && $route->hasDefault($token[3])) {
                    continue;
                }

                return true;
            }

            $optional = false;
        }

        return false;
    }
}
