<?php

namespace Ekyna\Bundle\CmsBundle\Routing;

use Ekyna\Bundle\CmsBundle\Entity\PageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Cmf\Component\Routing\RouteProviderInterface;

/**
 * Class RouteProvider
 * @package Ekyna\Bundle\CmsBundle\Routing
 * @author Étienne Dauvergne <contact@ekyna.com>
 * @see http://symfony.com/doc/master/cmf/components/routing/nested_matcher.html#the-routeprovider
 */
class RouteProvider implements RouteProviderInterface
{
    /**
     * @var PageRepository
     */
    protected $pageRepository;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var array
     */
    protected $locales;

    /**
     * Constructor.
     *
     * @param PageRepository $pageRepository
     * @param array          $config
     * @param array          $locales
     */
    public function __construct(PageRepository $pageRepository, array $config, array $locales)
    {
        $this->pageRepository = $pageRepository;
        $this->config         = $config;
        $this->locales        = $locales;
    }

    /**
     * Finds Routes that match the given request.
     *
     * @param Request $request
     *
     * @return RouteCollection
     */
    public function getRouteCollectionForRequest(Request $request)
    {
        $collection = new RouteCollection();

        // Fetch pages IDs by request path.
        $qb = $this->pageRepository->createQueryBuilder('p');
        $qb
            ->select('p.id')
            ->join('p.translations', 't')
            ->andWhere($qb->expr()->eq('p.static', 0))
            ->andWhere($qb->expr()->eq('t.path', $qb->expr()->literal(rawurldecode($request->getPathInfo()))))
        ;
        $results = $qb
            ->getQuery()
            ->getArrayResult()
        ;
        if (empty($results)) {
            return $collection;
        }

        $ids = array_unique(array_map(function($row) {
            return $row['id'];
        }, $results));

        // Fetch pages by IDs
        $qb = $this->pageRepository->createQueryBuilder('p');
        $qb
            ->select('p.route, p.controller, t.path, t.locale')
            ->join('p.translations', 't')
            ->andWhere($qb->expr()->eq('p.static', 0))
            ->andWhere($qb->expr()->in('p.id', $ids))
        ;
        $results = $qb
            ->getQuery()
            ->getArrayResult()
        ;
        $routes =  $this->transformResultsToRoutes($results);

        // Build collection
        foreach ($routes as $name => $route) {
            $collection->add($name, $route);
        }

        return $collection;
    }

    /**
     * Find Routes by name.
     *
     * @param array|null $names
     * @param array $parameters
     *
     * @return array|Route[]
     */
    public function getRoutesByNames($names, $parameters = array())
    {
        $qb = $this->pageRepository->createQueryBuilder('p');
        $qb
            ->select('p.route, p.controller, t.path, t.locale')
            ->join('p.translations', 't')
            ->andWhere($qb->expr()->eq('p.static', 0))
        ;
        if (is_array($names) && !empty($names)) {
            $qb->andWhere($qb->expr()->in('p.route', $names));
        } elseif (is_string($names) && 0 < strlen($names)) {
            $qb->andWhere($qb->expr()->eq('p.route', $qb->expr()->literal($names)));
        }

        $results = $qb
            ->getQuery()
            // TODO cache
            ->getArrayResult()
        ;

        return $this->transformResultsToRoutes($results);
    }

    /**
     * Finds a Route by name.
     *
     * @param string $name
     * @param array $parameters
     *
     * @return null|Route
     */
    public function getRouteByName($name, $parameters = array())
    {
        $qb = $this->pageRepository->createQueryBuilder('p');
        $qb
            ->select('p.route, p.controller, t.path, t.locale')
            ->join('p.translations', 't')
            ->andWhere($qb->expr()->eq('p.static', 0))
            ->andWhere($qb->expr()->eq('p.route', $qb->expr()->literal($name)))
        ;

        $results = $qb
            ->getQuery()
            // TODO cache
            ->getArrayResult()
        ;

        $routes = $this->transformResultsToRoutes($results);
        if (count($routes) == 1) {
            return $routes[0];
        }

        return null;
    }

    /**
     * Transforms the array results into routes.
     *
     * @param array $results
     * @return array|Route[]
     */
    protected function transformResultsToRoutes(array $results)
    {
        $routes = array();
        foreach ($results as $result) {
            $name = $result['route'];
            if (array_key_exists($name, $routes)) {
                /** @var Route $route */
                $route = $routes[$name];
                $paths = $route->getOption('i18n_paths');
                $paths[$result['locale']] = $result['path'];
                $route->setOption('i18n_paths', $paths);
            } else {
                $route = new Route($result['path']);
                if (!array_key_exists($result['controller'], $this->config['controllers'])) {
                    throw new \RuntimeException(sprintf('Undefined controller "%s".', $result['controller']));
                }
                $paths = array($result['locale'] => $result['path']);
                $route
                    ->setDefault('_controller', $this->config['controllers'][$result['controller']]['value'])
                    ->setMethods(array('GET'))
                    ->setOption('i18n_paths', $paths)
                ;
                $routes[$name] = $route;
            }
        }
        // TODO check that i18n_paths are set for all locales ?
        return $routes;
    }
}
