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
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        if (true === $this->loaded) {
            throw new \RuntimeException('Do not add the "cms" routes loader twice.');
        }

        $collection = new RouteCollection();

        if ($this->config['enable']) {
            $cookieInfos = new Route('/cookies-informations');
            $cookieInfos->setDefaults(array(
                '_controller' => $this->config['controller'],
            ));
            $cookieInfos->setOptions(array(
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
            $collection->add('cookies_informations', $cookieInfos);
        }

        $this->loaded = true;

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return 'cms' === $type;
    }
}
