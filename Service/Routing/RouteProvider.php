<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Service\Routing;

use Ekyna\Bundle\CmsBundle\Repository\PageRepositoryInterface;
use Psr\Cache\CacheItemPoolInterface;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\TemplateController;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

use function sprintf;

/**
 * Class RouteProvider
 * @package Ekyna\Bundle\CmsBundle\Service\Routing
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class RouteProvider
{
    public const CACHE_KEY = 'ekyna_cms.router_data';

    protected PageRepositoryInterface $pageRepository;
    protected CacheItemPoolInterface  $cache;
    protected array                   $config;
    protected array                   $locales;

    public function __construct(
        PageRepositoryInterface $pageRepository,
        CacheItemPoolInterface  $cache,
        array                   $config,
        array                   $locales
    ) {
        $this->pageRepository = $pageRepository;
        $this->cache = $cache;
        $this->config = $config;
        $this->locales = $locales;
    }

    public function getRouteCollection(): RouteCollection
    {
        $collection = new RouteCollection();

        $data = $this->loadData();

        if (empty($data)) {
            return $collection;
        }

        foreach ($data as $datum) {
            $name = $datum['route'] . '.' . $datum['locale'];

            $route = $this->transformDataToRoute($datum);

            $collection->add($name, $route);
        }

        return $collection;
    }

    protected function loadData(): array
    {
        $item = $this->cache->getItem(self::CACHE_KEY);

        if ($item->isHit()) {
            return $item->get();
        }

        $data = $this
            ->pageRepository
            ->getDynamicRouterData();

        $item->set($data);

        $this->cache->save($item);

        return $data;
    }

    /**
     * Transforms the data into a route.
     */
    protected function transformDataToRoute(array $data): Route
    {
        // TODO Host (from resource bundle)
        $route = new Route($data['path']);
        $route
            ->setDefaults([
                '_locale'          => $data['locale'],
                '_canonical_route' => $data['route'],
            ])
            ->setMethods(['GET']);

        if (!empty($template = $data['template'])) {
            $route->setDefaults([
                '_controller' => TemplateController::class,
                'template'   => $template,
            ]);

            return $route;
        }

        $controller = $data['controller'];

        if (!isset($this->config['controllers'][$controller])) {
            throw new RuntimeException(sprintf('Undefined controller "%s".', $controller));
        }

        $route->setDefault('_controller', $this->config['controllers'][$controller]['value']);

        return $route;
    }
}
