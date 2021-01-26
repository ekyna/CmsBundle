<?php

namespace Ekyna\Bundle\CmsBundle\Model;

use Ekyna\Component\Resource\Model\TranslationInterface;

/**
 * Interface MenuTranslationInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
interface MenuTranslationInterface extends TranslationInterface
{
    /**
     * Sets the title.
     *
     * @param string $title
     *
     * @return MenuTranslationInterface|$this
     */
    public function setTitle(string $title): MenuTranslationInterface;

    /**
     * Returns the title.
     *
     * @return string
     */
    public function getTitle(): ?string;

    /**
     * Sets the path.
     *
     * @param string|null $path
     *
     * @return MenuTranslationInterface|$this
     */
    public function setPath(string $path = null): MenuTranslationInterface;

    /**
     * Returns the path.
     *
     * @return string
     */
    public function getPath(): ?string;
}
