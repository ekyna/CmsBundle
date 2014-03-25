<?php

namespace Ekyna\Bundle\CmsBundle\Model;

interface ContentInterface
{
    /**
     * Get blocks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBlocks();
}
