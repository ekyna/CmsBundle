<?php

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
     * @return $this
     */
    public function register(TypeInterface $type);

    /**
     * Returns the slide type by its name.
     *
     * @param string $name
     *
     * @return TypeInterface
     */
    public function get($name);

    /**
     * Returns the register types.
     *
     * @return TypeInterface[]
     */
    public function all();
}
