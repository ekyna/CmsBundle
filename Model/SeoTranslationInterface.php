<?php

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
     * @param string $title
     *
     * @return SeoTranslationInterface|$this
     */
    public function setTitle($title);

    /**
     * Returns the title.
     *
     * @return string
     */
    public function getTitle();

    /**
     * Sets the description.
     *
     * @param string $description
     *
     * @return SeoTranslationInterface|$this
     */
    public function setDescription($description);

    /**
     * Returns the description.
     *
     * @return string
     */
    public function getDescription();

    /**
     * Sets the keywords.
     *
     * @param mixed $keywords
     *
     * @return SeoTranslationInterface|$this
     */
    public function setKeywords($keywords);

    /**
     * Returns the keywords.
     *
     * @return mixed
     */
    public function getKeywords();

    /**
     * Returns whether or not the seo translation should be considered as empty.
     *
     * @return bool
     */
    public function isEmpty();
}
