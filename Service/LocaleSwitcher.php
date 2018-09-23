<?php

namespace Ekyna\Bundle\CmsBundle\Service;

use Ekyna\Bundle\AdminBundle\Helper\ResourceHelper;
use Ekyna\Component\Resource\Model\ResourceInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class LocaleSwitcher
 * @package Ekyna\Bundle\CmsBundle\Service
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class LocaleSwitcher
{
    /**
     * @var ResourceHelper
     */
    private $resourceHelper;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var ResourceInterface
     */
    private $resource;

    /**
     * @var array
     */
    private $urls;


    /**
     * Constructor.
     *
     * @param ResourceHelper        $resourceHelper
     * @param UrlGeneratorInterface $urlGenerator
     * @param RequestStack          $requestStack
     */
    public function __construct(
        ResourceHelper $resourceHelper,
        UrlGeneratorInterface $urlGenerator,
        RequestStack $requestStack
    ) {
        $this->resourceHelper = $resourceHelper;
        $this->urlGenerator = $urlGenerator;
        $this->requestStack = $requestStack;
    }

    /**
     * Sets the current resource.
     *
     * @param ResourceInterface $resource
     *
     * @return LocaleSwitcher
     */
    public function setResource(ResourceInterface $resource = null)
    {
        $this->resource = $resource;

        // Clear urls
        $this->urls = null;

        return $this;
    }

    /**
     * Returns whether the resource is set.
     *
     * @return bool
     */
    public function hasResource()
    {
        return !!$this->resource;
    }

    /**
     * Sets the urls.
     *
     * @param array $urls
     *
     * @return LocaleSwitcher
     */
    public function setUrls(array $urls = null)
    {
        $this->urls = $urls;

        return $this;
    }

    /**
     * Returns the urls.
     *
     * @param array $locales
     *
     * @return array
     */
    public function getUrls(array $locales)
    {
        if (!is_null($this->urls)) {
            return $this->urls;
        }

        $this->urls = [];

        // By resource
        if ($this->resource) {
            $this->resourceHelper->getEntityManager()->refresh($this->resource);

            foreach ($locales as $locale) {
                $this->urls[$locale] = $this->resourceHelper->generatePublicUrl($this->resource, false, $locale);
            }
        }

        // By route and parameters
        elseif ($request = $this->requestStack->getMasterRequest()) {
            $route = $request->attributes->get('_route');
            $parameters = $request->attributes->get('_route_params');

            foreach ($locales as $locale) {
                $params = array_replace($parameters, ['_locale' => $locale]);
                $this->urls[$locale] = $this->urlGenerator->generate($route, $params, UrlGeneratorInterface::ABSOLUTE_PATH);
            }
        }

        return $this->urls;
    }
}