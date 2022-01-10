<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Service\Routing;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class Router
 * @package Ekyna\Bundle\CmsBundle\Service\Routing
 * @author  Étienne Dauvergne <contact@ekyna.com>
 *
 * @see https://github.com/symfony-cmf/Routing/blob/2.1/src/DynamicRouter.php
 */
class DynamicRouter implements RouterInterface, RequestMatcherInterface
{
    protected RouteProvider    $provider;
    protected RequestContext   $context;
    protected ?LoggerInterface $logger;
    protected string           $defaultLocale;

    protected ?RouteCollection       $routes    = null;
    protected ?UrlMatcherInterface   $matcher   = null;
    protected ?UrlGeneratorInterface $generator = null;

    public function __construct(
        RouteProvider    $provider,
        RequestContext   $context,
        ?LoggerInterface $logger,
        string           $defaultLocale
    ) {
        $this->provider = $provider;
        $this->context = $context;
        $this->logger = $logger;
        $this->defaultLocale = $defaultLocale;
    }

    public function setContext(RequestContext $context): void
    {
        $this->context = $context;
    }

    public function getContext(): RequestContext
    {
        return $this->context;
    }

    public function getRouteCollection(): RouteCollection
    {
        if ($this->routes) {
            return $this->routes;
        }

        return $this->routes = $this->provider->getRouteCollection();
    }

    public function match(string $pathinfo): array
    {
        return $this->getMatcher()->match($pathinfo);
    }

    public function matchRequest(Request $request): array
    {
        return $this->getMatcher()->matchRequest($request);
    }

    public function generate(string $name, array $parameters = [], int $referenceType = self::ABSOLUTE_PATH): string
    {
        return $this->getGenerator()->generate($name, $parameters, $referenceType);
    }

    protected function getMatcher(): UrlMatcherInterface
    {
        if (null !== $this->matcher) {
            return $this->matcher;
        }

        return $this->matcher = new UrlMatcher(
            $this->getRouteCollection(),
            $this->context
        );
    }

    protected function getGenerator(): UrlGeneratorInterface
    {
        if (null !== $this->generator) {
            return $this->generator;
        }

        return $this->generator = new UrlGenerator(
            $this->getRouteCollection(),
            $this->context,
            $this->logger,
            $this->defaultLocale
        );
    }
}
