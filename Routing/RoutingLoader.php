<?php

namespace Ekyna\Bundle\CmsBundle\Routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class RoutingLoader
 * @package Ekyna\Bundle\CmsBundle\Routing
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class RoutingLoader extends Loader
{
    /**
     * @var bool
     */
    private $loaded = false;

    /**
     * @var array
     */
    private $config;

    /**
     * Constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function load($resource, $type = null)
    {
        if (true === $this->loaded) {
            throw new \RuntimeException('Do not add the "cms" routes loader twice.');
        }

        $collection = new RouteCollection();

        $config = $this->config['cookie_consent'];
        if ($config['enabled']) {
            $cookiesPolicy = new Route('/cookies-privacy-policy');
            $cookiesPolicy->setDefaults(array(
                '_controller' => $config['controller'],
            ));
            $cookiesPolicy->setOptions(array(
                'expose' => true,
                '_cms' => array(
                    'name' => 'Utilisation des cookies',
                    'advanced' => true,
                    'position' => 999,
                    'seo' => array(
                        'index'  => false,
                        'follow' => false,
                    ),
                ),
            ));
            $cookiesPolicy->setMethods(array('GET'));
            $collection->add('cookies_privacy_policy', $cookiesPolicy);
        }

        $config = $this->config['wide_search'];
        if ($config['enabled']) {
            $wideSearch = new Route('/search');
            $wideSearch->setDefaults(array(
                '_controller' => $config['controller'],
            ));
            $wideSearch->setOptions(array(
                'expose' => true,
                '_cms' => array(
                    'name' => 'Rechercher',
                    'advanced' => true,
                    'position' => 998,
                    'seo' => array(
                        'index'  => false,
                        'follow' => false,
                    ),
                ),
            ));
            $wideSearch->setMethods(array('GET', 'POST'));
            $collection->add('wide_search', $wideSearch);
        }

        $this->loaded = true;

        return $collection;
    }

    /**
     * @inheritdoc
     */
    public function supports($resource, $type = null)
    {
        return 'cms' === $type;
    }
}
