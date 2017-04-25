<?php

namespace Ekyna\Bundle\CmsBundle\Model;

use Ekyna\Component\Resource\Model\ResourceInterface;

/**
 * Interface TagInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
interface TagInterface extends ResourceInterface
{
    /**
     * Returns the name.
     *
     * @return string
     */
    public function getName();

    /**
     * Sets the name.
     *
     * @param string $name
     *
     * @return $this|TagInterface
     */
    public function setName($name);

    /**
     * Returns the icon.
     *
     * @return string
     */
    public function getIcon();

    /**
     * Sets the icon.
     *
     * @param string $icon
     *
     * @return $this|TagInterface
     */
    public function setIcon($icon);

    /**
     * Returns the theme.
     *
     * @return string
     */
    public function getTheme();

    /**
     * Sets the theme.
     *
     * @param string $theme
     *
     * @return $this|TagInterface
     */
    public function setTheme($theme);
}
