<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Service\Helper;

use Ekyna\Bundle\CmsBundle\Model\PageInterface;
use Ekyna\Bundle\CmsBundle\Repository\PageRepositoryInterface;
use Psr\Cache\CacheItemPoolInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;

use function preg_match;

/**
 * Class PageHelper
 * @package Ekyna\Bundle\CmsBundle\Service\Helper
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class PageHelper
{
    public const CACHE_KEY = 'ekyna_cms.routes_names';

    protected PageRepositoryInterface $repository;
    protected CacheItemPoolInterface  $cache;
    private string                    $homeRoute;

    private ?array   $routes  = null;
    private ?Request $request = null;

    private ?PageInterface $currentPage;

    private bool           $homePageLoaded = false;
    private ?PageInterface $homePage;

    public function __construct(PageRepositoryInterface $repository, CacheItemPoolInterface $cache, string $homeRoute)
    {
        $this->repository = $repository;
        $this->cache = $cache;
        $this->homeRoute = $homeRoute;
    }

    /**
     * Initializes the helper.
     */
    public function init(Request $request): ?PageInterface
    {
        if (null !== $this->request) {
            throw new RuntimeException('The page helper has already been initialized.');
        }

        $this->request = $request;

        return $this->currentPage = $this->findByRequest($this->request);
    }

    /**
     * Returns whether the helper has been initialized.
     *
     * @return bool
     */
    public function isInitialized(): bool
    {
        return null !== $this->request;
    }

    /**
     * Finds the page by route.
     */
    public function findByRequest(Request $request): ?PageInterface
    {
        if (null !== $route = $request->attributes->get('_route')) {
            return $this->findByRoute($route);
        }

        return null;
    }

    /**
     * Finds the page by route.
     */
    public function findByRoute(string $route): ?PageInterface
    {
        if (preg_match('~^([a-zA-Z0-9_-]+)\.([a-z]{2})$~', $route, $matches)) {
            $route = $matches[1];
        }

        if (!in_array($route, $this->getCmsRoutes(), true)) {
            return null;
        }

        return $this->repository->findOneByRoute($route, true);
    }

    /**
     * Returns the current page.
     */
    public function getCurrent(): ?PageInterface
    {
        return $this->currentPage;
    }

    /**
     * Returns the home page.
     */
    public function getHomePage(): ?PageInterface
    {
        if ($this->homePageLoaded) {
            return $this->homePage;
        }

        return $this->homePage = $this->findByRoute($this->homeRoute);
    }

    /**
     * Returns the request.
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * Returns the page repository.
     */
    public function getPageRepository(): PageRepositoryInterface
    {
        return $this->repository;
    }

    /**
     * Returns the cms routes list.
     */
    private function getCmsRoutes(): array
    {
        if ($this->routes) {
            return $this->routes;
        }

        $item = $this->cache->getItem(self::CACHE_KEY);

        if ($item->isHit()) {
            return $this->routes = $item->get();
        }

        $routes = $this->repository->getPagesRoutesNames();

        $item->set($routes);
        $this->cache->save($item);

        return $this->routes = $routes;
    }
}
