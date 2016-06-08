<?php

namespace Ekyna\Bundle\CmsBundle\Editor\View;

/**
 * Class RowView
 * @package Ekyna\Bundle\CmsBundle\Editor\View
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class RowView
{
    /**
     * @var array
     */
    public $attributes = [];

    /**
     * @var array|BlockView[]
     */
    public $blocks = [];
}
