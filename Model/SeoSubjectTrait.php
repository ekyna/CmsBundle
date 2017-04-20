<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Model;

/**
 * Trait SeoSubjectTrait
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
trait SeoSubjectTrait
{
    protected ?SeoInterface $seo = null;

    /**
     * Returns the seo.
     *
     * @return SeoInterface|null
     */
    public function getSeo(): ?SeoInterface
    {
        return $this->seo;
    }

    /**
     * Sets the seo.
     *
     * @param SeoInterface|null $seo
     *
     * @return SeoSubjectInterface|$this
     */
    public function setSeo(SeoInterface $seo = null): SeoSubjectInterface
    {
        $this->seo = $seo;

        return $this;
    }
}
