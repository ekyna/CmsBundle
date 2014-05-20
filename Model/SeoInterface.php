<?php

namespace Ekyna\Bundle\CmsBundle\Model;

/**
 * SeoInterface.
 *
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
     * Returns the title.
     *
     * @return string
     */
    public function getTitle();

    /**
     * Returns the description.
     *
     * @return string
     */
    public function getDescription();

    /**
     * Returns the change frequency.
     *
     * @return string
     */
    public function getChangefreq();

    /**
     * Returns the priority.
     *
     * @return string
     */
    public function getPriority();
}
