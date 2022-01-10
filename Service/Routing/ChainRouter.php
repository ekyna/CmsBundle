<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Service\Routing;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\CacheWarmer\WarmableInterface;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RequestContextAwareInterface;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

use function array_push;
use function call_user_func_array;
use function get_class;
use function krsort;
use function sprintf;

/**
 * Class ChainRouter
 * @package Ekyna\Bundle\CmsBundle\Service\Routing
 * @author  Étienne Dauvergne <contact@ekyna.com>
 *
 * @see https://github.com/symfony-cmf/Routing/blob/2.1/src/ChainRouter.php
 */
final class ChainRouter implements RouterInterface, RequestMatcherInterface, WarmableInterface
{
    private RequestContext   $context;
    private ?LoggerInterface $logger;

    /** @var array<int, array<int, RouterInterface>> */
    private array $registeredRouters = [];
    /** @var array<int, RouterInterface>|null */
    private ?array $sortedRouters = null;

    private ?RouteCollection $routes = null;

    public function __construct(RequestContext $context, ?LoggerInterface $logger)
    {
        $this->context = $context;
        $this->logger = $logger;
    }

    public function setContext(RequestContext $context)
    {
        $this->context = $context;

        foreach ($this->getSortedRouters() as $router) {
            if ($router instanceof RequestContextAwareInterface) {
                $router->setContext($context);
            }
        }
    }

    public function getContext(): RequestContext
    {
        return $this->context;
    }

    public function registerRouter(RouterInterface $router, int $priority = 0): void
    {
        if (!isset($this->registeredRouters[$priority])) {
            $this->registeredRouters[$priority] = [];
        }

        $this->registeredRouters[$priority][] = $router;
        $this->sortedRouters = null;
    }

    private function getSortedRouters(): array
    {
        if (null !== $this->sortedRouters) {
            return $this->sortedRouters;
        }

        if (empty($this->registeredRouters)) {
            return $this->sortedRouters = [];
        }

        krsort($this->registeredRouters);

        return $this->sortedRouters = call_user_func_array('array_merge', $this->registeredRouters);
    }

    public function getRouteCollection(): RouteCollection
    {
        if (null !== $this->routes) {
            return $this->routes;
        }

        $this->routes = new ChainRouteCollection();
        foreach ($this->getSortedRouters() as $router) {
            $this->routes->addCollection($router->getRouteCollection());
        }

        return $this->routes;
    }

    public function match(string $pathinfo): array
    {
        return $this->doMatch($pathinfo);
    }

    public function matchRequest(Request $request): array
    {
        return $this->doMatch($request->getPathInfo(), $request);
    }

    private function doMatch(string $pathinfo, Request $request = null): array
    {
        $methodNotAllowed = null;

        $requestForMatching = $request;
        foreach ($this->getSortedRouters() as $router) {
            try {
                // the request/url match logic is the same as in
                // Symfony/Component/HttpKernel/EventListener/RouterListener.php
                // matching requests is more powerful than matching URLs only, so try that first
                if ($router instanceof RequestMatcherInterface) {
                    if (null === $requestForMatching) {
                        $requestForMatching = $this->rebuildRequest($pathinfo);
                    }

                    return $router->matchRequest($requestForMatching);
                }

                return $router->match($pathinfo);
            } catch (ResourceNotFoundException $e) {
                if ($this->logger) {
                    $message = sprintf(
                        'Router %s was not able to match, message "%s"',
                        get_class($router),
                        $e->getMessage()
                    );
                    $this->logger->debug($message);
                }
            } catch (MethodNotAllowedException $e) {
                if ($this->logger) {
                    $message = sprintf(
                        'Router %s throws MethodNotAllowedException with message "%s"',
                        get_class($router),
                        $e->getMessage()
                    );
                    $this->logger->debug($message);
                }
                $methodNotAllowed = $e;
            }
        }

        $info = $request
            ? "this request\n$request"
            : "url '$pathinfo'";

        throw $methodNotAllowed ?: new ResourceNotFoundException("None of the routers in the chain matched $info");
    }

    private function rebuildRequest(string $pathinfo): Request
    {
        $context = $this->getContext();

        $uri = $pathinfo;

        $server = [];
        if ($context->getBaseUrl()) {
            $uri = $context->getBaseUrl().$pathinfo;
            $server['SCRIPT_FILENAME'] = $context->getBaseUrl();
            $server['PHP_SELF'] = $context->getBaseUrl();
        }
        $host = $context->getHost() ?: 'localhost';
        if ('https' === $context->getScheme() && 443 !== $context->getHttpsPort()) {
            $host .= ':'.$context->getHttpsPort();
        }
        if ('http' === $context->getScheme() && 80 !== $context->getHttpPort()) {
            $host .= ':'.$context->getHttpPort();
        }
        $uri = $context->getScheme().'://'.$host.$uri.'?'.$context->getQueryString();

        return Request::create($uri, $context->getMethod(), $context->getParameters(), [], [], $server);
    }

    public function generate(string $name, array $parameters = [], int $referenceType = self::ABSOLUTE_PATH): string
    {
        $debug = [];

        foreach ($this->getSortedRouters() as $router) {
            try {
                return $router->generate($name, $parameters, $referenceType);
            } catch (RouteNotFoundException $e) {
                $debug[] = 'Router ' . get_class($router) . ' was unable to generate route. ' . $e->getMessage();
            }
        }

        if ($debug && $this->logger) {
            foreach ($debug as $message) {
                $this->logger->debug($message);
            }
        }

        throw new RouteNotFoundException(sprintf('None of the chained routers were able to generate route: %s', $name));
    }

    public function warmUp(string $cacheDir): array
    {
        $result = [];

        foreach ($this->getSortedRouters() as $router) {
            if ($router instanceof WarmableInterface) {
                array_push($result, ...$router->warmUp($cacheDir));
            }
        }

        return $result;
    }
}
