<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Ekyna\Bundle\MediaBundle\Model as Media;

/**
 * Class ImageBlock
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class ImageBlock extends AbstractBlock implements Media\MediaSubjectInterface
{
    use Media\MediaSubjectTrait;

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'image';
    }

    /**
     * {@inheritdoc}
     */
    public static function getEntityTagPrefix()
    {
        return 'ekyna_cms.image_block';
    }
}
