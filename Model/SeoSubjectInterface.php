<?php

namespace Ekyna\Bundle\CmsBundle\Model;

/**
 * Class SeoSubjectInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
interface SeoSubjectInterface
{
    /**
     * Returns the seo.
     *
     * @return SeoInterface
     */
    public function getSeo();

    /**
     * Sets the seo.
     *
     * @param SeoInterface $seo
     * @return SeoSubjectInterface|$this
     */
    public function setSeo(SeoInterface $seo);
}
