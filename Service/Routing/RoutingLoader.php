<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Service\Routing;

use RuntimeException;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

use function preg_quote;

/**
 * Class RoutingLoader
 * @package Ekyna\Bundle\CmsBundle\Service\Routing
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class RoutingLoader extends Loader
{
    private array $config;
    private bool  $loaded = false;

    public function __construct(array $config, string $env = null)
    {
        parent::__construct($env);

        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function load($resource, $type = null)
    {
        if (true === $this->loaded) {
            throw new RuntimeException('Do not add the "cms" routes loader twice.');
        }

        $this->loaded = true;

        $collection = new RouteCollection();

        $config = $this->config['wide_search'];
        if ($config['enabled']) {
            $route = new Route('/search');
            $route
                ->setDefault('_controller', $config['controller'])
                ->setOptions([
                    'expose' => true,
                    '_cms'   => [
                        'name'     => 'Rechercher',
                        'advanced' => true,
                        'position' => 998,
                        'seo'      => [
                            'index'  => false,
                            'follow' => false,
                        ],
                    ],
                ])
                ->setMethods(['GET', 'POST']);

            $paths = [
                'en' => '/search',
                'fr' => '/rechercher',
                'es' => '/buscar',
            ];
            foreach ($paths as $locale => $path) {
                /** @see \Symfony\Component\Routing\Loader\Configurator\Traits\PrefixTrait::addPrefix */
                $localizedRoute = clone $route;
                $localizedRoute->setDefault('_locale', $locale);
                $localizedRoute->setRequirement('_locale', preg_quote($locale));
                $localizedRoute->setDefault('_canonical_route', 'wide_search');
                $localizedRoute->setPath($path);
                $collection->add('wide_search.' . $locale, $localizedRoute);
            }
        }

        $config = $this->config['cookie_consent'];
        if ($config['enabled']) {
            $route = new Route('/cookies-privacy-policy');
            $route
                ->setDefault('_controller', $config['controller'])
                ->setOptions([
                    'expose' => true,
                    '_cms'   => [
                        'name'     => 'Utilisation des cookies',
                        'advanced' => true,
                        'position' => 999,
                        'seo'      => [
                            'index'  => false,
                            'follow' => false,
                        ],
                    ],
                ])
                ->setMethods(['GET']);

            $paths = [
                'en' => '/cookies-privacy-policy',
                'fr' => '/politique-de-confidentialite-cookies',
                'es' => '/politica-de-privacidad-de-cookies',
            ];
            foreach ($paths as $locale => $path) {
                /** @see \Symfony\Component\Routing\Loader\Configurator\Traits\PrefixTrait::addPrefix */
                $localizedRoute = clone $route;
                $localizedRoute->setDefault('_locale', $locale);
                $localizedRoute->setRequirement('_locale', preg_quote($locale));
                $localizedRoute->setDefault('_canonical_route', 'cookies_privacy_policy');
                $localizedRoute->setPath($path);
                $collection->add('cookies_privacy_policy.' . $locale, $localizedRoute);
            }
        }

        return $collection;
    }

    /**
     * @inheritDoc
     */
    public function supports($resource, $type = null): bool
    {
        return 'cms' === $type;
    }
}
