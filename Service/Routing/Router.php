<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Service\Routing;

use Symfony\Cmf\Component\Routing\DynamicRouter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * Class Router
 * @package Ekyna\Bundle\CmsBundle\Service\Routing
 * @author  Étienne Dauvergne <contact@ekyna.com>
 * @see     http://symfony.com/doc/master/cmf/components/routing/dynamic.html
 * @see     https://github.com/symfony-cmf/routing-bundle/blob/master/src/Routing/DynamicRouter.php
 */
class Router extends DynamicRouter
{
    private RequestStack $requestStack;


    /**
     * Sets the request stack.
     */
    public function setRequestStack(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * Get the current request from the request stack.
     *
     * @return Request
     *
     * @throws ResourceNotFoundException
     */
    public function getRequest(): Request
    {
        $currentRequest = $this->requestStack->getCurrentRequest();

        if (!$currentRequest) {
            throw new ResourceNotFoundException('There is no request in the request stack');
        }

        return $currentRequest;
    }
}
