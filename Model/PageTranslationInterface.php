<?php

namespace Ekyna\Bundle\CmsBundle\Model;

/**
 * Interface PageTranslationInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
interface PageTranslationInterface
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
     * @return PageTranslationInterface|$this
     */
    public function setTitle($title);

    /**
     * Returns the title.
     *
     * @return string
     */
    public function getTitle();

    /**
     * Sets the html.
     *
     * @param string $html
     * @return PageTranslationInterface|$this
     */
    public function setHtml($html);

    /**
     * Returns the html.
     *
     * @return string
     */
    public function getHtml();
}
