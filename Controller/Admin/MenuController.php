<?php

namespace Ekyna\Bundle\CmsBundle\Controller\Admin;

use Ekyna\Bundle\AdminBundle\Controller\Resource;
use Ekyna\Bundle\AdminBundle\Controller\ResourceController;

/**
 * Class MenuController
 * @package Ekyna\Bundle\CmsBundle\Controller\Admin
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class MenuController extends ResourceController
{
    use Resource\NestedTrait,
        Resource\ToggleableTrait;
}
