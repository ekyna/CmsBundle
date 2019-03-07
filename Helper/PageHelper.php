<?php

namespace Ekyna\Bundle\CmsBundle\Helper;

use Ekyna\Bundle\CmsBundle\Entity\PageRepository;
use Ekyna\Bundle\CmsBundle\Model\PageInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PageHelper
 * @package Ekyna\Bundle\CmsBundle\Helper
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class PageHelper
{
    private const PAGES_ROUTES_CACHE_KEY = 'cms_pages_routes';

    /**
     * @var PageRepository
     */
    protected $repository;

    /**
     * @var AdapterInterface
     */
    protected $cache;

    /**
     * @var string
     */
    private $homeRoute;

    /**
     * @var array
     */
    private $routes;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var PageInterface
     */
    private $currentPage = false;

    /**
     * @var PageInterface
     */
    private $homePage = false;


    /**
     * Constructor.
     *
     * @param PageRepository   $repository
     * @param AdapterInterface $cache
     * @param string           $homeRoute
     */
    public function __construct(PageRepository $repository, AdapterInterface $cache, string $homeRoute)
    {
        $this->repository = $repository;
        $this->cache = $cache;
        $this->homeRoute = $homeRoute;
    }

    /**
     * Initializes the helper.
     *
     * @param Request $request
     *
     * @return PageInterface|null
     */
    public function init(Request $request): ?PageInterface
    {
        $this->request = $request;

        return $this->getCurrent();
    }

    /**
     * Finds the page by route.
     *
     * @param Request $request
     *
     * @return PageInterface|null
     */
    public function findByRequest(Request $request): ?PageInterface
    {
        if (null !== $route = $request->attributes->get('_route', null)) {
            return $this->findByRoute($route);
        }

        return null;
    }

    /**
     * Finds the page by route.
     *
     * @param string $route
     *
     * @return PageInterface|null
     */
    public function findByRoute(string $route): ?PageInterface
    {
        if (!in_array($route, $this->getCmsRoutes(), true)) {
            return null;
        }

        return $this->repository->findOneByRoute($route);
    }

    /**
     * Returns the current page.
     *
     * @return PageInterface|null
     */
    public function getCurrent(): ?PageInterface
    {
        if (false === $this->currentPage) {
            if (null === $this->request) {
                throw new \RuntimeException('The page helper must be initialized first.');
            }
            $this->currentPage = $this->findByRequest($this->request);
        }

        return $this->currentPage;
    }

    /**
     * Returns the home page.
     *
     * @return PageInterface|null
     */
    public function getHomePage(): ?PageInterface
    {
        if (false === $this->homePage) {
            $this->homePage = $this->findByRoute($this->homeRoute);
        }

        return $this->homePage;
    }

    /**
     * Returns the request.
     *
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * Returns the page repository.
     *
     * @return PageRepository
     */
    public function getPageRepository(): PageRepository
    {
        return $this->repository;
    }

    /**
     * Returns the cms routes list.
     *
     * @return array
     */
    private function getCmsRoutes(): array
    {
        if ($this->routes) {
            return $this->routes;
        }

        $item = $this->cache->getItem(self::PAGES_ROUTES_CACHE_KEY);

        if ($item->isHit()) {
            return $this->routes = $item->get();
        }

        $routes = $this->repository->getPagesRoutes();

        $item->set($routes);
        $this->cache->save($item);

        return $this->routes = $routes;
    }
}
