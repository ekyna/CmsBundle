<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Ekyna\Bundle\CoreBundle\Model;

/**
 * Class ImageBlock
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class ImageBlock extends AbstractBlock implements Model\ImageInterface
{
    use Model\ImageTrait;

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'image';
    }
}
