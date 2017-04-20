<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Service;

use Ekyna\Bundle\ResourceBundle\Helper\ResourceHelper;
use Ekyna\Component\Resource\Manager\ManagerFactoryInterface;
use Ekyna\Component\Resource\Model\ResourceInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use function get_class;

/**
 * Class LocaleSwitcher
 * @package Ekyna\Bundle\CmsBundle\Service
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class LocaleSwitcher
{
    private ResourceHelper          $resourceHelper;
    private ManagerFactoryInterface $managerFactory;
    private UrlGeneratorInterface   $urlGenerator;
    private RequestStack            $requestStack;
    private array                   $locales;

    private ?ResourceInterface $resource = null;
    private ?array             $urls     = null;

    public function __construct(
        ResourceHelper          $resourceHelper,
        ManagerFactoryInterface $managerFactory,
        UrlGeneratorInterface   $urlGenerator,
        RequestStack            $requestStack,
        array                   $locales
    ) {
        $this->resourceHelper = $resourceHelper;
        $this->managerFactory = $managerFactory;
        $this->urlGenerator = $urlGenerator;
        $this->requestStack = $requestStack;
        $this->locales = $locales;
    }

    /**
     * Sets the current resource.
     */
    public function setResource(?ResourceInterface $resource): LocaleSwitcher
    {
        $this->resource = $resource;

        // Clear urls
        $this->urls = null;

        return $this;
    }

    /**
     * Returns whether the resource is set.
     */
    public function hasResource(): bool
    {
        return null !== $this->resource;
    }

    public function getUrls(array $locales = []): ?array
    {
        if (!is_null($this->urls)) {
            return $this->urls;
        }

        $locales = $locales ?: $this->locales;
        $this->urls = [];

        // By resource
        if ($this->resource) {
            $this->managerFactory->getManager(get_class($this->resource))->refresh($this->resource);

            foreach ($locales as $locale) {
                $this->urls[$locale] = $this->resourceHelper->generatePublicUrl($this->resource, false, $locale);
            }
        } // By route and parameters
        elseif ($request = $this->requestStack->getMainRequest()) {
            $route = $request->attributes->get('_route');
            $parameters = $request->attributes->get('_route_params');

            foreach ($locales as $locale) {
                $params = array_replace($parameters, ['_locale' => $locale]);

                $this->urls[$locale] = $this
                    ->urlGenerator
                    ->generate($route, $params, UrlGeneratorInterface::ABSOLUTE_URL);
            }
        }

        return $this->urls;
    }

    public function setUrls(array $urls = null): LocaleSwitcher
    {
        $this->urls = $urls;

        return $this;
    }
}
