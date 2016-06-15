<?php

namespace Ekyna\Bundle\CmsBundle\Editor\View;

/**
 * Class ContainerView
 * @package Ekyna\Bundle\CmsBundle\Editor\View
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class ContainerView
{
    /**
     * @var array
     */
    public $attributes = [];

    /**
     * @var array
     */
    public $innerAttributes = [];

    /**
     * @var array|RowView[]
     */
    public $rows = [];
}
