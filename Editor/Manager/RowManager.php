<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Manager;

use Ekyna\Bundle\CmsBundle\Model\RowInterface;

/**
 * Class RowManager
 * @package Ekyna\Bundle\CmsBundle\Editor\Manager
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class RowManager
{
    /**
     * Fix the row layout.
     *
     * @param RowInterface $row
     */
    public function fixLayout(RowInterface $row)
    {
        $blocks = $row->getBlocks();

        $total = 0;
        foreach ($blocks as $block) {
            $total += $block->getSize();
        }

        $diff = 12 - $total;
        if (0 == $diff) {
            return;
        }

        if (0 < $diff) { // too small
            $avg = ceil(12 / $blocks->count());
            $mod = 1;
        } else { // too large
            $avg = floor(12 / $blocks->count());
            $mod = -1;
        }

        while (0 != $diff) {
            foreach ($blocks as $block) {
                if ($avg != $block->getSize()) {
                    $block->setSize($block->getSize() + $mod);
                    $diff -= $mod;

                    if (0 == $diff) {
                        break 2;
                    }
                }
            }
            reset($blocks);
        }
    }
}
