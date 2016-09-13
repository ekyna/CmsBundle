<?php

namespace Ekyna\Bundle\CmsBundle\Model;

use Ekyna\Component\Resource\Model\TranslationInterface;

/**
 * Interface MenuTranslationInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
interface MenuTranslationInterface extends TranslationInterface
{
    /**
     * Sets the title.
     *
     * @param string $title
     * @return MenuTranslationInterface|$this
     */
    public function setTitle($title);

    /**
     * Returns the title.
     *
     * @return string
     */
    public function getTitle();

    /**
     * Sets the path.
     *
     * @param string $path
     * @return MenuTranslationInterface|$this
     */
    public function setPath($path);

    /**
     * Returns the path.
     *
     * @return string
     */
    public function getPath();
}
