<?php

namespace Ekyna\Bundle\CmsBundle\Search\Wide;

/**
 * Class Search
 * @package Ekyna\Bundle\CmsBundle\Search\Wide
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class Search
{
    /**
     * @var array|ProviderInterface[]
     */
    private $providers = [];


    /**
     * Constructor.
     *
     * @param array|ProviderInterface[] $providers
     */
    public function __construct(array $providers)
    {
        foreach ($providers as $provider) {
            $this->addProvider($provider);
        }
    }

    /**
     * Adds the provider.
     *
     * @param ProviderInterface $provider
     */
    public function addProvider(ProviderInterface $provider)
    {
        if (array_key_exists($name = $provider->getName(), $this->providers)) {
            throw new \InvalidArgumentException("Wide search provider '{$name}' is already registered.");
        }

        $this->providers[$name] = $provider;
    }

    /**
     * Performs a wide site search.
     *
     * @param string $expression
     *
     * @return array|Result[]
     */
    public function search(string $expression = null): array
    {
        if (empty($expression)) {
            return [];
        }

        $results = [];

        foreach ($this->providers as $provider) {
            $results = array_merge($results, $provider->search($expression));
        }

        usort($results, function (Result $a, Result $b) {
            if ($a->getScore() == $b->getScore()) {
                return 0;
            }
            return $a->getScore() > $b->getScore() ? -1 : 1;
        });

        return $results;
    }
}
