<?php

namespace Ekyna\Bundle\CmsBundle\Model;

/**
 * BlockInterface
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
interface BlockInterface
{
    /**
     * Returns the type of the block
     */
    public function getType();
}
