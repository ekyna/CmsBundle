<?php

namespace Ekyna\Bundle\CmsBundle\Model;

use Ekyna\Bundle\CmsBundle\Entity\Seo;

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
     * @return Seo
     */
    public function getSeo();

    /**
     * Sets the seo.
     *
     * @param Seo $seo
     * @return SeoSubjectInterface|$this
     */
    public function setSeo(Seo $seo);
}
