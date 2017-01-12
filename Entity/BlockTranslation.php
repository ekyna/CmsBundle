<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Ekyna\Bundle\CmsBundle\Editor\Exception\InvalidArgumentException;
use Ekyna\Bundle\CmsBundle\Editor\Model\DataTrait;
use Ekyna\Component\Resource\Model\AbstractTranslation;
use Ekyna\Bundle\CmsBundle\Model\BlockTranslationInterface;

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
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }
}
