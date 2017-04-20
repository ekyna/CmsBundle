<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Model;

use Ekyna\Component\Resource\Model\TranslationInterface;

/**
 * Interface SeoTranslationInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
interface SeoTranslationInterface extends TranslationInterface
{
    /**
     * Sets the title.
     *
     * @param string|null $title
     *
     * @return SeoTranslationInterface|$this
     */
    public function setTitle(string $title = null): SeoTranslationInterface;

    /**
     * Returns the title.
     *
     * @return string|null
     */
    public function getTitle(): ?string;

    /**
     * Sets the description.
     *
     * @param string|null $description
     *
     * @return SeoTranslationInterface|$this
     */
    public function setDescription(string $description = null): SeoTranslationInterface;

    /**
     * Returns the description.
     *
     * @return string|null
     */
    public function getDescription(): ?string;

    /**
     * Sets the keywords.
     *
     * @param string|null $keywords
     *
     * @return SeoTranslationInterface|$this
     */
    public function setKeywords(string $keywords = null): SeoTranslationInterface;

    /**
     * Returns the keywords.
     *
     * @return string|null
     */
    public function getKeywords(): ?string;

    /**
     * Returns whether or not the seo translation should be considered as empty.
     *
     * @return bool
     */
    public function isEmpty(): bool;
}
