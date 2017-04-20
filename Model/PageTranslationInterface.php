<?php

declare(strict_types=1);

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
     * @param string|null $title
     *
     * @return PageTranslationInterface|$this
     */
    public function setTitle(string $title = null): PageTranslationInterface;

    /**
     * Returns the title.
     *
     * @return string|null
     */
    public function getTitle(): ?string;

    /**
     * Sets the breadcrumb.
     *
     * @param string|null $breadcrumb
     *
     * @return PageTranslationInterface|$this
     */
    public function setBreadcrumb(string $breadcrumb = null): PageTranslationInterface;

    /**
     * Returns the breadcrumb.
     *
     * @return string|null
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
     * @return string|null
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
     * @return string|null
     */
    public function getPath(): ?string;
}
