<?php

namespace Ekyna\Bundle\CmsBundle\Entity;

use Ekyna\Component\Resource\Model as RM;

/**
 * Class SlideTranslation
 * @package Ekyna\Bundle\CmsBundle\Entity
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class SlideTranslation extends RM\AbstractTranslation
{
    /**
     * @var array
     */
    private $data = [];


    /**
     * Returns the data.
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Sets the data.
     *
     * @param array $data
     *
     * @return SlideTranslation
     */
    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }
}
