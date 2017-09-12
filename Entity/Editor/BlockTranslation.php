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
     * @var integer
     */
    protected $id;


    /**
     * Clones the block translation.
     */
    public function __clone()
    {
        if ($this->id) {
            $this->id = null;
            $this->translatable = null;
        }
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }
}
