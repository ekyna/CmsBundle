<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\SlideShow;

use Ekyna\Bundle\CmsBundle\SlideShow\Type\TypeInterface;

/**
 * Interface SlideTypeRegistryInterface
 * @package Ekyna\Bundle\CmsBundle\SlideShow
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
interface TypeRegistryInterface
{
    /**
     * Registers the slide type.
     *
     * @param TypeInterface $type
     *
     * @return TypeRegistryInterface
     */
    public function register(TypeInterface $type): TypeRegistryInterface;

    /**
     * Returns the slide type by its name.
     *
     * @param string $name
     *
     * @return TypeInterface
     */
    public function get(string $name): TypeInterface;

    /**
     * Returns the register types.
     *
     * @return TypeInterface[]
     */
    public function all(): array;
}
