<?php

namespace Ekyna\Bundle\CmsBundle\Model;

use Ekyna\Bundle\AdminBundle\Model\TranslationInterface;

/**
 * Interface PageTranslationInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
interface PageTranslationInterface extends TranslationInterface
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

    /**
     * Set path
     *
     * @param string $path
     * @return PageTranslationInterface|$this
     */
    public function setPath($path);

    /**
     * Get path
     *
     * @return string
     */
    public function getPath();
}
