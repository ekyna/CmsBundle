<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Ekyna\Bundle\CoreBundle\Model\ImageInterface;
use Ekyna\Bundle\CoreBundle\Model\ImageTrait;

/**
 * Class ImageBlock
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class ImageBlock extends AbstractBlock implements ImageInterface
{
    use ImageTrait;

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'image';
    }
}
