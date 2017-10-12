<?php

namespace Ekyna\Bundle\CmsBundle\SlideShow;

use Ekyna\Bundle\CmsBundle\SlideShow\Type\TypeInterface;

/**
 * Class SlideTypeRegistry
 * @package Ekyna\Bundle\CmsBundle\SlideShow
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class TypeRegistry implements TypeRegistryInterface
{
    /**
     * @var TypeInterface[]
     */
    private $types;


    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->types = [];
    }

    /**
     * Registers the slide type.
     *
     * @param TypeInterface $type
     *
     * @return $this
     */
    public function register(TypeInterface $type)
    {
        if (isset($this->types[$name = $type->getName()])) {
            throw new \RuntimeException("Slide type '$name' is already registered.");
        }

        $this->types[$name] = $type;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function get($name)
    {
        if (!isset($this->types)) {
            throw new \InvalidArgumentException("No slide type registered for name '$name'.");
        }

        return $this->types[$name];
    }

    /**
     * @inheritdoc
     */
    public function all()
    {
        return $this->types;
    }
}
