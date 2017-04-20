<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Service\Helper;

use RuntimeException;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

use function sprintf;

/**
 * Class RoutingHelper
 * @package Ekyna\Bundle\CmsBundle\Service\Helper
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class RoutingHelper
{
    private RouterInterface $router;
    private string          $defaultLocale;

    private ?RouteCollection $routes = null;


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
     * Builds the page's translation path.
     *
     * @param string      $name   The route name
     * @param string|null $locale The locale
     *
     * @return string
     */
    public function buildPagePath(string $name, ?string $locale): string
    {
        $locale = $locale ?: $this->defaultLocale;

        if (null === $route = $this->findRouteByName($name, $locale)) {
            if (null === $route = $this->findRouteByName($name, $this->defaultLocale)) {
                throw new RuntimeException(sprintf('Route \'%s\' not found.', $name));
            }
        }

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
                    $value = "\{$param\}";
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
     * Returns whether the page's path has at least one mandatory parameter.
     *
     * @param string      $name
     * @param string|null $locale
     *
     * @return bool
     */
    public function isPagePathDynamic(string $name, ?string $locale): bool
    {
        if (null === $route = $this->findRouteByName($name, $locale)) {
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

    /**
     * Finds the route by its name.
     *
     * @param string      $name
     * @param string|null $locale
     *
     * @return Route|null
     */
    public function findRouteByName(string $name, ?string $locale): ?Route
    {
        $routes = $this->getRoutes();

        if ($route = $routes->get($name)) {
            return $route;
        }

        $locale = $locale ?: $this->defaultLocale;

        return $routes->get($name . '.' . $locale);
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

        return $this->routes = $this->router->getRouteCollection();
    }
}
