<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\SlideShow;

use Ekyna\Bundle\CmsBundle\SlideShow\Type\TypeInterface;
use InvalidArgumentException;
use RuntimeException;

/**
 * Class SlideTypeRegistry
 * @package Ekyna\Bundle\CmsBundle\SlideShow
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class TypeRegistry implements TypeRegistryInterface
{
    /** @var TypeInterface[] */
    private array $types;


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
    public function register(TypeInterface $type): TypeRegistryInterface
    {
        if (isset($this->types[$name = $type->getName()])) {
            throw new RuntimeException("Slide type '$name' is already registered.");
        }

        $this->types[$name] = $type;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function get(string $name): TypeInterface
    {
        if (!isset($this->types)) {
            throw new InvalidArgumentException("No slide type registered for name '$name'.");
        }

        return $this->types[$name];
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        return $this->types;
    }
}
