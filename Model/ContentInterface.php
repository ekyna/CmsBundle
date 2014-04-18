<?php

namespace Ekyna\Bundle\CmsBundle\Model;

/**
 * ContentInterface
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
interface ContentInterface
{
    /**
     * Get blocks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBlocks();
}
