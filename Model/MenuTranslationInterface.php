<?php

namespace Ekyna\Bundle\CmsBundle\Model;

use Ekyna\Bundle\AdminBundle\Model\TranslationInterface;

/**
 * Interface MenuTranslationInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
interface MenuTranslationInterface extends TranslationInterface
{
    /**
     * Returns the id.
     *
     * @return int
     */
    public function getId();

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
