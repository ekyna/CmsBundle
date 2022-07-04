<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Service\Routing;

use ArrayIterator;
use Symfony\Component\Config\Resource\ResourceInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

use function array_map;
use function array_unique;
use function call_user_func_array;
use function count;

/**
 * Class ChainRouteCollection
 * @package Ekyna\Bundle\CmsBundle\Service\Routing
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class ChainRouteCollection extends RouteCollection
{
    /** @var array<RouteCollection> */
    private array            $routeCollections = [];
    private ?RouteCollection $routeCollection  = null;

    public function __clone()
    {
        foreach ($this->routeCollections as $routeCollection) {
            $this->routeCollections[] = clone $routeCollection;
        }
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new ArrayIterator($this->all());
    }

    public function count(): int
    {
        $count = 0;
        foreach ($this->routeCollections as $routeCollection) {
            $count += $routeCollection->count();
        }

        return $count;
    }

    public function add(string $name, Route $route, int $priority = 0)
    {
        $this->createInternalCollection();
        $this->routeCollection->add($name, $route, $priority);
    }

    /**
     * @return array<Route>
     */
    public function all(): array
    {
        $routeCollectionAll = new RouteCollection();
        foreach ($this->routeCollections as $routeCollection) {
            $routeCollectionAll->addCollection($routeCollection);
        }

        return $routeCollectionAll->all();
    }

    public function get(string $name): ?Route
    {
        foreach ($this->routeCollections as $routeCollection) {
            if (null !== $route = $routeCollection->get($name)) {
                return $route;
            }
        }

        return null;
    }

    /**
     * Removes a route or an array of routes by name from the collection.
     *
     * @param string|array $name The route name or an array of route names
     */
    public function remove($name): void
    {
        foreach ((array)$name as $n) {
            foreach ($this->routeCollections as $routeCollection) {
                if (null !== $routeCollection->get($n)) {
                    $routeCollection->remove($n);
                    continue 2;
                }
            }
        }
    }

    /**
     * Adds a route collection at the end of the current set by appending all
     * routes of the added collection.
     *
     * @param RouteCollection $collection A RouteCollection instance
     */
    public function addCollection(RouteCollection $collection): void
    {
        $this->routeCollections[] = $collection;
    }

    public function addPrefix(string $prefix, array $defaults = [], array $requirements = []): void
    {
        $this->createInternalCollection();

        foreach ($this->routeCollections as $routeCollection) {
            $routeCollection->addPrefix($prefix, $defaults, $requirements);
        }
    }

    public function setHost(?string $pattern, array $defaults = [], array $requirements = []): void
    {
        $this->createInternalCollection();

        foreach ($this->routeCollections as $routeCollection) {
            $routeCollection->setHost($pattern, $defaults, $requirements);
        }
    }

    public function addDefaults(array $defaults): void
    {
        $this->createInternalCollection();

        foreach ($this->routeCollections as $routeCollection) {
            $routeCollection->addDefaults($defaults);
        }
    }

    public function addRequirements(array $requirements): void
    {
        $this->createInternalCollection();

        foreach ($this->routeCollections as $routeCollection) {
            $routeCollection->addRequirements($requirements);
        }
    }

    public function addOptions(array $options): void
    {
        $this->createInternalCollection();

        foreach ($this->routeCollections as $routeCollection) {
            $routeCollection->addOptions($options);
        }
    }

    /**
     * @inheritDoc
     */
    public function setSchemes($schemes): void
    {
        $this->createInternalCollection();

        foreach ($this->routeCollections as $routeCollection) {
            $routeCollection->setSchemes($schemes);
        }
    }

    /**
     * @inheritDoc
     */
    public function setMethods($methods): void
    {
        $this->createInternalCollection();

        foreach ($this->routeCollections as $routeCollection) {
            $routeCollection->setMethods($methods);
        }
    }

    public function getResources(): array
    {
        if (0 === count($this->routeCollections)) {
            return [];
        }

        $resources = array_map(function (RouteCollection $routeCollection) {
            return $routeCollection->getResources();
        }, $this->routeCollections);

        return array_unique(call_user_func_array('array_merge', $resources));
    }

    public function addResource(ResourceInterface $resource): void
    {
        $this->createInternalCollection();
        $this->routeCollection->addResource($resource);
    }

    private function createInternalCollection()
    {
        if (!$this->routeCollection instanceof RouteCollection) {
            $this->routeCollection = new RouteCollection();
            $this->routeCollections[] = $this->routeCollection;
        }
    }
}
