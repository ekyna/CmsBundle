<?php

namespace Ekyna\Bundle\CmsBundle\Helper;

use Ekyna\Bundle\CmsBundle\Entity\PageRepository;
use Ekyna\Bundle\CmsBundle\Model\PageInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class PageHelper
 * @package Ekyna\Bundle\CmsBundle\Helper
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class PageHelper
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var PageRepository
     */
    private $repository;

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
     * @param RequestStack   $requestStack
     * @param PageRepository $repository
     * @param string         $homeRoute
     */
    public function __construct(RequestStack $requestStack, PageRepository $repository, $homeRoute)
    {
        $this->requestStack = $requestStack;
        $this->repository   = $repository;
        $this->homeRoute    = $homeRoute;
    }

    /**
     * Returns the current page.
     *
     * @return PageInterface|null
     */
    public function getCurrent()
    {
        if (false === $this->currentPage) {
            $this->currentPage = null;
            if (null !== $request = $this->requestStack->getCurrentRequest()) {
                $this->currentPage = $this->repository->findOneByRequest($request);
            }
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
            $this->homePage = $this->repository->findOneByRoute($this->homeRoute);
        }
        return $this->homePage;
    }
}
