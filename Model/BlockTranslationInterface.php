<?php

namespace Ekyna\Bundle\CmsBundle\Model;

use Ekyna\Bundle\AdminBundle\Model\TranslationInterface;

/**
 * Interface BlockTranslationInterface
 * @package Ekyna\Bundle\CmsBundle\Model
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
interface BlockTranslationInterface extends TranslationInterface
{
    /**
     * Sets the data.
     *
     * @param array $data
     *
     * @return BlockTranslationInterface|$this
     */
    public function setData(array $data);

    /**
     * Returns the data.
     *
     * @return array
     */
    public function getData();
}
