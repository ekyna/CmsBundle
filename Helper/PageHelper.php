<?php

namespace Ekyna\Bundle\CmsBundle\Helper;

use Ekyna\Bundle\CmsBundle\Entity\PageRepository;
use Ekyna\Bundle\CmsBundle\Model\PageInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PageHelper
 * @package Ekyna\Bundle\CmsBundle\Helper
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class PageHelper
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var PageRepository
     */
    private $pageRepository;

    /**
     * @var string
     */
    private $homeRoute;

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
     * @param PageRepository           $pageRepository
     * @param string                   $homeRoute
     */
    public function __construct(
        PageRepository           $pageRepository,
        $homeRoute
    ) {
        $this->pageRepository  = $pageRepository;
        $this->homeRoute       = $homeRoute;
    }

    /**
     * Initializes the helper.
     *
     * @param Request $request
     * @return PageInterface|null
     */
    public function init(Request $request)
    {
        $this->request = $request;

        return $this->getCurrent();
    }

    /**
     * Finds the page by route.
     *
     * @param Request $request
     * @return PageInterface|null
     */
    public function findByRequest(Request $request)
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
     * @return PageInterface|null
     */
    public function findByRoute($route)
    {
        return $this->pageRepository->findOneByRoute($route);
    }

    /**
     * Returns the current page.
     *
     * @return PageInterface|null
     */
    public function getCurrent()
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
    public function getHomePage()
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
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Returns the page repository.
     *
     * @return PageRepository
     */
    public function getPageRepository()
    {
        return $this->pageRepository;
    }
}
