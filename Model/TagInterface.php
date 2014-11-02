<?php

namespace Ekyna\Bundle\CmsBundle\Model;

/**
 * Interface TagInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
interface TagInterface
{
    /**
     * Returns the id.
     *
     * @return integer
     */
    public function getId();

    /**
     * Sets the name.
     *
     * @param string $name
     * @return TagInterface|$this
     */
    public function setName($name);

    /**
     * Returns the name.
     *
     * @return string
     */
    public function getName();
}
