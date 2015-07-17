<?php

namespace Ekyna\Bundle\CmsBundle\Model;

use Ekyna\Bundle\AdminBundle\Model\TranslatableInterface;
use Ekyna\Bundle\CoreBundle\Model\TaggedEntityInterface;

/**
 * Interface SeoInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 *
 * @method \Ekyna\Bundle\CmsBundle\Model\SeoTranslationInterface translate($locale = null, $create = false)
 */
interface SeoInterface extends TaggedEntityInterface, TranslatableInterface
{
    /**
     * Returns the valid changefreq choices.
     *
     * @return array
     */
    public static function getChangefreqs();

    /**
     * Get id
     *
     * @return integer
     */
    public function getId();

    /**
     * Set title
     *
     * @param string $title
     *
     * @return SeoInterface|$this
     */
    public function setTitle($title);

    /**
     * Returns the title.
     *
     * @return string
     */
    public function getTitle();

    /**
     * Set description
     *
     * @param string $description
     *
     * @return SeoInterface|$this
     */
    public function setDescription($description);

    /**
     * Returns the description.
     *
     * @return string
     */
    public function getDescription();

    /**
     * Set changefreq
     *
     * @param string $changefreq
     *
     * @return SeoInterface|$this
     */
    public function setChangefreq($changefreq);

    /**
     * Returns the change frequency.
     *
     * @return string
     */
    public function getChangefreq();

    /**
     * Set priority
     *
     * @param string $priority
     *
     * @return SeoInterface|$this
     */
    public function setPriority($priority);

    /**
     * Returns the priority.
     *
     * @return string
     */
    public function getPriority();


    /**
     * Returns the follow.
     *
     * @return boolean
     */
    public function getFollow();

    /**
     * Sets the follow.
     *
     * @param boolean $follow
     * @return SeoInterface|$this
     */
    public function setFollow($follow);

    /**
     * Returns the index.
     *
     * @return boolean
     */
    public function getIndex();

    /**
     * Sets the index.
     *
     * @param boolean $index
     * @return SeoInterface|$this
     */
    public function setIndex($index);

    /**
     * Returns the canonical.
     *
     * @return string
     */
    public function getCanonical();

    /**
     * Sets the canonical.
     *
     * @param string $canonical
     * @return SeoInterface|$this
     */
    public function setCanonical($canonical);
}
