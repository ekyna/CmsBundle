<?php

declare(strict_types=1);

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
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * Sets the name.
     *
     * @param string|null $name
     *
     * @return $this|TagInterface
     */
    public function setName(string $name = null): TagInterface;

    /**
     * Returns the theme.
     *
     * @return string
     */
    public function getTheme(): string;

    /**
     * Sets the theme.
     *
     * @param string $theme
     *
     * @return $this|TagInterface
     */
    public function setTheme(string $theme): TagInterface;

    /**
     * Returns the icon.
     *
     * @return string|null
     */
    public function getIcon(): ?string;

    /**
     * Sets the icon.
     *
     * @param string|null $icon
     *
     * @return $this|TagInterface
     */
    public function setIcon(string $icon = null): TagInterface;
}
