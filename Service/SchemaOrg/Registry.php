<?php

namespace Ekyna\Bundle\CmsBundle\Service\SchemaOrg;

use Ekyna\Bundle\CmsBundle\Exception\InvalidArgumentException;
use Ekyna\Bundle\CmsBundle\Exception\RuntimeException;

/**
 * Class ProviderRegistry
 * @package Ekyna\Bundle\CmsBundle\Service\SchemaOrg
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class Registry implements RegistryInterface
{
    use BuilderAwareTrait;

    /**
     * @var ProviderInterface[]
     */
    protected $providers = [];

    /**
     * @var string[]
     */
    protected $classes = [];

    /**
     * @var bool
     */
    protected $initialized = false;


    /**
     * @inheritDoc
     */
    public function registerClass($classes)
    {
        if (is_array($classes)) {
            foreach ($classes as $class) {
                $this->addClass($class);
            }
        } elseif(is_string($classes)) {
            $this->addClass($classes);
        } else {
            throw new InvalidArgumentException("Expected string or array of string.");
        }
    }

    /**
     * @inheritDoc
     */
    public function registerProvider(ProviderInterface $provider)
    {
        if ($this->initialized) {
            throw new RuntimeException("You can't register provider as registry as been initialized.");
        }

        $this->providers[] = $provider;
    }

    /**
     * @inheritDoc
     */
    public function getProvider($object)
    {
        $this->initialize();

        if (!is_object($object)) {
            throw new InvalidArgumentException("Expected object.");
        }

        foreach ($this->providers as $provider) {
            if ($provider->supports($object)) {
                return $provider;
            }
        }

        $class = get_class($object);

        throw new InvalidArgumentException("Provider not found for object of class $class");
    }

    /**
     * Adds the given provider class.
     *
     * @param string $class
     */
    protected function addClass(string $class)
    {
        if ($this->initialized) {
            throw new RuntimeException("You can't register provider class as registry as been initialized.");
        }

        if (!class_exists($class)) {
            throw new InvalidArgumentException("Class $class does not exist.");
        }

        if (!is_subclass_of($class, ProviderInterface::class)) {
            throw new InvalidArgumentException("Class $class must implement " . ProviderInterface::class);
        }

        $this->classes[] = $class;
    }

    /**
     * Initializes the registry.
     */
    protected function initialize()
    {
        if ($this->initialized) {
            return;
        }

        foreach ($this->classes as $class) {
            $this->providers[] = new $class;
        }

        foreach ($this->providers as $provider) {
            if ($provider instanceof BuilderAwareInterface) {
                $provider->setSchemaBuilder($this->schemaBuilder);
            }
        }

        $this->initialized = true;
    }
}
