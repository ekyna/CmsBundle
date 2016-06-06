<?php

namespace Ekyna\Bundle\CmsBundle\Editor\View;

/**
 * Class Block
 * @package Ekyna\Bundle\CmsBundle\Editor\View
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class Block
{
    /**
     * @var array
     */
    public $columnAttributes = [];

    /**
     * @var array
     */
    public $blockAttributes = []; // TODO plugin attributes ?

    /**
     * @var string
     */
    public $content = '';
}
