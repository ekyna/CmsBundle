<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Ekyna\Bundle\AdminBundle\Model\AbstractTranslation;
use Ekyna\Bundle\CmsBundle\Model\BlockTranslationInterface;

/**
 * Class BlockTranslation
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class BlockTranslation extends AbstractTranslation implements BlockTranslationInterface
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var array
     */
    protected $data;


    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the data.
     *
     * @param array $data
     *
     * @return BlockTranslation
     */
    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Returns the data.
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}
