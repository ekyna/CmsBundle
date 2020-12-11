<?php

namespace Ekyna\Bundle\CmsBundle\Entity\Editor;

use Ekyna\Bundle\CmsBundle\Editor\Model\DataTrait;
use Ekyna\Component\Resource\Model\AbstractTranslation;
use Ekyna\Bundle\CmsBundle\Editor\Model\BlockTranslationInterface;

/**
 * Class BlockTranslation
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class BlockTranslation extends AbstractTranslation implements BlockTranslationInterface
{
    use DataTrait;


    /**
     * @var int
     */
    protected $id;


    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }
}
