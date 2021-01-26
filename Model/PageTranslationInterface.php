<?php

namespace Ekyna\Bundle\CmsBundle\Model;

use Ekyna\Component\Resource\Model\TranslationInterface;

/**
 * Interface PageTranslationInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
interface PageTranslationInterface extends TranslationInterface
{
    /**
     * Sets the title.
     *
     * @param string $title
     *
     * @return PageTranslationInterface|$this
     */
    public function setTitle(string $title): PageTranslationInterface;

    /**
     * Returns the title.
     *
     * @return string
     */
    public function getTitle(): ?string;

    /**
     * Sets the breadcrumb.
     *
     * @param string $breadcrumb
     *
     * @return PageTranslationInterface|$this
     */
    public function setBreadcrumb(string $breadcrumb): PageTranslationInterface;

    /**
     * Returns the breadcrumb.
     *
     * @return string
     */
    public function getBreadcrumb(): ?string;

    /**
     * Sets the html.
     *
     * @param string|null $html
     *
     * @return PageTranslationInterface|$this
     */
    public function setHtml(string $html = null): PageTranslationInterface;

    /**
     * Returns the html.
     *
     * @return string
     */
    public function getHtml(): ?string;

    /**
     * Set path
     *
     * @param string|null $path
     *
     * @return PageTranslationInterface|$this
     */
    public function setPath(string $path = null): PageTranslationInterface;

    /**
     * Get path
     *
     * @return string
     */
    public function getPath(): ?string;
}
