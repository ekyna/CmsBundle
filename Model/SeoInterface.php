<?php

namespace Ekyna\Bundle\CmsBundle\Model;

/**
 * Interface SeoInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
interface SeoInterface
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
}
