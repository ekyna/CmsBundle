<?php

namespace Ekyna\Bundle\CmsBundle\Model;

/**
 * Trait SeoSubjectTrait
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
trait SeoSubjectTrait
{
    /**
     * @var SeoInterface
     */
    protected $seo;

    /**
     * Returns the seo.
     *
     * @return SeoInterface
     */
    public function getSeo()
    {
        return $this->seo;
    }

    /**
     * Sets the seo.
     *
     * @param SeoInterface $seo
     * @return SeoSubjectTrait|$this
     */
    public function setSeo(SeoInterface $seo)
    {
        $this->seo = $seo;

        return $this;
    }
}
