<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Service\Routing;

use Ekyna\Bundle\CmsBundle\Repository\PageRepositoryInterface;
use RuntimeException;
use Symfony\Cmf\Component\Routing\RouteProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

use function rawurldecode;
use function sprintf;

/**
 * Class RouteProvider
 * @package Ekyna\Bundle\CmsBundle\Service\Routing
 * @author  Étienne Dauvergne <contact@ekyna.com>
 * @see     http://symfony.com/doc/master/cmf/components/routing/nested_matcher.html#the-routeprovider
 */
class RouteProvider implements RouteProviderInterface
{
    protected PageRepositoryInterface $pageRepository;
    protected array                   $config;
    protected array                   $locales;


    /**
     * Constructor.
     *
     * @param PageRepositoryInterface $pageRepository
     * @param array                   $config
     * @param array                   $locales
     */
    public function __construct(PageRepositoryInterface $pageRepository, array $config, array $locales)
    {
        $this->pageRepository = $pageRepository;
        $this->config = $config;
        $this->locales = $locales;
    }

    /**
     * @inheritDoc
     */
    public function getRouteCollectionForRequest(Request $request): RouteCollection
    {
        $collection = new RouteCollection();

        $path = rawurldecode($request->getPathInfo());

        $results = $this
            ->pageRepository
            ->getRoutesDataByPath($path);

        if (empty($results)) {
            return $collection;
        }

        $routes = $this->transformResultsToRoutes($results);

        // Build collection
        foreach ($routes as $name => $route) {
            $collection->add($name, $route);
        }

        return $collection;
    }

    /**
     * @inheritDoc
     */
    public function getRoutesByNames($names, array $parameters = []): array
    {
        $results = $this
            ->pageRepository
            ->getRoutesDataByNames($names);

        return $this->transformResultsToRoutes($results);
    }

    /**
     * @inheritDoc
     */
    public function getRouteByName($name, array $parameters = []): Route
    {
        $data = $this
            ->pageRepository
            ->getRouteDataByName($name);

        if ($data) {
            return $this->transformDataToRoute($data);
        }

        throw new RouteNotFoundException();
    }

    /**
     * Transforms the results into routes.
     *
     * @param array $results
     *
     * @return array|Route[]
     */
    protected function transformResultsToRoutes(array $results): array
    {
        $routes = [];

        foreach ($results as $result) {
            $routes[$result['route']] = $this->transformDataToRoute($result);
        }

        return $routes;
    }

    /**
     * Transforms the data into a route.
     *
     * @param array $data
     *
     * @return Route
     */
    protected function transformDataToRoute(array $data): Route
    {
        $controller = $data['controller'];

        if (!isset($this->config['controllers'][$controller])) {
            throw new RuntimeException(sprintf('Undefined controller "%s".', $controller));
        }

        // Host (with resource bundle)

        $route = new Route($data['path']);
        $route
            ->setDefaults([
                '_controller' => $this->config['controllers'][$controller]['value'],
                '_locale'     => $data['locale'],
            ])
            ->setMethods(['GET']);

        return $route;
    }
}
