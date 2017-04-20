<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Model;

/**
 * Class SeoSubjectInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
interface SeoSubjectInterface
{
    /**
     * Returns the seo.
     *
     * @return SeoInterface|null
     */
    public function getSeo(): ?SeoInterface;

    /**
     * Sets the seo.
     *
     * @param SeoInterface|null $seo
     *
     * @return SeoSubjectInterface|$this
     */
    public function setSeo(SeoInterface $seo = null): SeoSubjectInterface;
}
