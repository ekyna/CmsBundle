<?php

namespace Ekyna\Bundle\CmsBundle\Model;

/**
 * Interface MenuTranslationInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
interface MenuTranslationInterface
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
}
